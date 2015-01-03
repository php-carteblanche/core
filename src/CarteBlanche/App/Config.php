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

use \CarteBlanche\App\Kernel;
use \CarteBlanche\Interfaces\ConfigInterface;
use \Patterns\Commons\Registry;
use \Library\Helper\Code as CodeHelper;
use \CarteBlanche\Exception\ErrorException;
use \CarteBlanche\Exception\InvalidArgumentException;

/**
 * This is the configuration manager of CarteBlanche
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Config
    implements ConfigInterface
{

    /**
     * Constant to use to not throw error if a configuration is not found
     */
    const NOT_FOUND_GRACEFULLY = 1;

    /**
     * Constant to use to throw an error if a configuration is not found
     */
    const NOT_FOUND_ERROR = 2;

    /**
     * @var \Patterns\Commons\Registry The singleton instance of the registry (MAIN REGISTRY OBJECT)
     */
    protected $registry;

    /**
     * @var array
     */
    protected $files_loaded;

    /**
     * @var int The current global configuration stack ID
     */
    private static $global_config_id = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setRegistry(new Registry);
        self::$global_config_id = time();
        $this->getRegistry()->saveStack(self::$global_config_id);
    }

    /**
     * Load and parse a configuration file
     *
     * @param   string      $filename
     * @param   bool        $merge_globals
     * @param   null|string $stack_name
     * @param   null|string $handler    A classname to parse concerned config content
     * @return  self
     * @throws  \CarteBlanche\Exception\ErrorException if the file is not found
     */
    public function load($filename, $merge_globals = true, $stack_name = null, $handler = null)
    {
        $file = new \SplFileInfo($filename);
        if ($file->isFile()) {
            if (empty($handler)) {
                $handler = $file->getExtension();
            }
            $factory = \Library\Factory::create()
                ->factoryName(__CLASS__)
                ->mustImplement(Kernel::CONFIG_FILETYPE_INTERFACE)
                ->defaultNamespace(Kernel::CONFIG_FILETYPE_DEFAULT_NAMESPACE)
                ->classNameMask(array('%s', '%s'.Kernel::CONFIG_FILETYPE_SUFFIX))
                ;
            $config = $factory->build($handler);
            $values = $config->parse($file);
            if (!empty($values)) {
                $this->set($values, $merge_globals, $stack_name);
                $this->_registerConfigFile($filename, count($values));
            } else {
                $this->_registerConfigFile($filename, 'empty content');
            }
        } else {
            throw new ErrorException(
                sprintf('Configuration file "%s" not found!', $filename)
            );
        }
        return $this;
    }

    /**
     * Parse and set a configuration array
     *
     * @param   array       $config
     * @param   bool        $merge_globals
     * @param   null/string $stack_name
     * @return  self/array  Returns the parsed array if `$merge_globals` is false, the config object otherwise
     */
    public function set(array $config, $merge_globals = true, $stack_name = null)
    {
        if (!empty($config)) {
            $config = $this->_buildConfigStack($config);
            if (!empty($stack_name)) {
                $this->getRegistry()->loadStack($stack_name);
            }
            foreach ($config as $index=>$val) {
                $this->getRegistry()->setEntry($index, $val);
            }
            if (!empty($stack_name)) {
                $this->getRegistry()->saveStack($stack_name);
            }
        }

        if (!$merge_globals) {
            return $config;
        } else {
            $this->getRegistry()->loadStack(self::$global_config_id);
            $globals = array_replace_recursive(
                $this->getRegistry()->dump(), $config
            );
            foreach ($globals as $index=>$val) {
                $this->getRegistry()->setEntry($index, $val);
            }            
            $this->getRegistry()->saveStack(self::$global_config_id);
            return $this;
        }
    }

    /**
     * Get a configuration stack or entry
     *
     * @param   string      $index
     * @param   int         $flag
     * @param   null/mixed   $default
     * @param   string      $stack_name
     * @return  mixed
     * @throws  \CarteBlanche\Exception\InvalidArgumentException if the index doesn't exist and `$flag` is NOT_FOUND_ERROR
     */
    public function get($index, $flag = self::NOT_FOUND_GRACEFULLY, $default = null, $stack_name = 'global')
    {
        $value = null;
        if ($stack_name==='global') {
            $config = $this->getRegistry()->dumpStack(self::$global_config_id);
        } else {
            if ($this->getRegistry()->isStack($stack_name)) {
                $config = $this->getRegistry()->dumpStack($stack_name);
            } else {
                if ($flag & self::NOT_FOUND_ERROR) {
                    throw new InvalidArgumentException(
                        sprintf('Unknown configuration stack "%s"!', $stack_name)
                    );
                } else {
                    return $default;
                }
            }
        }

        if (strpos($index, self::$depth_separator_char)!==false) {
            $depth_index = explode(self::$depth_separator_char, $index);
            $tmp_conf =& $config;
            foreach ($depth_index as $count=>$_i) {
                if (array_key_exists($_i, $tmp_conf)) {
                    $tmp_conf =& $tmp_conf[$_i];
                } else {
                    if ($count===0) {
                        $stack_name = array_shift($depth_index);
                        return $this->get(
                            implode(self::$depth_separator_char, $depth_index),
                            $flag, $default, $stack_name
                        );
                    }
                    $tmp_conf = array();
                }
            }
            if (!empty($tmp_conf)) {
                $value = $this->_parseConfig($tmp_conf, $stack_name);
            }
        } else {
            if (array_key_exists($index, $config)) {
                $value = $this->_parseConfig($config[$index], $stack_name);
            }
        }

        if (!$value) {
            if ($flag & self::NOT_FOUND_GRACEFULLY) {
                return $default;
            } else {
                throw new InvalidArgumentException(
                    sprintf('Unknown configuration entry "%s"!', $index)
                );
            }
        } else {
            return $value;
        }
    }

    /**
     * Get the full configuration array
     *
     * @return array
     */
    public function dump()
    {
        return $this->getRegistry()->dumpStack(self::$global_config_id);
    }

