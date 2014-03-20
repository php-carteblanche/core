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

namespace CarteBlanche\Abstracts;

use \CarteBlanche\Abstracts\AbstractController;
use \CarteBlanche\Interfaces\ControllerInterface;

/**
 * The default distribution controller class
 *
 * Any ditributed controller must extend this class.
 *
 * @author  Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
abstract class AbstractControllerCarteBlancheDefault
    extends AbstractController
    implements ControllerInterface
{

	/**
	 * The default action of the controller, considered as 'home'
	 *
	 * @return misc
	abstract function indexAction();
	 */

	/**
	 * System booting errors page
	 *
	 * @params array $errors    Table of errors (strings)
	 * @return misc
	abstract function errorAction(array $errors = null);
	 */

	/**
	 * System booting errors page
	 *
	 * @params array $errors    Table of errors (strings)
	 * @return misc
	 */
	abstract function bootErrorAction(array $errors = null);

	/**
	 * Generated content page
	 *
	 * @param string $type  A special type for the generated content
	 * @return misc
	 */
	abstract function loremIpsumAction($type = 'html');

	/**
	 * Uninstalled application page
	 *
	 * @param string $altdb An alternative database
	 * @return misc
	 */
	abstract function emptyAction($altdb = 'default');

}

// Endfile