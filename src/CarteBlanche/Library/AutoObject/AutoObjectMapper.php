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

namespace CarteBlanche\Library\AutoObject;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Locator;
use \Patterns\Abstracts\AbstractSingleton;
use \Patterns\Commons\Registry;

/**
 */
class AutoObjectMapper extends AbstractSingleton
{

	protected $registry;

	protected function init()
	{
		$this->registry = new Registry;
		$this->registry->setEntry('entityManager', array());
		$this->registry->setEntry('objectStructure', array());
	}

	/**
	 * Get an entityManager
	 * @param string $emname The name of the entity manager to search
	 * @return object Returns an object to manage the entity
	 */
	static function getEntityManager($emname)
	{
		$_this = self::getInstance();
		$emname = $_this->buildEntityName($emname);

		$known_manager = $_this->registry->getEntry($emname, 'entityManager');
		if ($known_manager) {
			return $known_manager;
		} else {
//			$manager = $_this->buildEntityManager($emname);
    	    $manager = CarteBlanche::getContainer()->get('entity_manager')
	            ->getStorageEngine($emname);
			$_this->registry->setEntry($emname, $manager, 'entityManager');
			return $manager;
		}
	}

	/**
	 * Get the objects structure from a specific database
	 * @param string $dbname The name of the database to search
	 * @return array Returns an array of the objects structure (FALSE if nothing is found)
	 */
	static function getObjectsStructure($dbname = null) 
	{
		$_this = self::getInstance();
		$dbname = $_this->buildEntityName($dbname);

		$known_structure = $_this->registry->getEntry($dbname, 'objectStructure');
		if ($known_structure) {
			return $known_structure;
		} else {
			$structure = $_this->buildObjectsStructure($dbname);
			$_this->registry->setEntry($dbname, $structure, 'objectStructure');
			return $structure;
		}
	}

	/**
	 * Get a table entry from a specific database
	 * @param string $tablename The name of the table to search
	 * @param string $dbname The name of the database to search
	 * @return array Returns an array of the table structure (FALSE if nothing is find)
	 */
	static function getAutoObject($tablename = null, $dbname = null) 
	{
		if (empty($tablename)) return false;
		$tables = self::getObjectsStructure( $dbname );
		if ($tables) {
			foreach ($tables as $_tbl_name=>$_tbl_obj) {
				if ($_tbl_obj->getTableName()==$tablename) {
					return $_tbl_obj;
				}
			}
		}
		return false;
	}

	/**
	 * Get a table structure from a specific database
	 * @param string $tablename The name of the table to search
	 * @param string $dbname The name of the database to search
	 * @return array Returns an array of the table structure (FALSE if nothing is find)
	 */
	static function getTableStructure($tablename = null, $dbname = null) 
	{
		$_tbl_obj = self::getAutoObject( $tablename, $dbname );
		if (!empty($_tbl_obj)) {
			return $_tbl_obj->getStructureEntry('structure');
		}
		return false;
	}

	/**
	 * Get an auto-object model
	 * @param string $tablename The name of the table to search
	 * @param string $dbname The name of the database to search
	 * @return array Returns an instance of the model (FALSE if nothing is find)
	 */
	static function getModel($tablename = null, $dbname = null) 
	{
		$_tbl_obj = self::getAutoObject( $tablename, $dbname );
		if (!empty($_tbl_obj)) {
			return $_tbl_obj->getModel();
		}
		return false;
	}

	/**
	 * Get an auto-object fields list organized by fields types
	 * @param string $tablename The name of the table to search
	 * @param string $dbname The name of the database to search
	 * @return array Returns an array of the fields organized by type (FALSE if nothing is find)
	 */
	static function getFieldsByType($tablename = null, $dbname = null) 
	{
		$_tbl_obj = self::getAutoObject( $tablename, $dbname );
		if (!empty($_tbl_obj)) {
			return $_tbl_obj->getFieldsByType();
		}
		return false;
	}

