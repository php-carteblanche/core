<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * Application documentation builder
 *
 * See https://github.com/fabpot/Sami
 *
 * To build doc, run:
 *     $ php src/vendor/sami/sami/sami.php render etc/vendor/sami.config.php
 *
 * To update it, run:
 *     $ php src/vendor/sami/sami/sami.php update etc/vendor/sami.config.php
 *
 */

use Sami\Sami;
use Symfony\Component\Finder\Finder;
$root_dir = __DIR__.'/../../..';

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('vendor')
    ->exclude('views')
    ->in($root_dir.'/src/CarteBlanche')
    ->in($root_dir.'/src/vendor/atelierspierrot/carte-blanche-installers/src')
;

$options = array(
    'title'                => 'CarteBlanche',
    'build_dir'            => $root_dir.'/phpdoc',
    'cache_dir'            => $root_dir.'/build/sami',
    'default_opened_level' => 0,
);

return new Sami($iterator, $options);

// Endfile