# prestashop-sitemap
Ce script PHP auto-génère un fichier sitemap XML pour un site PrestaShop. Il récupère les URL des catégories, des produits, des fournisseurs, des marques et des pages CMS pour les inclure dans le sitemap.
Générateur de Sitemap pour PrestaShop
Description
Ce script PHP génère un fichier sitemap XML pour un site PrestaShop. Il récupère les URL des catégories, des produits, des fournisseurs, des marques et des pages CMS pour les inclure dans le sitemap. Le script prend également en charge des règles de réécriture d'URL personnalisées.

Fonctionnalités
Génère un fichier sitemap.xml pour les moteurs de recherche.
Prend en charge des règles de réécriture d'URL personnalisées.
Vérifie et ajoute une règle de réécriture dans le fichier .htaccess si nécessaire.
Option pour ne pas régénérer le sitemap si celui-ci a été généré le jour même.
Prérequis
Serveur web Apache avec mod_rewrite activé.
PHP 7.0 ou supérieur.
Accès à la base de données PrestaShop.
Installation
Téléchargez le fichier sitemap.php et placez-le à la racine de votre installation PrestaShop.
Assurez-vous que le fichier parameters.php de PrestaShop est accessible pour que le script puisse se connecter à la base de données.
Donnez les permissions d'écriture au fichier .htaccess pour que le script puisse ajouter la règle de réécriture.
Utilisation
Accédez simplement à http://votre-domaine.com/sitemap.php pour générer le fichier sitemap.xml. Si le fichier a déjà été généré le jour même, le script retournera le fichier existant pour éviter des temps de chargement inutiles.

Personnalisation des règles d'URL
Le script prend en charge des règles de réécriture d'URL personnalisées. Vous pouvez les ajuster dans la section correspondante du script.

Contribution
Les contributions sont les bienvenues. N'hésitez pas à ouvrir une issue ou à soumettre une pull request.

Licence
Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.
