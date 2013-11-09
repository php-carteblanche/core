<?php

namespace Lib\Manifest\Field;

class SemVer extends AbstractField
{

    public function validate($entry, $default = null)
    {
        return (is_string($entry) ? $entry : $default);
    }
    
}

// Endfile