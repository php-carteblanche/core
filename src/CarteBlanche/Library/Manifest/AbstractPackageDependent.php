<?php

namespace Lib\Manifest;

use \ArrayAccess;
use \Lib\Manifest\ManifestArrayAccess;
use \Lib\Manifest\Package;

abstract class AbstractPackageDependent extends ManifestArrayAccess implements ArrayAccess
{

    /**
     * The class default settings
     */
    public static $defaults = array();

    /**
     * The manifest file object received by the contsructor
     */
    protected $package;

    /**
     * Constructor receiving a manifest file entity
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
        $this->init()->parse();
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
    abstract public function parse();

}

// Endfile