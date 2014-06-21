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

namespace CarteBlanche\Library\AutoObject;

use \CarteBlanche\Library\Callback;

/**
 */
class DataCallback extends Callback
{

    protected $data;

    /**
     * Construction of a new callback execution
     * @param string $callback_name The name of the callback stack to execute in each field
     * @param array $data The data to work on, passed by reference
     * @param array $fields The collection of fields where to search the callback stack
     */
    public function __construct($callback_name, &$data = null, $fields = null)
    {
        if (!empty($data) || is_array($data)) {
            $this->data =& $data;
        } else {
            return;
        }

        if (!empty($fields) || is_array($fields)) {
            $this->fields = $fields;
        } else {
            return;
        }

        parent::__construct($this->data, null, 'reference', $callback_name, $this->fields);
    }

    protected function getCallbackStack()
    {
        foreach ($this->fields as $_field_name=>$_field) {
            $callback_stack = null;
            if ($_field instanceof \CarteBlanche\Library\AutoObject\Field) {
                $callback_stack = $_field->getCallbacksStack( $this->callback_name );
            } elseif (is_array($_field) && isset($_field['callbacks'])) {
                $callback_stack = isset($_field['callbacks'][$this->callback_name]) ?
                    $_field['callbacks'][$this->callback_name] : null;
            } else {
                throw new \RuntimeException(
                    sprintf('Invalid field "%s" to execute callback "%s" (must be an array or a "Lib\AutoObject\Field" object)!',
                        $_field_name, $this->callback_name)
                );
            }

            if (!empty($callback_stack)) {
                $this->executeCallbackStack( $callback_stack, $_field_name, $this->data );
            }
        }
    }

    /**
     * Execution of a callback array on an entry of the data array
     * @param array $callback_stack The callbacks array to execute
     * @param string $entry The entry name to execute the callbacks functions on from $data
     * @param array $data The data array to work on, passed by reference
    protected function executeCallbackStack(array $callback_stack, $entry, array &$data)
    {
        if (!isset($data[$entry])) $data[$entry] = '';
        foreach ($callback_stack as $_callback) {
            $this->executeCallbackAsReference( $_callback, $data[$entry] );
        }
    }
     */

}

// Endfile