<?php

namespace Lib\Manifest\Type;

use Lib\Manifest\Field\AbstractField;

abstract class AbstractType
{

    protected $default;
    protected $value;

    /**
     * Field default value
     *
     * @param misc $default The default value to set
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Get field default value
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Field entry value
     *
     * @param misc $value The value to set
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * get field entry value
     */
    public function getValue()
    {
        return $this->value;
    }

    abstract public function parse(AbstractField $objField = null);

}

// Endfile