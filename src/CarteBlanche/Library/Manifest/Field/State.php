<?php

namespace Lib\Manifest\Field;

class State extends AbstractField
{

    public static $default_states = array(
        'dev', 'alpha', 'beta', 'RC', 'stable'
    );

    public function validate($entry, $default = null)
    {
        return ($this->isState($entry) ? $entry : $default);
    }

    public static function isState($string)
    {
        if (!is_string($string)) return false;
        return in_array($string, self::$default_states);
    }
    
}

// Endfile