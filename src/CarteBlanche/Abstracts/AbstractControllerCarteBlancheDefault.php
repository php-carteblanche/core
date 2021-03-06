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

namespace CarteBlanche\Abstracts;

use \CarteBlanche\Abstracts\AbstractController;
use \CarteBlanche\Interfaces\ControllerInterface;

/**
 * The default distribution controller class
 *
 * Any distributed controller must extend this class.
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractControllerCarteBlancheDefault
    extends AbstractController
    implements ControllerInterface
{

    /**
     * The default action of the controller, considered as 'home'
     *
     * @return mixed
     */
//    abstract function indexAction();

    /**
     * System booting errors page
     *
     * @param array $errors    Table of errors (strings)
     * @return mixed
     */
//    abstract function errorAction(array $errors = null);

    /**
     * System booting errors page
     *
     * @param   null|array  $errors    Table of errors (strings)
     * @return  mixed
     */
    abstract function bootErrorAction(array $errors = null);

    /**
     * Generated content page
     *
     * @param string $type  A special type for the generated content
     * @return mixed
     */
    abstract function loremIpsumAction($type = 'html');

    /**
     * Uninstalled application page
     *
     * @param string $altdb An alternative database
     * @return mixed
     */
    abstract function emptyAction($altdb = 'default');

}

// Endfile