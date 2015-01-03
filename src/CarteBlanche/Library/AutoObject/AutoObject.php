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

namespace CarteBlanche\Library\AutoObject;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Library\AutoObject\Database;
use \CarteBlanche\Library\AutoObject\Table;
use \CarteBlanche\Library\AutoObject\Field;
use \CarteBlanche\Library\AutoObject\Relation;
use \CarteBlanche\Library\AutoObject\AutoObjectMapper;
use \CarteBlanche\Model\BaseModel;
use \Patterns\Commons\Collection;

/**
 */
class AutoObject
{

    protected $object_name;
    protected $object_table_name;
    protected $object_database_name;
    protected $object_model_name;
    protected $object_structure;

    protected $object_model;
    protected $object_table;
    protected $object_fields;
    protected $object_relations;
    protected $object_fields_by_type;

    protected $editable;

    public function __construct($table, $structure, $name = null, $db = null)
    {
        $this->setTableName( $table );
        $this->setStructure( $structure );
        $this->setName( !is_null($name) ? $name : $table );
        $this->setDatabaseName( !is_null($db) ? $db : 'default' );

        $_model = $this->getStructureEntry('model');
        $this->setModelName( !is_null($_model) ? $_model : '\CarteBlanche\Model\BaseModel' );

        $this->editable = $this->getStructureEntry('editable', false);

        $this->object_fields = new Collection;
        $this->object_relations = new Collection;
    }

// ------------------
// SETTERS
// ------------------

    /**
     * Set the object name
     * @param string $name The object name to set
     * @return self Return the object for method chaining
     */
    public function setName($name = null)
    {
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException(
                sprintf('Object name is invalid! (must be a string, got "%s")', var_export($name,1))
            );
        }
        $this->object_table_name = $name;
        return $this;
    }

    /**
     * Set the object structure
     * @param string $name The object structure to set
     * @return self Return the object for method chaining
     */
    public function setStructure($structure = null)
    {
        if (empty($structure) || !is_array($structure)) {
            throw new \InvalidArgumentException(
                sprintf('Object structure is invalid! (must be an array, got "%s")', gettype($structure))
            );
        }
        $this->object_structure = $structure;
        return $this;
    }

    /**
     * Set the object table name
     * @param string $name The object table name to set
     * @return self Return the object for method chaining
     */
    public function setTableName($name = null)
    {
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException(
                sprintf('Object table name is invalid! (must be a string, got "%s")', var_export($name,1))
            );
        }
        $this->object_table_name = $name;
        return $this;
    }

    /**
     * Set the object database name
     * @param string $name The object database name to set
     * @return self Return the object for method chaining
     */
    public function setDatabaseName($name = null)
    {
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException(
                sprintf('Object database name is invalid! (must be a string, got "%s")', var_export($name,1))
            );
        }
        $this->object_database_name = $name;
        return $this;
    }

    /**
     * Set the object model name
     * @param string $name The object model name to set
     * @return self Return the object for method chaining
     */
    public function setModelName($name = null)
    {
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException(
                sprintf('Object model name is invalid! (must be a string, got "%s")', var_export($name,1))
            );
        }
        if (false===\CarteBlanche\App\Loader::classExists($name)) {
            throw new \InvalidArgumentException(
                sprintf('Object model\'s class can\'t be found! (got "%s")', var_export($name,1))
            );
        }
        $this->object_model_name = $name;
        return $this;
    }

