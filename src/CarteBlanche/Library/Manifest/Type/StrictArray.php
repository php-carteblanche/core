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