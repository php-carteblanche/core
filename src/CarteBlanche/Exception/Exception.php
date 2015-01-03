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

namespace CarteBlanche\Exception;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\CarteBlancheExceptionInterface;
use \DevDebug\Exception as BaseException;

/**
 * Special application exception handler
 *
 * To use it, write something like :
 *
 *     try {
 *         something wrong ...
 *     } catch (\CarteBlanche\Exception\Exception $e) {
 *         echo $e;
 *     }
 *
 * All exceptions are written in the logs (by default in the "error.log" file)
 * except if `app.modes._APP_MODE.log_exceptions=false`
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Exception
    extends BaseException
    implements CarteBlancheExceptionInterface
{

    /**
     * Construction of the exception - a message is needed (1st argument)
     *
     * @param   string  $message    The exception message
     * @param   int     $code       The exception code
     * @param   mixed   $previous   The previous exception if so
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        // kernel configuration data
        $mode_data = CarteBlanche::getKernelMode(true);

        // parent constructor
        if (!is_array($mode_data)
            || !isset($mode_data['debug'])
            || false==$mode_data['debug']) {
                parent::__construct($message, $code, $previous, true);
        } else {
            parent::__construct($message, $code, $previous, false);
        }

        // log?
        if (is_array($mode_data)
            && isset($mode_data['log_exceptions'])
            && true==$mode_data['log_exceptions']) {
                $this->log();
        }
    }

    /**
     * Render the Exception as string
     *
     * @return string
     */
    public function __toString()
    {
        $mode_data = CarteBlanche::getKernelMode(true);
        if (!is_array($mode_data)
            || !isset($mode_data['debug'])
            || false==$mode_data['debug']) {
                return $this->productionRendering();
        } else {
            return $this->debugRendering();
        }
    }

    /**
     * Render of a production error
     *
     * @return void
     */
    public function productionRendering()
    {
        $args = array('message'=>$this->getAppMessage());
        return CarteBlanche::getContainer()->get('front_controller')
            ->renderProductionError($args, 500);
    }

    /**
     * Render of a debug error
     *
     * @return void
     */
    public function debugRendering()
    {
        $args = array('message'=>$this->getAppMessage());
        return CarteBlanche::getContainer()->get('front_controller')
            ->renderError($args, $this);
    }

    /**
     * Log the exception in `error.log`
     *
     * @return void
     */
    public function log()
    {
        CarteBlanche::log(
            $this->getAppMessage()."\n".$this->getTraceAsString(),
            \Library\Logger::ERROR
        );
    }

    /**
     * Get the CarteBlanche information string about an Exception
     *
     * @return string
     */
    public function getAppMessage()
    {
        return sprintf('[%s] "%s"', get_class($this), $this->getMessage());
    }

}

// TESTS

/*/
use \CarteBlanche\Exception\NotFoundException,
    \CarteBlanche\Exception\AccessForbiddenException,
    \CarteBlanche\Exception\InternalServerErrorException,
    \CarteBlanche\Exception\Exception,
    \CarteBlanche\Exception\ErrorException,
    \CarteBlanche\Exception\DomainException,
    \CarteBlanche\Exception\RuntimeException,
    \CarteBlanche\Exception\InvalidArgumentException,
    \CarteBlanche\Exception\UnexpectedValueException;
//*/
/*/

		try{
//			fopen(); // error
			if (2 != 4) // false
//				throw new NotFoundException("Capture l'exception par défaut", 12);
//				throw new InternalServerErrorException("Capture l'exception par défaut", 12);
//				throw new AccessForbiddenException("Capture l'exception par défaut", 12);

//				throw new Exception("Capture l'exception par défaut", 12);
//				throw new ErrorException("Capture l'exception par défaut", 12);
//				throw new DomainException("Capture l'exception par défaut", 12);
//				throw new RuntimeException("Capture l'exception par défaut", 12);
//				throw new InvalidArgumentException("Capture l'exception par défaut", 12);
//				throw new UnexpectedValueException("Capture l'exception par défaut", 12);

        } catch(\CarteBlanche\Exception\UnexpectedValueException $e) {
            echo $e;
        } catch(\CarteBlanche\Exception\InvalidArgumentException $e) {
            echo $e;
        } catch(\CarteBlanche\Exception\RuntimeException $e) {
            echo $e;
        } catch(\CarteBlanche\Exception\DomainException $e) {
            echo $e;
        } catch(\CarteBlanche\Exception\Exception $e) {
            echo $e;
        }
exit('yo');
//*/


// Endfile