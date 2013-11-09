<?php

namespace Lib\Manifest\Type;

use Lib\Manifest\Field\AbstractField;

class String extends AbstractType
{

    public function parse(AbstractField $objField = null)
    {
        $val = (string) $this->getValue();
        if (!is_null($objField)) {
            $val = $objField->validate($val, $this->getDefault());
        }
        return (string) $val;
    }

    public static function getSlug($string)
    {
        return str_replace(array(' '), '_', strtolower($string));
    }

}

// Endfile