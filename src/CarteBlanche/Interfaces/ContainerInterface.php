<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Interfaces;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
interface ContainerInterface
{

	/**
	 * Global getter
	 *
	 * @param string $name The variable name to get
	 *
	 * @return object The value of the registry variable
	 */
	public function get($name);

	/**
	 * Global setter
	 *
	 * @param string $name The variable name to set
	 * @param object $object The object to set
	 * @param bool $force_overload Over-write a pre-existant value ?
	 *
	 * @return bool
	 */
	public function set($name, $object, $force_overload = false);

	/**
	 * Clear a container entry
	 *
	 * @param string $name The variable name to set
	 *
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
	 *
	 * @return bool
	 */
	public function load($name, array $params = array(), $object_loader = null, $force_overload = false);

}

// Endfile