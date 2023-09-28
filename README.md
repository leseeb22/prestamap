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

🌟 Découvrez le Générateur de Sitemap Automatique pour PrestaShop : Votre Assistant SEO Invisible ! 🌟

L'Histoire 📖
Imaginez un monde où votre site PrestaShop est toujours en phase avec les moteurs de recherche, où chaque nouveau produit ou catégorie est instantanément indexé, sans que vous ayez à lever le petit doigt. Cela semble trop beau pour être vrai ? Pas avec notre Générateur de Sitemap Automatique !

Pourquoi ce Script est Votre Nouveau Meilleur Ami 🤝
Il Prend Soin de Votre .htaccess : Vous vous souvenez de ce fichier .htaccess intimidant ? Oubliez les heures passées à comprendre comment ajouter une règle de réécriture. Notre script s'en occupe pour vous ! 🛠️

Connexion Automatique à Votre PrestaShop : Pas besoin de configurer des chaînes de connexion compliquées. Le script utilise intelligemment votre fichier parameters.php pour se connecter à votre base de données. 🌐

Pas Besoin de Cron Jobs : Oubliez les tâches cron compliquées. Ce script est si intelligent qu'il n'a pas besoin d'être exécuté à intervalles réguliers. Il fait tout le travail à chaque appel, en un clin d'œil ! ⏲️

Votre Sitemap est Toujours à Jour : Chaque fois que vous ajoutez un nouveau produit, une nouvelle catégorie ou même une nouvelle page, le script le sait. Votre sitemap est toujours frais comme une rose, prêt à être cueilli par les moteurs de recherche. 🌹

Comment ça Marche ? 🤔
Placez simplement le fichier sitemap.php à la racine de votre site PrestaShop et laissez-le faire sa magie. Accédez à http://votre-domaine.com/sitemap.php et voilà ! Votre sitemap est généré, sauvegardé et prêt à être exploré par les moteurs de recherche.

Les Avantages 🎉
Facilité d'Installation : Un seul fichier à placer, et vous êtes prêt à partir.
Autonomie : Pas besoin de maintenance régulière ou de configuration compliquée.
Performance Optimisée : Le script est conçu pour être rapide et efficace, minimisant l'impact sur les performances de votre site.
Conclusion 🌈
Dans le monde du SEO, chaque petit avantage compte. Notre Générateur de Sitemap Automatique pour PrestaShop est l'outil que vous ne saviez pas qu'il vous fallait, mais que vous ne pourrez plus jamais abandonner. Essayez-le aujourd'hui et donnez à votre site l'attention qu'il mérite ! 🚀

Alors, qu'attendez-vous ? Faites le premier pas vers un SEO plus intelligent et plus efficace dès aujourd'hui ! 🌟

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
