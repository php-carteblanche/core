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

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Interfaces\ModelInterface;
use \CarteBlanche\Library\StorageEngine\StorageEngineAwareInterface;
use \CarteBlanche\Interfaces\StorageEngineInterface;
use \CarteBlanche\Model\AbstractCrud;
use \CarteBlanche\Library\AutoObject\DataCallback;

/**
 * The default model used by the application
 *
 * Basic model extending the CRUD abstract class
 * This class is a sort of canva used to create a CRUD model on the fly for each table.
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class BaseModel
    extends AbstractCrud
    implements ModelInterface, StorageEngineAwareInterface
{

    protected $__storage_engine;

    /**
     * The table name for the model (overwritten for each creation)
     */
    var $_table_name='basic_model';

    /**
     * The table fields array of the model
     */
    var $_table_fields=array();

    /**
     * The table structure of the model (overwritten for each creation)
     */
    var $_table_structure=array(
        'id'=>array(
            'type'=>'integer',
            'null'=>false,
            'default'=>'',
            'index'=>'primary key asc',
        ),
        'created_at'=>array(
            'type'=>'datetime',
            'null'=>true,
            'default'=>'',
            'index'=>false,
        ),
        'updated_at'=>array(
            'type'=>'datetime',
            'null'=>true,
            'default'=>'',
            'index'=>false,
        ),
    );

    /**
     * Array of related objects that this one can have once
     */
    var $_has_one=array();

    /**
     * Array of related objects that this one can have more than once
     */
    var $_has_many=array();

    /**
     * Array of object relations
     */
    var $_object_relations=array();

    /**
     * Allow advanced search
     */
    var $_advanced_search=false;

    /**
     * A flag to tell the model to populate related objects automatically
     */
    private $__auto_populate=false;

    /**
     * A flag to tell the model if relations had been loaded
     */
    private $__relations_loaded=false;

    /**
     * A flag for initialization
     */
    private $isInit=false;

    /**
     * Construction : defines the table name and structure and the object relations
     *
     * @param string $_table_name The table name
     * @param array $_table_fields The table fields array
     * @param array $_table_structure The table structure array
     * @param bool $auto_populate The value for the auto_populate class flag (default is TRUE)
     * @param bool $advanced_search The value for the advanced_search class flag (default is FALSE)
     */
    public function __construct(
        $_table_name = null, $_table_fields = null, $_table_structure = null, $auto_populate = true, $advanced_search = false
    ) {
        if (!empty($_table_name) && !empty($_table_structure)) {
            $this->_table_name = $_table_name;
            $this->_table_fields = $_table_fields;
            $this->_table_structure =
                isset($_table_structure['structure']) ? $_table_structure['structure'] : $_table_structure;
            $this->_has_one =
                isset($_table_structure['has_one']) ? $_table_structure['has_one'] : null;
            $this->_has_many =
                isset($_table_structure['has_many']) ? $_table_structure['has_many'] : null;
            if ($auto_populate===true)
                $this->__auto_populate=true;
            if ($advanced_search===true)
                $this->_advanced_search=true;
        } else {
            throw new \InvalidArgumentException( 'Model "BaseModel" called with non-well formated arguments!' );
        }
    }

    /**
     * Initialization of the object relations
     */
    protected function init()
    {
        if (true===$this->isInit) return;
        if ($this->__auto_populate===true) {
            $this->getObjectRelations();
        }
        $this->isInit=true;
    }

    /**
     * Set the storage engine
     *
     * @param \CarteBlanche\Interfaces\StorageEngineInterface $storage_engine
     */
    public function setStorageEngine(StorageEngineInterface $storage_engine)
    {
        $this->__storage_engine = $storage_engine;
        return $this;
    }

    /**
     * Get the storage engine
     *
     * @return \CarteBlanche\Interfaces\StorageEngineInterface
     */
    public function getStorageEngine()
    {
        return $this->__storage_engine;
    }

