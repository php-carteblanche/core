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
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class RouterLoader
    implements DependencyLoaderInterface
{

    /**
     * Instance loader
     *
     * @param   array                       $config
     * @param   \CarteBlanche\App\Container $container
     * @return  \CarteBlanche\App\Router
     */
    public function load(array $config = null, \CarteBlanche\App\Container $container)
    {
//        $route = null, array $routes_table = array(), array $arguments_table = array(), array $matchers_table = array()
        return new \CarteBlanche\App\Router(
            null,
            $container->get('config')->get('routing.routes', \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, array()),
            $container->get('config')->get('routing.arguments_mapping', \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, array()),
            $container->get('config')->get('routing.masks', \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, array())
        );
    }

}

// Endfile