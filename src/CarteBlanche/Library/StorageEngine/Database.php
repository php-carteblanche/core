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

namespace CarteBlanche\Library\StorageEngine;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Interfaces\StorageEngineInterface;
use \CarteBlanche\Interfaces\QueryBuilderInterface;

/**
 * Database full abstraction class
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Database
    implements StorageEngineInterface, QueryBuilderInterface
{

    /**
     * The current database connexion
     */
    var $db;

    /**
     * An array of the queries executed on the database
     */
    protected $queries=array();

    /**
     * An array of the queries and their results for caching
     */
    protected $queries_results=array();

    /**
     * An array of the database tables
     */
    var $tables=array();

    /**
     * The current database name
     */
    private $__db_name;

    /**
     * The current database type adapter object
     */
    protected $__adapter;

    /**
     * The adapter object options passed to constructor
     */
    protected $__adapter_options;

    /**
     * The adapter object name
     */
    protected $__adapter_type='sqlite';

    /**
     * QueryConstruct : the query type
     */
    var $query_type='SELECT';

    /**
     * QueryConstruct : the fields on to query
     */
    var $fields;

    /**
     * QueryConstruct : the table(s) name
     */
    var $from;

    /**
     * QueryConstruct : the where clause
     */
    var $where='';

    /**
     * QueryConstruct : the limit condition
     */
    var $limit;

    /**
     * QueryConstruct : the offset for the query
     */
    var $offset;

    /**
     * QueryConstruct : the order by condition
     */
    var $order_by;

    /**
     * QueryConstruct : the order way condition
     */
    var $order_way;

    /**
     * Construction : connexion to the database
     *
     * @param array $options An array of options passed to the adapter
     */
    public function __construct(array $options)
    {
        $this->__adapter_options= $options;
        if (isset($options['db_name'])) {
            $this->__db_name = $options['db_name'];
        }
        if (isset($options['adapter'])) {
            $this->__adapter_type = $options['adapter'];
        }
        if (!empty($this->__db_name)) {
            self::_loadAdapter();
        }
    }

    /**
     * Load the database adapter
     *
     * @param bool $autoconnect Build the connexion after adapter construction
     * @return self $this for method chaining
     */
    protected function _loadAdapter($autoconnect = true)
    {
        $factory = \Library\Factory::create()
            ->factoryName(__CLASS__)
            ->mustImplementAndExtend(array(
                Kernel::DATABASE_ADAPTER_ABSTRACT,
                Kernel::DATABASE_ADAPTER_INTERFACE
            ))
            ->defaultNamespace(Kernel::DATABASE_ADAPTER_DEFAULT_NAMESPACE)
            ->classNameMask(array('%s', '%s'.Kernel::DATABASE_ADAPTER_SUFFIX))
            ;
        $params = array(
            $this, $this->__db_name, $this->__adapter_options
        );
        $this->__adapter = $factory->build($this->__adapter_type, $params);
    /*
        $_adapter_class = '\Lib\DatabaseAdapter\\'.ucfirst($this->__adapter_type);
        if (!\CarteBlanche\App\Loader::classExists($_adapter_class)) {
            $_adapter_class .= 'Adapter';
        }
        if (\CarteBlanche\App\Loader::classExists($_adapter_class)) {
            try {
                $this->__adapter = new $_adapter_class(
                    $this, CarteBlanche::getPath('db_path').$this->__db_name, $this->__adapter_options
                );
            } catch ( \Exception $e ) {
                throw new \RuntimeException(
                    sprintf('An error occured while trying to load Database Adapter "%s"!', $_adapter_class)
                );
            }
            if (!($this->__adapter instanceof \Lib\DatabaseAdapter\AbstractDatabaseAdapter)) {
                throw new \DomainException(
                    sprintf('A Database Adapter must extend the "\Lib\DatabaseAdapter\AbstractDatabaseAdapter" class (got "%s")!', $_adapter_class)
                );
            }
            if (!($this->__adapter instanceof \Lib\DatabaseAdapter\DatabaseAdapterInterface)) {
                throw new \DomainException(
                    sprintf('A Database Adapter must implements the "\Lib\DatabaseAdapter\DatabaseAdapterInterface" interface (got "%s")!', $_adapter_class)
                );
            }
        } else {
            throw new \RuntimeException(
                sprintf('Database adapter for type "%s" doesn\'t exist!', $this->__adapter_type)
            );
        }
    */
        if (true===$autoconnect) $this->connect();
        return $this;
    }

    /**
     * Get the current storage engine adapter
     *
     * @return object
     */
    public function getAdapter()
    {
        return $this->__adapter;
    }

    /**
     * Connexion to the database
     */
    protected function connect()
    {
        if (empty($this->__adapter)) self::_loadAdapter();
        $this->db = $this->__adapter->connect();
        return $this;
    }

    /**
     * Close the connexion to the database
     */
    public function disconnect()
    {
        if (!empty($this->__adapter))
            $this->__adapter->disconnect();
        return $this;
    }

    /**
     * Get a connexion clone, query constructors are empty
     *
     * @return object A copy of the current object
     */
    public function getClone()
    {
        $db = clone $this;
        $db->clear();
        return $db;
    }

    /**
     * Get current connexion
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Get current database name
     */
    public function getDbName()
    {
        return $this->__db_name;
    }

    /**
     * Get the object cache
     *
     * @return array The cached queries
     */
    public function getCache()
    {
        return $this->queries;
    }

    /**
     * Add a query and its results in the object cache
     *
     * @param string $query The query string to cache
     * @param mixed $results The results of the query execution to cache
     * @return self $this for method chaining
     */
    public function addCachedQuery($query, $results = null)
    {
        $this->queries[] = $query;
        if (!is_null($results))
            $this->queries_results[$query] = $results;
        return $this;
    }

    /**
     * Test if a query is in the object cache
     *
     * @param string $query The query string to search in the cache
     * @return bool True if the query is cached, false otherwise
     */
    public function isCachedQuery( $query )
    {
        return array_key_exists($query, $this->queries_results);
    }

    /**
     * Get a query from the object cache if so
     *
     * @param string $query The query string to search in the cache
     * @return mixed The results cached if so, false otherwise
     */
    public function getCachedResults( $query )
    {
        return isset($this->queries_results[$query]) ? $this->queries_results[$query] : false;
    }

