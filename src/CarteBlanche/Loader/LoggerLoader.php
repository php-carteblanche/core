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

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class LoggerLoader implements DependencyLoaderInterface
{

	/**
	 * Instance loader
	 *
	 * @param array $config
	 *
	 * @return object
	 */
    public function load(array $config = null, \CarteBlanche\App\Container $container)
    {
        $mode = $container->get('kernel')->getMode();
/*
        if ($mode && $mode==='prod'){
            $logger = new \Psr\Log\NullLogger;
        } else {
*/
            $log_path = $container->get('kernel')->getPath('log_path');
            if (empty($log_path)) {
                $log_dir = $container->get('kernel')->getPath('var_dir').'logs'.DIRECTORY_SEPARATOR;
                $container->get('kernel')->addPath('log_dir', $log_dir);
                $log_path = $container->get('kernel')->getPath('root_path').$log_dir;
                $container->get('kernel')->addPath('log_path', $log_path, true, true);
            }
            $logger = new \CarteBlanche\App\Logger;
//        }
        return $logger;
    }

}

// Endfile