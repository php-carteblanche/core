<?php

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