CarteBlanche - Framework structure
==================================


## Global CarteBlanche filesystem structure

For each of the root directories shown in the schema below, you can access it in two ways:

-   by calling `CarteBlanche::getPath(XXX_dir)` which will return concerned relative directory name,
-   by calling `CarteBlanche::getPath(XXX_path)` which will return concerned absolute directory path.

It is also possible to get a sub-directory or file from one of these components calling
`$loader->locate(FILENAME, XXX_dir)` which will search the file named `FILENAME` in an
application sub-directory get with `CarteBlanche::getPath(XXX_dir)`.

    [root directory]
    |
    | composer.json
    |
    | bin/
    |
    | config/
    | ------- vendor/
    |
    | i18n/
    | ------- vendor/
    |
    | [doc/]
    |
    | [phpdoc/]
    |
    | src/
    | ---- App/
    | --------- (...)
    | ---- bundles/
    | ------------- (...)
    | ---- tools/
    | ----------- (...)
    | ---- vendor/
    | ------------ (...)
    | ---- lib/
    | --------- (...)
    | ----- [views/]
    |
    | user/
    | ----- src/
    | ----- views/
    | ----- (...)
    |
    | var/
    | ---- tmp/
    | ---- logs/
    | ---- (...)
    |
    | www/
    | ---- skins/
    | ---- tmp/
    | ---- vendor/
    | ---- assets/
    | ---- (...)
    |

Only the `www/` directory must be accessible by your webserver, everything outside the
`www/` must never been accessed by HTTP.

#### `bin`: the binaries

#### `config`: the configurations

Must be writable by your webserver user during installation or update process.

#### `i18n`: the language strings

Must be writable by your webserver user during installation or update process.

#### `src`: the PHP sources & views

#### `user`: the user overrides

#### `var`: the variable files (cache, db etc)

Must be writable by your webserver user.

#### `www`: the web interfaces, assets & temporary files (web cache etc)

Must be accessible by your webserver user via HTTP protocol.


## Structure of the code : a classic "bundle"

### Global schema

    [bundle root directory]
    | 
    | composer.json
    |
    | bin/
    |
    | config/
    |
    | i18n/
    |
    | src/
    |
    | www/

### PHP code schema

All the code must follow the [PHP Framework Interoperability Group standards](https://github.com/php-fig/fig-standards)
so a class must be named following its filesystem position.

    [bundle root "src/" directory]
    | 
    | [nameBundle.php]
    |
    | Controller/
    |
    | Loader/
    |
    | views/
    |
    | (...)

#### `Controller`

Contains your controllers (nothing special here). Any controller must implements the
`\CarteBlanche\App\Interfaces\ControllerInterface` or extends the `\CarteBlanche\App\Abstracts\AbstractController` class.

#### `Loader`

Contains some dependencies loaders to be used by the global container to load a service
automatically passing it some configuration settings. Any service's loader must implements
the `\CarteBlanche\App\Interfaces\DependencyLoaderInterface` interface.

#### `views`


## Document root

As any web file must be stored in the `www/` directory, the best practice is to configure
your web-server to use it as your domain document root. This way, all files in another
sub-directory won't be accessible by web and safely secured.

Below is a classic [Apache](http://httpd.apache.org/) virtual host configuration for CarteBlanche:

    <VirtualHost *:80>
        ServerAdmin your@email
        ServerName your.domain
        DocumentRoot /your/carte-blanche/path/www
        <Directory "/your/carte-blanche/path/www">
            AllowOverride All
            Order allow,deny
            allow from all
        </Directory>
    </VirtualHost>


## Carte Blanche in the core

The `CarteBlanche` framework is based on a simple application that handles various bundles.

The core of the framework is as simple as the schema below. The rule is quite simple: CarteBlanche
is just a Kernel and a Container, all other stuff is loaded as dependencies or services.

### "Real" core of CarteBlanche

-   `\CarteBlanche\App\Kernel` : it handles and wraps the whole application ; this is the entry point of
    CarteBlanche called in web interfaces ;
-   `\CarteBlanche\App\Container` : it manages all dependencies as services for inversion of control ; it
    has to be seen as a global dispatcher/loader for all dependencies.

### "Enlarged" core : dependencies that are always loaded

-   `\CarteBlanche\App\Config` : the configuration manager ; it handles all configuration settings following
    some specific rules ;
-   `\CarteBlanche\App\Locator` : the locator class ; it is in charge to find a file in the CarteBlanche
    filesystem architecture ;
-   `\CarteBlanche\App\Loader` : the loader is the complementary of the locator for PHP scripts ; it is
    in charge to search a class and load it ;
-   `\CarteBlanche\App\Logger` : the logger of CarteBlanche.

### "User" core : dependencies that are overloadable by your app

-   `\CarteBlanche\App\Request` : the request manager ; it is in charge to handle the request ;
-   `\CarteBlanche\App\Response` : the response manager ; it is in charge to send final response to client ;
-   `\CarteBlanche\App\Router` : the router of CarteBlanche ; it is in charge to map the received request
    according to CarteBlanche rules and configuration settings to a specific action available
    in the application ;
-   `\CarteBlanche\App\FrontController` : the front controller is in charge to distribute the request to
    corresponding action(s) and to display the final response if necessary ;

### Useful classes

-   `\CarteBlanche\App\Bundle` : this class is created for each bundle of CarteBlanche ; it is just
    a shortcut to handle filesystem separation of code and files in bundles sub-directories ;
-   `\CarteBlanche\App\Debugger` : the debugger is a development tool to analyze current CarteBlanche
    work ;
-   `\CarteBlanche\App\Exception` : this namespace contains a set of customized `Exception` classes that
    can be handled by CarteBlanche.


----
**(c) 2013-2015 [Les Ateliers Pierrot](http://www.ateliers-pierrot.fr/)** - Paris, France - Some rights reserved.

This documentation is licensed under the [Creative Commons - Attribution - Share Alike - Unported - version 3.0](http://creativecommons.org/licenses/by-sa/3.0/) license.
