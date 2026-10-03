<?php
/**
 * Prestamap — générateur autonome de sitemap pour PrestaShop.
 * Sébastien VIDOTTO — Heteractis — Licence MIT.
 * Les routes historiques et id_lang = 1 sont conservés : voir README.md.
 */

// Les erreurs ne doivent jamais corrompre le XML ou exposer la configuration.
ini_set('display_errors', '0');
$sitemapFile = __DIR__ . '/sitemap.xml';
$temporaryFile = null;
$conn = null;

try {
    // Servir le cache avant de charger la configuration ou de contacter MySQL.
    clearstatcache(true, $sitemapFile);
    if (is_file($sitemapFile) && time() - filemtime($sitemapFile) < 86400) {
        $cachedXml = @file_get_contents($sitemapFile);
        $cacheDocument = new DOMDocument();
        $previousErrors = libxml_use_internal_errors(true);
        try {
            $validCache = is_string($cachedXml) && $cachedXml !== ''
                && $cacheDocument->loadXML($cachedXml, LIBXML_NONET)
                && $cacheDocument->doctype === null
                && $cacheDocument->documentElement->localName === 'urlset'
                && $cacheDocument->documentElement->namespaceURI === 'http://www.sitemaps.org/schemas/sitemap/0.9';
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }
        if ($validCache) {
            header('Content-Type: application/xml; charset=UTF-8');
            echo $cachedXml;
            return;
        }
    }

    $parametersPath = __DIR__ . '/app/config/parameters.php';
    if (!is_readable($parametersPath)) {
        throw new RuntimeException('PrestaShop configuration unavailable');
    }
    $configuration = include $parametersPath;
    if (!is_array($configuration) || !isset($configuration['parameters'])) {
        throw new RuntimeException('Invalid PrestaShop configuration');
    }
    $parameters = $configuration['parameters'];
    foreach (array('database_host', 'database_user', 'database_password', 'database_name', 'database_prefix') as $key) {
        if (!isset($parameters[$key]) || !is_string($parameters[$key])) {
            throw new RuntimeException('Missing database configuration');
        }
    }
    $prefix = $parameters['database_prefix'];
    if (!preg_match('/^[a-zA-Z0-9_]+$/D', $prefix)) {
        throw new RuntimeException('Invalid database prefix');
    }
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    if (!is_string($host) || !preg_match('/^[a-zA-Z0-9.-]+(?::[0-9]{1,5})?$/D', $host)) {
        throw new RuntimeException('A valid HTTP host is required');
    }
    // Comportement historique : HTTPS, boutique à la racine du domaine.
    $serverURL = 'https://' . $host;
    $port = !empty($parameters['database_port']) ? (int) $parameters['database_port'] : 3306;
    $conn = new mysqli($parameters['database_host'], $parameters['database_user'], $parameters['database_password'], $parameters['database_name'], $port);
    if ($conn->connect_error || !$conn->set_charset('utf8mb4')) {
        throw new RuntimeException('Database connection unavailable');
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $urlset = $dom->createElement('urlset');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $dom->appendChild($urlset);

    // Requêtes et formats historiques : aucune nouvelle promesse de compatibilité.
    $sources = array(
        array(
            'sql' => "SELECT cl.id_category, cl.link_rewrite FROM {$prefix}category_lang AS cl JOIN {$prefix}category AS c ON cl.id_category = c.id_category WHERE cl.id_lang = 1 AND c.id_parent != 0",
            'id' => 'id_category', 'prefix' => '/boutique-', 'suffix' => ''
        ),
        array(
            'sql' => "SELECT id_product, link_rewrite FROM {$prefix}product_lang WHERE id_lang = 1",
            'id' => 'id_product', 'prefix' => '/', 'suffix' => '.html'
        ),
        array(
            'sql' => "SELECT id_cms, link_rewrite FROM {$prefix}cms_lang WHERE id_lang = 1",
            'id' => 'id_cms', 'prefix' => '/', 'suffix' => ''
        )
    );
    foreach ($sources as $source) {
        $result = $conn->query($source['sql']);
        if ($result === false) {
            throw new RuntimeException('Sitemap query failed');
        }
        while ($row = $result->fetch_assoc()) {
            $url = $dom->createElement('url');
            $loc = $dom->createElement('loc');
            $loc->appendChild($dom->createTextNode($serverURL . $source['prefix'] . $row[$source['id']] . '-' . $row['link_rewrite'] . $source['suffix']));
            $url->appendChild($loc);
            $urlset->appendChild($url);
        }
    }
    $xml = $dom->saveXML();
    if ($xml === false) {
        throw new RuntimeException('XML generation failed');
    }
    // Ne remplacer l'ancien sitemap qu'une fois le nouveau entièrement écrit.
    $temporaryFile = @tempnam(__DIR__, '.prestamap-');
    if ($temporaryFile === false || dirname($temporaryFile) !== __DIR__
        || @file_put_contents($temporaryFile, $xml, LOCK_EX) !== strlen($xml)
        || !@chmod($temporaryFile, 0644)
        || !@rename($temporaryFile, $sitemapFile)) {
        throw new RuntimeException('Sitemap publication failed');
    }
    $temporaryFile = null;
    header('Content-Type: application/xml; charset=UTF-8');
    echo $xml;
} catch (Throwable $error) {
    // Ni SQL, ni identifiants, ni trace d'exception dans la réponse publique.
    error_log('Prestamap: sitemap generation failed; check configuration, database and directory permissions.');
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Retry-After: 300');
    echo "Sitemap temporairement indisponible.\n";
} finally {
    if (is_string($temporaryFile) && is_file($temporaryFile)) {
        @unlink($temporaryFile);
    }
    if ($conn !== null) {
        $conn->close();
    }
}
