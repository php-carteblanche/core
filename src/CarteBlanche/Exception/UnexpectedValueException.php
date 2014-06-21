<?php
/**
 * CarteBlanche - PHP framework package
 * (c) Pierre Cassat and contributors
 * 
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * License Apache-2.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CarteBlanche\Exception;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\CarteBlancheExceptionInterface;
use \UnexpectedValueException as BaseException;

/**
 * Special application "unexpected value" exception handler
 *
 * All exceptions are written in the logs (by default in the "error.log" file)
 * except if `app.modes._APP_MODE.log_exceptions=false`
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class UnexpectedValueException
    extends BaseException
    implements CarteBlancheExceptionInterface
{

    /**
     * Construction of the exception - a message is needed (1st argument)
     *
     * @param string $message The exception message
     * @param int $code The exception code
     * @param mixed $previous The previous exception if so
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        // parent constructor
        parent::__construct($message, $code, $previous);

        // kernel configuration data
        $mode_data = CarteBlanche::getKernelMode(true);

        // log?
        if (is_array($mode_data)
            && isset($mode_data['log_errors'])
            && true==$mode_data['log_errors']) {
                $this->log();
        }

        // rendering?
        if (!is_array($mode_data)
            || !isset($mode_data['debug'])
            || false==$mode_data['debug']) {
                $this->productionRendering();
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
