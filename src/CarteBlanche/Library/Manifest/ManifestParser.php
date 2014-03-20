<?php

namespace Lib\Manifest;

use \Exception;
use Lib\Manifest\Type\AbstractType;
use Lib\Manifest\Field\AbstractField;

class ManifestParser
{

    protected $config;
    protected $entry;

    public function __construct($entry, array $config)
    {
        $this->setEntry($entry)->setConfig($config);
    }

    public function setEntry($entry)
    {
        $this->entry = $entry;
        return $this;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function getConfig($name = null, $default = null)
    {
        if (!is_null($name)) {
            return (isset($this->config[$name]) ? $this->config[$name] : $default);
        }
        return $this->config;
    }

    public function parse($entry = null, array $config = array())
    {
        if (!empty($entry)) $this->setEntry($entry);
        if (!empty($config)) $this->setEntry($config);

        // the entry type
        $_type = $this->getConfig('type');
        if (empty($_type)) {
            if (is_array($this->entry)) $_type = 'array';
            elseif (is_string($this->entry)) $_type = 'string';
        }
        $clsType = 'Lib\\Manifest\\Type\\'.ucfirst($_type);
        if (class_exists($clsType)) {
            $objType = new $clsType;
            if (!($objType instanceof AbstractType)) {
                throw new Exception(
                    sprintf('Manifest type class "%s" must extend "Lib\Manifest\Type\AbstractType"!', $clsType)
                );
            }
        } else {
            throw new Exception(
                sprintf('Unknown manifest field type "%s"!', $_type)
            );
        }
        
        // the entry field        
        $_field = $this->getConfig('field');
        if (!empty($_field)) {
            $clsField = 'Lib\\Manifest\\Field\\'.ucfirst($_field);
            if (class_exists($clsField)) {
                $objField = new $clsField;
                if (!($objField instanceof AbstractField)) {
                    throw new Exception(
                        sprintf('Manifest field class "%s" must extend "Lib\Manifest\Field\AbstractField"!', $clsField)
                    );
                }
            } else {
                throw new Exception(
                    sprintf('Unknown manifest field "%s"!', $_field)
                );
            }
        }

        // the default value
        $_default = $this->getConfig('default');
        if (!empty($_default)) {
            $objType->setDefault($_default);
        }

        // the value
        $objType->setValue($this->getEntry());

        return $objType->parse(isset($objField) ? $objField : null);
    }

}

// Endfile