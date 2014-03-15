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

use \Patterns\Abstracts\AbstractSingleton;
use \Patterns\Commons\Registry;
use \Library\Helper\Code as CodeHelper;
use \Library\Helper\Text as TextHelper;

use \CarteBlanche\App\Kernel;
use \CarteBlanche\App\Bundle;
use \CarteBlanche\Interfaces\ContainerInterface;

use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerInterface;

/**
 * The global container singleton class
 *
 * The Container is callable from any script as long as the Kernel had been created.
 * It contains and manages every dependencies objects.
 *
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
final class Container
    extends AbstractSingleton
    implements ContainerInterface, LoggerAwareInterface
{

	/**
	 * The instances registry
	 *
	 * @var Patterns\Commons\Registry
	 */
	protected $instances;

	/**
	 * Constructor : defines the current URL and gets the routes
	 */
	protected function __construct()
	{
		$this->instances = new Registry;
	}

	/**
	 * Global getter
	 *
	 * @param string $name The variable name to get
	 *
	 * @return object The value of the registry variable
	 */
	public function get($name)
	{
		return true===$this->instances->isEntry($name) ? $this->instances->getEntry($name) : null;
	}

	/**
	 * Global setter
	 *
	 * @param string $name The variable name to set
	 * @param object $object The object to set
	 * @param bool $force_overload Over-write a pre-existant value ?
	 *
	 * @return bool
	 */
	public function set($name, $object, $force_overload = false)
	{
		if (!is_object($object)) {
			throw new \DomainException(
				sprintf('A container entry must be an object (got "%s" for "%s")!', gettype($object), $name)
			);
		}
		if (true===$this->instances->isEntry($name) && true!==$force_overload) {
			throw new \InvalidArgumentException(
				sprintf('You can not over-write a container entry (for "%s")!', $name)
			);
		}
		return (Boolean) $this->instances->setEntry($name, $object);
	}

	/**
	 * Instance loader and setter
	 *
	 * @param string $name The variable name to set
	 * @param array $params An array of parameters to pass to loader
	 * @param object $object_loader The loader class if so
	 * @param bool $force_overload Over-write a pre-existant value ?
	 *
	 * @return bool
	 *
	 * @todo Pass arguments (via parameter here and configuration settings)
	 */
	public function load($name, array $params = array(), $object_loader = null, $force_overload = false)
	{
        $config = $this->get('config')->get($name, \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, array());
        $factory = \Library\Factory::create()
            ->factoryName(__CLASS__)
            ->mustImplement(Kernel::DEPENDENCY_LOADER_INTERFACE)
            ->defaultNamespace(array(
                Kernel::DEPENDENCY_LOADER_DEFAULT_NAMESPACE,
                Kernel::CARTE_BLANCHE_NAMESPACE.Kernel::DEPENDENCY_LOADER_DEFAULT_NAMESPACE
            ))
            ->classNameMask(array('%s', '%s'.Kernel::DEPENDENCY_LOADER_SUFFIX))
            ->callMethod('load')
            ;
        if (!isset($params['config'])) {
            $params['config'] = $config;
        }
        if (!isset($params['options'])) {
            $params['options'] = $config;
        }
        if (!isset($params['container'])) {
            $params['container'] = $this;
        }
        $object = $factory->build(empty($object_loader) ? $name : $object_loader, $params);

/*
	    if (empty($object_loader)) {
	        $object_loader = Kernel::DEPENDENCY_LOADER_DEFAULT_NAMESPACE
	            .ucfirst(TextHelper::toCamelCase($name));
            if (!class_exists($object_loader)) {
    	        $object_loader .= Kernel::DEPENDENCY_LOADER_SUFFIX;
            }
	    }
        if (!class_exists($object_loader)) {
            throw new \RuntimeException(
                sprintf('Unknown dependency loader for reference "%s"!', $name)
            );
        } elseif (!CodeHelper::impelementsInterface($object_loader, Kernel::DEPENDENCY_LOADER_INTERFACE)) {
            throw new \DomainException(
                sprintf('Dependency loader "%s" doesn\'t implement the "%s" interface!',
                    $object_loader, Kernel::DEPENDENCY_LOADER_INTERFACE)
            );
        }
        $loader = new $object_loader;
        $config = $this->get('config')->get($name, \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, array());
        $object = $loader->load($config, $this);
*/
		return $this->set($name, $object, $force_overload);
	}

	/**
	 * Clear a container entry
	 *
	 * @param string $name The variable name to set
	 *
	 * @return bool
	 */
	public function clear($name)
	{
		if (true===$this->instances->isEntry($name)) {
			$this->instances->setEntry($name, null);
		}
	}

	/**
	 * Global bundle getter
	 *
	 * @param string $var The bundle name to get
	 * @return misc The value of the registry bundle
	 * @see Patterns\Commons\Registry::getEntry()
	 */
	public function getBundle($name)
	{
		if (true===$this->instances->isEntry($name,'bundles')) {
			return $this->instances->getEntry($name,'bundles');
		}
		return null;
	}

	/**
	 * Global bundle setter
	 *
	 * @param string $name The bundle name to set
	 * @param misc $val The bundle value to set
	 * @param bool $force_overload Over-write a pre-existant value ?
	 * @see Patterns\Commons\Registry::setEntry()
	 */
	public function setBundle($name = null, $val = null, $force_overload = false)
	{
		if (!is_object($val)) {
			throw new \DomainException(
				sprintf('A bundle entry must be an object (got "%s" for bundle "%s")!', gettype($val), $name)
			);
		}
		if (!CodeHelper::isClassInstance($val, Kernel::BUNDLE_CLASS)) {
			throw new \DomainException(
				sprintf('A bundle entry must be a "%s" instance (for "%s")!', Kernel::BUNDLE_CLASS, $name)
			);
		}
		if (true===$this->instances->isEntry($name, 'bundles') && true!==$force_overload) {
			throw new \InvalidArgumentException(
				sprintf('You can not over-write a container bundle (for "%s")!', $name)
			);
		}
		return $this->instances->setEntry($name,$val,'bundles');
	}

	/**
	 * Clear a container bundle entry
	 *
	 * @param string $name The bundle name to unset
	 */
	public function clearBundle($name)
	{
		if (true===$this->instances->isEntry($name,'bundles')) {
			$this->instances->setEntry($name,null,'bundles');
		}
	}

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
		$this->instances->setEntry('logger',$logger);
    }

}

// Endfile