// ------------------------------------------
// CRUD Methods
// ------------------------------------------

    /**
     * Count the table entries
     *
     * @param string $search_str A string to limit counting
     * @return int The result of the count
     */
    public function count($search_str = null)
    {
        $this->init();
        $db = $this->getStorageEngine();
        $db->clear();
        $db
            ->select('count(*)')
            ->from($this->_table_name);
        if (!empty($search_str)) {
            $ok = self::search( $search_str );
            if (false===$ok) return 0;
        }

        return $db->get_single();
    }

    /**
     * Execute a dump of the table according to parameters
     *
     * @param int $offset An offset to use for the dump
     * @param int $limit A limit to use for the dump
     * @param bool $get_relations Get the dumped results relations (default is FALSE)
     * @param string $order_by A field name to order the dump results
     * @param string $order_way The way used for ordering (default is 'asc')
     * @param string $search_str A string to search
     * @param string $where_str A WHERE statement
     * @return array The result of the dump
     */
    public function dump(
        $offset = 0, $limit = null, $get_relations = false, $order_by = null, $order_way = 'asc',
        $search_str = null, $where_str = null
    )
    {
        $this->init();
        $db = $this->getStorageEngine();
        $db->clear();
        $db
            ->select('*')
            ->from($this->_table_name);
        if (!empty($search_str)) {
            $ok = self::search( $search_str );
            if (false===$ok) return array();
        }
        if (!empty($where_str))
            $db->where( $where_str );
        if (isset($order_by))
            $db->order_by($order_by, $order_way);
        if (isset($limit))
            $db->limit($limit);
        if (isset($offset))
            $db->offset($offset);
    //echo $db->get_query();exit('yo');
        $results = $db->get();
        if ($get_relations===true) {
    //			$results_table=array();
            $results_table = new \Patterns\Commons\Collection;
            $fields_list = self::getFieldsList();
            $objects_table = array();
            foreach($results as $_id=>$object) {
                $_obj = null;
                $_obj = $this->getModelClone();
                $_obj->setId( $object['id'] );
                $_obj->setData( $object );
                $_obj->getRelations();
                $_objdat = $_obj->getData();
                $_objdat['id'] = $_obj->getId();
    //				$results_table[] = $_objdat;
                $results_table->push( $_objdat );
            }
            return $results_table;
        }
        return $results;
    }

    /**
     * Search a string or value in table entries, depending of the fields format
     *
     * @param string $search_str A string to search
     * @param bool $standalone Clear the BDD query constructors (default is FALSE)
     * @return numeric The result of the search
     */
    public function search($search_str = null, $standalone = false)
    {
        $this->init();
        if (!empty($search_str)) {
            $db = $this->getStorageEngine();
            if ($standalone===true)
                $db->clear();
            $fields = self::getFieldsList();
            $search_fields = \CarteBlanche\Library\AutoObject\AutoObjectMapper::getFieldsByType( $this->_table_name );
            // advanced search ...
            if (false===$this->advancedSearch($search_str, $search_fields))
                return false;
        }
    }

    /**
     * Build a search request based on a string using logical operators
     *
     * @param string $search_str A string to search
     * @param string $search_fields The field name or array to search in
     * @return void
     */
    public function advancedSearch($search_str = null, $search_fields = null)
    {
        $this->init();
        $db = $this->getStorageEngine();
        if (true===$this->_advanced_search && class_exists('\Tool\AdvancedSearch')) {
            $search_str = $db->escape($search_str);
            $tool = new \Tool\AdvancedSearch(array(
                'search_str'    => $search_str,
                'field'         => $search_fields
            ));
            $tool->setStorageEngine($this->getStorageEngine());
    //echo $s_str = $tool->getQuerySearchString();exit('yo');
            $s_str = $tool->getQuerySearchString();
            if ($s_str) {
                $db->where_str( $s_str );
    //echo $db->get_query();exit('yo');
            } else {
                return false;
            }
        } else {
            $search_str = $db->escape($search_str, true);
            if (is_numeric($search_str)) {
                foreach($search_fields['num'] as $_f)
                    $db->or_where_like($_f, $search_str);
            } elseif (is_string($search_str)) {
                foreach($search_fields['str'] as $_f)
                    $db->or_where_like($_f, '"%'.$search_str.'%"');
            }
    //echo $db->get_query();exit('yo');
        }
    }

    /**
     * Make a dump query with weighted fields results
     *
     * @param array $weighted_indexes An array of the fields names and their weight
     * @param int $offset An offset to use for the dump
     * @param int $limit A limit to use for the dump
     * @param bool $get_relations Get the dumped results relations (default is FALSE)
     * @param string $search_str A string to search
     * @param string $where_str A WHERE statement
     * @return array The result of the dump query with a 'points' value for each row
     */
    public function weightedDump(
        $weighted_indexes = array(),
        $offset = 0, $limit = null, $get_relations = false, $search_str = null, $where_str = null
    ) {
        $this->init();
        $db = $this->getStorageEngine();
        $db->clear();

        $query_select = '*';
        $weighters=array();
        $i=0;
        foreach($weighted_indexes as $ind=>$val) {
            $query_select .= ', '.$ind.' AS a'.$i;
            $weighters['a'.$i] = $val;
            $i++;
        }
        $weighters = array_flip($weighters);
        ksort( $weighters );
        $weighters = array_reverse($weighters, true);

        $db
            ->select( $query_select )
            ->from($this->_table_name);

        if (!empty($search_str)) {
            $ok = self::search( $search_str );
            if (false===$ok) return array();
        }
        if (!empty($where_str))
            $db->where( $where_str );
        $order_by='';
        foreach($weighters as $_i=>$weig)
            $order_by .= $weig.',';
        $db->order_by(rtrim($order_by, ','), 'asc');
        if (isset($limit))
            $db->limit($limit);
        if (isset($offset))
            $db->offset($offset);

        $results = $db->get();
        $rearranged_results=array();

        foreach($results as $__id=>$_object) {
            $points=0;
            foreach($weighted_indexes as $_ind=>$_val) {

                if ( preg_match('/(.*) '.$search_str.' (.*)/i', $_object[$_ind], $matches) ) {
                    $points += (count($matches)-1)*2;
                }

                if ( preg_match('/(.*)'.$search_str.'(.*)/i', $_object[$_ind], $matches) ) {
                    $points += count($matches)-1;
                }

            }
            $results[$__id]['points'] = $points;
            $rearranged_results[$__id] = $points;
        }
        rsort( $rearranged_results, SORT_NUMERIC );

        $new_results=array();
        foreach($rearranged_results as $j=>$_pts) {
            $new_results[] = $results[$j];
        }
        $results=$new_results;

        if ($get_relations===true) {
            $results_table=array();
            $fields_list = self::getFieldsList();
            $objects_table = array();
            foreach($results as $_id=>$object) {
                $_obj = null;
                $_obj = $this->getModelClone();
                $_obj->setId( $object['id'] );
                $_obj->setData( $object );
                $_obj->getRelations();
                $_objdat = $_obj->getData();
                $_objdat['id'] = $_obj->getId();
                $results_table[] = $_objdat;
            }
            return $results_table;
        }
        return $results;
    }

    /**
     * Search a string or value in table entries, depending of the fields format, with weighted results
     *
     * @param string $search_str A string to search
     * @param bool $standalone Clear the BDD query constructors (default is FALSE)
     * @return int The result of the search with a 'points' value for each row
     */
    public function weightedSearch($search_str = null, $standalone = false)
    {
        $this->init();
        if (!empty($search_str)) {
            $db = $this->getStorageEngine();
            if ($standalone===true)
                $db->clear();
            $search_str = $db->escape($search_str);
            $fields = self::getFieldsList();
            $search_fields = \CarteBlanche\Library\AutoObject\AutoObjectMapper::getFieldsByType( $this->table_name );
            if (is_numeric($search_str)) {
                foreach($search_fields['num'] as $_f)
                    $db->or_where_like($_f, $search_str);
            } elseif (is_string($search_str)) {
                foreach($search_fields['str'] as $_f)
                    $db->or_where_like($_f, '"%'.$search_str.'%"');
            }
        }
    }

    /**
     * Read a set of rows from the table for an array of IDs
     *
     * @param array $id The primary ID values to read
     * @param bool $get_relations Load the object relations (default is FALSE)
     * @param string $fields The string used in the select statement for the query (default is '*')
     * @return array The collection of data of objects if they exists
     */
    public function readCollection($ids = null, $get_relations = false, $fields = '*')
    {
        $this->init();
        if (!empty($ids) && is_array($ids)) {
            if ($this->exists() && $this->__loaded===true) {
                if ($get_relations===true && $this->__relations_loaded!==true)
                    $this->getRelations();
                return $this->getData();
            }
            $db = $this->getStorageEngine();
            $db->clear();
            $db
                ->select($fields)
                ->from($this->_table_name)
                ->where_in('id', $ids)
                ->limit( count($ids) );
            $objects = $db->get();

            if ($get_relations===true) {
//				$results_table = array();
                $results_table = new \Patterns\Commons\Collection();
                $fields_list = self::getFieldsList();
                $objects_table = array();
                foreach($results as $_id=>$object) {
                    $_obj = null;
                    $_obj = $this->getModelClone();
                    $_obj->setId( $object['id'] );
                    $_obj->setData( $object );
                    $_obj->getRelations();
                    $_objdat = $_obj->getData();
                    $_objdat['id'] = $_obj->getId();
//					$results_table[] = $_objdat;
                    $results_table->push( $_objdat );
                }
                return $results_table;
            }
            return $objects;
        }
        return null;
    }

    /**
     * Read a row from the table for a specific ID
     *
     * @param int $id The primary ID value to read
     * @param bool $get_relations Load the object relations (default is FALSE)
     * @param string $fields The string used in the select statement for the query (default is '*')
     * @return array The data of the object if it exists
     */
    public function read($id = null, $get_relations = false, $fields = '*')
    {
        $this->init();
        if (!empty($id)) {
            if ($this->exists() && $this->__loaded===true) {
                if ($get_relations===true && $this->__relations_loaded!==true)
                    $this->getRelations();
                return $this->getData();
            }
            $db = $this->getStorageEngine();
            $db->clear();
    /*
            $query = "SELECT {$fields} FROM {$this->_table_name} WHERE id={$id} LIMIT 1;";
            $object = $SQLITE->arrayQuery($query);
    */
            $db
                ->select($fields)
                ->from($this->_table_name)
                ->where('id', $id)
                ->limit(1);
            $object = $db->get_array();

            if (!empty($object[0])) {
                $this->setId($id);
                $this->setData($object[0]);
                $this->__loaded=true;
                if ($get_relations===true) $this->getRelations();
                return $this->getData();
            }
        }
        return null;
    }

    /**
     * Add a new row in the table
     *
     * @param array $data The new row values
     * @return bool TRUE if the new row had been added
     */
    public function create($data = null)
    {
        $this->init();

        // the 'pre_create' callback
        $cb = new DataCallback('pre_create', $data, $this->_table_fields);

        $this->setData($data);
        $db = $this->getStorageEngine();
        $insert_fields=$fields_list='';
        foreach($this->data as $_var=>$_val) {
            if (isset($this->_table_structure[$_var]) && $_var!='id') {
                $fields_list .= $_var.',';
                $insert_fields .= "'".$db->escape($_val)."',";
            }
        }

        $query = "INSERT INTO {$this->_table_name} ("
            .rtrim($fields_list, ',')
            .") VALUES ("
            .rtrim($insert_fields, ',')
            .");";
        $results = $db->query($query);
        $this->setId( $db->lastInsertId() );

        // the 'post_create' callback
        $cb = new DataCallback('post_create', $data, $this->_table_fields);

        return true;
    }

    /**
     * Update a row in the table
     *
     * @param int $id The primary ID value to update
     * @param array $data The new row values
     * @return bool TRUE if the new row had been updated
     */
    public function update($id = null, $data = null)
    {
        $this->init();

        // the 'pre_update' callback
        $cb = new DataCallback('pre_update', $data, $this->_table_fields);

        $this->read($id,false);
        $this->setData( $data );
        if (!empty($id)) {
            $db = $this->getStorageEngine();
            $update_str='';
            foreach($this->data as $_var=>$_val) {
                if (isset($this->_table_structure[$_var])) {
                    $update_str .= $_var."='".$db->escape($_val)."',";
                }
            }

            $query = "UPDATE {$this->_table_name} SET ".rtrim($update_str, ',')." WHERE id={$id};";
            $results = $db->query($query);
            $this->setId( $id );

            // the 'post_update' callback
            $cb = new DataCallback('post_update', $data, $this->_table_fields);

            return true;
        }
        return false;
    }

    /**
     * Delete a row from the table
     *
     * @param int $id The primary ID value to update
     * @return bool TRUE if the new row had been deleted
     */
    public function delete($id = null)
    {
        $this->init();
        if (!empty($id)) {
            // the 'pre_delete' callback
            $cb = new DataCallback('pre_delete', $data, $this->_table_fields);

            $query = "DELETE FROM {$this->_table_name} WHERE id={$id};";
            $db = $this->getStorageEngine();
            $results = $db->query($query);

            // the 'post_delete' callback
            $cb = new DataCallback('post_delete', $data, $this->_table_fields);

            return true;
        }
        return false;
    }

