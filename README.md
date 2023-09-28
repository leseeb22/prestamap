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


Fonctionnement 

Le fichier sitemap.php est un script PHP conçu pour générer un fichier sitemap XML pour un site web PrestaShop. Voici un résumé de ses fonctionnalités et actions :

Connexion à la Base de Données : Le script utilise les paramètres de connexion à la base de données stockés dans le fichier parameters.php de PrestaShop.

Vérification du .htaccess : Le script vérifie si une règle de réécriture pour rediriger sitemap.xml vers sitemap.php existe dans le fichier .htaccess. Si elle n'existe pas, le script l'ajoute.

Génération du Sitemap :

Le script crée un nouveau document XML.
Il récupère les URL des catégories, des produits, des fournisseurs, des marques et des pages CMS depuis la base de données PrestaShop.
Il ajoute ces URL au document XML en suivant le format sitemap standard.
Règles de Réécriture d'URL : Le script prend en compte les règles de réécriture d'URL personnalisées pour les catégories, les produits, etc.

Optimisation de la Performance :

Si un fichier sitemap.xml a déjà été généré le jour même, le script retourne ce fichier existant pour éviter un temps de chargement inutile.
Malgré cela, le script régénère le fichier sitemap.xml à chaque appel pour s'assurer qu'il est à jour, sauf s'il a été généré le jour même.
Sauvegarde du Fichier XML : Enfin, le fichier XML généré est sauvegardé en tant que sitemap.xml à la racine du site web.

Le script est conçu pour être aussi autonome que possible, nécessitant peu ou pas de configuration manuelle une fois placé à la racine de votre installation PrestaShop.


Personnalisation des règles d'URL
Le script prend en charge des règles de réécriture d'URL personnalisées. Vous pouvez les ajuster dans la section correspondante du script.


Si vous souhaitez utiliser les mêmes rèfles que moi voici les paramètre que vous devez changer dans 
> Paramètres de la boutique 
>> Trafic et SEO
>>> SEO & URL


* Route vers les produits
{id}{-:id_product_attribute}-{rewrite}.html

* Route vers la catégorie
boutique-{id}-{rewrite}

* Route vers les fournisseurs
fournisseurs-{id}__{rewrite}

* Route vers les marques
marques-{id}_{rewrite}

* Route vers les pages
{id}-{rewrite}

* Route vers les catégories de pages
categorie/{id}-{rewrite}

* Route vers les modules
module/{module}{/:controller}

Contribution

Les contributions sont les bienvenues. Contactez moi pour  ouvrir une issue ou à soumettre une pull request.



Licence
Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.
