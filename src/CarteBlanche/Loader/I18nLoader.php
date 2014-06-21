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
 */

namespace CarteBlanche\Loader;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;
use \I18n\Loader as OriginalI18nLoader;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class I18nLoader
    extends OriginalI18nLoader
    implements DependencyLoaderInterface
{

    /**
     * Creation of a Loader with an optional user defined set of options
     *
     * @param array $user_options An array of options values to over-write defaults
     */
    public function __construct()
    {
        $config = CarteBlanche::getConfig('i18n');
        $root_path = CarteBlanche::getPath('root_path');
        $var_path = CarteBlanche::getPath('var_dir');
        $langs = isset($config['available_languages']) ?
            $config['available_languages'] : array('en'=>'en_US_USD');

        $language_strings_db_filename_locator = function($i) {
            return str_replace(
                    CarteBlanche::getPath('root_path'), '',
                    CarteBlanche::getContainer()->get('locator')->locateLanguage($i)
                );
        };
        $options = array_merge($config, array(
            'available_languages'       => $langs,
            'language_directory'        => $var_path.$config['language_directory'],
            'language_strings_db_directory'  => $root_path,
            'language_strings_db_filename_closure'  => $language_strings_db_filename_locator,
            'force_rebuild' => true,
        ));
        if (strtolower(CarteBlanche::getKernel()->getMode())==='dev') {
            $options['show_untranslated'] = true;
        }

        \Library\Helper\Directory::ensureExists($options['language_directory']);
        parent::__construct($options);
    }

    /**
     * Instance loader
     *
     * @param   array                       $config
     * @param   \CarteBlanche\App\Container $container
     * @return object
     */
    public function load(array $config = null, \CarteBlanche\App\Container $container)
    {
        try {
            $i18n = \I18n\I18n::getInstance($this); 
        } catch (\Exception $e) {
            return $e;
        }
        return $i18n;
    }

}

// Endfile