// ------------------------
// Query execution
// ------------------------

    /**
     * Execute a single query
     *
     * @param string $query The query string to execute
     * @param bool $cached If set to TRUE (default), the query results are first searched in the object cache
     * @return mixed The query result(s)
     * @see http://www.php.net/manual/en/function.sqlite-single-query.php
     */
    public function singleQuery($query = null, $cached = true)
    {
        if (empty($this->__adapter)) self::_loadAdapter();
        return $this->__adapter->singleQuery( $query, $cached );
    }

    /**
     * Execute a query
     *
     * @param string $query The query string to execute
     * @param string $type The query type (see php documentation)
     * @param string $method The query method to use
     * @param bool $cached If set to TRUE (default), the query results are first searched in the object cache
     * @return mixed The query result(s)
     * @see http://www.php.net/manual/en/function.sqlite-query.php
     */
    public function query($query = null, $type = null, $method = 'query', $cached = true)
    {
        if (empty($this->__adapter)) self::_loadAdapter();
        return $this->__adapter->query( $query, $type, $method, $cached );
    }

    /**
     * Execute an array query
     *
     * @param string $query The query string to execute
     * @param string $method The query method to use
     * @param bool $cached If set to TRUE (default), the query results are first searched in the object cache
     * @return mixed The query result(s)
     * @see http://www.php.net/manual/en/function.sqlite-array-query.php
     */
    public function arrayQuery($query = null, $method = 'arrayQuery', $cached = true)
    {
        if (empty($this->__adapter)) self::_loadAdapter();
        return $this->__adapter->arrayQuery( $query, $method, $cached );
    }

    /**
     * Get the primary ID of the last insertion
     */
    public function lastInsertId()
    {
        if (empty($this->__adapter)) self::_loadAdapter();
        return $this->__adapter->lastInsertId();
    }

