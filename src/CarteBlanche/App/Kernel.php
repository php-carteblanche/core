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

use \CarteBlanche\App\Container;
use \CarteBlanche\App\Config;
use \CarteBlanche\App\Locator;
use \CarteBlanche\App\Loader;
use \CarteBlanche\App\FrontController;
use \CarteBlanche\Exception\Exception;
use \CarteBlanche\Exception\ErrorException;
use \CarteBlanche\Exception\NotFoundException;
use \CarteBlanche\Exception\DomainException;
use \CarteBlanche\Exception\InvalidArgumentException;
use \CarteBlanche\Exception\RuntimeException;
use \CarteBlanche\Exception\UnexpectedValueException;
use \Patterns\Commons\Registry;
use \Patterns\Interfaces\StaticCreatorInterface;
use \Library\Helper\Html as HtmlHelper;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * This is the global singleton instance of the CarteBlanche application
 *
 * @author      Piero Wbmstr <me@e-piwi.fr>
 */
final class Kernel
    implements StaticCreatorInterface
{

// -----------------------------------
// CarteBlanche package infos
// -----------------------------------

    /**
     * The package name
     */
    const CARTE_BLANCHE_PACKAGE = 'carte-blanche/core';

    /**
     * The kernel name
     */
    const CARTE_BLANCHE_NAME = 'CarteBlanche';

    /**
     * The kernel version
     */
    const CARTE_BLANCHE_VERSION = '0.1.0';

    /**
     * The kernel homepage
     */
    const CARTE_BLANCHE_HOMEPAGE = 'http://github.com/php-carteblanche/';

    /**
     * The kernel online documentation
     */
    const CARTE_BLANCHE_DOCUMENTATION = 'http://carte-blanche.docs.ateliers-pierrot.fr/';

// -----------------------------------
// CarteBlanche constants
// -----------------------------------

    /**
     * The manifest file
     */
    const CARTE_BLANCHE_MANIFEST = 'composer.json';

    /**
     * The default internal configuration file
     */
    const CARTE_BLANCHE_CONFIG_FILE = 'carteblanche.ini';

    /**
     * The mask for server settings specific to CarteBlanche
     */
    const CARTE_BLANCHE_SERVER_SETTING_PREFIX = 'CARTE_BLANCHE__';

    /**
     * The internal views directory
     */
    const CARTE_BLANCHE_INTERNAL_VIEWSDIR = 'views';

    /**
     * The global CarteBlanche namespace
     */
    const CARTE_BLANCHE_NAMESPACE = '\CarteBlanche';

    /**
     * The name of the interface any dependency loader must implement
     */
    const DEPENDENCY_LOADER_INTERFACE = '\CarteBlanche\Interfaces\DependencyLoaderInterface';

    /**
     * The name of the default prefix for dependency loader classes
     */
    const DEPENDENCY_LOADER_DEFAULT_NAMESPACE = '\Loader\\';

    /**
     * The name of the possible suffix for dependency loader classes
     */
    const DEPENDENCY_LOADER_SUFFIX = 'Loader';

    /**
     * The name of the bundle class
     */
    const BUNDLE_CLASS = '\CarteBlanche\App\Bundle';

    /**
     * Name of the mandatory interface for configuration file types parsers
     */
    const CONFIG_FILETYPE_INTERFACE = '\CarteBlanche\Interfaces\ConfigFiletypeInterface';

    /**
     * Name of the default namespace prefix for configuration parsers
     */
    const CONFIG_FILETYPE_DEFAULT_NAMESPACE = '\CarteBlanche\App\ConfigFiletype\\';

    /**
     * Name of the possible namespace suffix for configuration parsers
     */
    const CONFIG_FILETYPE_SUFFIX = 'Filetype';

    /**
     * The name of the interface any front controller must implement
     */
    const FRONT_CONTROLLER_INTERFACE = '\CarteBlanche\Interfaces\FrontControllerInterface';

    /**
     * The name of the interface any controller must implement
     */
    const CONTROLLER_INTERFACE = '\CarteBlanche\Interfaces\DependencyLoaderInterface';

    /**
     * The name of the default prefix for controllers
     */
    const CONTROLLER_DEFAULT_NAMESPACE = '\Controller\\';

    /**
     * The name of the possible suffix for controllers
     */
    const CONTROLLER_SUFFIX = 'Controller';

    /**
     * Name of the mandatory interface for entity managers
     */
    const ENTITY_MANAGER_INTERFACE = '\CarteBlanche\Interfaces\EntityManagerInterface';

    /**
     * Name of the mandatory interface for storage engines
     */
    const STORAGE_ENGINE_INTERFACE = '\CarteBlanche\Interfaces\StorageEngineInterface';

    /**
     * Name of the mandatory interface for database adapters
     */
    const DATABASE_ADAPTER_INTERFACE = '\CarteBlanche\Library\StorageEngine\DatabaseAdapter\DatabaseAdapterInterface';

    /**
     * Name of the mandatory interface for database adapters
     */
    const DATABASE_ADAPTER_ABSTRACT = '\CarteBlanche\Library\StorageEngine\DatabaseAdapter\AbstractDatabaseAdapter';

    /**
     * Name of the default namespace prefix for database adapters
     */
    const DATABASE_ADAPTER_DEFAULT_NAMESPACE = '\CarteBlanche\Library\StorageEngine\DatabaseAdapter\\';

    /**
     * Name of the possible namespace suffix for database adapters
     */
    const DATABASE_ADAPTER_SUFFIX = 'Adapter';

    /**
     * Name of the mandatory interface for database drivers
     */
    const DATABASE_DRIVER_INTERFACE = '\CarteBlanche\Library\StorageEngine\DatabaseDriver\DatabaseDriverInterface';

    /**
     * Name of the mandatory interface for database drivers
     */
    const DATABASE_DRIVER_ABSTRACT = '\CarteBlanche\Library\StorageEngine\DatabaseDriver\AbstractDatabaseDriver';

    /**
     * Name of the default namespace prefix for database drivers
     */
    const DATABASE_DRIVER_DEFAULT_NAMESPACE = '\CarteBlanche\Library\StorageEngine\DatabaseDriver\\';

    /**
     * Name of the possible namespace suffix for database drivers
     */
    const DATABASE_DRIVER_SUFFIX = 'Driver';


// -----------------------------------
// Kernel object
// -----------------------------------

    /**
     * @var bool
     */
    protected $is_booted = false;

    /**
     * @var array
     */
    protected $boot_errors = array();

    /**
     * @var bool
     */
    protected $is_debug = false;

    /**
     * @var bool
     */
    protected $is_shutdown = false;

    /**
     * @var string
     */
    protected $mode = 'dev';

    /**
     * Constructor : defines the current URL and gets the routes
     *
     * @param   string/array    $config_file
     * @param   array           $user_config
     * @param   string          $mode
     * @return  \CarteBlanche\App\Kernel
     */
    public static function create($config_files = null, array $user_config = null, $mode = null)
    {
        $_cls = get_called_class();
        $_obj = new $_cls;
        call_user_func_array(array($_obj, 'init'), func_get_args());
        return $_obj;
    }

    /**
     * Initializer : defines required paths and parse configuration
     *
     * @param   null/string/array    $config_files
     * @param   null/array           $user_config
     * @param   null/string          $mode_arg       The kernel mode, can be 'prod' or 'dev' (for now)
     * @return  void
     */
    public function init($config_files = null, array $user_config = null, $mode_arg = null)
    {
        $this->getContainer()->set('kernel', $this);
        $this->getContainer()->set('config', new \CarteBlanche\App\Config);
        $config = $this->getContainer()->get('config');

        if (!defined('_ROOTFILE')) {
            die('You need to define the "_ROOTFILE" constant to run CarteBlanche!');
        }
        if (!defined('_ROOTPATH')) {
            die('You need to define the "_ROOTPATH" constant to run CarteBlanche!');
        }

        spl_autoload_register(array('\CarteBlanche\App\Loader', 'autoload'));
        register_shutdown_function(array($this, 'shutdown'));
        set_include_path( get_include_path().PATH_SEPARATOR._ROOTPATH );

        // base required paths
        $this
            ->initConstantPath('_ROOTFILE', 'root_file')
            ->initConstantPath('_ROOTPATH', 'root_path', true)
            ->addPath('carte_blanche_core', realpath(__DIR__.'/../'))
            ;
        if (!$this->isCli()) {
            if (!defined('_ROOTHTTP')) {
                self::__getHttpRoot();
            }
            $this->initConstantPath('_ROOTHTTP', 'root_http');
        }

        // user config file
        $_defconf = false;
        if (!is_array($config_files)) $config_files = array($config_files);
        $config_files = array_filter($config_files);
        if (!empty($config_files)) {
            foreach($config_files as $cfgf) {
                if (substr_count($cfgf, self::CARTE_BLANCHE_CONFIG_FILE)) {
                    $_defconf = true;
                }
            }
        }
        if (true!==$_defconf) {
            $this->__loadDefaultConfig();
        }
        if (!empty($config_files)) {
            foreach($config_files as $cfgf) {
                if ( ! file_exists($cfgf)) {
                    $cfgf = $this->getPath('root_path').$cfgf;
                }
                $config->load($cfgf);
            }
        }

        // server config
        if (!empty($_SERVER)) {
            foreach ($_SERVER as $_name=>$_conf) {
                if (substr($_name, 0, strlen(self::CARTE_BLANCHE_SERVER_SETTING_PREFIX))===self::CARTE_BLANCHE_SERVER_SETTING_PREFIX) {
                    $config->set(array(
                            str_replace(self::CARTE_BLANCHE_SERVER_SETTING_PREFIX, '', $_name) => $_conf
                        ), true, 'server');
                }
            }
        }

        // user config
        if (!empty($user_config)) {
            $config->set($user_config, true, 'user');
        }

        // error reporting
        if (!empty($mode_arg)) {
            $this->__setMode($mode_arg);
        }

        // manifest file
        $_f = $this->getPath('root_path').'/composer.json';
        if (file_exists($_f = $this->getPath('root_path').self::CARTE_BLANCHE_MANIFEST)) {
            $manifest = @json_decode(@file_get_contents($_f), true);
            $config->set($manifest, false, 'manifest');
        }

        // application directories
        $app_dirs = $config->get('carte_blanche.app_dirs', null, true);
        foreach ($app_dirs as $name=>$dir) {
            $this->addPath($name, $dir);
        }

        // application required directories
        $app_dirs = $config->get('carte_blanche.required_dirs', Config::NOT_FOUND_GRACEFULLY, array());
        foreach ($app_dirs as $i=>$name) {
            try {
                $this->addPath($name, $this->getPath($name), true);
            } catch (ErrorException $e) {
                $this->addBootError(
                    sprintf('An error occured while booting: "%s" [03]', $e->getMessage())
                );
            } catch (Exception $e) {
                $this->addBootError(
                    sprintf('An error occured while booting: "%s" [04]', $e->getMessage())
                );
            }
        }

        // application required directories
        $app_dirs = $config->get('carte_blanche.writable_dirs', Config::NOT_FOUND_GRACEFULLY, array());
        foreach ($app_dirs as $i=>$name) {
            try {
                $this->addPath($name, $this->getPath($name), true, true);
            } catch (ErrorException $e) {
                $this->addBootError(
                    sprintf('An error occured while booting: "%s" [05]', $e->getMessage())
                );
            } catch (Exception $e) {
                $this->addBootError(
                    sprintf('An error occured while booting: "%s" [06]', $e->getMessage())
                );
            }
        }

        $base_objects = $config->get('carte_blanche.base_objects');
        foreach(array('loader', 'locator', 'response') as $type) {
            $class_name = isset($base_objects[$type]) ?
                $base_objects[$type] : '\CarteBlanche\App\\'.ucfirst($type);
            if (class_exists($class_name)) {
                $this->getContainer()->set($type, new $class_name);
            } else {
                $this->addBootError(
                    sprintf('Required base object type "%s" not found! (searching class "%s")',
                        $type,
                        $class_name
                    )
                );
            }
        }
    }

    /**
     * Boot: execute the app bootstrap and creates necessary dirs
     *
     * @return self
     * @throws  \CarteBlanche\Exception\ErrorException if the Bootstrap file can't be found
     */
    private function boot()
    {
        $config = $this->getContainer()->get('config');

        // load php settings
        $php_settings = $config->get('carte_blanche.php_settings');
        foreach ($php_settings as $type=>$setting) {
            if ($type==='date_timezone' && is_array($setting) && isset($setting['default'])) {                
                @date_default_timezone_set($setting['default']);
            } else {
                @ini_set($type, $setting); 
            }
        }

        // load internal dependencies
        $internal_deps = $config->get('carte_blanche.internal_dependencies');
        foreach ($internal_deps as $name=>$dep) {
            try {
                $this->getContainer()->load(
                    is_numeric($name) ? $dep : $name,
                    array(),
                    $dep
                );
            } catch (Exception $e) {
                $this->addBootError(
                    sprintf('An error occured while loading a dependency: "%s"', $e->getMessage())
                );
            }
        }

        // load the app bootstrap
        if (false===$this->is_booted) {
            $namespace = $config->get('carte_blanche.app_namespace', 'App');
            if ($_f = Locator::locateData('bootstrap.php')) {
                include_once $_f;
                $bootstrap_class = '\\'.$namespace.'\Bootstrap\ContainerBootstrap';
                $cont = new $bootstrap_class($this);
            } else {
                throw new ErrorException("Bootstrap file can't be found!");
            }
        }

        $this->is_booted=true;
        return $this;
    }

    /**
     * Application specific shutdown handling
     */
    public function shutdown(&$arg = null, $callback = null)
    {
        if ($this->getDebug() && false===$this->is_shutdown) {
            return \DevDebug\Debugger::shutdown(true, $callback);
        }
    }

    /**
     * Defines the request to treat
     *
     * @param   \CarteBlanche\App\Request   $request
     * @return  self
     */
    public function handles(Request $request)
    {
        if (!$this->is_booted) {
            $this->boot();
        }
        $this->getContainer()->set('request', $request);
        $this->getContainer()->get('router')->setUrl($request->buildUrl());
        $mode_data = $this->getMode(true);
        if (isset($mode_data['log_requests']) && $mode_data['log_requests']) {
            \CarteBlanche\CarteBlanche::log(
                get_class($request).' :: '.$this->getContainer()->get('request')->getUrl(),
                \Library\Logger::INFO
            );
        }
        return $this;
    }

    /**
     * Distributes the application actions, controllers and views
     *
     * If no request is defined yet, this will handle current HTTP request if so.
     *
     * @return string|void
     * @see \CarteBlanche\App\FrontController::distribute()
     */
    public function distribute()
    {
        if (!$this->is_booted) {
            $this->boot();
        }
        $req = $this->getContainer()->get('request');
        if (empty($req)) {
            $this->handles(new Request);
        }
        return $this->getContainer()->get('front_controller')
                ->distribute();
    }

    /**
     * Load a new bundle namespace and map it to its path
     *
     * @param string $space
     * @param string $dir
     * @return bool
     */
    public function initBundle($space, $dir)
    {
        $bundle = new \CarteBlanche\App\Bundle($space, $dir);
        return $this->getContainer()->setBundle($space, $bundle);
    }

// ------------------------
// Setters / Getters
// ------------------------

    /**
     * Get the current kernel mode or mode configuration settings
     *
     * @param bool $data    Get the configuration data (`true`) or just the mode name (default)
     * @return string
     */
    public function getMode($data = false)
    {
        return true===$data ? $this->mode_data : $this->mode;
    }

    /**
     * Define the current kernel debug mode
     *
     * @param bool|string $debug
     * @return self
     */
    public function setDebug($debug)
    {
        $this->is_debug = (Boolean) $debug;
        return $this;
    }

    /**
     * Get the current kernel debug mode
     *
     * @return bool
     */
    public function getDebug()
    {
        return (Boolean) $this->is_debug;
    }

    /**
     * Add a booting error
     *
     * @param string $string
     * @return self
     */
    public function addBootError($string)
    {
        $this->boot_errors[] = $string;
        if ($this->getMode()==='dev') {
            die(
                sprintf("[boot error] : %s", $string)
            );
        }
        return $this;
    }

    /**
     * Test if current kernel has booting errors
     *
     * @return bool
     */
    public function hasBootErrors()
    {
        return !empty($this->boot_errors);
    }

    /**
     * Get the booting errors stack
     *
     * @return array
     */
    public function getBootErrors()
    {
        return $this->boot_errors;
    }

    /**
     * Get the global \CarteBlanche\App\Container object
     *
     * @return \CarteBlanche\App\Container
     */
    public function getContainer()
    {
        return Container::getInstance();
    }

    /**
     * Initialize a path from a constant
     *
     * @param string $cst               A constant name
     * @param string $path_ref          The path reference
     * @param bool $must_exists         Check if concerned path exists
     * @param bool $must_be_writable    Check if concerned path is writable
     * @return self
     *
     * @throws ErrorException if the constant is not defined
     */
    public function initConstantPath($cst, $path_ref, $must_exists = false, $must_be_writable = false)
    {
        if (defined($cst)) {
            $this->addPath($path_ref, constant($cst), $must_exists, $must_be_writable);
        } else {
            throw new ErrorException(
                sprintf('Missing constant path "%s"!', $cst)
            );
        }
        return $this;
    }

    /**
     * References a path value
     *
     * @param string $name              The path reference
     * @param string $value             The path value
     * @param bool $must_exists         Check if concerned path exists
     * @param bool $must_be_writable    Check if concerned path is writable
     * @return self
     *
     * @throws RuntimeException if the path does not exists and `$must_exists` is true
     * @throws ErrorException if the concerned path is not writable while it was required
     */
    public function addPath($name, $value, $must_exists = false, $must_be_writable = false)
    {
        $config = $this->getContainer()->get('config');
        if ($must_exists) {
            $realpath = $this->getAbsolutePath($value);
            if (!empty($realpath)) {
                $value = $realpath;
            } else {
                DirectoryHelper::ensureExists($value);
                if (!file_exists($value)) {
                    $this->addBootError(
                        sprintf('Directory "%s" defined as an application path doesn\'t exist and can\'t be created!', $value)
                    );
/*
                    throw new RuntimeException(
                        sprintf('Directory "%s" defined as an application path doesn\'t exist and can\'t be created!', $value)
                    );
*/
                }
            }
        }
        if ($must_be_writable && !is_writable($value)) {
           $this->addBootError(
                sprintf('Directory "%s" must be writable! (%s)', $value, $name)
            );
/*
            throw new RuntimeException(
                sprintf('Directory "%s" must be writable!', $value)
            );
*/
        }
        $config->getRegistry()
            ->loadStack('paths')
            ->setEntry($name, $value)
            ->saveStack('paths', true);
        return $this;
    }

    /**
     * Get a path value
     *
     * @param   string    $name       The path reference
     * @param   bool      $full_path  Must return an absolute path or not (default)
     * @return  string|null
     */
    public function getPath($name, $full_path = false)
    {
        $config = $this->getContainer()->get('config');
        $path = $config->getRegistry()->getStackEntry($name, null, 'paths');
        if (file_exists($path) && is_dir($path)) {
            $path = DirectoryHelper::slashDirname($path);
        }
        if (true===$full_path) {
            return $this->getAbsolutePath($path);
        }
        return $path;
    }

    /**
     * Build an app absolute path
     *
     * @param   string    $path       The relative path to build
     * @return  string|null
     */
    public function getAbsolutePath($path)
    {
        $root = $this->getPath('root_path');
        if (empty($root)) {
            return null;
        }
        if (empty($path)) {
            return $root;
        }
        if (0===substr_count($path, $root)) {
            $path = DirectoryHelper::slashDirname($root).$path;
        }
        return file_exists($path) ? DirectoryHelper::slashDirname(realpath($path)) : null;
    }

    /**
     * Define if current kernel must launch its `shutdown` steps
     *
     * @param   bool    $bool
     * @return  self
     */
    public function setShutdown($bool = false)
    {
        $this->is_shutdown = (Boolean) $bool;
        return $this;
    }

// ------------------------
// Utilities
// ------------------------

    /**
     * Get current user name running the app
     *
     * @return string
     */
    public function whoAmI()
    {
        $cmd = new \Library\Command;
        $whoami = $cmd->run('whoami');
        return !empty($whoami[0]) ? $whoami[0] : null;
    }

    /**
     * Check if app is in 'CLI' call mode
     *
     * @return bool
     */
    public function isCli()
    {
        return (strpos(strtolower(php_sapi_name()),'cli')!==false);
    }

// ------------------------
// Private Setters / Getters
// ------------------------

    /**
     * Define the current kernel mode
     *
     * Launch settings according to the MODE config entry
     *
     * @param string $mode
     * @return self
     */
    private function __setMode($mode = 'dev')
    {
        $config = $this->getContainer()->get('config');
        $mode_data = $config->get('carte_blanche.modes', array(), 'app');

        if (array_key_exists($mode, $mode_data)) {
            $this->mode = strtolower($mode);
        } else {
            $this->mode = isset($mode_data['default']) ? strtolower($mode_data['default']) : 'dev';
        }

        if (isset($mode_data[$this->mode])) {
            $this->mode_data = $mode_data[$this->mode];
            if (isset($this->mode_data['display_errors'])) {
                @ini_set('display_errors',$this->mode_data['display_errors']);
            }
            if (isset($this->mode_data['error_reporting'])) {
                @error_reporting($this->mode_data['error_reporting']);
            }
            if (isset($this->mode_data['debug']) && $this->mode_data['debug']) {
                $this->setDebug(true);
            } else {
                $this->setDebug(false);
            }
        }
        return $this;
    }

    /**
     * Load the default configuration file
     *
     * @throws \CarteBlanche\Exception\ErrorException
     */
    private function __loadDefaultConfig()
    {
        $app_cfgfile = __DIR__.'/../../../config/'.self::CARTE_BLANCHE_CONFIG_FILE;
        if (!file_exists($app_cfgfile)) {
            throw new ErrorException( 
                sprintf('Default application configuration file not found in "%s" [%s]!', $this->getPath('config_dir'), $app_cfgfile)
            );
        }
        $this->getContainer()->get('config')->load($app_cfgfile);
    }

    /**
     * Retrieve the base URL to use to construct the application routes (found from the current domain and path URL)
     */
    private function __getHttpRoot()
    {
        if (!defined('_ROOTHTTP')) {
            $_roothttp = '';
            if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                $_roothttp = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])!='off') ? 'https://' : 'http://';
                $_roothttp .= $_SERVER['HTTP_HOST'];
            }
            if (isset($_SERVER['PHP_SELF']) && !empty($_SERVER['PHP_SELF'])) {
                $_roothttp .= str_replace( '\\', '/', dirname($_SERVER['PHP_SELF']));
            }
            if (strlen($_roothttp)>0 && substr($_roothttp, -1) != '/') $_roothttp .= '/';
            define('_ROOTHTTP', $_roothttp);
        }
    }

}

// Endfile