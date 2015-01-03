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

namespace CarteBlanche\Interfaces;

/**
 */
interface FrontControllerInterface
{

    /**
     * Set the current controller
     *
     * @param \CarteBlanche\Interfaces\ControllerInterface $ctrl
     */
    public function setController(\CarteBlanche\Interfaces\ControllerInterface $ctrl);

    /**
     * Get the current controller
     *
     * @return \CarteBlanche\Interfaces\ControllerInterface
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