	/**
	 * Get an auto-object relations array
	 * @param string $tablename The name of the table to search
	 * @param string $dbname The name of the database to search
	 * @return array Returns an array of the relations of the object (FALSE if nothing is find)
	 */
	static function getRelations($tablename = null, $dbname = null) 
	{
		$_tbl_obj = self::getAutoObject( $tablename, $dbname );
		if (!empty($_tbl_obj)) {
			return $_tbl_obj->getRelations();
		}
		return false;
	}

// ------------------
// Entities Builders
// ------------------

	/**
	 * Transform the name of an entity (skipping '..._db' and '..._em' suffix or retrieving it if '..._base')
	 * @param string $dbname The name of the database to search
	 * @return string The re-formed name if so
	 */
	protected function buildEntityName($name = null) 
	{
		if (empty($name)) {
			$_name = CarteBlanche::getContainer()->get('request')->getUrlArg('altdb');
  			if (!empty($_name))
  				$name = $_name;
  			else
				$name = 'default';
		}
		if (substr_count($name, '_base')) {
  			$_name = CarteBlanche::getKernel()->getRegistry()->getStackKey( $name, 'db_name', 'config' );
  			if (empty($_name))
	  			$_name = CarteBlanche::getKernel()->getRegistry()->getStackKey( $name, 'db_name', 'app' );
  			if (!empty($_name))
  				$name = $_name;
  		}
		return str_replace(array('_database', '_entity_manager', '_base'), '', $name);
	}
	
	/**
	 * Build a specific entity manager
	 * @param string $emname The name of the entity manager to build
	 * @return object Returns an object to manage the entity
	 */
	protected function buildEntityManager($emname = 'default') 
	{
		$em_cfg = CarteBlanche::getConfig($emname.'_entity_manager');

		if (!isset($em_cfg['class'])) {
			throw new \RuntimeException(
				'An entity manager configuration must define a class name!'
			);
		}
		$em_class = $em_cfg['class'];

		if (!isset($em_cfg['options'])) {
			throw new \RuntimeException(
				'An entity manager configuration must define an options array!'
			);
		}

		if (\CarteBlanche\App\Loader::classExists($em_class)) {
			try {
				$em = new $em_class( $em_cfg['options'] );
			} catch ( \Exception $e ) {
				throw new \RuntimeException(
					sprintf('An error occured while trying to load entity manager "%s"!', $em_class)
				);
			}
			if (!($em instanceof \CarteBlanche\Interfaces\EntityManagerInterface)) {
				throw new \DomainException(
					sprintf('An entity manager must implements the "\CarteBlanche\Interfaces\EntityManagerInterface" interface (in "%s")!', $em_class)
				);
			}
		} else {
			throw new \RuntimeException(
				sprintf('Entity manager named "%s" doesn\'t exist!', $em_class)
			);
		}
		return $em;
	}

	/**
	 * Build the objects structure from a specific entity
	 * @param string $dbname The name of the database to build
	 * @return array Returns an array of the objects structure (FALSE if nothing is found)
	 */
	protected function buildObjectsStructure($dbname = null) 
	{
		$dbname = str_replace(array(CarteBlanche::getPath('root_path'), CarteBlanche::getPath('config_dir')), '', $dbname);
		$tables_def = CarteBlanche::getConfig($dbname.'_database.db_structure');
		$tables_def_file = Locator::locateConfig($tables_def);

		if ($tables_def_file && @file_exists($tables_def_file) && @is_file($tables_def_file)) {
			$table = array();
			include $tables_def_file;
			if (!empty($tables)) {
				$tables_stacks=array();
				foreach ($tables as $_tbl) {
					$_t_n = $_tbl['table'];
					$tables_stacks[$_t_n] = new \CarteBlanche\Library\AutoObject\AutoObject( $_t_n, $_tbl, $_t_n, $dbname );
					$this->registry->setEntry(
						$_tbl['table'], 
						$tables_stacks[$_t_n],
						$dbname
					);
				}
				return $tables_stacks;
			} else
				throw new \RuntimeException( 
					sprintf('Tables definition can\'t be found in file "%s"!', $tables_def)
				);
		} else
			throw new \RuntimeException( 
				sprintf('Tables definition file "%s" can\'t be found!', $tables_def)
			);
	}

}

// Endfile