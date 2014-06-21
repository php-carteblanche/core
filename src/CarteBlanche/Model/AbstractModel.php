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

namespace CarteBlanche\Model;

/**
 * The default model abstract class
 *
 * Any model must extend this abstract class.
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractModel
{

    /**
     * The name of the model's default table
     */
    var $_table_name;

    /**
     * The structure of the model's default table
     */
    var $_table_structure;

    /**
     * The set of relations for the model objects
     */
    var $_object_relations=array();

    /**
     * The primary ID of the current object
     */
    var $id=null;

    /**
     * The data of the current object
     */
    var $data=null;

// ------------------------------------------
// Abstract methods
// ------------------------------------------

    /**
     * Must return TRUE if the object exists
     *
     * Basically, it returns `TRUE` when the object `ID` is not empty
     */
    abstract function exists();

    /**
     * Sets the object primary ID
     *
     * @param int $id The primary ID of the object
     */
    abstract function setId($id = null);

    /**
     * Gets the object primary ID
     */
    abstract function getId();

    /**
     * Sets the object data
     *
     * @param array $data An array of the object contents
     */
    abstract function setData($data = null);

    /**
     * Gets the object data
     */
    abstract function getData();

    /**
     * Sets one of the object related object
     *
     * @param string $model The name of the related model
     * @param array $data An array of the contents of the related object
     */
    abstract function setRelated($model = null, $data = null);

    /**
     * Gets one of the object related object
     *
     * @param string $model The name of the related model
     */
    abstract function getRelated($model = null);

// ------------------------------------------
// Common Getters
// ------------------------------------------

    /**
     * Gets the table fields list
     *
     * @return array The fields names list as an array
     */
    public function getFieldsList()
    {
        return array_keys($this->_table_structure);
    }

    /**
     * Does the table have a slug field
     *
     * @return bool True if the model has a slug field
     */
    public function hasSlugField()
    {
        foreach($this->_table_structure as $_field=>$field_structure) {
            if (isset($field_structure['slug']) && $field_structure['slug']===true) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the table slug field if so
     *
     * @return mixed The slug field name if found, false otherwise
     */
    public function getSlugField()
    {
        foreach($this->_table_structure as $_field=>$field_structure) {
            if (isset($field_structure['slug']) && $field_structure['slug']===true) {
                return $_field;
            }
        }
        return false;
    }

    /**
     * Gets a table special field if found
     *
     * @param string $field The field name to search
     * @param mixed $value The corresponding value of the field
     * @return array The special search fields list if found
     */
    public function getSpecialFields($field, $value = true)
    {
        $fields = array();
        foreach($this->_table_structure as $_field=>$field_structure) {
            if (isset($field_structure[$field]) && $field_structure[$field]===$value) {
                $fields[] = $_field;
            }
        }
        return $fields;
    }

    /**
     * Gets a list of fields by their type
     *
     * @param string $type The field's type to search
     * @return array The array of the fields matching the type if found
     */
    public function getFieldsByType($type)
    {
        $fields = array();
        foreach($this->_table_structure as $_field=>$field_structure) {
            if (isset($field_structure['type']) && $field_structure['type']==$type) {
                $fields[] = $_field;
            }
        }
        return $fields;
    }

    /**
     * Gets the table name
     *
     * @return string The table name
     */
    public function getTableName()
    {
        return $this->_table_name;
    }

    /**
     * Gets the table structure
     *
     * @return array The full array of the table structure
     */
    public function getTableStructure()
    {
        return $this->_table_structure;
    }

    /**
     * Get a clone of an object
     *
     * @return object A clone of the current object
     * @throw Throws a RuntimeExcpetion if the object doesn't exist
     */
    public function getClone()
    {
        if (self::exists()) {
            return clone $this;
        } else {
            throw new \RuntimeException( "Trying to clone an empty object [{$this->_table_name}]!" );
        }
    }

    /**
     * Get a clone of the model (*anything concerning the object itself will be empty*)
     *
     * @return object A clone of the model with ID and Data empty
     */
    public function getModelClone()
    {
        $_this = clone $this;
        if ($_this->exists()) $_this->id=$_this->data=null;
        return $_this;
    }

    /**
     * Get the current date
     */
    public static function getNow()
    {
        return date('Y-m-d H:i:s');
    }

}

// Endfile