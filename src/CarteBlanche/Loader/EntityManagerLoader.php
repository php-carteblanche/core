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
class EntityManagerLoader implements DependencyLoaderInterface
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
        $em_name = isset($config['name']) ? $config['name'] : 'default';
	    $em_fullname = $em_name.'_entity_manager';

        $existing = $container->get($em_fullname);
        if (!empty($existing)) {
            return $existing;
        }
        return $this->_buildEntityManager($em_fullname);
    }

	/**
	 * Build a specific entity manager
	 * @param string $emname The name of the entity manager to build
	 * @return object Returns an object to manage the entity
	 */
	protected function _buildEntityManager($em_fullname) 
	{
		$em_cfg = CarteBlanche::getConfig($em_fullname);

		if (isset($em_cfg['class'])) {
    		$em_class = $em_cfg['class'];
		} else {
			throw new \RuntimeException(
				sprintf('An entity manager configuration must define a class name ("%s")!', $em_fullname)
			);
		}

        $em_options = array();
		if (isset($em_cfg['options'])) {
    		$em_options = $em_cfg['options'];
/*
		} else {
			throw new \RuntimeException(
				sprintf('An entity manager configuration must define an options array ("%s")!', $em_fullname)
			);
*/
		}

        if (!empty($em_class)) {
            $factory = \Library\Factory::create()
                ->factoryName('EntityManager')
                ->mustImplement(Kernel::ENTITY_MANAGER_INTERFACE)
                ;
            return $factory->build($em_class, $em_options);
        }
        return null;
	}

}

// Endfile