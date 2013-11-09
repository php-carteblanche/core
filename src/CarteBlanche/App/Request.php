<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\App;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;

use \Library\HttpFundamental\Request as BaseRequest;
use \Library\Helper\Url as UrlHelper;

/**
 * The global request class
 *
 * This is the global request instance of the application
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @todo Transform GET and POST to get the real user value and pass them thru XSSclean
 */
class Request extends BaseRequest
{

	/**
	 * Get the value of a specific argument value from current parsed URL
	 *
	 * @param string $param The parameter name if so, or 'args' to get all parameters values
	 * @param misc $default The default value sent if the argument is not setted
	 * @return string The value retrieved, $default otherwise
	 */
	public function getUrlArg($param = null, $default = false) 
	{
		$routes = CarteBlanche::getContainer()->get('router')->getRouteParsed();
  		if (!empty($routes)) {
	  		if ($param=='args') {
  				if (isset($routes['args']))
  					return $routes['args'];
	  		} else {
		  		if (isset($routes[$param]))
  					return $routes[$param];
  				if (isset($routes['args'][$param]))
  					return $routes['args'][$param];
	  		}
  		}
	  	return $default;
	}

}

// Endfile