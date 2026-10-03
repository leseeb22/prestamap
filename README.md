# Prestamap

Générateur de sitemap XML autonome pour PrestaShop, développé par **Sébastien Vidotto — Heteractis**. Un seul fichier PHP à installer, sans module ni dépendance applicative supplémentaire.

## Ce que fait cette version

- Exporte les catégories, les produits et les pages CMS selon les routes ci-dessous.
- Renvoie le XML dès le premier appel et enregistre `sitemap.xml` à côté du script.
- Réutilise un sitemap valide pendant **24 heures depuis sa génération**, sans connexion MySQL.
- Régénère un cache expiré ou malformé.
- Remplace le fichier par renommage après une écriture complète ; une erreur conserve le précédent sitemap et renvoie HTTP 503.
- Ne modifie **jamais** le `.htaccess` et ne renvoie pas les détails des erreurs internes au visiteur.

## Périmètre et limites à vérifier avant installation

Ce script historique n'est pas encore un générateur universel pour toutes les configurations PrestaShop :

- configuration attendue dans `app/config/parameters.php` ; aucune compatibilité avec toutes les versions PrestaShop n'est certifiée ;
- boutique unique, à la racine d'un domaine HTTPS ;
- langue fixée à `id_lang = 1` dans les requêtes ;
- routes codées en dur, sans lecture des routes configurées dans PrestaShop ;
- absence de filtrage des produits, catégories et pages CMS inactifs ou non indexables ;
- absence de gestion multiboutique, multilingue, déclinaisons et dédoublonnage ;
- fournisseurs, marques et catégories CMS **non exportés** ;
- absence de découpage en plusieurs sitemaps pour les catalogues volumineux ;
- domaine déduit de l'en-tête HTTP Host : le serveur doit accepter uniquement le domaine canonique de la boutique pour éviter un cache contenant un autre domaine.

Les routes de votre boutique doivent correspondre exactement à celles-ci :

| Contenu | Route produite |
| --- | --- |
| Catégorie | `/boutique-{id_category}-{link_rewrite}` |
| Produit | `/{id_product}-{link_rewrite}.html` |
| Page CMS | `/{id_cms}-{link_rewrite}` |

**Vérifiez les URL générées et le périmètre des contenus sur une préproduction avant de soumettre le sitemap aux moteurs.** Si votre boutique utilise d'autres routes ou contient des contenus qui doivent être exclus, adaptez les requêtes et les routes avant utilisation.

## Installation

1. Copier uniquement `sitemap.php` à la racine de la boutique, à côté du dossier `app`.
2. Utiliser PHP avec les extensions `mysqli`, `dom` et `libxml`. Le code utilise la syntaxe PHP 7+ ; les tests automatisés couvrent PHP 7.4 et PHP 8.3. Choisir une version PHP maintenue et compatible avec votre boutique.
3. Autoriser PHP à lire la configuration et à écrire dans le répertoire du script. Ne pas rendre tout le site accessible en écriture à tous les utilisateurs.
4. Ouvrir `https://votre-domaine.tld/sitemap.php` : la réponse et le fichier `sitemap.xml` contiennent le même document.

Le cache dure 24 heures. Une modification du catalogue ne déclenche pas de régénération immédiate. Pour la forcer, supprimer le fichier généré puis rappeler `sitemap.php`.

### Actualisation automatique via Apache

Sans réécriture, l'accès direct au fichier statique `sitemap.xml` ne lance pas PHP. Pour actualiser à la demande, ajouter **manuellement** cette règle avant les règles PrestaShop et les conditions qui court-circuitent les fichiers existants :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^sitemap\.xml$ sitemap.php [L]
</IfModule>
```

Sauvegarder le `.htaccess` avant toute édition. Si vous aviez utilisé l'ancienne version, vérifier la règle qu'elle a pu ajouter et éviter les doublons. Sous Nginx, configurer l'équivalent côté serveur ou déclencher régulièrement une requête HTTP vers `sitemap.php`. L'exécution directe en CLI n'est pas prise en charge : le script attend un hôte HTTP.

## Tests

```sh
php -l sitemap.php
php tests/sitemap_test.php
```

Les tests isolent uniquement MySQL avec un double dans un espace de noms. Ils exécutent le script réel sur des fichiers temporaires et vérifient le XML, le cache, les chemins, les erreurs et la préservation du `.htaccess`. Ils ne valident pas le schéma SQL d'une version PrestaShop, le routage Apache, ni une boutique réelle.

La CI exécute ces contrôles sur les pull requests et sur `main`.

## Licence

[MIT](LICENSE).
