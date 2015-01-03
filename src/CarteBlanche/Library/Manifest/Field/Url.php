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

class Url extends AbstractField
{

    public static $default_types = array(
      'http', 'https', 'irc'  
    );

    public function validate($entry, $default = null)
    {
        return (self::isUrl($entry) ? $entry : $default);
    }

    public static function isUrl($string)
    {
        if (!is_string($string)) return false;
        foreach(self::$default_types as $type) {
            if (substr($string, 0, strlen($type.'://'))===$type.'://') {
                return true;
            }
        }
        return false;
    }
    
}

// Endfile