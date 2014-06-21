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
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class EntityManagerLoader implements DependencyLoaderInterface
{

    /**
     * Instance loader
     *
     * @param   array   $config
     * @param   \CarteBlanche\Interfaces\ContainerInterface $container
     * @return  object
     */
    public function load(array $config = null, \CarteBlanche\Interfaces\ContainerInterface $container)
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
     *
     * @param   string  $em_fullname    The name of the entity manager to build
     * @return  null/object Returns an object to manage the entity
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