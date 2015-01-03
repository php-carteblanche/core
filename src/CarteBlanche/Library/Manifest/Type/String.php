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