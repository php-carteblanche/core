<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\App;

use \CarteBlanche\App\Kernel;
use \Library\HttpFundamental\Response as BaseResponse;

/**
 * The global response class
 *
 * This is the global response of the application
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Response extends BaseResponse
{

    /**
     * Constructor : defines the current URL and gets the routes
     */
    public function __construct($content = null, $type = null)
    {
        if (!is_null($content)) {
            $this->setContents(is_array($content) ? $content : array($content));
        }
        $this->setContentType(!is_null($type) ? $type : 'html');
    }

    /**
     * Send the response to the device
     */
    public function send($content = null, $type = null) 
    {
        if (!is_null($content)) {
            $this->setContents(is_array($content) ? $content : array($content));
        }
        if (!is_null($type)) {
            $this->setContentType($type);
        }
        return parent::send();
    }
    
}

// Endfile