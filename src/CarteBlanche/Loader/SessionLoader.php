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
 * @author  Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class SessionLoader implements DependencyLoaderInterface
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
        $session = new \CarteBlanche\Library\Session;
        $session->start();
        return $session;
    }

}

// Endfile