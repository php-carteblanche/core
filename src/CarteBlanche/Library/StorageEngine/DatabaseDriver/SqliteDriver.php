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

namespace CarteBlanche\Library\StorageEngine\DatabaseDriver;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Library\StorageEngine\DatabaseDriver\AbstractDatabaseDriver;
use \CarteBlanche\Library\StorageEngine\DatabaseDriver\DatabaseDriverInterface;

/**
 * Database full driver
 *
 * SQLite DataBase driver
 *
 * @TODO    rewrite the whole class with SQLite3
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class SqliteDriver
    extends AbstractDatabaseDriver
    implements DatabaseDriverInterface
{

// ------------------------
// Properties
// ------------------------

    protected $db_name;
    protected $db_options;
    protected $db;
    protected $db_driver;

// ------------------------
// Construction
// ------------------------

    public function __construct(\CarteBlanche\Library\StorageEngine\Database $db_driver, $db_name, $db_options = null)
    {
        $this->db_driver = $db_driver;
        $this->db_name = (isset($db_options['path']) ? $db_options['path'] : '').$db_name;
        if (!empty($db_options) && is_array($db_options))
            $this->db_options = $db_options;
        else
            $this->db_options = array();
        $this->_loadStatics();
    }

    protected function _loadStatics()
    {
        parent::_buildStatics(array(
            'assoc'=>SQLITE_ASSOC,
            'num'=>SQLITE_NUM,
            'both'=>SQLITE_BOTH
        ));
    }

// ------------------------
// Connexion
// ------------------------

    /**
     * Connexion to the BDD
     */
    public function connect()
    {
/*
        $this->db = new \SQLiteDatabase(
            $this->db_name,
            isset($this->db_options['chmod']) ? $this->db_options['chmod'] : 0644,
            $err
        );
        if ($err) throw new ErrorException(
            sprintf('Can not connect or create SQLite database "%s" [%s]!', $this->db_name, $err)
        );
*/
        try {
            $this->db = new \SQLite3($this->db_name);
        } catch (\Exception $e) {
            throw $e;
        }

        $this->db_name = str_replace(array(
            CarteBlanche::getPath('root_path'),
            CarteBlanche::getPath('db_dir')
        ), '', $this->db_name);
        return $this->db;
    }

    /**
     * Close the connexion to the BDD
     */
    public function disconnect()
    {
        sqlite_close( $this->db );
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
        if ($cached===true && $this->db_driver->isCachedQuery($query))
            return $this->db_driver->getCachedResults( $query );

        $err = null;
        $results = $this->db->singleQuery($query, $err);
        if ($err) trigger_error( "Error on query '{$query}' [$err]!", E_USER_ERROR);
        $this->db_driver->addCachedQuery( $query, $results );
        return $results;
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
        if (is_null($type)) $type = self::$DBADAPTER_BOTH;

        if ($cached===true && $this->db_driver->isCachedQuery($query))
            return $this->db_driver->getCachedResults( $query );

        $err = null;
        $results = $this->db->{$method}($query, $type, $err);
        if ($err) trigger_error( "Error on query '{$query}' [$err]!", E_USER_ERROR);
        $this->db_driver->addCachedQuery( $query, $results );
        return $results;
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
    public function arrayQuery($query = null, $method = 'arrayQuery', $cached = true )
    {
        if ($cached===true && $this->db_driver->isCachedQuery($query))
            return $this->db_driver->getCachedResults( $query );

        $err = null;
        $results = $this->db->{$method}($query, $err);
        if ($err) trigger_error( "Error on query '{$query}' [$err]!", E_USER_ERROR);
        $this->db_driver->addCachedQuery( $query, $results );
        return $results;
    }

    /**
     * Get the primary ID of the last insertion
     */
    public function lastInsertId()
    {
        return $this->db->lastInsertRowid();
    }

    /**
     * Escape a string using the internal PHP sqlite function
     *
     * @param string $str The string to escape
     * @param bool $double_quotes If true, the double-quotes will be doubled for escaping
     * @return string The escaped string
     * @see http://www.php.net/manual/en/function.sqlite-escape-string.php
     */
    static function escape($str = null, $double_quotes = false)
    {
        return sqlite_escape_string(
            true===$double_quotes ? str_replace('"', '""', $str) : $str
        );
    }

}

// Endfile