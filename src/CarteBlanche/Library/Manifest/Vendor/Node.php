<?php

namespace Lib\Manifest\Vendor;

use \Lib\Manifest\AbstractPackageDependent;
use \Lib\Manifest\ManifestGeneratorInterface;

class Node extends AbstractPackageDependent implements ManifestGeneratorInterface
{

    public static $defaults = array(
        'name'             => '@PACKAGE_NAME@',
        'description'      => '@PACKAGE_DESCRIPTION@',
        'keywords'         => array(),
        'version'          => '@X.Y.Z@',
        'license'          => '@LICENSE@',
        'author'           => '@AUTHOR@',
        'contributors'     => array( array('name'=>'@CONTRIBUTOR@') ),
        'bin'              => array(),
        'scripts'          => array(),
        'main'             => '@MAIN@',
        'repository'       => array(),
        'dependencies'     => array(),
        'devDependencies'  => array(),
        'bundledDependencies'=> array(),
        'analyze'          => false,
        'preferGlobal'     => true,
        'engines'          => array(),
    );

    public static $scripts_types = array(
        'start', 'test', 'predeploy', 'postdeploy'
    );

    public function parse()
    {
        foreach(self::$defaults as $name=>$value) {
            if (isset($this->package[$name])) {
                if (is_array($value) && !is_array($this->package[$name])) {
                    $this->{$name} = explode(',', $this->package[$name]);
                } else {
                    $this->{$name} = $this->package[$name];
                }
            }
        }
    }

    public function generate()
    {
        return json_encode( (array) $this );
    }
    
}

// Endfile