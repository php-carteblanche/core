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

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Library\StorageEngine\DatabaseAdapter\AbstractDatabaseAdapter;
use \CarteBlanche\Library\StorageEngine\DatabaseAdapter\DatabaseAdapterInterface;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * Database full driver
 *
 * SQLite DataBase driver
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
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
        $db_path = $container->get('kernel')->getPath('db_dir');
        if (empty($db_path)) {
            $db_path = DirectoryHelper::slashDirname($container->get('kernel')->getPath('var_dir'))
                .'db'.DIRECTORY_SEPARATOR;
            $container->get('kernel')->addPath('db_dir', $db_path, true, true);
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
        return file_exists(CarteBlanche::getFullPath('db_dir') . CarteBlanche::getConfig($dbname.'_database.db_name'));
    }

}

// Endfile