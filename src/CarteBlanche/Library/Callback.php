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

namespace CarteBlanche\Library;

use \CarteBlanche\Exception\RuntimeException;
use \CarteBlanche\Exception\InvalidArgumentException;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Callback
{

    protected $callback_response_type='reference';

    protected $callback_name;

    protected $callback_stack;

    protected $value;

    protected $fields;

    protected $result;

    /**
     * Construction of a new callback execution
     *
     * @param   mixed       $value
     * @param   null/array  $callback_stack
     * @param   string      $callback_type
     * @param   string      $callback_name
     * @param   array       $fields         The collection of fields where to search the callback stack
     * @throws  \CarteBlanche\Exception\InvalidArgumentException if the callback type can't be found
     * @throws  \CarteBlanche\Exception\InvalidArgumentException if the stack seems empty
     */
    public function __construct($value, $callback_stack = null, $callback_type = null, $callback_name = null, $fields = null)
    {
        $this->value = $value;
        if (!empty($callback_stack) || is_array($callback_stack))
            $this->callback_stack = $callback_stack;
        if (!empty($fields) || is_array($fields))
            $this->fields = $fields;
        if (!empty($callback_name) || is_string($callback_name))
            $this->callback_name = $callback_name;

        if (!empty($callback_type)) {
            $this->callback_response_type = $callback_type;
            $_meth = '_executeCallbackAs'.ucfirst($this->callback_response_type);
            if (!method_exists($this, $_meth) || !is_callable(array($this, $_meth)))
                throw new InvalidArgumentException(
                    sprintf('Unknown callback type "%s"!', $this->callback_response_type)
                );
        }

        if (empty($this->callback_stack)) {
            if (!empty($fields) && !empty($this->callback_name))
                $this->_getCallbackStack();
            else
                throw new InvalidArgumentException(
                    'Unknown callback stack to execute (missing the stack or the callback name and the fields list)!'
                );
        }

        $this->result = $this->_executeCallbackStack();
    }

    /**
     * @return  mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @throws  \CarteBlanche\Exception\RuntimeException
     */
    protected function _getCallbackStack()
    {
        foreach($this->fields as $_field_name=>$_field) {
            $callback_stack = null;
            if ($_field instanceof \CarteBlanche\Library\AutoObject\Field) {
                $callback_stack = $_field->getCallbacksStack( $this->callback_name );
            } elseif (is_array($_field) && isset($_field['callbacks'])) {
                $callback_stack = isset($_field['callbacks'][$this->callback_name]) ?
                    $_field['callbacks'][$this->callback_name] : null;
            } else {
                throw new RuntimeException(
                    sprintf('Invalid field "%s" to execute callback "%s" (must be an array or a "Lib\AutoObject\Field" object)!',
                        $_field_name, $this->callback_name)
                );
            }

            if (!empty($callback_stack)) {
                $this->callback_stack = $callback_stack;
                if (is_callable(array($_field, 'getValue')))
                    $this->value = $_field->getValue();
                elseif (is_array($_field) && isset($_field['value']))
                    $this->value = $_field['value'];
            }
        }
    }

    /**
     * Execution of a callback array on an entry of the data array
     *
     * @return  mixed
     */
    protected function _executeCallbackStack()
    {
        if (!empty($this->callback_stack)) {
            $_meth = '_executeCallbackAs'.ucfirst($this->callback_response_type);
            foreach ($this->callback_stack as $_callback) {
                $response = $this->$_meth( $_callback, $this->value );
            }
            return $response;
        }
        return null;
    }

// --------------------
// Executors
// --------------------

    /**
     * Execution of one callback on a value returning it by reference
     *
     * @param   string  $fct            The function name to execute, it will be called passing the current value as first argument
     * @param   mixed   $entry_value    The entry value to execute the callback, passed by reference
     * @return  mixed
     */
    protected function _executeCallbackAsReference($fct, &$entry_value)
    {
        list($fct_name, $args) = $this->_parseCallbackFunctionName($fct);
        array_unshift($args, $entry_value);
        $entry_value = $this->_executeCallback($fct_name, $args);
        return $entry_value;
    }

    /**
     * Execution of one callback on a value
     *
     * @param   string  $fct            The function name to execute, it will be called passing the current value as first argument
     * @param   mixed   $entry_value    The entry value to execute the callback
     * @return  bool
     */
    protected function _executeCallbackAsBoolean($fct, $entry_value)
    {
        list($fct_name, $args) = $this->_parseCallbackFunctionName($fct);
        array_unshift($args, $entry_value);
        $response = $this->_executeCallback($fct_name, $args);
        if ($response==$entry_value)
            return true;
        return (bool) $response;
    }

// --------------------
// Utilities
// --------------------

    /**
     * Parse a function name for a callback extracting the arguments if so
     *
     * @param   string  $fct   The function name to parse
     * @return  array   An array composed of the function name to execute and the arguments array to pass
     */
    protected function _parseCallbackFunctionName($fct)
    {
        $fct_name = $fct;
        $args = array();

        // case "fct_name(arg1,arg2)"
        if (0!=preg_match('/^
            ([^\(]+)                # the function name
            \(                      # the opening parenthesis of arguments
                (?:
                    ([^\)]+)        # anything but a coma
                ){1,}               # 1 or more times
            \)                      # the closing parenthesis of arguments
        $/xi', $fct, $matches)) {
            $fct_name = $matches[1];
            $args = explode(',', $matches[2]);
        }

        return array($fct_name, $args);
    }

    /**
     * Execution of one callback on a value
     *
     * @param   string  $fct   The function name to execute
     * @param   array   $args  The arguments to pass to the function
     * @return  mixed   The result of the function execution on arguments
     * @throws  \CarteBlanche\Exception\RuntimeException in case of error
     */
    protected function _executeCallback($fct, $args)
    {
        if (function_exists($fct)) {
            try {
                $response = call_user_func_array($fct, $args);
                return $response;
            } catch (\Exception $e) {
                throw new RuntimeException($e->getMessage());
            }
        }
        return isset($args[0]) ? $args[0] : null;
    }

}

// Endfile