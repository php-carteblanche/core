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

namespace Lib\Manifest;

use \DateTime;
use \Lib\Manifest\AbstractManifestDependent;
use \Lib\Manifest\ManifestParser;

class Package extends AbstractManifestDependent
{

    public static $defaults = array(
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
            'type'      => 'strictArray'
        ),
        'web'              => array(
            'type'      => 'strictArray',
            'field'     => 'emailOrUrl'
        ),
        'sources'          => array(
            'type'      => 'strictArray',
            'field'     => 'source',
            'default'   => array(
                'url' => 'https://github.com/%s',
                'type' => 'git',
                'name' => 'GitHub.com'
            )
        ),
        'authors'          => array( 
            'type'      => 'strictArray',
            'field'     => 'person',
            'default'   => array(
                'name'=>'@AUTHOR@'
            )
        ),
        'contributors'     => array( 
            'type'      => 'strictArray',
            'field'     => 'person'
        ),
        'maintainers'      => array( 
            'type'      => 'strictArray',
            'field'     => 'person'
        ),
        'licenses'         => array( 
            'type'      => 'stringOrArray',
            'default'   => array(
                'type'=>'@LICENSE@'
            )
        ),
        'dependencies'     => array(
            'type'      => 'strictArray'
        ),
        'dependencies-dev' => array(
            'type'      => 'strictArray'
        ),
        'compatibilities'  => array(
            'type'      => 'strictArray'
        ),
        'compatibilities-dev'=> array(
            'type'      => 'strictArray'
        ),
        'incompatibilities'=> array(
            'type'      => 'strictArray'
        ),
        'incompatibilities-dev'=> array(
            'type'      => 'strictArray'
        ),
        'time'             => array(
            'type'      => 'string',
            'field'     => 'date'
        ),
        'type'            => array(
            'type'      => 'string',
            'default'   => 'library'
        ),
    );

    public function parse(array $data = array())
    {
        $full_data = array_merge(self::$defaults, $data);
        foreach($full_data as $name=>$value) {
            if (array_key_exists($name, self::$defaults)) {
                $parser = new ManifestParser($value, self::$defaults[$name]);
                $this->{$name} = $parser->parse();
            } else {
                $this->{$name} = $value;
            }
        }
/*
        foreach(self::$defaults as $name=>$value) {
            if (isset($data[$name])) {
                if (is_array($value) && !is_array($data[$name])) {
                    $this->{$name} = explode(',', $data[$name]);
                } else {
                    $this->{$name} = $data[$name];
                }
            }
        }

        // title
        if ($this->title===self::$defaults['title'] && $this->name!==self::$defaults['name']) {
            $this->title = $this->getSlug($this->name);
        }

        // slogan
        if ($this->slogan===self::$defaults['slogan'] && $this->description!==self::$defaults['description']) {
            $this->slogan = $this->description;
        }

        // license
        if (isset($data['license'])) {
            if ($this->licenses!==self::$defaults['licenses']) {
                $this->licenses[] = $data['license'];
            } else {
                $this->licenses = array( array( 'type'=>$data['license'] ) );
            }
        }

        // author
        if (isset($data['author'])) {
            if ($this->authors!==self::$defaults['authors']) {
                $this->authors[] = $data['author'];
            } else {
                $this->authors = array( array( $data['author'] ) );
            }
        }

        // dependency
        if (isset($data['dependency'])) {
            if ($this->dependencies!==self::$defaults['dependencies']) {
                $this->dependencies[] = $data['dependency'];
            } else {
                $this->dependencies = array( array( $data['dependency'] ) );
            }
        }

        // compatibility
        if (isset($data['compatibility'])) {
            if ($this->compatibilities!==self::$defaults['compatibilities']) {
                $this->compatibilities[] = $data['compatibility'];
            } else {
                $this->compatibilities = array( array( $data['compatibility'] ) );
            }
        }

        // require
        if (isset($data['require']) && $this->dependencies===self::$defaults['dependencies']) {
            $this->dependencies = $data['require'];
        }

        // suggest
        if (isset($data['suggest']) && $this->compatibilities===self::$defaults['compatibilities']) {
            $this->compatibilities = $data['suggest'];
        }

        // conflict
        if (isset($data['conflict']) && $this->incompatibilities===self::$defaults['incompatibilities']) {
            $this->incompatibilities = $data['conflict'];
        }

        // time
        if (!empty($this->time)) {
            $this->time = new DateTime( $this->time );
        } else {
            $this->time = $this->getDateTimeFromTimestamp( $this->manifest->getMTime() );
        }

        // the web infos
        $infos = array('homepage', 'url', 'docs', 'demo', 'bugs','download');
        foreach($infos as $_info) {
            if (isset($data[$_info])) {
                $this->web[$_info] = $data[$_info];
            }
        }
        
        // the support infos
        if (isset($data['support']) && $this->web===self::$defaults['web']) {
            $this->web = $data['support'];
        }

        // the sources
        if (isset($data['repository'])) {
            if ($this->sources!==self::$defaults['sources']) {
                $this->sources[] = $data['repository'];
            } else {
                $this->sources = $data['repository'];
            }
        }
        
        // the 'download' link
        if (isset($data['download']) && $this->sources!==self::$defaults['sources'] && $this->sources['url']===self::$defaults['sources']['url']) {
            $this->sources['url'] = $data['download'];
        }
        
        // the 'source' link
        if (isset($data['support']) && isset($data['support']['source']) && $this->sources!==self::$defaults['sources'] && $this->sources['url']===self::$defaults['sources']['url']) {
            $this->sources['url'] = $data['support']['source'];
        }
        
        // last
        $this->sources = array_merge(self::$defaults['sources'], (array) $this->sources);
        if ($this->sources['url']===self::$defaults['sources']['url']) {
            $this->sources['url'] = sprintf($this->sources['url'], $this->name);
        }
*/        
    }
    
}

// Endfile