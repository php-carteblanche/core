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

use Lib\Manifest\ManifestParser;

class Source extends AbstractField
{

    public static $defaults = array(
        'url'      => array(
            'type'      => 'string',
            'field'     => 'url'
        ),
        'type'     => array(
            'type'      => 'string',
            'field'     => 'sourceType'
        ),
        'name'  => array(
            'type'      => 'string',
        ),
    );

    public function validate($entry, $default = null)
    {
        if (!is_array($entry) && is_string($entry)) {
            $entry = array('name'=>$entry);
        }
        if (!is_array($default) && is_string($default)) {
            $default = array('name'=>$default);
        }

        $source = array();
        $full_entry = array_merge((array) $default, (array) $entry);
        foreach(self::$defaults as $name=>$value) {
            $parser = new ManifestParser(isset($full_entry[$name]) ? $full_entry[$name] : (isset($value['default']) ? $value['default'] : null), $value);
            $source[$name] = $parser->parse();
        }
        array_filter($source);
        return $source;
    }

}

// Endfile