// ---------------------------------
// Registry Getter/Setter
// ---------------------------------

    /**
     * Set the configuration object registry
     *
     * @param   \Patterns\Commons\Registry $registry
     * @return  self
     */
    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
        return $this;
    }

    /**
     * Get the configuration object registry
     *
     * @return  \Patterns\Commons\Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Add a configuration file in the files registry
     *
     * @param   string  $file_name
     * @param   int     $length
     * @return  self
     */
    protected function _registerConfigFile($file_name, $length)
    {
        $this->files_loaded[$file_name] = $length;
        return $this;
    }

    /**
     * Get the configuration files loaded
     *
     * @return array
     */
    public function getRegisteredConfigFiles()
    {
        return $this->files_loaded;
    }

// ---------------------------------
// Process
// ---------------------------------

    /**
     * @var string  Indexes depth separator
     */
    public static $depth_separator_char = '.';

    /**
     * @var array   Characters list to replace in a configuration index (dot is not included as it is the separator)
     */
    public static $slugify_stripped_chars = array(
        ' ', '-', '/', '\\', ',', '?', ';', ':', '=', '+', '%', '§', '<', '>', '|',
        '`', '$', '^', '¨', '*', '€', '£', ')', '!', '(', "'", '"', '&', '#', '@'
    );

    /**
     * @var string  Replacement character in a configuration index
     */
    public static $slugify_replacement_char = '_';
    
    /**
     * Rebuild a configuration array
     *
     * This will slugify all indexes and dispatch values following the dotted indexes.
     *
     * @param   array   $config_array
     * @return  array
     */
    protected function _buildConfigStack(array $config_array)
    {
        $config = array();
        foreach ($config_array as $index=>$val) {
            $index = $this->_slugify($index);
            $val = $this->_treatValue($val);

            if (strpos($index, self::$depth_separator_char)!==false) {
                $depth_index = explode(self::$depth_separator_char, $index);
                $tmp_conf =& $config;
                foreach ($depth_index as $count=>$_i) {
                    if (!array_key_exists($_i, $tmp_conf)) {
                        $tmp_conf[$_i] = array();
                    }
                    $tmp_conf =& $tmp_conf[$_i];
                    if ($count===count($depth_index)-1) {
                        $tmp_conf = $val;
                    }
                }
                unset($config[$index]);
            } else {
                if (is_array($val)) {
                    foreach ($val as $val_index=>$val_val) {
                        if (strpos($val_index, self::$depth_separator_char)!==false) {
                            $val = $this->_buildConfigStack($val);
                        }
                    }
                }
                $config[$index] = $val;
            }
        }
        return $config;
    }

    /**
     * Rebuild an index as a slug: lower case with no punctuation
     *
     * @param   string  $index
     * @return  string
     */
    protected function _slugify($index)
    {
        $index = utf8_encode(strtolower($index));
        $index = str_replace(self::$slugify_stripped_chars, self::$slugify_replacement_char, $index);
        $doubled = self::$slugify_replacement_char.self::$slugify_replacement_char;
        do {
            $index = str_replace($doubled, self::$slugify_replacement_char, $index);
        } while (strpos($index, $doubled)!==false);
        return $index;
    }

    /**
     * Process special treatment on configuration values such as BIT values
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _treatValue($value)
    {
        if (is_array($value)) {
            $values = array();
            foreach ($value as $j=>$v) {
                $values[$j] = $this->_treatValue($v);
            }
        }
        if (is_string($value) && (
            in_array(strtolower(trim($value)), array('on', 'off')) ||
            in_array(strtolower(trim($value)), array('true', 'false')) ||
            in_array(trim($value), array('0', '1')) ||
            is_bool($value)
        )) {
            if (in_array(strtolower(trim($value)), array('on', 'true'))) {
                $value = true;
            } elseif (in_array(strtolower(trim($value)), array('off', 'false'))) {
                $value = false;
            }
            return (Boolean) $value;
        }
        return $value;
    }

    /**
     * Parse a configuration stack or entry
     *
     * This function will complete a configuration entry replacing references to other entries written like :
     *    'name' => '%entry%'
     * where "entry" is the name of another defined configuration entry.
     *
     * @param   mixed   $conf
     * @param   string  $stack_name
     * @return  mixed
     */
    protected function _parseConfig($conf, $stack_name = 'global')
    {
        if (is_string($conf)) {
            $this->_parseConfigRecursive($conf, null, $stack_name);
        } elseif (is_array($conf)) {
            array_walk_recursive($conf, array($this, '_parseConfigRecursive'), $stack_name);
            $conf = array_filter($conf);
        }
        return $conf;
    }

    /**
     * Parse a configuration stack array recursively
     *
     * @param   mixed       $value
     * @param   null/string $key
     * @param   string      $stack_name
     * @return  void        As the value is set by reference, nothing is returned but the original array is modified
     */
    protected function _parseConfigRecursive(&$value, $key = null, $stack_name = 'global')
    {
        if (!is_string($value)) return;

        // escape any '\%'
        $hash = 'XH'.uniqid();
        $value = str_replace('\%', $hash, $value);

        // configuration value notation : %name%
        while (is_string($value) && 0!=preg_match('/^(.*)\%(.*)\%(.*)$/i', $value, $matches) && count($matches)>1) {
            $_cf = $matches[2];
            if ($_cfg_val = $this->get($_cf, self::NOT_FOUND_GRACEFULLY, null, $stack_name)) {
                if (is_array($_cfg_val)) {
                    $value = $_cfg_val;
                } else {
                    $value = $matches[1].$_cfg_val.$matches[3];
                }
            }
            $matches = array();
        }

        // un-escape any '\%'
        if (is_string($value)) {
            $value = str_replace($hash, '%', $value);
        }

        // configuration stack notation : {name}
        if (is_string($value) && 0!=preg_match('/^\{(.*)\}$/i', $value, $matches)) {
            $_cf = $matches[1];
            if (substr(trim($_cf), 0, strlen('function'))!=='function') {
                $_cf = 'function(){ '.$_cf.'; }';
            }
            @eval("\$_cfg_closure = $_cf;");
            if ($_cfg_closure && is_callable($_cfg_closure)) {
                $value = call_user_func($_cfg_closure);
            }
        }
    }

}

// Endfile