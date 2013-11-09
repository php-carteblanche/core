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
class RouterLoader implements DependencyLoaderInterface
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