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