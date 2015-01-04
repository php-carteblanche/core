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
use \CarteBlanche\Interfaces\BundleInterface;
use \CarteBlanche\Exception\ErrorException;
use \CarteBlanche\App\Locator;
use \Patterns\Abstracts\AbstractOptionable;

abstract class AbstractBundle
    extends AbstractOptionable
    implements BundleInterface
{

    /**
     * @var string Bundle's language file name
     */
    protected static $bundle_language_file = 'dirindexer';

    /**
     * @var string Bundle's configuration file name
     */
    protected static $bundle_config_file = 'dirindexer_config.ini';

    /**
     * @var string The bundle's name
     */
    protected $_namespace;

    /**
     * @var string The bundle's data (extracted from assets JSON)
     */
    protected $_data;

    /**
     * @param   array $options
     * @return  mixed
     * @throws  \CarteBlanche\Exception\ErrorException
     */
    public function init(array $options = array())
    {
        $_name = $this->getName();
        $_shortname = strtolower($_name);
        $bundle_defaults = CarteBlanche::getConfig('bundle_defaults');

        // store package's data
        $this->setData($options);

        // load the configuration file if so
        $config_files = $this->getData('config_files');
        if (!empty($config_files)) {
            foreach ($config_files as $cfgf) {
                $cfgfile = Locator::locateConfig($cfgf);
                if (!file_exists($cfgfile)) {
                    throw new ErrorException(
                        sprintf('Configuration file "%s" for bundle "%s" can not be found!', $cfgf, $_name)
                    );
                }
                CarteBlanche::getContainer()->get('config')
                    ->load($cfgfile, true, $_shortname);
            }

            $this->setOptions(
                CarteBlanche::getContainer()->get('config')->get($_shortname)
            );
        }

        // load the language file if so
        $ln_files = $this->getData('language_files');
        if (!empty(self::$bundle_language_file)) {
            $i18nfile = Locator::locateLanguage(self::$bundle_language_file);
            if (!file_exists($i18nfile)) {
                throw new ErrorException(
                    sprintf('Language file for bundle "%s" can not be found!', $_name)
                );
            }
            CarteBlanche::getContainer()->get('i18n')
                ->loadFile($i18nfile);
        }
    }

    /**
     * This may return the bundle name
     * @return string
     */
    public function getName()
    {
        return $this->_namespace;
    }

    /**
     * Define the bundle name if it was empty
     * @param $_namespace
     * @return $this
     */
    public function setName($_namespace)
    {
        if (empty($this->_namespace)) {
            $this->_namespace = $_namespace;
        }
        return $this;
    }

    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    public function getData($name = null)
    {
        if (!is_null($name)) {
            return (isset($this->_data[$name]) ? $this->_data[$name] : null);
        } else {
            return $this->_data;
        }
    }

}

// Endfile