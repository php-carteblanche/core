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

    /**
     * @var string
     */
    protected $shortname;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var bool|string
     */
    protected $directory;

    /**
     * @var
     */
    protected $instance;

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
            }
        }
    }

    /**
     * Get bundle's directory
     *
     * @return  string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Get bundle's namespace
     *
     * @return  string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Get bundle's short name
     *
     * @return  string
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * Get bundle's loader instance
     *
     * @return  object
     */
    public function getInstance()
    {
        return $this->instance;
    }

}

// Endfile