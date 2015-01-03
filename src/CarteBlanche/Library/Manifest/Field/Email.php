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