// ------------------
// GETTERS
// ------------------

    public function getName()
    {
        return $this->object_name;
    }

    public function getTableName()
    {
        return $this->object_table_name;
    }

    public function getDatabaseName()
    {
        return $this->object_database_name;
    }

    public function getStructure()
    {
        return $this->object_structure;
    }

    public function getStructureEntry($entry, $default = null)
    {
        return isset($this->object_structure[$entry]) ? $this->object_structure[$entry] : $default;
    }

    public function getTable()
    {
        if (empty($this->object_table))
            $this->_buildTable();
        return $this->object_table;
    }

    public function getModel($auto_populate = true, $advanced_search = false)
    {
        if (empty($this->object_model))
            $this->_buildModel($auto_populate,$advanced_search);
        return $this->object_model;
    }

    public function getFields()
    {
        if ($this->object_fields->count()==0)
            $this->_buildFields();
        return $this->object_fields->getCollection();
    }

    public function getField($field_name, $default = null)
    {
        if ($this->object_fields->count()==0)
            $this->_buildFields();
        return $this->object_fields->key_exists($field_name) ? $this->object_fields->getEntry($field_name) : $default;
    }

    public function getRelations()
    {
        if ($this->object_relations->count()==0)
            $this->_buildRelations();
        return $this->object_relations->getCollection();
    }

    public function getRelation($field_name, $default = null)
    {
        if ($this->object_relations->count()==0)
            $this->_buildRelations();
        return $this->object_relations->key_exists($field_name) ? $this->object_relations->getEntry($field_name) : $default;
    }

    public function getFieldsByType()
    {
        if (empty($this->object_fields_by_type))
            $this->_buildFieldsByType();
        return $this->object_fields_by_type;
    }

    public function isEditable()
    {
        return true===$this->editable;
    }

