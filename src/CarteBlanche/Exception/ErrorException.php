<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Exception;

use DevDebug\ErrorException as BaseErrorException;

/**
 * Special application error handler
 *
 * To use it, write something like :
 *
 *     trigger_error( 'Error info', E_USER_ERROR|E_USER_WARNING|E_USER_NOTICE );
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class ErrorException extends BaseErrorException
{

	/**
	 * Construction of the exception - a message is needed (1st argument)
	 *
	 * @param string $message The exception message
	 * @param numeric $code The exception code
	 * @param misc $previous The previous exception if so
	 */
	public function __construct($message, $code = 0, $severity = 1, $filename = __FILE__, $lineno = __LINE__, $previous = null) 
	{
	    \CarteBlanche\CarteBlanche::log($message, \Library\Logger::ERROR);

		// parent constructor
		parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
	}

}

// Endfile