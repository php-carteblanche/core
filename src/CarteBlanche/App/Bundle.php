<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\App;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Loader;
use \CarteBlanche\App\Locator;

/**
 * The global bundle object
 *
 * @author      Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Bundle
{

	protected $shortname;
	protected $namespace;
	protected $directory;
	protected $instance;
	
	/**
	 * Construction of a bundle
	 *
	 * @param string $namespace The bundle's namespace
	 * @param string $directory The bundle's directory in `$bundles_dir`
	 *
	 * @throws ErrorException if the `$bundles_dir` doesn't exist
	 * @throws ErrorException if the current loaded bundle directory doesn't exist
	 */
	public function __construct($namespace, $directory)
	{
	    $bundles_dir = CarteBlanche::getPath('bundles_dir');
		if (!Locator::locate($bundles_dir)) {
			throw new ErrorException("Bundles directory does not exist!");
		}
		if ($d = Locator::locate($bundles_dir.$directory)) {
			$this->directory = $d;
		} else {
			throw new ErrorException(sprintf("Bundle directory '%s' does not exist!", $directory));
		}
		$this->namespace = $namespace;
		$this->shortname = substr($directory, 0, strpos($directory, '/'));
		$this->init();
	}

    /**
     * Initialize a bundle : register its namespace and load its global `[bundle name]Bundle` file
     */
    protected function init()
    {
        $_bundle_fname = $this->getNamespace().'Bundle';
        $_bundle_f = rtrim($this->getDirectory(), '/').'/'.$_bundle_fname;
        if (@file_exists($_bundle_f.'.php')) {
            $cls_name = '\\'.$this->getNamespace().'\\'.$_bundle_fname;
            $this->instance = new $cls_name;
        } else {
            $_bundle_fname = $this->getNamespace().'Bundle';
            $_bundle_f = rtrim($this->getDirectory(), '/') . '/' 
                . rtrim($this->getNamespace(), '/') . '/' 
                . $_bundle_fname;
            if (@file_exists($_bundle_f.'.php')) {
                $cls_name = '\\'.$this->getNamespace().'\\'.$_bundle_fname;
                $this->instance = new $cls_name;
            }
        }
    }

	/**
	 * Get bundle's directory
	 */
	public function getDirectory()
	{
		return $this->directory;
	}

	/**
	 * Get bundle's namespace
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * Get bundle's short name
	 */
	public function getShortname()
	{
		return $this->shortname;
	}

	/**
	 * Get bundle's loader instance
	 */
	public function getInstance()
	{
		return $this->instance;
	}

}

// Endfile