<?php
namespace PrestamapTest;
use DOMDocument;
use RuntimeException;
// Run: php tests/sitemap_test.php
// A namespace isolates the database double; XML, filesystem and entry point are real.
class mysqli
{
    public static $connections = 0;
    public static $fail = false;
    public static $slug = 'cafe';
    public $connect_error = null;
    public function __construct(...$args) { self::$connections++; }
    public function set_charset($charset) { return true; }
    public function query($sql) {
        if (self::$fail) { throw new RuntimeException('database-password-must-not-leak'); }
        if (strpos($sql, 'category_lang') !== false) {
            $rows = array(array('id_category' => 2, 'link_rewrite' => 'accessoires'));
        } elseif (strpos($sql, 'product_lang') !== false) {
            $rows = array(array('id_product' => 7, 'link_rewrite' => self::$slug));
        } elseif (strpos($sql, 'cms_lang') !== false) {
            $rows = array(array('id_cms' => 3, 'link_rewrite' => 'livraison'));
        } else { throw new RuntimeException('Unexpected query'); }
        return new class($rows) {
            public $num_rows;
            private $rows;
            public function __construct($rows) { $this->rows = $rows; $this->num_rows = count($rows); }
            public function fetch_assoc() { return array_shift($this->rows); }
        };
    }
    public function close() {}
}

function check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
function runSitemap($directory) {
    ob_start();
    try { include $directory . '/sitemap.php'; return ob_get_contents(); }
    finally { ob_end_clean(); }
}
function removeFixture($path) {
    foreach (scandir($path) as $name) {
        if ($name === '.' || $name === '..') { continue; }
        $file = $path . '/' . $name;
        if (is_dir($file)) { removeFixture($file); } else { unlink($file); }
    }
    rmdir($path);
}

$fixture = sys_get_temp_dir() . '/prestamap-' . uniqid();
mkdir($fixture . '/app/config', 0777, true);
file_put_contents($fixture . '/sitemap.php', preg_replace('/^<\?php/', '<?php namespace PrestamapTest; use \\DOMDocument; use \\RuntimeException; use \\Throwable;', file_get_contents(dirname(__DIR__) . '/sitemap.php'), 1));
file_put_contents($fixture . '/app/config/parameters.php', '<?php return ' . var_export(array('parameters' => array(
    'database_host' => 'localhost', 'database_user' => 'test', 'database_password' => 'test',
    'database_name' => 'test', 'database_port' => 3306, 'database_prefix' => 'ps_'
)), true) . ';');
file_put_contents($fixture . '/.htaccess', "# existing shop rules\n");
ini_set('error_log', $fixture . '/error.log');
$_SERVER['DOCUMENT_ROOT'] = $fixture;
$_SERVER['HTTP_HOST'] = 'shop.example.test';
$originalDirectory = getcwd();
chdir($fixture);
try {
    $first = runSitemap($fixture);
    $xml = new DOMDocument();
    check(@$xml->loadXML($first), 'First request must return a valid XML document');
    check($xml->getElementsByTagName('loc')->length === 3, 'All three supported entity types must be returned');
    check($xml->getElementsByTagName('loc')->item(1)->textContent === 'https://shop.example.test/7-cafe.html', 'Existing product route must be preserved');
    check(file_get_contents($fixture . '/sitemap.xml') === $first, 'Response must match saved sitemap');
    check(file_get_contents($fixture . '/.htaccess') === "# existing shop rules\n", 'Requests must not modify shop routing');

    $connections = mysqli::$connections;
    mysqli::$fail = true;
    check(runSitemap($fixture) === $first, 'Fresh cache must remain usable when database is unavailable');
    check(mysqli::$connections === $connections, 'Fresh cache must bypass the database');

    touch($fixture . '/sitemap.xml', time() - 90000);
    clearstatcache();
    $error = runSitemap($fixture);
    check(http_response_code() === 503, 'Failed regeneration must return 503');
    check(strpos($error, 'database-password') === false, 'Public response must not expose exception details');
    check(file_get_contents($fixture . '/sitemap.xml') === $first, 'Failed regeneration must preserve old sitemap');

    mysqli::$fail = false;
    http_response_code(200);
    chdir(sys_get_temp_dir());
    check(runSitemap($fixture) === $first, 'Expired cache must regenerate from script directory, independent of working directory');
    check(time() - filemtime($fixture . '/sitemap.xml') < 10, 'Expired cache must be replaced');

    file_put_contents($fixture . '/sitemap.xml', 'broken XML');
    check(runSitemap($fixture) === $first, 'Malformed cache must be regenerated');
    touch($fixture . '/sitemap.xml', time() - 90000);
    mysqli::$slug = 'café&the';
    $escaped = runSitemap($fixture);
    check(@$xml->loadXML($escaped), 'Special characters must not corrupt XML');
    check($xml->getElementsByTagName('loc')->item(1)->textContent === 'https://shop.example.test/7-café&the.html', 'XML text must preserve accents and ampersands');

    unlink($fixture . '/sitemap.xml');
    mkdir($fixture . '/sitemap.xml');
    http_response_code(200);
    $failedWrite = runSitemap($fixture);
    check(http_response_code() === 503, 'Failed publication must return 503');
    check(strpos($failedWrite, '<?xml') === false, 'Failed publication must not advertise a successful sitemap');
    check(count(glob($fixture . '/.prestamap-*')) === 0, 'Failed publication must remove temporary files');
    check(strpos(file_get_contents($fixture . '/error.log'), 'database-password') === false, 'Logs must not expose database exception details');
    echo "PASS: generation, cache, routing preservation, failure recovery, paths, malformed cache\n";
} finally {
    chdir($originalDirectory);
    removeFixture($fixture);
}
