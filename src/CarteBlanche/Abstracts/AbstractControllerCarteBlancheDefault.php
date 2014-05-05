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
     * System booting errors page
     *
     * @params  null/array  $errors    Table of errors (strings)
     */
    abstract function bootErrorAction(array $errors = null);

    /**
     * Generated content page
     *
     * @param   string  $type  A special type for the generated content
     */
    abstract function loremIpsumAction($type = 'html');

    /**
     * Uninstalled application page
     *
     * @param   string  $altdb  An alternative database
     */
    abstract function emptyAction($altdb = 'default');

}

// Endfile