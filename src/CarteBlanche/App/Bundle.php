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

namespace CarteBlanche\App;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Loader;
use \CarteBlanche\App\Locator;
use \CarteBlanche\Exception\ErrorException;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * The global bundle object
 *
 * @author      Piero Wbmstr <me@e-piwi.fr>
 */
class Bundle
{

    protected $shortname;
    protected $namespace;
    protected $directory;
    protected $instance;
    protected $package_name;
    protected $package_data;

    /**
     * Construction of a bundle
     *
     * @param   string  $namespace  The bundle's namespace
     * @param   string  $directory  The bundle's directory in `$bundles_dir`
     * @throws  \CarteBlanche\Exception\ErrorException if the `$bundles_dir` doesn't exist
     * @throws  \CarteBlanche\Exception\ErrorException if the current loaded bundle directory doesn't exist
     */
    public function __construct($namespace, $directory)
    {
        $bundles_dir = CarteBlanche::getFullPath('bundles_dir');
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
        $this->package_name = 'carte-blanche/bundle-'.strtolower($namespace);
        $assets = CarteBlanche::getContainer()->get('template_engine')
            ->getAssetsLoader()
            ->getAssets();
        if (!empty($assets) && isset($assets[$this->package_name])) {
            $this->package_data = $assets[$this->package_name];
        }

        $this->init();
    }

    /**
     * Initialize a bundle : register its namespace and load its global `[bundle name]Bundle` file
     */
    protected function init()
    {
        $_bundle_fname = $this->getNamespace().'Bundle';
        $_bundle_f = DirectoryHelper::slashDirname($this->getDirectory()).$_bundle_fname;
        if (@file_exists($_bundle_f.'.php')) {
            $cls_name = '\\'.$this->getNamespace().'\\'.$_bundle_fname;
            $this->instance = new $cls_name;
        } else {
            $_bundle_fname = $this->getNamespace().'Bundle';
            $_bundle_f = DirectoryHelper::slashDirname($this->getDirectory())
                . DirectoryHelper::slashDirname($this->getNamespace())
                . $_bundle_fname;
            if (@file_exists($_bundle_f.'.php')) {
                $cls_name = '\\'.$this->getNamespace().'\\'.$_bundle_fname;
                $this->instance = new $cls_name;
                if (method_exists($this->instance, 'setName')) {
//echo "> calling 'setName' on instance of ".get_class($this->instance).PHP_EOL;
                    call_user_func(array($this->instance, 'setName'), $this->getNamespace());
                }
                if (method_exists($this->instance, 'setPackageName')) {
//echo "> calling 'setPackageName' on instance of ".get_class($this->instance).PHP_EOL;
                    call_user_func(array($this->instance, 'setPackageName'), $this->getPackageName());
                }
                if (method_exists($this->instance, 'init')) {
//echo "> calling 'init' on instance of ".get_class($this->instance).PHP_EOL;
                    call_user_func(array($this->instance, 'init'), $this->getPackageData());
                }
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
     * Get bundle's package name
     */
    public function getPackageName()
    {
        return $this->package_name;
    }

    /**
     * Get bundle's package's JSON data
     */
    public function getPackageData()
    {
        return $this->package_data;
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