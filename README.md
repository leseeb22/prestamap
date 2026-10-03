# Prestamap

Générateur de sitemap XML autonome pour **PrestaShop**, sans module supplémentaire.

Le projet tient volontairement dans un fichier PHP afin de rester simple à installer, auditer et retirer.

## Fonctionnalités

- génération des URL produits, catégories, fabricants, fournisseurs et pages CMS ;
- prise en compte des routes PrestaShop ;
- génération de `sitemap.xml` ;
- évite une régénération inutile lorsqu'un sitemap du jour existe déjà ;
- intégration possible via une règle de réécriture Apache.

## Installation

1. Copier `sitemap.php` à la racine de votre installation PrestaShop.
2. Vérifier que PHP peut lire la configuration PrestaShop et écrire `sitemap.xml`.
3. Appeler le script depuis le navigateur ou votre système d'automatisation.

```text
https://votre-domaine.tld/sitemap.php
```

## Prérequis

- PrestaShop ;
- PHP 7+ ;
- accès à la base de données de la boutique ;
- Apache/mod_rewrite si vous utilisez la réécriture vers `sitemap.xml`.

## Philosophie

Prestamap privilégie une approche **autonome, légère et lisible** : pas de dépendance applicative supplémentaire et pas de module à maintenir.

## Prudence

Testez le script sur un environnement de préproduction avant usage sur une boutique en production, notamment si votre configuration d'URL ou votre `.htaccess` est personnalisé.

## Auteur

Sébastien Vidotto — Heteractis  
Architecture numérique, développement web sur mesure et automatisation.

## Licence

Voir [LICENSE](LICENSE).
