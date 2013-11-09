<?php

namespace Lib\Manifest\Vendor;

use \Lib\Manifest\AbstractPackageDependent;
use \Lib\Manifest\ManifestGeneratorInterface;

class Composer extends AbstractPackageDependent implements ManifestGeneratorInterface
{

    public static $defaults = array(
/*
        'name'             => array(
            'type'      => 'string',
            'default'   => '@PACKAGE_NAME@'
        ),
        'title'            => array(
            'type'      => 'string',
            'default'   => '@PACKAGE_TITLE@'
        ),
        'version'          => array(
            'type'      => 'string',
            'field'     => 'semVer',
            'default'   => '@X.Y.Z@'
        ),
        'state'            => array(
            'type'      => 'string',
            'field'     => 'state',
            'default'   => 'stable'
        ),
        'description'      => array(
            'type'      => 'string',
            'default'   => '@PACKAGE_DESCRIPTION@'
        ),
        'slogan'           => array(
            'type'      => 'string'
        ),
        'keywords'         => array(
            'type'      => 'array'
        ),
        'web'              => array(
            'type'      => 'array',
            'field'     => 'emailOrUrl'
        ),
        'sources'          => array(
            'type'      => 'array',
            'field'     => 'source',
            'default'   => array(
                'url' => 'https://github.com/%s',
                'type' => 'git',
                'name' => 'GitHub.com'
            )
        ),
        'authors'          => array( 
            'type'      => 'array',
            'field'     => 'person',
            'default'   => array(
                'name'=>'@AUTHOR@'
            )
        ),
        'contributors'     => array( 
            'type'      => 'array',
            'field'     => 'person'
        ),
        'maintainers'      => array( 
            'type'      => 'array',
            'field'     => 'person'
        ),
        'licenses'         => array( 
            'type'      => 'array',
            'field'     => 'person',
            'default'   => array(
                'type'=>'@LICENSE@'
            )
        ),
        'dependencies'     => array(
            'type'      => 'array'
        ),
        'compatibilities'  => array(
            'type'      => 'array'
        ),
        'incompatibilities'=> array(
            'type'      => 'array'
        ),
        'time'             => array(
            'type'      => 'datetime'
        ),
        'type'            => array(
            'type'      => 'string',
            'default'   => 'library'
        ),
*/
        'homepage'         => '@web:homepage@',
        'support'          => array(),
        'license'          => '@LICENSE@',
        'authors'          => array( array('name'=>'@AUTHOR@') ),
        'require'          => array(),
        'require-dev'      => array(),
        'conflict'         => array(),
        'replace'          => array(),
        'provide'          => array(),
        'suggest'          => array(),
        'autoload'         => array( 'psr-0'=>array(), 'classmap'=>array(), 'files'=>array() ),
        'include_path'     => array(),
        'target-dir'       => '',
        'minimum-stability'=> 'stable',
        'repositories'     => array(),
        'config'           => array(),
        'scripts'          => array(),
        'extra'            => array(),
        'bin'              => array(),
    );

    public static $support_types = array(
        'email', 'issues', 'forum', 'wiki', 'irc', 'source'
    );

    public static $stability_types = array(
        'dev', 'alpha', 'beta', 'RC', 'stable'
    );

    public static $repository_types = array(
        'composer', 'vcs', 'pear', 'package'
    );

    public static $config_types = array(
        'process-timeout', 'use-include-path', 'preferred-install', 'github-protocols',
        'github-oauth', 'vendor-dir', 'bin-dir', 'cache-dir', 'cache-files-dir', 'cache-repo-dir', 
        'cache-vcs-dir', 'cache-files-ttl', 'cache-files-maxsize', 'notify-on-install', 'discard-changes'
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