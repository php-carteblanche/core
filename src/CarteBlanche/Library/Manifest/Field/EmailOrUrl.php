<?php

namespace Lib\Manifest\Field;

class EmailOrUrl extends AbstractField
{

    public function validate($entry, $default = null)
    {
        return (Email::isEmail($entry) || Url::isUrl($entry) ? $entry : $default);
    }

}

// Endfile