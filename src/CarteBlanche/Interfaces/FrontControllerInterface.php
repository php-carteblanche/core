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
 */
interface FrontControllerInterface
{

	/**
	 * Set the current controller
	 *
	 * @param object $ctrl \CarteBlanche\Interfaces\ControllerInterface
	 */
    public function setController(\CarteBlanche\Interfaces\ControllerInterface $ctrl);

	/**
	 * Get the current controller
	 *
	 * @return object \App\Interfaces\ControllerInterface
	 */
    public function getController();

	/**
	 * Set the current controller name
	 *
	 * @param string $name
	 */
    public function setControllerName($name);

	/**
	 * Get the current controller name
	 */
    public function getControllerName();

	/**
	 * Set the current action requested by the router
	 *
	 * @param string $type
	 */
    public function setActionName($type);

	/**
	 * Get the current action requested by the router
	 */
    public function getActionName();

    public function distribute();

}

// Endfile
