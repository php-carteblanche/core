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

namespace CarteBlanche\Controller;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Controller\DefaultController;

/**
 * The default application controller for AJAX requests
 *
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class AjaxController
    extends DefaultController
{

	/**
	 * The default console template file
	 */
	static $template = 'empty.txt';

	/**
	 * The directory where to search the views files
	 */
	static $views_dir = '';

	/**
	 * The home page of the controller
	 *
	 * @param numeric $offset   The offset used for the tables dump
	 * @param numeric $limit    The limit used for the tables dump
	 * @param string $table     The name of a table to isolate it
	 * @param misc $show        ??
	 * @return string The home page view content
	 */
	public function indexAction($offset = 0, $limit = 5, $table = null, $show = null)
	{
		return array(self::$views_dir.'hello', array(
            'altdb'=>$this->getContainer()->get('request')->getUrlArg('altdb'),
            'title' => $this->trans("Hello")
		));
	}

}

// Endfile