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
use \DevDebug\ErrorException as BaseErrorException;

/**
 * Special application error handler
 *
 * To use it, write something like :
 *
 *     trigger_error( 'Error info', E_USER_ERROR|E_USER_WARNING|E_USER_NOTICE );
 *
 * All error exceptions are written in the logs (by default in the "error.log" file)
 * except if `app.modes._APP_MODE.log_errors=false`
 *
 * @author      Piero Wbmstr <me@e-piwi.fr>
 */
class ErrorException
    extends BaseErrorException
    implements CarteBlancheExceptionInterface
{

    /**
     * Construction of the exception - a message is needed (1st argument)
     *
     * @param   string  $message    The exception message
     * @param   int     $code       The exception code
     * @param   int     $severity
     * @param   string  $filename
     * @param   int     $lineno
     * @param   mixed   $previous
     */
    public function __construct(
        $message, $code = 0, $severity = 1, $filename = __FILE__, $lineno = __LINE__, $previous = null
    ) {
        // kernel configuration data
        $mode_data = CarteBlanche::getKernelMode(true);

        // parent constructor
        if (!is_array($mode_data)
            || !isset($mode_data['debug'])
            || false==$mode_data['debug']) {
                parent::__construct($message, $code, $severity, $filename, $lineno, $previous, true);
        } else {
            parent::__construct($message, $code, $severity, $filename, $lineno, $previous, false);
        }

        // log?
        if (is_array($mode_data)
            && isset($mode_data['log_errors'])
            && true==$mode_data['log_errors']) {
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
     * @return string
     */
    public function productionRendering()
    {
        $args   = array('message'=>$this->getAppMessage());
        $ft     = CarteBlanche::getContainer()->get('front_controller');
        return ($ft ? $ft->renderProductionError($args, 500) : parent::__toString());
    }

    /**
     * Render of a debug error
     *
     * @return string
     */
    public function debugRendering()
    {
        $args   = array('message'=>$this->getAppMessage());
        $ft     = CarteBlanche::getContainer()->get('front_controller');
        return ($ft ? $ft->renderError($args, $this) : parent::__toString());
    }

    /**
     * Log the exception in `history.log`
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
        return sprintf('[%s] "%s" (%d)', get_class($this), $this->getMessage(), $this->getCode());
    }

}

// Endfile