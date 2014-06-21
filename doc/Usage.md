CarteBlanche - Usage
====================

## How to use the core of CarteBlanche

This package is mostly used by [the CarteBlanche framework](http://github.com/php-carteblanche/carteblanche)
itself as the core structure. But you can use it "standalone" as a very simple framework.

### Installation

### Basic web interface



    require_once '/path/to/autoload.php';

    // _ROOTFILE : the filename handling the current request
    define('_ROOTFILE', basename(__FILE__));

    // _ROOTPATH : the dirname of the whole CarteBlanche installation
    define('_ROOTPATH', realpath(dirname(__FILE__)));

    // _ROOTHTTP : the base URL to use to construct the application routes (found from the current domain and path URL)
    define('_ROOTHTTP', 'http://my.domain.com/');

    // the application
    $main = \CarteBlanche\App\Kernel::create(
        '/paht/to/config.ini', 
        null, 
        'prod'  
    )
    ->distribute();

And that's it! All the work will be done in your controller.

### Basic controller

