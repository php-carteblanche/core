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
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class FrontControllerLoader implements DependencyLoaderInterface
{

    /**
     * Instance loader
     *
     * @param   array                       $config
     * @param   \CarteBlanche\App\Container $container
     * @return  null/object
     * @throws  \RuntimeException if the config doesn't define a FrontController classname
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