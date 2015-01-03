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

/**
 * The Database Driver interface
 *
 * Any database driver must implements this interface, and follow its constructor's rules
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface DatabaseDriverInterface
{

// ------------------------
// Construction
// ------------------------

    /**
     * Constructor with database name and some options
     *
     * @param object $db_manager The original DB manager object to build queries
     * @param string $db_name The name of the BDD to connect
     * @param array $db_options An array of options for the adapter
     */
    public function __construct(\CarteBlanche\Library\StorageEngine\Database $db_manager, $db_name, $db_options = null);

// ------------------------
// Connexion
// ------------------------

    /**
     * Connexion to the BDD
     */
    public function connect();

    /**
     * Close the connexion to the BDD
     */
    public function disconnect();

// ------------------------
// Query execution
// ------------------------

    /**
     * Execute a single query
     *
     * @param string $query The query string to execute
     * @param bool $cached If set to TRUE (default), the query results are first searched in the object cache
     */
    public function singleQuery($query = null, $cached = true);

    /**
     * Execute a query
     *
     * @param string $query The query string to execute
     * @param string $type The query type (see php documentation)
     * @param string $method The query method to use
     * @param bool $cached If set to TRUE (default), the query results are first searched in the object cache
     */
    public function query($query = null, $type = SQLITE_BOTH, $method = 'query', $cached = true);

    /**
     * Execute an array query
     *
     * @param string $query The query string to execute
     * @param string $method The query method to use
     * @param bool $cached If set to TRUE (default), the query results are first searched in the object cache
     */
    public function arrayQuery($query = null, $method = 'arrayQuery', $cached = true);

    /**
     * Get the primary ID of the last insertion
     */
    public function lastInsertId();

    /**
     * Escape a string using the internal PHP sqlite function
     *
     * @param string $str The string to escape
     * @param bool $double_quotes If true, the double-quotes will be doubled for escaping
     */
    static function escape($str = null, $double_quotes = false);

}

// Endfile