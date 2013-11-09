<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Model;

use \CarteBlanche\Model\AbstractModel;

/**
 * The default CRUD model abstract class
 *
 * Any CRUD model must extend this abstract class
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
abstract class AbstractCrud extends AbstractModel
{

	/**
	 * The table name for the model 
	 *
	 * This must be re-defined in the model (__unless it will send an error as the "_Crud_Model"
	 * table doesn't exist__).
	 */
	var $_table_name='_crud_model';

	/**
	 * The table structure
	 *
	 * It is redefined just to precise that it must be an array.
	 */
	var $_table_structure=array();

	/**
	 * The object relations
	 *
	 * It is redefined just to precise that it must be an array.
	 */
	var $_object_relations=array();

	/**
	 * A flag to know if the object is loaded
	 */
	private $__loaded=false;

	/**
	 * A flag to know if one or every object relation(s) are loaded
	 */
	private $__relations_loaded=false;

	/**
	 * Returns TRUE if the object primary ID is set
	 */
	public function exists()
	{
		return $this->id;
	}

// ------------------------------------------
// Setters / Getters
// ------------------------------------------

	/**
	 * Sets the object primary ID
	 *
	 * @param int $id The primary ID of the object
	 */
	public function setId($id = null)
	{
		if (!empty($id)) $this->id = $id;
	}

	/**
	 * Gets the object primary ID
	 *
	 * @return int The object primary ID
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Sets the object data
	 *
	 * @param array $data An array of the object contents
	 */
	public function setData($data = null)
	{
		if (!empty($data)) {
			foreach ($this->_table_structure as $_field=>$field_structure) {
				if (isset($data[$_field]))
					$this->data[$_field] = $data[$_field];
				elseif (empty($this->data[$_field]))
					$this->data[$_field] = null;
			}
		}
	}

	/**
	 * Gets the object data
	 *
	 * @return array The object data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Sets the object data
	 *
	 * @return array Empty object's data
	 */
	public function createEmpty()
	{
        foreach ($this->_table_structure as $_field=>$field_structure) {
            $this->data[$_field] = 
                isset($field_structure['default']) ? $field_structure['default'] : null;
        }
		return $this->getData();
	}

	/**
	 * Sets one of the object related object
	 *
	 * @param string $model The name of the related model
	 * @param array $data An array of the contents of the related object
	 * @param int $id The related object primary ID in case of multi-relations
	 */
	public function setRelated($model = null, $data = null, $id = null)
	{
		if (!empty($model) && !empty($data)) {
			if (!empty($id)) {
				if (!isset($this->data[$model]))
					$this->data[$model] = array();
				if (!is_array($this->data[$model]))
					$this->data[$model] = array_filter( array( $this->data[$model] ) );
				$this->data[$model][$id] = $data;
			} else
				$this->data[$model] = $data;
		}
	}

	/**
	 * Gets one of the object related object
	 *
	 * @param string $model The name of the related model
	 * @return misc The recorded related object if so
	 */
	public function getRelated($model = null)
	{
		return $this->data[$model];
	}

// ------------------------------------------
// CRUD Methods
// ------------------------------------------

	/**
	 * @return array Must return the counting of all model objects
	 */
	abstract function count();

	/**
	 * @return array Must return the list of all model objects
	 */
	abstract function dump();

	/**
	 * @param int $id The primary ID of the object
	 * @return object Must return the content of the object
	 */
	abstract function read($id = null);

	/**
	 * Create a new object
	 *
	 * @param array $data An array of the object contents
	 * @return bool Must return a boolean indicating if the creation has succeeded
	 */
	abstract function create($data = null);

	/**
	 * Update an existing object
	 *
	 * @param int $id The primary ID of the object
	 * @param array $data An array of the object contents
	 * @return bool Must return a boolean indicating if the update has succeeded
	 */
	abstract function update($id = null, $data = null);

	/**
	 * Delete an object
	 *
	 * @param int $id The primary ID of the object
	 * @return bool Must return a boolean indicating if the deletion has succeeded
	 */
	abstract function delete($id = null);

}

// Endfile