// ------------------------------------------
// Relations manager
// ------------------------------------------

    /**
     * Get the object relations table
     *
     * @return array The object relations table
     */
    public function getObjectRelations()
    {
        $this->_object_relations = \CarteBlanche\Library\AutoObject\AutoObjectMapper::getRelations( $this->_table_name );
        return $this->_object_relations;
    }

    /**
     * Build the relations between objects
     *
     * @param bool $full Get the full related object (default is TRUE)
     * @return object The whole object itself
     */
    public function getRelations($full = true)
    {
        if ($this->exists()) {
            $rels = $this->getObjectRelations();
            $obj_fields = $this->getFieldsList();
            if (!empty($rels))
            foreach($rels as $_field=>$_relation_table) {
                if ($_field=='parent') $__field = 'parent_id';
                else $__field = $_field;
                if (
                (in_array($__field, $obj_fields) && !empty($this->data[$__field])) OR
                !in_array($__field, $obj_fields)
            ) {
                    $this->fetchRelated( $_field, $full );
                }
            }
            $this->__relations_loaded=true;
            return $this;
        } else {
            trigger_error( "Searching relations of an empty object [{$this->_table_name}]!", E_USER_WARNING );
        }
    }

    /**
     * Find and fetch all related object
     *
     * @param string $relation The relation name to fetch
     * @param bool $full Get the full related object (default is TRUE)
     * @param string $fields The string used to get the related fields
     * @return bool TRUE if the related object(s) had been fetched, FALSE if an error occured
     */
    public function fetchRelated($relation = null, $full = true, $fields = null)
    {
        if (empty($relation)) return false;

        $rels = $this->getObjectRelations();
        $db = $this->getStorageEngine();
        $db = $db->getClone();
        if (isset($rels[$relation])) {
            if (isset($rels[$relation]['model'])) {
                if ($this->__relations_loaded===true && !empty($this->data[$relation]))
                    return $this->data[$relation];
                $_related_model = $rels[$relation]['model']->getModelClone();

                if (!empty($rels[$relation]['is_many']) && $rels[$relation]['is_many']==true) {
                    if ($full===true) $fields='*';
                    elseif (empty($fields)) {
                        $_slug = $_related_model->getSlugField();
                        $fields = 'id'.( !empty($_slug) ? ','.$_slug : '' );
                    }
                    $db->select('*');
                    $db->from( $_related_model->_table_name );
                    $db->where( $rels[$relation]['related_field'], $this->data[$rels[$relation]['internal_field']] );
                    $results = $db->get();

                    if ($results)
                    foreach($results as $row) {
                        $_relobj = $_related_model->getModelClone();
                        $_relobj->setId( $row['id'] );
                        $_relobj->setData( $row );
                        $this->setRelated( $relation, $_relobj->getData(), $_relobj->getId() );
                    }
                    return true;

                } else {
                    if ($full===true) $fields='*';
                    elseif (empty($fields)) {
                        $_slug = $_related_model->getSlugField();
                        $fields = 'id'.( !empty($_slug) ? ','.$_slug : '' );
                    }
                    $_related_model->read( $this->data[$rels[$relation]['internal_field']], false, $fields );

                    if ($_related_model->exists()) {
                        $this->setRelated( $relation, $_related_model->getData() );
                        return true;
                    }
                }
            }
        }

        return false;
    }

}

// Endfile