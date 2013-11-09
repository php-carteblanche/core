<?php

namespace Lib\Manifest\Field;

use Lib\Manifest\ManifestParser;

class Person extends AbstractField
{

    public static $defaults = array(
        'name'      => array(
            'type'      => 'string'
        ),
        'email'     => array(
            'type'      => 'string',
            'field'     => 'email'
        ),
        'homepage'  => array(
            'type'      => 'string',
            'field'     => 'url'
        ),
        'role'      => array(
            'type'      => 'string',
            'default'   => 'Developer'
        ),
    );

    public function validate($entry, $default = null)
    {
        if (!is_array($entry) && is_string($entry)) {
            $entry = array('name'=>$entry);
        }
        if (!is_array($default) && is_string($default)) {
            $default = array('name'=>$default);
        }

        $person = array();
        $full_entry = array_merge((array) $default, (array) $entry);
        foreach(self::$defaults as $name=>$value) {
            $parser = new ManifestParser(isset($full_entry[$name]) ? $full_entry[$name] : (isset($value['default']) ? $value['default'] : null), $value);
            $person[$name] = $parser->parse();
        }
        array_filter($person);
        return $person;
    }
    
}

// Endfile