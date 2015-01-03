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

abstract class AbstractType
{

    protected $default;
    protected $value;

    /**
     * Field default value
     *
     * @param mixed $default The default value to set
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
     * @param mixed $value The value to set
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