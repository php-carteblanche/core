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

namespace CarteBlanche\Library\StorageEngine\DatabaseAdapter;

/**
 * The Database Adpater interface
 *
 * Any database driver must implements this interface, and follow its constructor's rules
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface DatabaseAdapterInterface
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
    public function __construct(\CarteBlanche\Library\StorageEngine\Database $database, $db_name, array $db_options = null);

// ------------------------
// Driver
// ------------------------

    /**
     * Construction of the database driver
     *
     * @param array $params
     *
     * @return object \CarteBlanche\Library\StorageEngine\DatabaseDriver\DatabaseDriverInterface
     */
    public function getDriver(array $params = null);

    /**
     * Is the database installed
     *
     * @return bool
     */
    public function isInstalled($db_name);

}

// Endfile