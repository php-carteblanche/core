<?php
/**
 * CarteBlanche - PHP framework package
 * (c) Pierre Cassat and contributors
 * 
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * License Apache-2.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CarteBlanche\Library\Form;

use \Tool\Form\FormFieldInterface;

/**
 */
class Field implements FormFieldInterface
{

    protected $field_structure;

    protected $name;
    protected $type;
    protected $type_html5;
    protected $int_type;
    protected $default;
    protected $validations;
    protected $callbacks;
    protected $comments;
    protected $values;

    public function __construct($field_name, $field_structure)
    {
        $this->name = $field_name;
        $this->field_structure = $field_structure;
        self::init();
    }

    protected function init()
    {
        $callbacks = $validations = $comments = array();
        $_validate = null;

        if (isset($this->field_structure['null']))
            $this->nullable = (bool) $this->field_structure['null'];

        if (isset($this->field_structure['default']))
            $this->default = $this->field_structure['default'];

        if (isset($this->field_structure['type'])) {
            $this->int_type = $this->field_structure['type'];

            if (in_array($this->int_type, array('button', 'submit', 'reset'))) {
                $this->type = 'button';
                $this->type_html5 = 'button';
            }

            elseif (preg_match('/^(.*)_id$/i', $this->name, $matches)) {
    // we must not be here !!!
            }

            elseif ($this->name=='id') {
                $this->type = 'id';
            }

            elseif ($this->name=='created_at') {
                $this->type = 'string';
                $this->type_html5 = 'datetime';
                if (!isset($callbacks['pre_create']))
                    $callbacks['pre_create'] = array();
                $callbacks['pre_create'][] = 'get_now';
            }

            elseif ($this->name=='updated_at') {
                $this->type = 'string';
                $this->type_html5 = 'datetime';
                if (!isset($callbacks['pre_update']))
                    $callbacks['pre_update'] = array();
                $callbacks['pre_update'][] = 'get_now';
            }

            elseif (in_array($this->int_type, array('date', 'datetime', 'time'))) {
                $this->type = 'string';
                $this->type_html5 = $this->int_type;
                $validations[] = 'is_'.$this->int_type;
                if (false===$this->isNullable() && null==$this->getDefault())
                    $this->default = trim(
                        (in_array($this->int_type, array('date', 'datetime')) ? '0000-00-00 ' : '')
                        .(in_array($this->int_type, array('time', 'datetime')) ? '00:00:00' : '')
                    );
            }

            elseif (preg_match('/^(.*)_at$/i', $this->int_type, $matches)) {
                $this->type = 'string';
                $this->type_html5 = 'datetime';
                $validations[] = 'is_datetime';
                if (false===$this->isNullable() && null==$this->getDefault())
                    $this->default = '0000-00-00 00:00:00';
            }

            elseif (in_array($this->int_type, array('tinyint(1)', 'bit', 'boolean'))) {
                $this->type = 'toggler';
                if (false===$this->isNullable() && null==$this->getDefault())
                    $this->default='0';
            }

            elseif (preg_match('/^varchar(\((.*)\))?/i', $this->int_type, $matches)) {
                $this->type = 'string';
                $this->type_html5 = 'text';
                if (isset($matches[2]) && is_numeric($matches[2]))
                    $validations['maxlength'] = $matches[2];
                if (false===$this->isNullable() && null==$this->getDefault())
                    $this->default = '';

            }

            elseif (preg_match('/^integer(\((.*)\))?/i', $this->int_type, $matches) || preg_match('/^float(\((.*)\))?/i', $this->int_type, $matches)) {
                $this->type = 'numeric';
                $this->type_html5 = 0!==preg_match('/^integer(\((.*)\))?/i', $this->int_type) ? 'number' : 'text';
                $validations[] = 'is_numeric';
                if (isset($matches[2]) && is_numeric($matches[2]))
                    $validations['maxlength'] = $matches[2];
                if (false===$this->isNullable() && null==$this->getDefault())
                    $this->default = '0';
            }

            elseif (preg_match('/(.*)text$/i', $this->int_type, $matches)) {
                $this->type = 'text';
                $this->type_html5 = 'text';
                if (false===$this->isNullable() && null==$this->getDefault())
                    $this->default = '';
            }

            elseif (preg_match('/(.*)blob$/i', $this->int_type, $matches)) {
                $this->type = 'file';
                if (false===$this->isNullable() && null==$this->getDefault())
                    $this->default='';
                if (!empty($this->field_structure['maxlength'])) {
                    $maxlength = $this->field_structure['maxlength'];
                } else {
                    if (!empty($this->field_structure['filelength']))
                        $filelength = $this->field_structure['filelength'];
                    $maxlength = '100000';
                    if (isset($matches[2]) && is_numeric($matches[2]))
                        $filelength = $matches[2];
                    if (!empty($filelength) && $filelength=='medium')
                        $maxlength = '50000';
                    elseif (!empty($filelength) && $filelength=='tiny')
                        $maxlength = '10000';
                }
                $validations['maxfilesize'] = $maxlength;
                if (!empty($this->field_structure['accept'])) {
                    $accept = is_array($this->field_structure['accept']) ?
                        join(',', $this->field_structure['accept']) : $this->field_structure['accept'];
                    $validations['acceptedFiles'] = $accept;
                }
            }

            else {
                throw new \Exception(
                    sprintf('Unknown type "%s" for field named "%s"!', $this->int_type, $this->name)
                );
            }

        } else {
            throw new \Exception(
                sprintf('No type defined for field named "%s"!', $this->name)
            );
        }

        if (isset($this->field_structure['callback'])) {
            $callbacks = array_merge($callbacks, is_array($this->field_structure['callback']) ?
                $this->field_structure['callback'] : explode('|', $this->field_structure['callback'])
            );
        }
        if (isset($this->field_structure['callbacks'])) {
            $callbacks = array_merge($callbacks, is_array($this->field_structure['callbacks']) ?
                $this->field_structure['callbacks'] : explode('|', $this->field_structure['callbacks'])
            );
        }
        if (isset($this->field_structure['validation'])) {
            $validations = array_merge($validations, is_array($this->field_structure['validation']) ?
                $this->field_structure['validation'] : explode('|', $this->field_structure['validation'])
            );
        }
        if (isset($this->field_structure['validations'])) {
            $validations = array_merge($validations, is_array($this->field_structure['validations']) ?
                $this->field_structure['validations'] : explode('|', $this->field_structure['validations'])
            );
        }
        if (isset($this->field_structure['comment'])) {
            $comments = array_merge($comments, is_array($this->field_structure['comment']) ?
                $this->field_structure['comment'] : explode('|', $this->field_structure['comment'])
            );
        }
        if (isset($this->field_structure['comments'])) {
            $comments = array_merge($comments, is_array($this->field_structure['comments']) ?
                $this->field_structure['comments'] : explode('|', $this->field_structure['comments'])
            );
        }
        if (isset($this->field_structure['toggler'])) {
            $this->togglable = (bool) $this->field_structure['toggler'];
        }

        if (!empty($validations)) {
            $callbacks['validation'] = array();
            foreach ($validations as $j=>$_validate) {
                if (is_string($j)) {
                    $callbacks['validation'][] = $j.'('.$_validate.')';
                } else {
                    if ($_validate=='is_email') {
                        $callbacks['validation'][] = $_validate;
                        unset($validations[$j]);
                        $this->type_html5 = 'email';
                    }
                    elseif ($_validate=='is_url') {
                        $callbacks['validation'][] = $_validate;
                        unset($validations[$j]);
                        $this->type_html5 = 'url';
                    }
                    elseif ($_validate=='is_numeric') {
                        $callbacks['validation'][] = $_validate;
                        unset($validations[$j]);
                        $this->type_html5 = 'number';
                    }
                    else {
                        $callbacks['validation'][] = $_validate;
                    }
                }
            }
        }

        $this->callbacks = $callbacks;
        $this->validations = $validations;
        $this->comments = $comments;
    }

// -------------------
// Getters
// -------------------

    public function getData()
    {
        return $this->field_structure;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTypeHtml5()
    {
        return $this->type_html5;
    }

    public function getIntType()
    {
        return $this->int_type;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getValidations()
    {
        return $this->validations;
    }

    public function getValidationEntry($stack_name)
    {
        return isset($this->validations[$stack_name]) ? $this->validations[$stack_name] : null;
    }

    public function getCallbacks()
    {
        return $this->callbacks;
    }

    public function getCallbacksStack($stack_name)
    {
        return isset($this->callbacks[$stack_name]) ? $this->callbacks[$stack_name] : array();
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function isNullable()
    {
        return true===$this->nullable;
    }

    public function isSluggable()
    {
        return true===$this->sluggable;
    }

    public function isTogglable()
    {
        return true===$this->togglable;
    }

}

// Endfile