// ------------------
// BUILDERS
// ------------------

    /**
     * Build the object model
     * @return void
     */
    protected function _buildModel($auto_populate = true, $advanced_search = false)
    {
        if (\CarteBlanche\App\Loader::classExists($this->object_model_name)) {
            try {
                $this->object_model = new $this->object_model_name(
                    $this->getTableName(),
                    $this->getFields(),
                    $this->getStructure(),
                    $auto_populate, $advanced_search
                );
                $db = CarteBlanche::getContainer()->get('entity_manager')
                    ->getStorageEngine($this->getDatabaseName());
                $this->object_model->setStorageEngine($db);

            } catch ( \Exception $e ) {
                throw new \Exception(
                    sprintf('Model "%s" constructor seems not compliant with the "\CarteBlanche\Interfaces\ModelInterface" interface!', $this->object_model_name)
                );
            }
        } else {
            trigger_error( sprintf('Model "%s" can\'t be found!', $this->object_model_name), E_USER_ERROR);
        }
    }

    /**
     * Build the object table object
     * @return void
     */
    protected function _buildTable()
    {
        $this->object_table = new Table( $this->object_table_name, $this->object_database );
    }

    /**
     * Build an array of the object fields
     * @return void
     */
    protected function _buildFields()
    {
        $db = CarteBlanche::getContainer()->get('entity_manager')
            ->getStorageEngine($this->getDatabaseName());
        foreach ($this->getStructureEntry('structure') as $_field=>$_data)  {
            $_f = null;
            if (preg_match('/^(.*)_id$/i', $_field, $matches)) {
                if (isset($matches[1]) && is_string($matches[1])) {
                    $related_object = $matches[1];
                    if ($related_object=='parent')
                        $related_object = $this->getTableName();
                    $table = AutoObjectMapper::getAutoObject($related_object, $this->getDatabaseName());
                    $_f = new Relation(
                        $_field, $_data, $db, $related_object, $table->getModel()
                    );
                }
            } else {
                $_f = new Field( $_field, $_data, $db );
            }

            if (!is_null($_f))
                $this->object_fields->setEntry( $_field, $_f );
        }
    }

    /**
     * Build an organized array of the object fields, by type (string, numeric or relation)
     * @return void
     */
    protected function _buildFieldsByType()
    {
        $this->object_fields_by_type = array(
            'num'=>array(),
            'str'=>array(),
            'rel'=>array()
        );
        foreach ($this->getStructureEntry('structure') as $_field=>$_data) {
            if (
                preg_match('/^integer(\((.*)\))?/i', $_data['type']) ||
                preg_match('/^float(\((.*)\))?/i', $_data['type'])
            ) $this->object_fields_by_type['num'][] = $_field;
            elseif (
                preg_match('/^varchar(\((.*)\))?/i', $_data['type']) ||
                preg_match('/(.*)text$/i', $_data['type'])
            ) $this->object_fields_by_type['str'][] = $_field;
            elseif (
                preg_match('/^(.*)_id$/i', $_data['type'])
            ) $this->object_fields_by_type['rel'][] = $_field;
        }
    }

    /**
     * Build an array of the object relation fields
     * @return void
     */
    protected function _buildRelations()
    {
        $db = CarteBlanche::getContainer()->get('entity_manager')
            ->getStorageEngine($this->getDatabaseName());

        foreach ($this->getStructureEntry('structure') as $_field=>$_fdata) {
            $_relobject=$_relfield=null;

            if ($_field!='parent_id' && preg_match('/^(.*)_id$/i', $_field, $matches)) {
                if (isset($matches[1]) && is_string($matches[1]))
                    $_relobject = $matches[1];
                $_relfield = 'id';
                $_intfield = $_field;
                $_field = str_replace('_id', '', $_field);
            } elseif (!empty($_fdata['related'])) {
                $_relobject_f = $_fdata['related'];
                if (!substr_count($_relobject_f, ':')) $_relobject_f .= ':id';
                $matches = explode(':', $_relobject_f);
                $_relobject = $matches[0];
                $_relfield = $matches[1];
                $_intfield = $_field;
                $_field = 'parent';
            }

            if (isset($_relobject) && $_relobject==$this->getTableName()) {
                $this->object_relations->setEntry( $_field, array(
                    'related_table'=>$_relobject,
                    'related_field'=>$_relfield,
                    'internal_field'=>$_intfield,
                    'model'=>$this->getModel()->getModelClone(),
                    'slug_field'=>$this->getModel()->getSlugField(),
                    'self'=>true
                ));
            } elseif (isset($_relobject) && $db->table_exists($_relobject)) {
                $_relobject_structure = AutoObjectMapper::getAutoObject( $_relobject, $this->getDatabaseName() );
                if (!empty($_relobject_structure))
                    $_related = $_relobject_structure->getModel();
                $this->object_relations->setEntry( $_field, array(
                    'related_table'=>$_relobject,
                    'related_field'=>$_relfield,
                    'internal_field'=>$_intfield,
                    'model'=>$_related,
                    'slug_field'=>$_related->getSlugField(),
                ));
            }

        }

        $_relobject_f=null;
        if (!empty($this->getModel()->_has_many))
        foreach ($this->getModel()->_has_many as $_relname=>$_relobject_f) {
            $_relobject=$_relfield=null;

            if (!substr_count($_relobject_f, ':')) $_relobject_f .= ':id';
            $matches = explode(':', $_relobject_f);
            $_relobject = $matches[0];
            $_relfield = $matches[1];

            if (isset($_relobject) && $_relobject==$this->getTableName()) {
                $this->object_relations->setEntry( $_relname, array(
                    'related_table'=>$_relobject,
                    'related_field'=>$_relfield,
                    'internal_field'=>'id',
                    'model'=>$this->getModel()->getModelClone(),
                    'slug_field'=>$this->getModel()->getSlugField(),
                    'is_many'=>true,
                    'self'=>true
                ));
            } elseif (isset($_relobject) && $db->table_exists($_relobject)) {
                $_relobject_structure = AutoObjectMapper::getAutoObject( $_relobject, $this->getDatabaseName() );
                if (!empty($_relobject_structure)) {
                    $_related = $_relobject_structure->getModel();
                    $this->object_relations->setEntry( $_relname, array(
                        'related_table'=>$_relobject,
                        'related_field'=>$_relfield,
                        'internal_field'=>'id',
                        'model'=>$_related,
                        'slug_field'=>$_related->getSlugField(),
                        'is_many'=>true
                    ));
                } else {
                    throw new \RuntimeException( "Table structure for related field '$_relname' not found!");
                }
            }
        }
    }

}

// Endfile