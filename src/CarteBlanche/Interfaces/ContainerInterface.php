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

namespace CarteBlanche\Interfaces;

/**
 * @author  Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
interface ContainerInterface
{

    /**
     * Global getter
     *
     * @param string $name The variable name to get
     * @return object The value of the registry variable
     */
    public function get($name);

    /**
     * Global setter
     *
     * @param string $name The variable name to set
     * @param object $object The object to set
     * @param bool $force_overload Over-write a pre-existant value ?
     * @return bool
     */
    public function set($name, $object, $force_overload = false);

    /**
     * Clear a container entry
     *
     * @param string $name The variable name to set
     * @return bool
     */
    public function clear($name);

    /**
     * Instance loader and setter
     *
     * @param string $name The variable name to set
     * @param array $params An array of parameters to pass to loader
     * @param object $object_loader The loader class if so
     * @param bool $force_overload Over-write a pre-existant value ?
     * @return bool
     */
    public function load($name, array $params = array(), $object_loader = null, $force_overload = false);

}

// Endfile