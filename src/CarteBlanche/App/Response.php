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

namespace CarteBlanche\App;

use \CarteBlanche\App\Kernel;
use \Library\HttpFundamental\Response as BaseResponse;
use \Patterns\Interfaces\ResponseInterface;

/**
 * The global response class
 *
 * This is the global response of the application
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Response
    extends BaseResponse
    implements ResponseInterface
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