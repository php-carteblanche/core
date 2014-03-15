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
use \CarteBlanche\App\Locator;

/**
 * This is the global file loader
 *
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class Loader
{

    /**
     * System file loader : will load a class file and its helper if so
	 *
     * @param string $filename The file path to load
     * @return bool TRUE if the classfile had been found
     */
    public static function load($filename) 
    {
        if (empty($filename)) return false;
        $hlp_file = (string) str_replace('.php', '.helper.php', $filename);
        if ($_f = Locator::locate($filename)) {
            if ($hlp_file!=$filename && $_hf = Locator::locate($hlp_file)) require_once $_hf;
            require_once $_f;
            return true;
        }
        return false;
    }
    
	/**
	 * Class autoloader to scan bundles directory
	 *
	 * @param string $classname The name of the class to load
	 * @param string $type The namespace type (Controller for example)
	 * @param bool $silent Exit silently if not found (default is false)
	 * @return bool TRUE if the classfile had been found
	 */
	public static function loadClass($classname, $type = null, $silent = false)
	{
	    // search in bundles
		$bundles = CarteBlanche::getContainer()->get('bundles');
		$full_classname = $type.$classname;
		$classfile = ucfirst($classname).'.php';
		if (!empty($bundles)) {
            foreach($bundles as $_n=>$_bundle) {
                $_namespace = $_bundle->getNamespace();
                if (!empty($type)) {
                    $bundle_file = rtrim($_bundle->getDirectory(), '/').'/'
                        .rtrim($type, '/').'/'
                        .$classfile;
                    $full_namespace = '\\'.$_namespace.'\\'.$type.'\\'.ucfirst($classname);
                    if ($_ce = self::classExists($full_namespace)) {
                        return $full_namespace;
                    } elseif ($_f = Locator::locate($bundle_file)) {
                        if (true===self::load($_f)) {
                            return $full_namespace;
                        }
                    }
                } else {
                    if ($_namespace == substr(trim($classname, '\\'), 0, strlen($_namespace))) {
                        $bundle_file = str_replace('\\', DIRECTORY_SEPARATOR,
                            rtrim($_bundle->getDirectory(), '/')
                            .substr(trim($classname, '\\'), strlen($_namespace))
                            .'.php'
                        );
                        $full_namespace = $classname;
                        if ($_ce = self::classExists($full_namespace)) {
                            return $full_namespace;
                        } elseif ($_f = Locator::locate($bundle_file)) {
                            if (true===self::load($_f)) {
                                return $classname;
                            }
                        }
                    }
                }
            }
        }

		// silently return if the class has not been found
		if (0!==error_reporting() && true!==$silent) {
			throw new ErrorException("Class '$classname' not found!");
		}
	}

    /**
     * Application specific "class_exists()" function with silent SPL autoload
	 *
     * This will not throw exception or error if the class doesn't exist but return false
	 *
     * @param string $classname The class name to test
     * @return bool TRUE if the class exists
     */
    public static function classExists($classname, $autoload = true)
    {
        if (true===@class_exists($classname, false)) return true;
        if ($autoload) {
            try {
                if (true===@class_exists($classname)) return true;
            } catch (\Exception $e) {}
        }
        return false;
    }
    
    /**
     * System autoloader : will load a class file
	 *
     * @param string $classname The name of the class to load
     * @return bool TRUE if the classfile had been found
     */
    public static function autoload($classname) 
    {
        $classname = str_replace('\\\\', '\\', $classname);

        // already exists in application
        if (true===@class_exists($classname, false)) return $classname;
        try {
            if (true===@class_exists($classname)) return $classname;
        } catch (\Exception $e) {}

        // exists in internal application
		$cls = Kernel::CARTE_BLANCHE_NAMESPACE.$classname;
        if (true===@class_exists($cls, false)) return $cls;

        // from dependencies
        $tool_path = Locator::getToolPath($classname, true);
        if ($tool_path) {
            $cls_file = $tool_path;
        } else {
            $cls_file = (string) str_replace('\\', DIRECTORY_SEPARATOR, $classname.'.php');
        }
        if ($_f = Locator::locate($cls_file)) {
            return self::load($_f);
        } elseif (0!==error_reporting() && count(spl_autoload_functions())==1) {
            throw new ErrorException("Class '$classname' not found!");
        }
        return false;
    }

}

// Endfile