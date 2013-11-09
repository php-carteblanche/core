<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library\StorageEngine\DatabaseAdapter;

/**
 * The Database Adpater interface
 *
 * Any database driver must implements this interface, and follow its constructor's rules
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
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