<?php

namespace Lib\Manifest\Field;

class SourceType extends AbstractField
{

    public static $defaults = array(
        'composer', 'vcs', 'pear', 'package'
    );

    public function validate($entry, $default = null)
    {
        return (self::isSourceType($entry) ? $entry : $default);
    }

    public static function isSourceType($string)
    {
        if (!is_string($string)) return false;
    	return in_array($string, self::$defaults);
    }
    
}

// Endfile