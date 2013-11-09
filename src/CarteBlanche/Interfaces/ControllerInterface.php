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
 * Any controller must implement this interface.
 *
 * @author  Piero Wbmstr <piero.wbmstr@gmail.com>
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