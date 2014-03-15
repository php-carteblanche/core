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

use \CarteBlanche\CarteBlanche,
    \CarteBlanche\App\Container,
    \CarteBlanche\App\FrontController,
    \CarteBlanche\Abstracts\AbstractController;

use \Library\CommandLine\AbstractCommandLineController as Original,
    \Library\CommandLine\CommandLineControllerInterface,
    \Library\CommandLine\Helper;

/**
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
abstract class AbstractCommandLineController
    extends Original
    implements CommandLineControllerInterface
{

// ------------------------------------------
// Abstract methods & user definables
// ------------------------------------------

    /**
     * The script name
     * @var string
     */
    public static $_name = '';

    /**
     * The script version
     * @var string
     */
    public static $_version = '';

	/**
	 * The default action of the controller, considered as 'home'
	 */
	abstract function indexAction();

	/**
	 * Initialization: to be overwritten (if needed, this method is called at the end of constructor)
	 */
	protected function init(){}

// ------------------------------------------
// Object
// ------------------------------------------

	/**
	 * The directory where to search the views files
	 */
	static $views_dir = null;
	
	/**
	 * The default console template file
	 */
	static $template = 'empty.txt';

	/**
	 * Class constructor : it checks if the application is installed
	 *
	 * @see self::init()
	 */
	public function __construct(array $options = array())
	{
	    parent::__construct($options);
		
    	$_app = CarteBlanche::getConfig('app');
    	$_cls = get_called_class();
		if (empty($_cls::$_name)) self::$_name = $_app['name'];
		if (empty($_cls::$_version)) self::$_version = $_app['version'];
		
		// the initializer if so
		$this->init();
	}

	/**
	 * Get the global app container
	 *
	 * @see App\Container
	 */
	public function getContainer()
	{
		return CarteBlanche::getContainer();
	}

	/**
	 * Get the global kernel from any controller
	 *
	 * @see App\Kernel
	 */
	public function getKernel()
	{
		return CarteBlanche::getKernel();
	}

	/**
	 * Alias of App\FrontController->render()
	 *
	 * @see App\FrontController::render()
	 */
	public function render($params = null, $debug = null, $exception = null)
	{
		return FrontController::getInstance()
		    ->render( $params, $debug, $exception );
	}

	/**
	 * Alias of App\FrontController->view()
	 *
	 * @see App\FrontController::view()
	 */
	public function view($view = null, $params = null, $display = false, $exit = false) 
	{
		return FrontController::getInstance()
		    ->view( $view, $params, $display, $exit );
	}

}

// Endfile