<?php

namespace Lib\Manifest\Field;

class Email extends AbstractField
{

    public function validate($entry, $default = null)
    {
        return ($this->isEmail($entry) ? $entry : $default);
    }

    public static function isEmail($email)
    {
        if (!is_string($email)) return false;
    	return preg_match('/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/',$email);
    }
    
}

// Endfile