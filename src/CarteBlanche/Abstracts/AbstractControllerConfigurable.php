<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Abstracts;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\ControllerInterface;
use \CarteBlanche\Abstracts\AbstractController;
use \CarteBlanche\App\FrontController;

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
 * @author  Piero Wbmstr <piero.wbmstr@gmail.com>
 */
abstract class AbstractControllerConfigurable
    extends AbstractController
    implements ControllerInterface
{

    /**
     * The string used to identify the configuration entries
     *
     * This defaults to the class name in lower case.
     *
     * @var string
     */
    protected $_config_reference;

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * Set the object configuration entries
     *
     * @param array $config
     *
     * @return self
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
     *
     * @return misc
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