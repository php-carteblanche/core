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

namespace CarteBlanche\App;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Container;
use \CarteBlanche\Exception\NotFoundException;
use \CarteBlanche\Exception\RuntimeException;
use \CarteBlanche\Interfaces\FrontControllerInterface;
use \CarteBlanche\Library\Helper;
use \Patterns\Abstracts\AbstractSingleton;
use \Library\Helper\Html as HtmlHelper;
use \Library\Helper\Directory as DirectoryHelper;
use \Library\Helper\Code as CodeHelper;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class FrontController
    extends AbstractSingleton
    implements FrontControllerInterface
{

    /**
     * @var object The current controller, must inherit `\CarteBlanche\Abstracts\AbstractController`
     */
    protected $controller;

    /**
     * @var string The current controller requested
     */
    protected $controller_name;

    /**
     * @var string The current action requested in the controller
     */
    protected $action_name;

    /**
     */
    protected function init()
    {
        $this->prepareDom();
    }

// ---------------------------------
// Getters / Setters
// ---------------------------------

    /**
     * Set the current controller
     *
     * @param   \CarteBlanche\Interfaces\ControllerInterface    $ctrl
     * @return  self
     */
    public function setController(\CarteBlanche\Interfaces\ControllerInterface $ctrl)
    {
        $this->controller = $ctrl;
        return $this;
    }

    /**
     * Get the current controller
     *
     * @return      \CarteBlanche\Interfaces\ControllerInterface
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the current controller name
     *
     * @param string $name
     * @return self
     */
    public function setControllerName($name)
    {
        $this->controller_name = $name;
        return $this;
    }

    /**
     * Get the current controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller_name;
    }

    /**
     * Set the current action requested by the router
     *
     * @param string $type
     * @return self
     */
    public function setActionName($type)
    {
        $this->action_name = $type;
        return $this;
    }

    /**
     * Get the current action requested by the router
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->action_name;
    }

// -----------------------
// Request/Response Process
// -----------------------

    /**
     * Prepare the required DOM IDs used by the global template
     *
     * @return self
     */
    protected function prepareDom()
    {
        $dom_refs = array(
            'page_menu', 'page_content', 'page_header', 'page_footer', 'page_title'
        );
        foreach($dom_refs as $_ref) {
            HtmlHelper::getNewId($_ref, true);
        }
        return $this;
    }

    /**
     * Distribute the current request route
     *
     * @return mixed
     */
    public function distribute()
    {
        $this
            ->_processSessionValues()
            ->_processQueryArguments()
            ;

        // if kernerl has booting errors, treat them first
        if (CarteBlanche::getContainer()->get('kernel')->hasBootErrors()) {
            $routing = array(CarteBlanche::getContainer()->get('kernel')->getBootErrors());
            if (CarteBlanche::getContainer()->get('request')->isCli()) {
                $routing['controller'] = CarteBlanche::getConfig('routing.cli.default_controller');
                $routing['action'] = CarteBlanche::getConfig('routing.cli.booterrors_action');
            } elseif (CarteBlanche::getContainer()->get('request')->isAjax()) {
                $routing['controller'] = CarteBlanche::getConfig('routing.ajax.default_controller');
                $routing['action'] = CarteBlanche::getConfig('routing.ajax.booterrors_action');
            } else {
                $routing['controller'] = CarteBlanche::getConfig('routing.mvc.default_controller');
                $routing['action'] = CarteBlanche::getConfig('routing.mvc.booterrors_action');
            }
        } else {
            $routing = CarteBlanche::getContainer()->get('router')
                ->distribute()
                ->getRouteParsed();

            // controller
            if (empty($routing['controller'])) {
                if (CarteBlanche::getContainer()->get('request')->isCli()) {
                    $routing['controller'] = CarteBlanche::getConfig('routing.cli.default_controller');
                } elseif (CarteBlanche::getContainer()->get('request')->isAjax()) {
                    $routing['controller'] = CarteBlanche::getConfig('routing.ajax.default_controller');
                } else {
                    $routing['controller'] = CarteBlanche::getConfig('routing.mvc.default_controller');
                }
            }

            // action
            if (empty($routing['action'])) {
                if (CarteBlanche::getContainer()->get('request')->isCli()) {
                    $routing['action'] = CarteBlanche::getConfig('routing.cli.default_action');
                } elseif (CarteBlanche::getContainer()->get('request')->isAjax()) {
                    $routing['action'] = CarteBlanche::getConfig('routing.ajax.default_action');
                } else {
                    $routing['action'] = CarteBlanche::getConfig('routing.mvc.default_action');
                }
            }
        }

        // arguments
        $primary_args = $routing;
        unset($primary_args['all']);
        $args = isset($routing['all']) ? array_merge($primary_args, $routing['all']) : $primary_args;

        // dispatch
        return $this->dispatch(
            $routing['controller'], $routing['action'], $args
        );
    }

    /**
     * Dispatch a controller's action, passing it arguments
     *
     * @param   string      $controller_classname
     * @param   string      $action
     * @param   array|null  $arguments
     * @return  void
     * @throws  \CarteBlanche\Exception\NotFoundException if the controller or the action is not found
     * @throws  \CarteBlanche\Exception\RuntimeException if the controller's action doesn't return required objects
     */
    public function dispatch($controller_classname, $action = 'index', array $arguments = null)
    {
        // load controller
        $_ctrl = CarteBlanche::getContainer()
            ->get('locator')->locateController($controller_classname);
        if (!empty($_ctrl) && class_exists($_ctrl)) {
            $this
                ->setControllerName($_ctrl)
                ->setController(new $_ctrl(CarteBlanche::getContainer()));
        } else {
            throw new NotFoundException(
                sprintf("Controller '%s' can't be found!", $controller_classname)
            );
        }

        // load action
        if (false===strpos($action, 'Action')) {
            $action .= 'Action';
        }
        $this->setActionName($action);

        // dispatch
        if (method_exists($this->getController(), $this->getActionName())) {
            $result = CodeHelper::fetchArguments(
                $this->getActionName(), $arguments, $this->getController()
            );

            // controller method return treatment
                // string : content
            if (is_string($result)) {
                $this->render(array('output'=>$result));

                // array
            } elseif (is_array($result)) {
                // array : ( output , ... )
                if (isset($result['output'])) {
                    $this->render($result);

                // array : ( view , params )
                } else {
                    $template = $result[0];
                    $params = isset($result[1]) ? $result[1] : array();
                    $content = $this->view($template, $params);
                    if (!empty($content)) {
                        $tpl_params = array('output'=>$content);
                        if (isset($params['title'])) {
                            $tpl_params['title'] = $params['title'];
                        }
                        $this->render($tpl_params);
                    } else {
                        throw new RuntimeException(
                            sprintf("A controller action must return an array like (view file, params) in [%s -> %s]!",
                                get_class($this->getController()), $this->getActionName())
                        );
                    }
                }

            // response object
            } elseif (is_object($result) && ($result instanceof \CarteBlanche\App\Response)) {
                CarteBlanche::getContainer()->set('response', $return, true);
                $this->render();

                // error
            } else {
                throw new RuntimeException(
                    sprintf("A controller action must return a string, an array or a response object in [%s -> %s]!",
                        get_class($this->getController()), $this->getActionName())
                );
            }

        } else {
            throw new NotFoundException(
                sprintf("Action '%s' can't be found in controller '%s'!", $this->getActionName(), get_class($this->getController()))
            );
        }
    }

    /**
     * Render the full application page view
     *
     * @param   array       $params     An array of the parameters passed for the view parsing
     * @param   bool/str    $debug      Object to debug if so
     * @param   \Exception  $exception
     * @return  string
     * @see     self::view()
     */
    public function render($params = null, $debug = null, $exception = null)
    {
        $_router    = CarteBlanche::getContainer()->get('router');
        $_db        = CarteBlanche::getContainer()->get('database');
        $request    = CarteBlanche::getContainer()->get('request');
        $session    = CarteBlanche::getContainer()->get('session');

        $_dbg = $request->getGet('debug');
        if (empty($debug) && isset($_GET['debug']))
            $debug = $_GET['debug'];

        if (empty($params['title'])) {
            if (!empty($_router->routes['action']))
                $params['title'] = $_router->routes['action'];
            else
                $params['title'] = 'Welcome';
        }
        $params['page_title'] = Helper::buildPageTitle($params['title']);
        if ($_db) {
            $params['db_queries'] = $_db->getCache();
        }
        $params['profiler'] = Helper::getProfiler();
        $params['altdb'] = $request->getUrlArg('altdb');
        $params['controller'] = get_class($this->getController());
        $params['action'] = $this->getActionName();

        $params['usersession'] = $session->getAttributes();
        $params['flashsession'] = $session->getBackup('flashes');
        if ($session->hasFlash())
            $params['flash_messages'] = $this->getFlashMessages();

        $params['charset'] = CarteBlanche::getConfig('html.charset', 'utf-8', true);

        $i18n_cfg = CarteBlanche::getConfig('i18n', array(), true);
        $languages_cfg = CarteBlanche::getConfig('languages', array(), true);
        $i18n = CarteBlanche::getContainer()->get('i18n');
        if ($i18n) {
            $default_lang = CarteBlanche::getContainer()->get('i18n')->getLocale();
            $lang_data = isset($languages_cfg[$default_lang]) ? $languages_cfg[$default_lang] : null;
            $params['lang'] = CarteBlanche::getContainer()->get('i18n')->getLanguageCode()
                .'-'.CarteBlanche::getContainer()->get('i18n')->getRegionCode();
            if (!empty($lang_data) && isset($lang_data['charset'])) {
                $params['charset'] = $lang_data['charset'];
            }
        }

        $params['request'] = \Library\Helper\Url::getRequestUrl();

        $_app_globals = CarteBlanche::getConfig('globals', null, true);
        $_cfg_globals = CarteBlanche::getConfig('globals');
        $_globals = array_merge($_app_globals, $_cfg_globals);
        if (!empty($_globals)) {
            $params['_globals'] = $_globals;
            if (!empty($_globals['meta_title']))
                Response::header('X-Website: '.$_globals['meta_title']);
        }

        $_app = CarteBlanche::getContainer()->get('config')
            ->getRegistry()->dumpStack('app');
        if (!empty($_app['app'])) {
            $params['_app'] = $_app['app'];
            if (!empty($_app['app']['name']))
                Response::header('Composed-by: '
                    .$_app['app']['name'].(!empty($_app['app']['version']) ? ' '.$_app['app']['version'] : '')
                );
        }

        $_manifest = CarteBlanche::getContainer()->get('config')
            ->getRegistry()->dumpStack('manifest');
        if (!empty($_manifest['manifest'])) {
            $params['manifest'] = $_app['manifest'];
        }

        $router_views = array();
        $_views = CarteBlanche::getContainer()->get('config')
            ->getRegistry()->dumpStack('views');
        if (!empty($_views)) {
            foreach ($_views as $_view) {
                $viewid = isset($_view['tpl']) ? $_view['tpl'] : uniqid();
                if (!isset($router_views[$viewid])) {
                    $router_views[$viewid] = $_view;
                    $router_views[$viewid]['iterations'] = 1;
                } else {
                    $router_views[$viewid]['iterations']++;
                }
            }
        }

//        \CarteBlanche\App\Debugger::shutdown(true, '\CarteBlanche\App\FrontController::renderExceptionStatic', $params);
        if (isset($exception)) $this->renderError($params, $exception);
        elseif (isset($debug)) $this->renderDebug($params, $debug);

        $ctrl_class = get_class($this->getController());
        if (property_exists($ctrl_class, 'template')) $tpl = $ctrl_class::$template;
        else trigger_error( "No template defined for controller '$ctrl_class'!", E_USER_ERROR );
        $router_views[] = array(
            'tpl'=>$tpl,
            'params'=>$params
        );
        $params['router_views'] = $router_views;
        return $this->view($tpl, $params, true, true);
    /*
        $router_views[] = array(
            'tpl'=>$tpl,
            'params'=>$params
        );
        $ctrl_class = get_class($this->controller);
        $params['router_views'] = $router_views;
        if (property_exists($ctrl_class, 'template')) return $this->view( $ctrl_class::$template, $params, true, true);
        else trigger_error( "No template defined for controller '$ctrl_class'!", E_USER_ERROR );
    */
    }

    /**
     * Render an application error page view
     *
     * @param   array       $params     An array of the parameters passed for the view parsing (passed by reference)
     * @param   int         $code       The error code
     * @param   null/string $exception    The exception info
     * @return  mixed
     * @see     self::render()
     * @see     self::view()
     */
    public function renderError($params = null, $code = 404, $exception = null)
    {
        $mode_data = CarteBlanche::getKernelMode(true);
        if (!is_array($mode_data)
            || !isset($mode_data['debug'])
            || false==$mode_data['debug']
        ) {
            $action = 'error'.$code.'Action';
            $_ctrl = CarteBlanche::getContainer()
                ->get('locator')->locateController('Error');
            if (!empty($_ctrl) && class_exists($_ctrl)) {
                $_routes['controller'] = $_ctrl;
                $this->setController(new $_ctrl(CarteBlanche::getContainer()));
            } else {
                trigger_error("Controller 'Error' can't be found!", E_USER_ERROR);
            }

            // don't execute `shutdown` kernel steps
            CarteBlanche::getContainer()->get('kernel')
                ->setShutdown(false);

            return CodeHelper::fetchArguments(
                $action, $params, $this->getController()
            );
        } else {
            if (!empty($exception)) {
                if (!isset($params['message'])) $params['message'] = '';
                $params['message'] .= method_exists($exception, 'getAppMessage') ?
                    $exception->getAppMessage() : $exception->getMessage();
            }
            $debug = \CarteBlanche\App\Debugger::getInstance();
            $this->renderDebug($params, null, false);
            $params['title'] = $debug->getDebuggerTitle();
            $tpl = 'profiler/template_profiler';

            // don't execute `shutdown` kernel steps
//            CarteBlanche::getContainer()->get('kernel')->setShutdown(false);

            return $this->view($tpl, $params, true, true);
        }
    }

    public function renderProductionError()
    {
        return call_user_func_array(array($this, 'renderError'), func_get_args());
    }

    /**
     * Render a debug application page view
     *
     * @param   array           $params         An array of the parameters passed for the view parsing (passed by reference)
     * @param   string/array    $dbg            Which object variable to debug (default is 'all')
     * @param   bool            $parse_template Parse the debug template or not (default is TRUE)
     * @return  string
     * @see     self::render()
     */
    public function renderDebug(&$params = null, $dbg = 'all', $parse_template = true)
    {
        $all_debug_infos = array( 'backtrace', 'php', 'server', 'session', 'constants', 'headers', 'system', 'router', 'registry', 'db' );
        if (!is_array($dbg)) {
            if ($dbg=='all' || $dbg==1) $dbg = $all_debug_infos;
            else $dbg = array( $dbg );
        }
        $debug =& \CarteBlanche\App\Debugger::getInstance();
        foreach($dbg as $_dbg_info) {
            switch($_dbg_info) {
                case 'router':
                    $debug->addStack('object', CarteBlanche::getContainer()->get('router'), 'Router Object Dump');
                    break;
                case 'registry':
                    $debug->addStack('object', CarteBlanche::getContainer()->get('config')->getRegistry(), 'Registry Object Dump');
                    break;
                case 'db':
                    $debug->addStack('object', CarteBlanche::getContainer()->get('database'), 'Database Object Dump');
                    break;
                default:break;
            }
        }
        $params['debug'] = $debug;
        if ($parse_template) {
            $url_ref = str_replace('&', '&amp;', $_SERVER['HTTP_REFERER']);
            $params['return_url'] = $url_ref;
            $debug->setDebuggerTitle( 'CarteBlanche - Debugging request <a href="'.$url_ref.'" title="Back to this page"><em>'.str_replace(_ROOTHTTP, '/', $url_ref).'</em></a>' );
            $params['title'] = $debug->getDebuggerTitle();
            $tpl = 'profiler/template_profiler';
            return $this->view( $tpl, $params, true, true);
        }
        return;
    }

    /**
     * Treat the session flash messages constructing for each one an array like:
     *     array(
     *         'class'=> 'class name',
     *         'content'=> 'content string'
     *     )
     *
     * @return array An array of all flash messages
     */
    public function getFlashMessages()
    {
        $session    = CarteBlanche::getContainer()->get('session');
        $flash_msgs = array();
        foreach($session->allFlashes() as $msg) {
            if (preg_match('/^([a-z]*):(.*)/i', $msg, $matches)) {
                if (count($matches==3)) {
                    $flash_msgs[] = array(
                        'class'=> $matches[1],
                        'content'=> $matches[2]
                    );
                }
            }
        }
        return $flash_msgs;
    }

    /**
     * Render a view
     *
     * @param   string      $view      The view filename in `$views_dir`
     * @param   array       $params    An array of the parameters passed for the view parsing
     * @param   bool        $display   Display the view or return it (default is FALSE)
     * @param   bool        $exit      Exit after rendering (default is FALSE)
     * @return  string|void
     * @see     \TemplateEngine\TemplateEngine::render()
     */
    public function view($view = null, array $params = array(), $display = false, $exit = false)
    {
        $view_file = CarteBlanche::getContainer()
            ->get('locator')->locateView( $view );
        CarteBlanche::getContainer()->get('config')
            ->getRegistry()->loadStack('views');
        CarteBlanche::getContainer()->get('config')
            ->getRegistry()->setEntry(uniqid(), array(
                'tpl'=>$view,
                'params'=>$params
            ));
        CarteBlanche::getContainer()->get('config')
            ->getRegistry()->saveStack('views', true);
        $output = CarteBlanche::getContainer()->get('template_engine')
            ->render($view_file, $params, $display, $exit);

        if ($display===true) {
            CarteBlanche::getContainer()->get('response')
                ->addContent(null, $output)
                ->send();
            return;
        } else {
            return $output;
        }
    }

// ---------------------
// User settings
// ---------------------

    /**
     * @return self
     */
    protected function _processQueryArguments()
    {
        $args = CarteBlanche::getContainer()->get('request')->getArguments();
        if (!empty($args)) {
            $this->_parseUserSettings($args);
        }
        return $this;
    }

    /**
     * @return self
     */
    protected function _processSessionValues()
    {
        $session = CarteBlanche::getContainer()->get('session');
        $session_data = $session->getSessionTable();
        if (!empty($session_data[$session::SESSION_ATTRIBUTESNAME])) {
            $this->_parseUserSettings($session_data[$session::SESSION_ATTRIBUTESNAME]);
        }
        return $this;
    }

    /**
     * @param  array   $args
     * @return  void
     */
    protected function _parseUserSettings(array $args)
    {
        if (!empty($args)) {
            foreach ($args as $param=>$value) {

                if ($param==='lang') {
                    $langs = CarteBlanche::getContainer()->get('i18n')->getAvailableLanguages();
                    if (array_key_exists($value, $langs)) {
                        CarteBlanche::getContainer()->get('i18n')->setLanguage($value);
                        $session = CarteBlanche::getContainer()->get('session');
                        $true_language = CarteBlanche::getContainer()->get('i18n')->getLanguage();
                        if (!$session->has('lang') || $session->get('lang')!==$true_language) {
                            $session->set('lang', $true_language);
                        }
                    }
                }

            }
        }
    }

}

// Endfile