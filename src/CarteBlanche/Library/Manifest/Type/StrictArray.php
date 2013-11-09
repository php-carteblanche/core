<?php

namespace Lib\Manifest\Type;

use Lib\Manifest\Field\AbstractField;

class StrictArray extends AbstractType
{

    public function parse(AbstractField $objField = null)
    {
        $val = (array) $this->getValue();
        if (!is_null($objField)) {
            foreach($val as $key=>$value) {
                $val[$key] = $objField->validate($value);
            }
        }
        return (array) $val;
    }

}

// Endfile