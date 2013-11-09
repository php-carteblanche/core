<?php

namespace Lib\Manifest\Field;

abstract class AbstractField
{

    abstract public function validate($entry, $default = null);

}

// Endfile