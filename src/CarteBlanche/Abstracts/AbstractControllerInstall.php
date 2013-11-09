<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Abstracts;

use \CarteBlanche\Interfaces\ControllerInterface;
use \CarteBlanche\App\FrontController;

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
abstract class AbstractController
    implements ControllerInterface
{

	/**
	 * The default global template file
	 */
	static $template = 'template.htm';

	/**
	 * The controller views directory
	 *
	 * It must be a sub-directory of `_VIEWSDIR` or have to be found by `App\Locator::locateView()`
	 *
	 * @see App\Locator::locateView()
	 */
	static $views_dir = '';

// ------------------------------------------
// Abstract methods
// ------------------------------------------

	/**
	 * The default action of the controller, considered as 'home'
	 */
	abstract function indexAction();

	/**
	 * A special action used if the application is not installed
	 *
	 * @param string $altdb The database name to work on
	 */
	abstract function emptyAction($altdb);

	/**
	 * Initialization: to be overwritten (if needed, this method is called at the end of constructor)
	 */
	protected function init(){}

// ------------------------------------------
// Constructor
// ------------------------------------------

	/**
	 * Class constructor : it checks if the application is installed
	 *
	 * @see self::init()
	 */
	public function __construct()
	{
		// the initializer if so
		$this->init();
	}

	/**
	 * Alias of App\FrontController->render()
	 *
	 * @see App\FrontController::render()
	 */
	public function render($params = null, $debug = null, $exception = null)
	{
		return FrontController::getInstance()->render($params, $debug, $exception);
	}

	/**
	 * Alias of App\FrontController->view()
	 *
	 * @see App\FrontController::view()
	 */
	public function view($view = null, $params = null, $display = false, $exit = false) 
	{
		return FrontController::getInstance()->view($view, $params, $display, $exit);
	}

}

// Endfile