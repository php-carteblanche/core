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

use \CarteBlanche\Interfaces\ControllerInterface;
use \CarteBlanche\Abstracts\AbstractController;
use \Library\Helper\Text as TextHelper;

/**
 * The default controller abstract class
 *
 * Any controller must extend this abstract class.
 *
 * This class basically just defines the global template file and a constructor internally.
 * It forces controllers to define two required methods :
 *
 * -   `indexAction()` : the "home" view of the controller,
 * -   `emptyAction()` : a special method for an empty application (not yet installed)
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractControllerConfigurable
    extends AbstractController
    implements ControllerInterface
{

    /**
     * @var string  The string used to identify the configuration entries
     *
     * This defaults to the class name in lower case.
     */
    protected $_config_reference;

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * Set the object configuration entries
     *
     * @param   null|array  $config
     * @return  self
     */
    public function setConfig(array $config = null)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Get the object configuration entries or a single entry
     *
     * @param string $index
     * @return mixed
     */
    public function getConfig($index = null)
    {
        if (!empty($index)) {
            return isset($this->_config[$index]) ? $this->_config[$index] : null;
        }
        return $this->_config;
    }

// ------------------------------------------
// Constructor
// ------------------------------------------

    /**
     * Class constructor : load the configuration entries if so
     */
    public function __construct()
    {
        if (empty($this->_config_reference)) {
            $parts = explode('\\', get_class($this));
            $this->_config_reference = TextHelper::fromCamelCase(end($parts));
        }
        $this->setConfig(
            $this->getContainer()->get('config')->get($this->_config_reference)
        );
        parent::__construct();
    }

}

// Endfile