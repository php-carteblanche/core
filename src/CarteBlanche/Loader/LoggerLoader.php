<?php
/**
 * This file is part of the CarteBlanche PHP framework.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CarteBlanche\Loader;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class LoggerLoader
    implements DependencyLoaderInterface
{

    /**
     * Instance loader
     *
     * @param   array                       $config
     * @param   \CarteBlanche\App\Container $container
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