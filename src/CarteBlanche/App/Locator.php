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

namespace CarteBlanche\App;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Loader;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\App\FrontController;
use \Library\Helper\Text as TextHelper;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Locator
{

    /**
     * Hack to let the 'file_exists' function search in 'include_path'
     *
     * @param   string  $filename   The filename to find
     * @return  bool/string         The valid file path if found, false otherwise
     */
    public static function locate($filename)
    {
        if (@file_exists($filename)) return $filename;

        $_f = CarteBlanche::getPath('carte_blanche_core').$filename;
        if (!empty($_f) && @file_exists($_f)) return $_f;

        $include_paths = explode(PATH_SEPARATOR,get_include_path());
        foreach($include_paths as $_inc) {
            $_f = DirectoryHelper::slashDirname($_inc).$filename;
            if (@file_exists($_f)) return $_f;
        }

        if (class_exists('\CarteBlanche\CarteBlanche')) {
            $bundles = CarteBlanche::getContainer()->get('bundles');
            if (!empty($bundles)) {
                foreach($bundles as $_bundle) {
                    $_f = DirectoryHelper::slashDirname($_bundle->getDirectory()).$filename;
                    if (@file_exists($_f)) return $_f;
                }
            }
        }

        return false;
    }

    /**
     * Locate a file in "data/" directory
     *
     * @param   string $filename
     * @return  null/string
     */    
    public static function locateData($filename)
    {
        $etc_dir = CarteBlanche::getPath('config_dir');
        $f = self::locate( DirectoryHelper::slashDirname($etc_dir).$filename );
        if ($f) return $f;
        $var_dir = CarteBlanche::getPath('var_dir');
        $vf = self::locate( DirectoryHelper::slashDirname($var_dir).$filename );
        if ($vf) return $vf;
        return null;
    }
    
    /**
     * Locate a file in "config/" directory (vendor or not)
     *
     * @param   string  $filename
     * @param   bool    $fallback
     * @return  null/string
     */
    public static function locateConfig($filename, $fallback = true)
    {
        $etc_dir = CarteBlanche::getPath('config_dir');
        if ($fallback) {
            $f = self::locate( DirectoryHelper::slashDirname($etc_dir).$filename );
            if ($f) return $f;
        }
        $vf = self::locate( DirectoryHelper::slashDirname($etc_dir).'vendor/'.$filename );
        if ($vf) return $vf;
        return null;
    }
    
    /**
     * Locate a file in "i18n/" directory (vendor or not)
     *
     * @param   string  $filename
     * @param   bool    $fallback
     * @return  null/string
     */
    public static function locateLanguage($filename, $fallback = true)
    {
        $etc_dir = CarteBlanche::getPath('i18n_dir');
        if ($fallback) {
            $f = self::locate( DirectoryHelper::slashDirname($etc_dir).$filename );
            if ($f) return $f;
        }
        $vf = self::locate( DirectoryHelper::slashDirname($etc_dir).'vendor/'.$filename );
        if ($vf) return $vf;
        return null;
    }
    
    /**
     * Search a view file in the views directory and sub-directories
     *
     * @param string $view The view filename
     * @param bool $remap
     * @return mixed FALSE if nothing had been find, the filename otherwise
     */    
    public static function locateView($view = null, $remap = true)
    {
        if (empty($view)) return;

        // from views mapping
        if (true===$remap) {
            $views_mapping = CarteBlanche::getContainer()->get('config')->get('views');
            if (!empty($views_mapping) && array_key_exists($view, $views_mapping)) {
                $view = $views_mapping[$view];
                if (false!==($remaped = self::locateView($view, false))) {
                    return self::fallback($view, $remaped);
                }
            }
        }

        $views_dir = DirectoryHelper::slashDirname(CarteBlanche::getPath('views_dir'));

        // from the application
        if ($_f = self::locate($views_dir.$view)) {
            return self::fallback($view, $_f);
        }

        // from a bundle
        if ($_f = self::locate(
            DirectoryHelper::slashDirname(CarteBlanche::getPath('bundles_dir')).$view
        )) {
            return self::fallback($view, $_f);
        }

        // from a tool
        if ($_f = self::locate(
            DirectoryHelper::slashDirname(CarteBlanche::getPath('tools_dir')).$view
        )) {
            return self::fallback($view, $_f);
        }

        // globally
        if ($_f = self::locate($view)) {
            return self::fallback($view, $_f);
        }

        if (!self::locate($view)) {
            $view_file = $views_dir.$view;
        } else {
            $view_file = $view;
        }

        if (!self::locate($view_file)) {
            $_ctrl = CarteBlanche::getContainer()
                ->get('front_controller')->getController();
            if (!empty($_ctrl) && property_exists($_ctrl, 'views_dir')) {
                $subdir     = $_ctrl::$views_dir;
                $view_file  = $views_dir.$subdir.$view;
            }
        }

        if (!self::locate($view_file)) {
            $view_file = $view;
        }

        if (!self::locate($view_file)) {
            $view_file = $view;
        }

        if (self::locate($view_file)) {
            return self::fallback($view_file);
        }

        // not found: try with one of configured extensions
        if (true===$remap) {
            $views_extensions = CarteBlanche::getContainer()->get('config')->get('views.extensions');
            if (!empty($views_extensions)) {
                foreach ($views_extensions as $_ext) {
                    $_extended_view = $view.'.'.$_ext;
                    if ($found = self::locateView($_extended_view, false)) {
                        return self::fallback($_extended_view, $found);
                    }
                }
            }
        }

        return false;
    }
    
    /**
     * Search a controller : from the app or a bundle
     *
     * @param   string  $ctrl   The controller filename
     * @return  mixed   FALSE if nothing had been find, the filename otherwise
     */
    public static function locateController($ctrl)
    {
        $ctrl = TextHelper::toCamelCase($ctrl);
        if (Loader::classExists($ctrl)) {
            return $ctrl;
        }

       // from the application
        $controller = Kernel::CONTROLLER_DEFAULT_NAMESPACE.$ctrl;
        if (false!==($_found = Loader::autoload($controller))) {
            return $_found;
        }

        // from CarteBlanche
        $controller = Kernel::CARTE_BLANCHE_NAMESPACE.'\\'.Kernel::CONTROLLER_DEFAULT_NAMESPACE.$ctrl;
        if (false!==($_found = Loader::autoload($controller))) {
            return $_found;
        }

        // with a full name
        $controller .= Kernel::CONTROLLER_SUFFIX;
        if (false!==($_found = Loader::autoload($controller))) {
            return $_found;
        }

        // from bundles
        if (false!==($_found = Loader::loadClass($ctrl, 'Controller', true))) {
            return $_found;
        }
        // with a full name
        if (false!==($_found = Loader::loadClass($ctrl.Kernel::CONTROLLER_SUFFIX, 'Controller', true))) {
            return $_found;
        }
        return false;
    }

    /**
     * @param   string  $name
     * @param   bool    $is_class
     * @return  bool|mixed|string
     */
    public static function getToolPath($name, $is_class = false)
    {
        $name_parts = explode('\\', $name);
        $tools_dir = CarteBlanche::getFullPath('tools_dir');
        $tool_path = false;
        if ($name_parts[0]==='Tool') {
            $tool_path = str_replace('Tool'.DIRECTORY_SEPARATOR, $tools_dir, str_replace('\\', DIRECTORY_SEPARATOR, $name));
            if (true===$is_class) {
                if (count($name_parts)===2) $tool_path .= '/'.array_pop($name_parts);
                $tool_path .= '.php';
            }
        }
        return $tool_path;
    }

    /**
     * For user overridings
     * This will search a version of `$file` in `user/` and return it in replacement if so.
     *
     * @param string $file The relative file path to search
     * @param string $path The default file path
     * @return string
     */
    public static function fallback($file = null, $path = null)
    {
        $user_dir = CarteBlanche::getPath('user_path');
        if (file_exists($user_dir.$file)) {
            return $user_dir.$file;
        } else {
            return $path;
        }
    }

}

// Endfile