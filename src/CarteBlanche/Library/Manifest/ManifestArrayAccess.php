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

namespace Lib\Manifest;

use \ArrayAccess;

class ManifestArrayAccess implements ArrayAccess
{

    public function offsetExists($offset)
    {
        if (method_exists($this, 'get'.ucfirst($offset))) {
            return true;
        }
        return isset($this->{$offset});
    }
    
    public function offsetGet($offset)
    {
        $getter = 'get'.ucfirst($offset);
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }
        return isset($this->{$offset}) ? $this->{$offset} : null;
    }
    
    public function offsetSet($offset, $value)
    {
    }
    
    public function offsetUnset($offset)
    {
    }

}

// Endfile