// ------------------------
// Meta queries execution
// ------------------------

    /**
     * Add a table in the database
     *
     * @param string $tablename The new table name
     * @param array $table_structure An array of the table fields
     * @return bool TRUE if the table had been created, FALSE if an error occured
     */
    public function add_table($tablename = null, $table_structure = null)
    {
        if (empty($tablename) || empty($table_structure)) return false;

        if (!is_string($table_structure))
            $table_structure_str = join(',', self::build_fields_array( $table_structure ) );
        else
            $table_structure_str = $table_structure;
        if (!self::table_exists($tablename)) {
            $query = "CREATE TABLE $tablename ($table_structure_str);";
    /*
            $results = $this->db->queryExec($query, $err);
            if ($err) trigger_error( "Can not create table '$tablename' [$err]!", E_USER_ERROR );
    */
            $results = $this->query($query, null, 'queryExec');
            $this->addCachedQuery( $query, $results );
        }
        return self::table_exists($tablename);
    }

    /**
     * Add fields in a table
     *
     * @param string $tablename The new table name
     * @param array $table_structure An array of the table fields
     * @return bool TRUE if the field had been added, FALSE if an error occured
     */
    public function add_fields($tablename = null, $table_structure = null)
    {
        if (empty($tablename) || empty($table_structure)) return false;

        if (!is_string($table_structure))
            $table_structure_ar = self::build_fields_array( $table_structure );
        else
            $table_structure_ar = array($table_structure);

        if (self::table_exists($tablename)) {
            $query = "ALTER TABLE $tablename ADD COLUMN ( ".join(',', $table_structure_ar)." );";
    /*
            $this->queries[] = $query;
            $results = $this->db->queryExec($query, $err);
            if ($err) trigger_error( "Can not alter table '$tablename' [$err]!", E_USER_ERROR );
    */
            $results = $this->query($query, null, 'queryExec');
            $this->addCachedQuery( $query );
        }
        return true;
    }

    /**
     * Get infos of a table in the database
     *
     * @param string $tablename The new table name
     * @return array The request result row
     */
    public function table_infos($tablename = null)
    {
        if (!empty($tablename)) {
            if (isset($this->tables[$tablename]) && is_array($this->tables[$tablename]))
                return $this->tables[$tablename];
            $query = "PRAGMA table_info ('{$tablename}')";
            $results = $this->arrayQuery($query);
            $this->tables[$tablename] = $results;
            return $results;
        }
        return false;
    }

    /**
     * Check if a table exists in the database
     *
     * @param string $tablename The new table name
     * @return bool TRUE if the table exists, FALSE otherwise
     */
    public function table_exists($tablename = null)
    {
        if (!empty($tablename)) {
            if (isset($this->tables[$tablename])) return true;
            $query = "SELECT name FROM sqlite_master WHERE name='{$tablename}'";
            $results = $this->query($query);
            if ($results && $results->numRows()) {
                $this->tables[$tablename] = true;
                return true;
            }
        }
        return false;
    }

    /**
     * Build a query string to add a field in a table
     *
     * @param array $table_structure The structure of the field
     * @return string The query string to create the field
     */
    public function build_fields_array($table_structure = null)
    {
        if (empty($table_structure) || !is_array($table_structure)) return;
        $table_structure_ar=array();
        foreach($table_structure as $index=>$form) {
            if (is_string($form))
                $table_structure_ar[] = $index.' '.$form;
            else
                $table_structure_ar[] = $index.' '.$form['type'].' '
                    .( (!empty($form['null']) && $form['null']==true) ? 'null' : 'not null').' '
                    .(!empty($form['default']) ? 'default \''.$form['default'].'\'' : '').' '
                    .(!empty($form['index']) ? $form['index'] : '');
        }
        return $table_structure_ar;
    }

// ------------------------
// Utilities
// ------------------------

    /**
     * Escape a string using the internal PHP adapter function
     *
     * @param string $str The string to escape
     * @param bool $double_quotes If true, the double-quotes will be doubled for escaping
     * @return string The escaped string
     */
    public function escape($str = null, $double_quotes = false)
    {
        if (empty($this->__adapter)) self::_loadAdapter();
        return $this->__adapter->escape($str);
    }

    /**
     * Clear all query constructors
     */
    public function clear()
    {
        $this->query_type='SELECT';
        $this->fields=null;
        $this->from=null;
        $this->where='';
        $this->limit=null;
        $this->offset=null;
        $this->order_by=null;
        $this->order_way=null;
    }

