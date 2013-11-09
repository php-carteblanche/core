<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library\StorageEngine\DatabaseAdapter;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Library\StorageEngine\DatabaseAdapter\AbstractDatabaseAdapter;
use \CarteBlanche\Library\StorageEngine\DatabaseAdapter\DatabaseAdapterInterface;

/**
 * Database full driver
 *
 * SQLite DataBase driver
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class SqliteAdapter extends AbstractDatabaseAdapter implements DatabaseAdapterInterface
{

// ------------------------
// Properties
// ------------------------

	protected $db_name;
	protected $db_options;
	protected $database;
	protected $driver;

// ------------------------
// Construction
// ------------------------

	public function __construct(\CarteBlanche\Library\StorageEngine\Database $database, $db_name, array $db_options = null)
	{
	    $container = CarteBlanche::getContainer();
        $db_path = $container->get('kernel')->getPath('db_path');
        if (empty($db_path)) {
            $db_dir = $container->get('kernel')->getPath('var_dir').'db'.DIRECTORY_SEPARATOR;
            $db_path = $container->get('kernel')->getPath('var_path').'db'.DIRECTORY_SEPARATOR;
            $container->get('kernel')->addPath('db_dir', $db_dir);
            $container->get('kernel')->addPath('db_path', $db_path, true, true);
        }
//			$_altdb = $this->container->get('request')->getUrlArg('altdb');
//			return \AutoObject\AutoObjectMapper::getEntityManager( $_altdb );

		$this->database = $database;
		$this->db_name = $db_name;
		if (!empty($db_options) && is_array($db_options)) {
			$this->db_options = $db_options;
		} else {
			$this->db_options = array();
		}
		$this->db_options['path'] = $db_path;

//		$this->entity_manager = \CarteBlanche\Library\AutoObject\AutoObjectMapper::getEntityManager('default');
		$this->driver = $this->_buildDriver('sqlite', array(
		    $this->database, $this->db_name, $this->db_options
		));
/*
        $dbname = getContainer()->get('request')->getUrlArg('altdb');
        if (empty($dbname)) $dbname = 'default';
        $dbfile = $this->kernel->registry->getStackEntry('db_name', $dbname.'_db', 'config');
        return new \Lib\Database( _ROOTPATH._DBDIR.$dbfile, 0666 );
*/
	}

// ------------------------
// Connexion
// ------------------------

	/**
	 * Is the database installed
	 *
	 * @return bool
	 */
	public function isInstalled($db_name)
	{
		if (empty($dbname)) $dbname='default';
		return file_exists(CarteBlanche::getPath('db_path') . CarteBlanche::getConfig($dbname.'_database.db_name'));
	}

}

// Endfile