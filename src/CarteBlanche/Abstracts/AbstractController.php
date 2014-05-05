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

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\ControllerInterface;
use \CarteBlanche\App\FrontController;

/**
 * The default controller abstract class
 *
 * Any controller must extend this abstract class.
 *
 * ## Required methods
 *
 * This class basically just defines the global template file and a constructor internally.
 * It forces controllers to define two required methods :
 *
 * -   `indexAction()` : the "home" view of the controller,
 * -   `emptyAction()` : a special method for an empty application (not yet installed)
 *
 * ## Framework classes
 *
 * To use the framework error pages, you may "use":
 *
 *     use \CarteBlanche\Exception\NotFoundException,
 *         \CarteBlanche\Exception\AccessForbiddenException,
 *         \CarteBlanche\Exception\InternalServerErrorException;
 *
 * To redirect the code to a "404 not found" page, use:
 *
 *     throw new NotFoundException("message !");
 *
 * To redirect the code to a "403 forbidden" page, use:
 *
 *     throw new AccessForbiddenException("message !");
 *
 * To redirect the code to a "500 internal server error" page, use:
 *
 *     throw new InternalServerErrorException("message !");
 *
 * @author  Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
abstract class AbstractController
    implements ControllerInterface
{

    /**
     * @var string  The default global template file
     */
    static $template = 'template';

    /**
     * @var string  The controller views directory
     * @see \CarteBlanche\App\Locator::locateView()
     *
     * It must be a sub-directory of `_VIEWSDIR` or have to be found by `\CarteBlanche\App\Locator::locateView()`
     *
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
        if (CarteBlanche::getContainer()->get('request')->isAjax()) {
            self::$template = 'empty.txt';
        }
        // the initializer if so
        $this->init();
    }

    /**
     * Get the global app container
     *
     * @return  \CarteBlanche\Interfaces\ContainerInterface
     */
    public function getContainer()
    {
        return CarteBlanche::getContainer();
    }

    /**
     * Get the global kernel from any controller
     *
     * @return  \CarteBlanche\App\Kernel
     */
    public function getKernel()
    {
        return CarteBlanche::getKernel();
    }

    /**
     * Alias of FrontController->render()
     *
     * @param   null/array  $params
     * @param   bool        $debug
     * @param   null/string $exception
     * @return  \CarteBlanche\Interfaces\FrontControllerInterface::render()
     */
    public function render($params = null, $debug = null, $exception = null)
    {
        return FrontController::getInstance()->render($params, $debug, $exception);
    }

    /**
     * Alias of FrontController->view()
     *
     * @param   string      $view
     * @param   null/array  $params
     * @param   bool        $display
     * @param   bool        $exit
     * @return  \CarteBlanche\Interfaces\FrontControllerInterface::view()
     */
    public function view($view = null, $params = null, $display = false, $exit = false)
    {
        return FrontController::getInstance()->view($view, $params, $display, $exit);
    }

    /**
     * Alias of \CarteBlanche\CarteBlanche::trans()
     *
     * @see \CarteBlanche\CarteBlanche::trans()
     */
    public function trans()
    {
        return call_user_func_array(array('\CarteBlanche\CarteBlanche', 'trans'), func_get_args());
    }

}

// Endfile