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

namespace CarteBlanche\Interfaces;

/**
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface QueryBuilderInterface
{

    /**
     * Build a query
     *
     * @param string $type
     */
    public function query_type($type = 'select');

    /**
     * Build a SELECT query
     *
     * @param mixed $fields The fields list to select, as a string for single field or as
     *              an array like `as => field_name`
     */
    public function select($fields = '*');

    /**
     * Build an UPDATE query
     *
     * @param mixed $values The fields list to update with values like `field_name => value`
     */
    public function update(array $values = null);

    /**
     * Build a DELETE query
     */
    public function delete();

    /**
     * Select the table of a query
     *
     * @param string $tables The table(s) name(s) to query on
     */
    public function from($tables = null);

    /**
     * Build the condition of a query
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @param   string  $sign       The sign used to build the WHERE statement (default is '=')
     * @param   string  $operator   An operator used if a WHERE statement was already set
     * @return  object  The whole object itself for method chaining
     */
    public function where($argument = null, $value = null, $sign = '=', $operator = 'OR');

    /**
     * Add a WHERE string to a query
     *
     * @param   string  $str        The full formed WHERE statement
     * @param   string  $operator   An operator used if a WHERE statement was already set
     * @return  object  The whole object itself for method chaining
     */
    public function where_str($str = null, $operator = 'OR');

    /**
     * Alias for a WHERE statement with a IN sign
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   array   $value      The array of values to select
     * @param   string  $operator   An operator used if a WHERE statement was already set
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function where_in($argument = null, $value = null, $operator = 'OR');

    /**
     * Alias for a WHERE statement with a MATCH sign
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function where_match($argument = null, $value = null);

    /**
     * Alias for a WHERE statement with a LIKE sign
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function where_like($argument = null, $value = null);

    /**
     * Alias for a OR WHERE statement
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @param   string  $sign       The sign used to build the WHERE statement (default is '=')
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function or_where($argument = null, $value = null, $sign = '=');

    /**
     * Alias for a OR WHERE statement with a MATCH sign
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function or_where_match($argument = null, $value = null);

    /**
     * Alias for a OR WHERE statement with a LIKE sign
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function or_where_like($argument = null, $value = null);

    /**
     * Alias for a AND WHERE statement
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @param   string  $sign       The sign used to build the WHERE statement (default is '=')
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function and_where($argument = null, $value = null, $sign = '=');

    /**
     * Alias for a AND WHERE statement with a MATCH sign
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function and_where_match($argument = null, $value = null);

    /**
     * Alias for a AND WHERE statement with a LIKE sign
     *
     * @param   string  $argument   The argument of the WHERE statement
     * @param   string  $value      The value of the argument
     * @return  object  The whole object itself for method chaining
     * @see     self::where()
     */
    public function and_where_like($argument = null, $value = null);

    /**
     * Set a limit value for a query
     *
     * @param   int     $limit      The limit value to use
     * @param   int     $offset     The offset value to use
     * @return  object  The whole object itself for method chaining
     */
    public function limit($limit = null, $offset = null);

    /**
     * Set an offset value for a query limited
     *
     * @param   int     $offset     The offset value to use
     * @return  object  The whole object itself for method chaining
     */
    public function offset($offset = null);

    /**
     * Set the ORDER BY condition of a query
     *
     * @param   string  $order_by   The field name to order on
     * @param   string  $order_way  The way for ordering (asc or desc - default is 'asc')
     * @return  object  The whole object itself for method chaining
     */
    public function order_by($order_by = null, $order_way = 'asc');

    /**
     * Global query construction using the query constructors values
     *
     * @return  string  A full query string
     */
    public function get_query();

}

// Endfile