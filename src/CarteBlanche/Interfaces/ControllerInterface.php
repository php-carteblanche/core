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
 * Any controller must implement this interface.
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface ControllerInterface
{

    /**
     * Extension or alias of App\FrontController->render()
     * @see App\FrontController::render()
     */
    public function render($params = null, $debug = null, $exception = null);

    /**
     * Extension or alias of App\FrontController->view()
     * @see App\FrontController::view()
     */
    public function view($view = null, $params = null, $display = false, $exit = false);

}

// Endfile