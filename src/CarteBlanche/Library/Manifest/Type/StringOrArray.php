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

namespace Lib\Manifest\Type;

use Lib\Manifest\Field\AbstractField;

class StringOrArray extends AbstractType
{
    
    public function parse(AbstractField $objField = null)
    {
        $val = $this->getValue();
        if (!is_null($objField)) {
            if (!is_array($val)) $val = array($val);
            foreach($val as $k=>$v) {
                $val[$k] = $objField->validate($v, $this->getDefault());
            }
        }
        if (is_array($val) && count($val)===1) {
            $val = array_shift($val);
        }
        return $val;
    }

}

// Endfile