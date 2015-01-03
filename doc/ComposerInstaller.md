CarteBlanche Installers
========================

The [carte-blanche/installer](https://github.com/php-carteblanche/installer) package defines some custom
[Composer](http://getcomposer.org/) installers for the
[CarteBlanche](https://github.com/php-carteblanche/carteblanche) framework to install
Tools and Bundles.

It is required for each CarteBlanche installation (*and is included in the CarteBlanche
`composer.json` itself*).

## Install tools

To define a custom CarteBlanche's `Tool`, just use the following `composer.json` definition:

    "name": "carte-blanche/tool-ToolName",
    "type": "carte-blanche-tool",
    "require": {
        ...
        "carte-blanche/installer": "dev-master"
    }

A tool MUST be **named** as `carte-blanche/tool-NAME` and be **typed** as `carte-blanche-tool`.


## Install bundles

To define a custom CarteBlanche's `Bundle`, just use the following `composer.json` definition:

    "name": "carte-blanche/bundle-BundleName",
    "type": "carte-blanche-bundle",
    "require": {
        ...
        "carte-blanche/installer": "dev-master"
    },
    "autoload": { 
        "psr-0": { "BundleName": "path/to/library" },
        "classmap": [ "path/to/library/Controller" ]
    }

A bundle MUST be **named** as `carte-blanche/bundle-NAME` and be **typed** as `carte-blanche-bundle`.

### Automatic configuration

To inform CarteBlanche about your bundle requirements, configurations and paths, you can define
the following "extra" settings in the bundle's `composer.json`:

    "extra": {
        "assets-dir": "www",
        "views-functions": "src/Namespace/views_aliases.php",
        "views": "src/Namespace/views",
        "carte-blanche-configs": [ "config/my_config.ini", "config/my_other_config.php" ]
    }

All paths are related to the root directory of your bundle.


----
**(c) 2013-2015 [Les Ateliers Pierrot](http://www.ateliers-pierrot.fr/)** - Paris, France - Some rights reserved.

This documentation is licensed under the [Creative Commons - Attribution - Share Alike - Unported - version 3.0](http://creativecommons.org/licenses/by-sa/3.0/) license.
