<?php

namespace Lib\Manifest\Field;

class Url extends AbstractField
{

    public static $default_types = array(
      'http', 'https', 'irc'  
    );

    public function validate($entry, $default = null)
    {
        return (self::isUrl($entry) ? $entry : $default);
    }

    public static function isUrl($string)
    {
        if (!is_string($string)) return false;
        foreach(self::$default_types as $type) {
            if (substr($string, 0, strlen($type.'://'))===$type.'://') {
                return true;
            }
        }
        return false;
    }
    
}

// Endfile