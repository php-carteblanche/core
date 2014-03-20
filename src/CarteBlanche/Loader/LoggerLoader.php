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

/**
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
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
            $log_dir = $container->get('kernel')->getPath('log_dir');
            if (empty($log_dir)) {
                $log_dir = $container->get('kernel')->getPath('var_dir').'logs'.DIRECTORY_SEPARATOR;
            }
            $container->get('kernel')->addPath('log_dir', $log_dir, true, true);
            $logger = new \CarteBlanche\App\Logger;
//        }
        return $logger;
    }

}

// Endfile