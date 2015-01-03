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

class SourceType extends AbstractField
{

    public static $defaults = array(
        'composer', 'vcs', 'pear', 'package'
    );

    public function validate($entry, $default = null)
    {
        return (self::isSourceType($entry) ? $entry : $default);
    }

    public static function isSourceType($string)
    {
        if (!is_string($string)) return false;
    	return in_array($string, self::$defaults);
    }
    
}

// Endfile