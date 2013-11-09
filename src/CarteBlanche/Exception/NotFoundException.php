<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Exception;

use \CarteBlanche\App\FrontController;

/**
 * Exception application handler for not-found objects
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class NotFoundException extends \CarteBlanche\Exception\Exception
{

	/**
	 * Construction of the exception - a message is needed (1st argument)
	 *
	 * @param string $message The exception message
	 * @param numeric $code The exception code
	 * @param misc $previous The previous exception if so
	 */
	public function __construct($message, $code = 0, $previous = null) 
	{
	    \CarteBlanche\CarteBlanche::log($message, \Library\Logger::ERROR);

		if (defined('_APP_MODE') && _APP_MODE=='prod') {
			$args = array(
				'message'=>$message
			);
			FrontController::getInstance()
			    ->renderProductionError( $args, 404 );
			return;
		}

		// parent constructor
		parent::__construct($message, $code, $previous);
	}

}

// Endfile