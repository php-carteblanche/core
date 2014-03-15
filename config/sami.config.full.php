<?php
/**
 * CarteBlanche - PHP framework package
 * (c) Pierre Cassat and contributors
 * 
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * License Apache-2.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Application documentation builder for the whole project content except `vendor/`
 *
 * See https://github.com/fabpot/Sami
 *
 * To build doc, run:
 *     $ php src/vendor/sami/sami/sami.php render etc/vendor/sami.config.full.php
 *
 * To update it, run:
 *     $ php src/vendor/sami/sami/sami.php update etc/vendor/sami.config.full.php
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
    ->in($root_dir.'/src')
;

$options = array(
    'title'                => 'CarteBlanche (full doc)',
    'build_dir'            => $root_dir.'/phpdoc',
    'cache_dir'            => $root_dir.'/build/sami',
    'default_opened_level' => 0,
);

return new Sami($iterator, $options);

// Endfile