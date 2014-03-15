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

use \CarteBlanche\CarteBlanche,
    \CarteBlanche\App\FrontController,
    \CarteBlanche\Exception\Exception as BaseException;

/**
 * Exception application handler for not-found objects and "403 forbidden" page
 *
 * All exceptions are written in the logs (by default in the "error.log" file)
 * except if `app.modes._APP_MODE.log_exceptions=false`
 *
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class AccessForbiddenException
    extends BaseException
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
		// parent constructor
		parent::__construct($message, $code, $previous);
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
            ->renderProductionError($args, 403);
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