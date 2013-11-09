<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Exception;

use DevDebug\Exception as BaseException;

/**
 * Special application exception handler
 *
 * To use it, write something like :
 *
 *     try {
 *     		something wrong ...
 *     } catch (\CarteBlanche\Exception\Exception $e) {
 *     		echo $e;
 *     }
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Exception extends BaseException
{

}

// Endfile