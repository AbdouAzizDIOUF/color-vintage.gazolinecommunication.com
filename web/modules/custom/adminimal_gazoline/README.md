INSTALLATION
============

Via composer
------------
Ajouter les lignes suivantes dans le `composer.json` du projet pour utiliser le dépôt contenant le module :

```
{
        "type": "git",
        "url": "https://git.gazolinecommunication.com/gazoline/adminimal_gazoline.git"
}
```

Ces lignes sont à ajouter dans le bloc `repositories`.

Installer ensuite le module avec un `composer require drupal/adminimal_gazoline`.

A l'ancienne
------------
 - Installer le thème Adminimal (https://www.drupal.org/project/adminimal_theme),
 - Installer le module Adminimal Admin Toolbar (https://www.drupal.org/project/adminimal_admin_toolbar),
 - Installer le module Font Awesome Icons **en version 2.0 de préférence** (https://www.drupal.org/project/fontawesome),
 - Installer le module Adminimal Gazoline.

Facultatif
----------
Installer le module FontAwesome Menu Icons (https://www.drupal.org/project/fontawesome_menu_icons) avec la bibliothèque fontawesome-iconpicker (https://github.com/farbelous/fontawesome-iconpicker/releases) **en version 1.3.1 minimum** pour ajouter des icônes aux items du menu.

CONFIGURATION
=============

Se rendre dans `admin/config/system/adminimal-gazoline` pour choisir les couleurs du logo et d'afficher ou non les raccourcis.

INFORMATIONS
============

Lors de l'installation, le menu "Menu admin Gazoline" (`menu-admin`) est créé, et le bloc "Menu admin" (catégorie Adminimal Gazoline) est disponible.
Ajouter ce bloc à la région Content (Contenu) **du thème Adminimal**.

Vider les caches Drupal si le bloc n'apparaît pas après validation.

UTILISATION
===========

Ajouter simplement les liens voulus au menu.

Le support de FontAwesome Menu Icons permet d'ajouter des icônes aux items du menu, mais le placement n'est pas pris en compte. Les icônes seront toujours placées devant l'intitulé.

DESINSTALLATION
===============

Après suppression du module le menu `menu-admin` est toujours disponible. En cas de réinstallation du module supprimer d'abord le menu.

HISTORIQUE
==========

v.1.3.1
 - Fix cote manquante

v1.3
 - Ajout de la configuration du menu

v1.2.3
 - Ajout dans le README de la nouvelle méthode d'installation

v1.2.2
 - Ajout du fichier composer.json

v1.2.1
 - Fix problème de permission

v1.2 :
 - Corrections de bugs
 - Améliorations visuelles
 - Les modules Font Awesome Icons (https://www.drupal.org/project/fontawesome) et Adminimal Admin Toolbar (https://www.drupal.org/project/adminimal_admin_toolbar) sont requis

v1.1 :
 - Corrections de bugs
 - Support items enfants
 - Support scrollbar
 - Support élément actif
 - Support partiel FontAwesome Menu Icons (https://www.drupal.org/project/fontawesome_menu_icons)

v1.0 :
 - Release initiale