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
use \CarteBlanche\Exception\Exception as BaseException;

/**
 * Exception application handler for not-found objects and "403 forbidden" page
 *
 * All exceptions are written in the logs (by default in the "error.log" file)
 * except if `app.modes._APP_MODE.log_exceptions=false`
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class AccessForbiddenException
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
        // parent constructor
        parent::__construct($message, $code, $previous);
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
        return ($ft ? $ft->renderProductionError($args, 403) : parent::__toString());
    }

    /**
     * Get the CarteBlanche information string about an Exception
     *
     * @return string
     */
    public function getAppMessage()
    {
        return sprintf('[%s] "%s" (HTTP status 403)', get_class($this), $this->getMessage());
    }

}

// Endfile