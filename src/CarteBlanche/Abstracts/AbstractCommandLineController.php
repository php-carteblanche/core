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

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\FrontController;
use \Library\CommandLine\AbstractCommandLineController as Original;
use \Library\CommandLine\CommandLineControllerInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractCommandLineController
    extends Original
    implements CommandLineControllerInterface
{

// ------------------------------------------
// Abstract methods & user definables
// ------------------------------------------

    /**
     * @var string  The script name
     */
    public static $_name = '';

    /**
     * @var string  The script version
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
     * @return \CarteBlanche\App\Container
     * @see \CarteBlanche\App\Container
     */
    public function getContainer()
    {
        return CarteBlanche::getContainer();
    }

    /**
     * Get the global kernel from any controller
     *
     * @return \CarteBlanche\App\Kernel
     * @see \CarteBlanche\App\Kernel
     */
    public function getKernel()
    {
        return CarteBlanche::getKernel();
    }

    /**
     * Alias of App\FrontController->render()
     *
     * @return string
     * @see \CarteBlanche\App\FrontController::render()
     */
    public function render($params = null, $debug = null, $exception = null)
    {
        return FrontController::getInstance()
            ->render( $params, $debug, $exception );
    }

    /**
     * Alias of App\FrontController->view()
     *
     * @return string
     * @see \CarteBlanche\App\FrontController::view()
     */
    public function view($view = null, $params = null, $display = false, $exit = false)
    {
        return FrontController::getInstance()
            ->view( $view, $params, $display, $exit );
    }

}

// Endfile