<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Loader;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;
use \I18n\Loader as OriginalI18nLoader;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
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
        $var_path = CarteBlanche::getPath('var_path');
        $langs = isset($config['available_languages']) ?
            $config['available_languages'] : array('en');

        $options = array(
            'available_languages'       => $langs,
            'arg_wrapper_mask'          => $config['arg_wrapper_mask'],
            'language_varname'          => $config['language_vars_mask'],
            'language_filename'         => $config['language_files_mask'],
            'language_directory'        => $var_path.$config['language_directory'],
//            'language_strings_db_directory'  => $root_path._CarteBlanche_DIR,
            'language_strings_db_directory'  => $root_path,
            'language_strings_db_filename'  => str_replace($root_path, '', CarteBlanche::getContainer()->get('locator')
                ->locateLanguage($config['language_strings_db_filename'])),
            'force_rebuild' => true,
        );
        if (
            (isset($config['show_untranslated']) && $config['show_untranslated']) ||
            CarteBlanche::getKernel()->getMode()==='dev'
        ) {
            $options['show_untranslated'] = true;
            $options['show_untranslated_wrapper'] = '<span class="untranslated"><strong>%s</strong> (%s)</span>';
        }

        \Library\Helper\Directory::ensureExists($options['language_directory']);
        parent::__construct($options);
    }

	/**
	 * Instance loader
	 *
	 * @param array $config
	 *
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