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
use \CarteBlanche\App\FrontController;
use \CarteBlanche\Exception\Exception as BaseException;

/**
 * Exception application handler for not-found objects and "404 not found" page
 *
 * All exceptions are written in the logs (by default in the "error.log" file)
 * except if `app.modes._APP_MODE.log_exceptions=false`
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class NotFoundException
    extends BaseException
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
        parent::__construct($message, $code, $previous, false);
    }

    /**
     * Render of a production error
     *
     * @return void
     */
    public function productionRendering()
    {
        $args = array('message'=>$this->getAppMessage());
        return FrontController::getInstance()
            ->renderProductionError($args, 404);
    }

    /**
     * Get the CarteBlanche information string about an Exception
     *
     * @return string
     */
    public function getAppMessage()
    {
        return sprintf('[%s] "%s" (HTTP status 404)', get_class($this), $this->getMessage());
    }

}

// Endfile