// ------------------------
// Query constructors
// ------------------------

    /**
     * Build a query
     *
     * @param string $type
     */
    public function query_type($type = 'select')
    {
        $this->query_type = strtoupper($type);
        return $this;
    }

    /**
     * Build a select query
     *
     * @param string $fields The fields list to select
     * @return object The whole object itself for method chaining
     */
    public function select($fields = '*')
    {
        $this->query_type('select');
        $this->fields = $fields;
        return $this;
    }

    /**
     * Build an UPDATE query
     *
     * @param mixed $values The fields list to update with values like `field_name => value`
     */
    public function update(array $values = null)
    {
        $this->query_type('update');
        return $this;
    }

    /**
     * Build a DELETE query
     */
    public function delete()
    {
        $this->query_type('delete');
        return $this;
    }

    /**
     * Select the table of a query
     *
     * @param string $table The table(s) name(s) to query on
     * @return object The whole object itself for method chaining
     */
    public function from($table = null)
    {
        $this->from = $table;
        return $this;
    }

    /**
     * Build the condition of a query
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @param string $sign The sign used to build the WHERE statement (default is '=')
     * @param string $operator An operator used if a WHERE statement was already set
     * @return object The whole object itself for method chaining
     */
    public function where($arg = null, $val = null, $sign = '=', $operator = 'OR')
    {
        $this->where .=
            (strlen($this->where) ? " {$operator} " : '')
            .$arg
            .( !empty($val) ? " {$sign} ".self::escape($val) : '' );
        return $this;
    }

    /**
     * Add a WHERE string to a query
     *
     * @param string $str The full formed WHERE statement
     * @param string $operator An operator used if a WHERE statement was already set
     * @return object The whole object itself for method chaining
     */
    public function where_str($str = null, $operator = 'OR')
    {
        $this->where .=
            (strlen($this->where) ? " {$operator} " : '')
            .$str;
        return $this;
    }

    /**
     * Alias for a WHERE statement with a IN sign
     *
     * @param string $arg The argument of the WHERE statement
     * @param array $val The array of values to select
     * @param string $operator An operator used if a WHERE statement was already set
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function where_in($arg = null, $val = null, $operator = 'OR')
    {
        $this->where .=
            (strlen($this->where) ? " {$operator} " : '')
            .$arg
            .( !empty($val) && is_array($val) ? " IN (".self::escape( implode(',',$val) ).")" : '' );
        return $this;
    }

    /**
     * Alias for a WHERE statement with a MATCH sign
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function where_match($arg = null, $val = null)
    {
        return self::where($arg, $val, 'MATCH');
    }

    /**
     * Alias for a WHERE statement with a LIKE sign
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function where_like($arg = null, $val = null)
    {
        return self::where($arg, $val, 'LIKE');
    }

    /**
     * Alias for a OR WHERE statement
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @param string $sign The sign used to build the WHERE statement (default is '=')
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function or_where($arg = null, $val = null, $sign = '=')
    {
        return self::where($arg, $val, $sign, 'OR');
    }

    /**
     * Alias for a OR WHERE statement with a MATCH sign
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function or_where_match($arg = null, $val = null)
    {
        return self::or_where($arg, $val, 'MATCH');
    }

    /**
     * Alias for a OR WHERE statement with a LIKE sign
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function or_where_like($arg = null, $val = null)
    {
        return self::or_where($arg, $val, 'LIKE');
    }

    /**
     * Alias for a AND WHERE statement
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @param string $sign The sign used to build the WHERE statement (default is '=')
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function and_where($arg = null, $val = null, $sign = '=')
    {
        return self::where($arg, $val, $sign, 'AND');
    }

    /**
     * Alias for a AND WHERE statement with a MATCH sign
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function and_where_match($arg = null, $val = null)
    {
        return self::and_where($arg, $val, 'MATCH');
    }

    /**
     * Alias for a AND WHERE statement with a LIKE sign
     *
     * @param string $arg The argument of the WHERE statement
     * @param string $val The value of the argument
     * @return object The whole object itself for method chaining
     * @see self::where()
     */
    public function and_where_like($arg = null, $val = null)
    {
        return self::and_where($arg, $val, 'LIKE');
    }

    /**
     * Set a limit value for a query
     *
     * @param int $limit The limit value to use
     * @param int $offset The offset value to use
     * @return object The whole object itself for method chaining
     */
    public function limit($limit = null, $offset = null)
    {
        $this->limit = $limit;
        if ($offset) $this->offset($offset);
        return $this;
    }

    /**
     * Set an offset value for a query limited
     *
     * @param int $offset The offset value to use
     * @return object The whole object itself for method chaining
     */
    public function offset($offset = null)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set the ORDER BY condition of a query
     *
     * @param string $order_by The field name to order on
     * @param string $order_way The way for ordering (asc or desc - default is 'asc')
     * @return object The whole object itself for method chaining
     */
    public function order_by($order_by = null, $order_way = 'asc')
    {
        $this->order_by = $order_by;
        $this->order_way = $order_way;
        return $this;
    }

    /**
     * Global query construction using the query constructors values
     *
     * @return string A full query string
     */
    public function get_query()
    {
        $query =
            strtoupper($this->query_type)
            .' '.$this->fields
            .' FROM '.$this->from
            .( !empty($this->where) ?
                ' WHERE '.$this->where : '' )
            .( !empty($this->order_by) ?
                ' ORDER BY '.$this->order_by.' '.$this->order_way
                : '' )
            .( !empty($this->limit) ?
                ' LIMIT '.$this->limit
    //				.( !empty($this->offset) ? ','.$this->offset : '' )
                : '' )
            .( !empty($this->offset) ?
                ' OFFSET '.$this->offset
                : '' )
            .';';
        return $query;
    }

// ------------------------
// Queries aliases
// ------------------------

    /**
     * Execute the query constructing it with the query constructors values
     *
     * @return mixed The query results
     */
    public function get()
    {
        $query = $this->get_query();
        $results = $this->query($query);
        return $results->fetchAll();
    }

    /**
     * Execute a single query constructing it with the query constructors values
     *
     * @return mixed The query result as a single value
     */
    public function get_single()
    {
        $query = $this->get_query();
        $results = $this->singleQuery($query);
        return isset($results[0]) ? $results[0] : null;
    }

    /**
     * Execute an array query constructing it with the query constructors values
     *
     * @return mixed The query results as array
     */
    public function get_array()
    {
        $query = $this->get_query();
        $results = $this->arrayQuery($query);
        return $results;
    }

}

// Endfile