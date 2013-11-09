<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\App;

use \CarteBlanche\App\Container,
    \CarteBlanche\App\Config,
    \CarteBlanche\App\Locator,
    \CarteBlanche\App\Loader,
    \CarteBlanche\App\FrontController,
    \CarteBlanche\Exception\Exception,
    \CarteBlanche\Exception\ErrorException,
    \CarteBlanche\Exception\NotFoundException,
    \CarteBlanche\Exception\DomainException,
    \CarteBlanche\Exception\InvalidArgumentException,
    \CarteBlanche\Exception\RuntimeException,
    \CarteBlanche\Exception\UnexpectedValueException;

use \Patterns\Commons\Registry,
    \Patterns\Interfaces\StaticCreatorInterface;

use \Library\Helper\Html as HtmlHelper,
    \Library\Helper\Directory as DirectoryHelper;

/**
 * This is the global singleton instance of the application
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
final class Kernel implements StaticCreatorInterface
{

// -----------------------------------
// CarteBlanche package infos
// -----------------------------------

    /**
     * The package name
     */
    public static $CARTE_BLANCHE_PACKAGE = 'atelierspierrot/carte-blanche';

    /**
     * The kernel name
     */
    public static $CARTE_BLANCHE_NAME = 'CarteBlanche';

    /**
     * The kernel version
     */
    public static $CARTE_BLANCHE_VERSION = '0.1.0';

    /**
     * The kernel homepage
     */
    public static $CARTE_BLANCHE_HOMEPAGE = 'http://github.com/php-carteblanche/carteblanche';

    /**
     * The kernel online documentation
     */
    public static $CARTE_BLANCHE_DOCUMENTATION = 'http://carte-blanche.docs.ateliers-pierrot.fr/';

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
	 * @var string
	 */
	protected $mode = 'prod';

	/**
	 * Constructor : defines the current URL and gets the routes
	 *
	 * @param string|array $config_file
	 * @param array $user_config
	 * @param string $mode
	 *
	 * @return \CarteBlanche\App\Kernel
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
	 * @param string|array $config_files
	 * @param array $user_config
	 * @param string $mode The kernel mode, can be 'prod' or 'dev' (for now)
	 *
	 * @throws ErrorException if the application default configuration file is not found
	 * @throws ErrorException if the user configuration file is defined but not found
	 */
	public function init($config_files = null, array $user_config = null, $mode = null)
	{
	    if (!empty($mode)) {
	        $this->setMode($mode);
	    }
		$this->getContainer()->set('kernel', $this);
		$this->getContainer()->set('config', new \CarteBlanche\App\Config);
        $config = $this->getContainer()->get('config');
		$this->getContainer()->set('loader', new \CarteBlanche\App\Loader);
		$this->getContainer()->set('locator', new \CarteBlanche\App\Locator);
		$this->getContainer()->set('response', new \CarteBlanche\App\Response);

        spl_autoload_register(array('\CarteBlanche\App\Loader', 'autoload'));
        register_shutdown_function(array($this, 'shutdown'));

        // app paths
        try {
            $this
                // global required current application paths
                ->initConstantPath('_ROOTFILE', 'root_file')
                ->initConstantPath('_ROOTPATH', 'root_path', true)
                ->initConstantPath('_VENDORDIRNAME', 'vendor_dir_name')                
                // global required relative/absolute paths
                ->initConstantPath('_CONFIGDIR', 'config_dir')
                ->addPath('config_path', $this->getPath('root_path').$this->getPath('config_dir'), true, true)
                ->initConstantPath('_LANGUAGEDIR', 'i18n_dir')
                ->addPath('i18n_path', $this->getPath('root_path').$this->getPath('i18n_dir'), true, true)
                ->initConstantPath('_VARDIR', 'var_dir')
                ->addPath('var_path', $this->getPath('root_path').$this->getPath('var_dir'), true, true)
                ->initConstantPath('_SRCDIR', 'src_dir')
                ->addPath('src_path', $this->getPath('root_path').$this->getPath('src_dir'), true)
                ->initConstantPath('_BINDIR', 'bin_dir')
                ->addPath('bin_path', $this->getPath('root_path').$this->getPath('bin_dir'), true)
                ->initConstantPath('_WEBDIR', 'web_dir')
                ->addPath('web_path', $this->getPath('root_path').$this->getPath('web_dir'), true)
                ->initConstantPath('_LIBDIR', 'lib_dir')
                ->addPath('lib_path', $this->getPath('src_path').$this->getPath('lib_dir'))
                // internal cache dir
                ->initConstantPath('_APPCACHEDIR', 'app_cache_dir')
                ->addPath('app_cache_path', $this->getPath('root_path').$this->getPath('app_cache_dir'), true, true)
                // paths from src
                ->initConstantPath('_BUNDLESDIR', 'bundles_dir')
                ->addPath('bundles_path', $this->getPath('src_path').$this->getPath('bundles_dir'), true)
                ->initConstantPath('_TOOLSDIR', 'tools_dir')
                ->addPath('tools_path', $this->getPath('src_path').$this->getPath('tools_dir'), true)
                ->initConstantPath('_VIEWSDIRNAME', 'views_dir')
                // global CarteBlanche path
                ->addPath('carte_blanche_core', $this->getPath('src_path').'/vendor/carte-blanche/core/src/CarteBlanche/')
                // user fallbacks
                ->initConstantPath('_USERDIR', 'user_dir')
                ->addPath('user_path', $this->getPath('root_path').$this->getPath('user_dir'))
                ;
            if (defined('_CLI_CALL') && false===_CLI_CALL) {
                $this
                    ->initConstantPath('_ROOTHTTP', 'root_http')
                    // paths from www
                    ->initConstantPath('_WEBTMPDIR', 'tmp_dir')
                    ->addPath('tmp_path', $this->getPath('root_path').$this->getPath('tmp_dir'), true, true)
                    ->initConstantPath('_ASSETSDIR', 'assets_dir')
                    ->addPath('assets_path', $this->getPath('web_path').$this->getPath('assets_dir'))
                    ->initConstantPath('_SKINSDIR', 'skins_dir')
                    ->addPath('skins_path', $this->getPath('web_path').$this->getPath('skins_dir'));
            }
        } catch (ErrorException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        // manifest file
        if (file_exists($_f = $this->getPath('root_path').self::CARTE_BLANCHE_MANIFEST)) {
            $manifest = @json_decode(@file_get_contents($_f), true);
            if (!empty($manifest) && isset($manifest['name']) && $manifest['name']===self::$CARTE_BLANCHE_PACKAGE) {
                self::$CARTE_BLANCHE_VERSION = $manifest['version'];
                self::$CARTE_BLANCHE_HOMEPAGE = $manifest['homepage'];
        		$config->set($manifest, false, 'manifest');
            }
        }

		// default config
		    // internal
		$app_cfgfile = Locator::locateConfig(self::CARTE_BLANCHE_CONFIG_FILE, true);
		if (!file_exists($app_cfgfile)) {
			throw new ErrorException( 
			    sprintf('Application configuration file not found in "%s" [%s]!', $this->getPath('config_dir'), $app_cfgfile)
			);
		}
		$config->load($app_cfgfile, true, 'app');
		    // user
		$app_usr_cfgfile = Locator::locateConfig(self::CARTE_BLANCHE_CONFIG_FILE);
		if (file_exists($app_usr_cfgfile)) {
    		$config->load($app_usr_cfgfile, true, 'app');
		}
	
	    // user config file
		if (!empty($config_files)) {
			if (!is_array($config_files)) $config_files = array($config_files);
			foreach($config_files as $cfgf) {
        		$user_cfgfile = \CarteBlanche\App\Locator::locateConfig($cfgf);
				if (!file_exists($user_cfgfile)) {
        			throw new ErrorException( 
		        	    sprintf('Defined configuration file not found in "%s" [%s]!', $this->getPath('config_dir'), $cfgf)
			        );
				}
        		$config->load($user_cfgfile, true, 'config');
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
	}

	/**
	 * Boot: execute the app bootstrap and creates necessary dirs
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
        foreach ($internal_deps as $dep) {
            $this->getContainer()->load($dep);
        }

        // load the app bootstrap
		if (false===$this->is_booted) {
			if ($_f = \CarteBlanche\App\Locator::locateData('bootstrap.php')) {
				include_once $_f;
				$cont = new \App\Bootstrap\ContainerBootstrap($this);
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
        if ($this->getDebug()) {
            return \DevDebug\Debugger::shutdown(true, $callback);
        }
    }

	/**
	 * Defines the request to treat
	 */
	public function handles(Request $request)
	{
	    if (!$this->is_booted) {
    		$this->boot();
    	}
	    $this->getContainer()->set('request', $request);
	    $this->getContainer()->get('router')->setUrl($request->buildUrl());
	    return $this;
	}

	/**
	 * Distributes the application actions, controllers and views
	 *
	 * If no request is defined yet, this will handle current HTTP request if so.
	 *
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
	    return $this->getContainer()->get('front_controller')->distribute();
	}

// ------------------------
// Setters / Getters
// ------------------------

	/**
	 * Define the current kernel mode
	 *
	 * @param string $mode
	 *
	 * @return self
	 */
    public function setMode($mode)
    {
        $this->mode = strtoupper($mode);
        switch ($this->mode) {
            case 'DEV':
                $this->setDebug(true);
                break;
            case 'PROD': default:
                $this->setDebug(false);
                break;
        }
        return $this;
    }

	/**
	 * Get the current kernel mode
	 *
	 * @return string
	 */
    public function getMode()
    {
        return $this->mode;
    }

	/**
	 * Define the current kernel debug mode
	 *
	 * @param string $debug
	 *
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
	 *
	 * @return self
	 */
    public function addBootError($string)
    {
        $this->boot_errors[] = $string;
        return $this;
    }

	/**
	 * Test if current kernel has botting errors
	 *
	 * @return bool
	 */
    public function hasBootErrors()
    {
        return !empty($this->boot_errors);
    }

	/**
	 * Get the botting errors stack
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
	 * @param string $cst A constant name
	 * @param string $path_ref The path reference
	 * @param bool $must_exists Check if concerned path exists
	 * @param bool $must_be_writable Check if concerned path is writable
	 *
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
	 * @param string $name The path reference
	 * @param string $value The path value
	 * @param bool $must_exists Check if concerned path exists
	 * @param bool $must_be_writable Check if concerned path is writable
	 *
	 * @return self
	 *
	 * @throws RuntimeException if the path does not exists and `$must_exists` is true
	 * @throws ErrorException if the concerned path is not writable while it was required
	 */
    public function addPath($name, $value, $must_exists = false, $must_be_writable = false)
    {
	    $config = $this->getContainer()->get('config');
        if ($must_exists) {
            $realpath = realpath($value);
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
                sprintf('Directory "%s" must be writable!', $value)
            );
/*
            throw new RuntimeException(
                sprintf('Directory "%s" must be writable!', $value)
            );
*/
        }
        $config->getRegistry()->loadStack('paths');
        $config->getRegistry()->setEntry($name, $value);
        $config->getRegistry()->saveStack('paths', true);
        return $this;
    }

	/**
	 * Get a path value
	 *
	 * @param string $name The path reference
	 *
	 * @return string|null
	 */
    public function getPath($name)
    {
	    $config = $this->getContainer()->get('config');
        $path = $config->getRegistry()->getStackEntry($name, null, 'paths');
        if (file_exists($path) && is_dir($path)) {
            $path = DirectoryHelper::slashDirname($path);
        }
        return $path;
    }

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
     * Load a new bundle namespace and map it to its path
     *
     * @param string $space
     * @param string $dir
     *
     * @return bool
     */
    public function initBundle($space, $dir)
    {
        $bundle = new \CarteBlanche\App\Bundle($space, $dir);
        return $this->getContainer()->setBundle($space, $bundle);
    }

}

// Endfile