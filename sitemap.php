<?php
/*
  -------------------------------------------
  ___  ____ ____ ____ ___ ____ _  _ ____ ___  
  |__] |__/ |___ [__   |  |__| |\/| |__| |__] 
  |    |  \ |___ ___]  |  |  | |  | |  | |  

  -------------------------------------------
  
  PrestaMap - Votre Générateur de Sitemap Automatique pour PrestaShop
  Auteur : Sébastien VIDOTTO
  Version : 1.0
  Date : 28 Spetembre 2023
  Licence : MIT
  
  Description :
  -------------
  PrestaMap est un script PHP qui génère automatiquement un fichier sitemap.xml pour votre site PrestaShop.
  Il prend en charge les règles de réécriture d'URL personnalisées et s'occupe également de la configuration du fichier .htaccess.
  
  Avantages :
  -----------
  1. Gestion automatique du fichier .htaccess.
  2. Connexion automatique à votre base de données PrestaShop.
  3. Pas besoin de tâches cron.
  4. Votre sitemap est toujours à jour.
  
  Routes :
  --------
  * Route vers les produits : {id}{-:id_product_attribute}-{rewrite}.html
  * Route vers la catégorie : boutique-{id}-{rewrite}
  * Route vers les fournisseurs : fournisseurs-{id}__{rewrite}
  * Route vers les marques : marques-{id}_{rewrite}
  * Route vers les pages : {id}-{rewrite}
  * Route vers les catégories de pages : categorie/{id}-{rewrite}
  * Route vers les modules : module/{module}{/:controller}
  
*/


error_reporting(E_ALL); ini_set("display_errors", 1); 

	// Définition des variables pour la gestion du fichier .htaccess
	
	$htaccessFile = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess'; // Chemin vers le fichier .htaccess
	
	
    $rewriteRule = "RewriteRule ^sitemap\\.xml$ /sitemap.php [L]\n"; // Règle de réécriture pour rediriger sitemap.xml vers sitemap.php
    $modRewriteCheck = "<IfModule mod_rewrite.c>"; // Vérification de la présence du module mod_rewrite
	
	// Vérifie si le fichier .htaccess existe
    if (file_exists($htaccessFile)) {
        $htaccessContent = file_get_contents($htaccessFile); // Lecture du contenu du fichier .htaccess
		
		 // Vérifie si la règle de réécriture n'est pas déjà présente
        if (strpos($htaccessContent, $rewriteRule) === false) {
            // Vérifie si la condition mod_rewrite existe déjà
            if (strpos($htaccessContent, $modRewriteCheck) !== false) {
                // Ajoute la règle à l'intérieur de la section mod_rewrite existante
                $newContent = preg_replace("/(<IfModule mod_rewrite.c>)/", "$1\n    " . $rewriteRule, $htaccessContent);
                file_put_contents($htaccessFile, $newContent);
            } else {
                // Ajoute une nouvelle section mod_rewrite
                $rewriteRule = $modRewriteCheck . "\n    " . $rewriteRule . "</IfModule>\n";
                file_put_contents($htaccessFile, $rewriteRule, FILE_APPEND);
            }
        }
    } else {
        // Crée un nouveau fichier .htaccess avec la section mod_rewrite
        $rewriteRule = $modRewriteCheck . "\n    " . $rewriteRule . "</IfModule>\n";
        file_put_contents($htaccessFile, $rewriteRule);
    }






// Construction du chemin vers le fichier parameters.php
$parametersPath = $_SERVER['DOCUMENT_ROOT'] . '/app/config/parameters.php';

// Inclusion des paramètres de PrestaShop
$parameters = include($parametersPath);

// Connexion à la base de données PrestaShop
$servername = $parameters['parameters']['database_host'];
$username = $parameters['parameters']['database_user'];
$password = $parameters['parameters']['database_password'];
$dbname = $parameters['parameters']['database_name'];
$port = $parameters['parameters']['database_port'];

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Détection de l'URL du serveur
$serverURL = 'https://' . $_SERVER['HTTP_HOST'];

