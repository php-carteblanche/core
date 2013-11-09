<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\App;

use \CarteBlanche\CarteBlanche,
    \CarteBlanche\App\Loader,
    \CarteBlanche\App\Kernel,
    \CarteBlanche\App\FrontController;

use \Library\Helper\Text as TextHelper;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Locator
{

    /**
     * Hack to let the 'file_exists' function search in 'include_path'
	 *
     * @param string $file The filename to find
     * @return bool|string The valid file path if found, false otherwise
     */
    public static function locate($filename)
    {
        if (@file_exists($filename)) return $filename;

        $_f = CarteBlanche::getPath('carte_blanche_core').$filename;
        if (@file_exists($_f)) return $_f;

        $include_pathes = explode(PATH_SEPARATOR,get_include_path());
        foreach($include_pathes as $_inc) {
            $_f = rtrim($_inc, '/').'/'.$filename;
            if (@file_exists($_f)) return $_f;
        }

        if (class_exists('\CarteBlanche\CarteBlanche')) {
            $bundles = CarteBlanche::getContainer()->get('bundles');
            if (!empty($bundles)) {
                foreach($bundles as $_n=>$_bundle) {
                    $_f = rtrim($_bundle->getDirectory(), '/').'/'.$filename;
                    if (@file_exists($_f)) return $_f;
                }
            }
        }

        return false;
    }

    public static function getToolPath($name, $is_class = false)
    {
        $name_parts = explode('\\', $name);
        $tools_dir = CarteBlanche::getPath('tools_dir');
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
     * Locate a file in "data/" directory
     * @param string $filename
     */    
    public static function locateData($filename)
    {
        $etc_dir = CarteBlanche::getPath('config_dir');
        $var_dir = CarteBlanche::getPath('var_dir');
        $f = self::locate( rtrim($etc_dir, '/').'/'.$filename );
        if ($f) return $f;
        $vf = self::locate( rtrim($var_dir, '/').'/'.$filename );
        if ($vf) return $vf;
        return null;
    }
    
    /**
     * Locate a file in "config/" directory (vendor or not)
     * @param string $filename
     */    
    public static function locateConfig($filename, $fallback = true)
    {
        $etc_dir = CarteBlanche::getPath('config_dir');
        if ($fallback) {
            $f = self::locate( rtrim($etc_dir, '/').'/'.$filename );
            if ($f) return $f;
        }
        $vf = self::locate( rtrim($etc_dir, '/').'/vendor/'.$filename );
        if ($vf) return $vf;
        return null;
    }
    
    /**
     * Locate a file in "i18n/" directory (vendor or not)
     * @param string $filename
     */    
    public static function locateLanguage($filename, $fallback = true)
    {
        $etc_dir = CarteBlanche::getPath('i18n_dir');
        if ($fallback) {
            $f = self::locate( rtrim($etc_dir, '/').'/'.$filename );
            if ($f) return $f;
        }
        $vf = self::locate( rtrim($etc_dir, '/').'/vendor/'.$filename );
        if ($vf) return $vf;
        return null;
    }
    
    /**
	 * Search a view file in the views directory and sub-directories
	 *
	 * @param string $view The view filename
	 * @param bool $remap
	 * @return misc FALSE if nothing had been find, the filename otherwise
     */    
    public static function locateView($view = null, $remap = true)
    {
		if (empty($view)) return;

		// from views mapping
		if (true===$remap) {
            $views_mapping = CarteBlanche::getContainer()->get('config')->get('views');
            if (!empty($views_mapping) && array_key_exists($view, $views_mapping)) {
                $view = $views_mapping[$view];
                if ($remaped = self::locateView($view, false)) {
                    return $remaped;
                }
            }
        }

		// from the application
		if ($_f = self::locate(CarteBlanche::getPath('views_dir').$view)) {
			return $_f;
		}
		
		// from a bundle
		if ($_f = self::locate(CarteBlanche::getPath('bundles_dir').$view)) {
			return $_f;
		}

		// from a tool
		if ($_f = self::locate(CarteBlanche::getPath('tools_dir').$view)) {
			return $_f;
		}

		// globally
		if ($_f = self::locate($view)) {
			return $_f;
		}

        $views_dir = CarteBlanche::getPath('views_dir');

		if (!self::locate($view)) {
			$view_file = $views_dir.$view;
		} else {
			$view_file = $view;
		}

		if (!self::locate($view_file)) {
			$_ctrl = CarteBlanche::getContainer()
			    ->get('front_controller')->getController();
			if (property_exists($_ctrl, 'views_dir')) {
                $subdir = $_ctrl::$views_dir;
                $view_file = $views_dir.$subdir.$view;
			}
		}

		if (!self::locate($view_file)) {
			$view_file = $view;
		}

		if (self::locate($view_file)) {
		    return $view_file;
		}
		
		// not found: try with one of configured extensions
		if (true===$remap) {
            $views_extensions = CarteBlanche::getContainer()->get('config')->get('views.extensions');
            if (!empty($views_extensions)) {
                foreach ($views_extensions as $_ext) {
                    $_extended_view = $view.'.'.$_ext;
                    if ($found = self::locateView($_extended_view, false)) {
                        return $found;
                    }
                }
            }
        }

		return false;
    }
    
	/**
	 * Search a controller : from the app or a bundle
	 *
	 * @param string $view The controller filename
	 * @return misc FALSE if nothing had been find, the filename otherwise
	 */
	public static function locateController($ctrl)
	{
	    $ctrl = TextHelper::toCamelCase($ctrl);
        if (Loader::classExists($ctrl)) {
            return $ctrl;
        }

		// from the application
		$controller = Kernel::CONTROLLER_DEFAULT_NAMESPACE.$ctrl;
		if ($_found = Loader::autoload($controller)) {
			return $_found;
		}

		// from CarteBlanche
		$controller = Kernel::CARTE_BLANCHE_NAMESPACE.'\\'.Kernel::CONTROLLER_DEFAULT_NAMESPACE.$ctrl;
		if ($_found = Loader::autoload($controller)) {
			return $_found;
		}

		// with a full name
		$controller .= Kernel::CONTROLLER_SUFFIX;
		if ($_found = Loader::autoload($controller)) {
			return $_found;
		}
		
		// from bundles
		if ($_found = Loader::loadClass($ctrl, 'Controller', true)) {
			return $_found;
		}
		// with a full name
		if ($_found = Loader::loadClass($ctrl.Kernel::CONTROLLER_SUFFIX, 'Controller', true)) {
			return $_found;
		}
		return false;
	}

}

// Endfile