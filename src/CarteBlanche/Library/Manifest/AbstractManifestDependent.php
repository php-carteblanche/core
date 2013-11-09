<?php

namespace Lib\Manifest;

use \ArrayAccess;
use \Lib\Manifest\ManifestArrayAccess;
use \Lib\Manifest\Manifest;

abstract class AbstractManifestDependent extends ManifestArrayAccess implements ArrayAccess
{

    /**
     * The class default settings
     */
    public static $defaults = array();

    /**
     * The manifest file object received by the contsructor
     */
    protected $manifest;

    /**
     * Constructor receiving a manifest file entity
     */
    public function __construct(Manifest $file)
    {
        $this->manifest = $file;
        $this->init()->parse($this->manifest->getEntries());
    }

    /**
     * Initialize the object with default values and first settings
     *
     * @return self `$this` for method chaining
     */
    protected function init()
    {
        $clsname = get_called_class();
        if (property_exists($clsname, 'defaults')) {
            foreach($clsname::$defaults as $name=>$value) {
                $this->{$name} = $value;
            }
        }
        return $this;
    }

    /**
     * Parse the values with the confguration file data
     */
    abstract public function parse(array $data = array());

}

// Endfile