// Si le fichier sitemap.xml existe déjà, retournez-le
if (file_exists('sitemap.xml')) {
    $sitemap = simplexml_load_file('sitemap.xml');
    header('Content-Type: text/xml');
    echo $sitemap->asXML();
	 $fileTime = filemtime('sitemap.xml');
    $currentTime = time();
    if (($currentTime - $fileTime) < 86400) { // 86400 secondes dans une journée
        $regenerateSitemap = false;
    }
}

// if en cas de spam ( En cours de test )

//if ($regenerateSitemap) {

		// Création du fichier XML
		$dom = new DOMDocument("1.0", "UTF-8");
		$urlset = $dom->createElement("urlset");
		$urlset->setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
		$dom->appendChild($urlset);

		// Récupération des URL des catégories
		$sql = "SELECT cl.id_category, cl.link_rewrite FROM " . $parameters['parameters']['database_prefix'] . "category_lang AS cl JOIN " . $parameters['parameters']['database_prefix'] . "category AS c ON cl.id_category = c.id_category WHERE cl.id_lang = 1 AND c.id_parent != 0";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$url = $dom->createElement("url");
				$loc = $dom->createElement("loc", $serverURL . "/boutique-" . $row["id_category"] . "-" . $row["link_rewrite"]);
				$url->appendChild($loc);
				$urlset->appendChild($url);
			}
		}

		// Récupération des URL des produits
		$sql = "SELECT id_product, link_rewrite FROM " . $parameters['parameters']['database_prefix'] . "product_lang WHERE id_lang = 1";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$url = $dom->createElement("url");
				$loc = $dom->createElement("loc", $serverURL . "/" . $row["id_product"] . "-" . $row["link_rewrite"] . ".html");
				$url->appendChild($loc);
				$urlset->appendChild($url);
			}
		}

		// Récupération des URL des pages CMS
		$sql = "SELECT id_cms, link_rewrite FROM " . $parameters['parameters']['database_prefix'] . "cms_lang WHERE id_lang = 1";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$url = $dom->createElement("url");
				$loc = $dom->createElement("loc", $serverURL . "/" . $row["id_cms"] . "-" . $row["link_rewrite"]);
				$url->appendChild($loc);
				$urlset->appendChild($url);
			}
		}

/*


// Récupération des URL des fournisseurs
$sql = "SELECT id_supplier, name FROM " . $parameters['parameters']['database_prefix'] . "supplier_lang WHERE id_lang = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $url = $dom->createElement("url");
        $loc = $dom->createElement("loc", $serverURL . "/fournisseurs-" . $row["id_supplier"] . "__" . $row["name"]);
        $url->appendChild($loc);
        $urlset->appendChild($url);
    }
}

// Récupération des URL des marques
$sql = "SELECT id_manufacturer, name FROM " . $parameters['parameters']['database_prefix'] . "manufacturer_lang WHERE id_lang = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $url = $dom->createElement("url");
        $loc = $dom->createElement("loc", $serverURL . "/marques-" . $row["id_manufacturer"] . "_" . $row["name"]);
        $url->appendChild($loc);
        $urlset->appendChild($url);
    }
}

// Récupération des URL des catégories de pages
$sql = "SELECT id_cms_category, link_rewrite FROM " . $parameters['parameters']['database_prefix'] . "cms_category_lang WHERE id_lang = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $url = $dom->createElement("url");
        $loc = $dom->createElement("loc", $serverURL . "/categorie/" . $row["id_cms_category"] . "-" . $row["link_rewrite"]);
        $url->appendChild($loc);
        $urlset->appendChild($url);
    }
}
*/

	// Enregistrement du nouveau fichier XML
	$dom->save("sitemap.xml");
//}
$conn->close();
?>

