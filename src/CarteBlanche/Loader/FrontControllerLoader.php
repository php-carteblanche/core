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
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class FrontControllerLoader implements DependencyLoaderInterface
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
		$em_cfg = CarteBlanche::getConfig('default_front_controller');
		if (isset($em_cfg['class'])) {
    		$em_class = $em_cfg['class'];
		} else {
			throw new \RuntimeException(
				sprintf('Front controller configuration must define a class name ("%s")!', 'default_front_controller')
			);
		}
        if (!empty($em_class)) {
            $factory = \Library\Factory::create()
                ->factoryName('FrontController')
                ->mustImplement(Kernel::FRONT_CONTROLLER_INTERFACE)
                ->callMethod('getInstance')
                ;
            return $factory->build($em_class);
        }
        return null;
	}

}

// Endfile