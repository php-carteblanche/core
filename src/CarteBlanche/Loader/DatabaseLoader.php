<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Loader;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class DatabaseLoader implements DependencyLoaderInterface
{

	/**
	 * Instance loader
	 *
	 * @param array $config
	 *
	 * @return object
	 */
    public function load(array $config = null, \CarteBlanche\App\Container $container)
    {
        $config = $container->get('kernel')->getPath('default_em');
        if (empty($db_path)) {
            $db_dir = $container->get('kernel')->getPath('var_dir').'db'.DIRECTORY_SEPARATOR;
            $db_path = $container->get('kernel')->getPath('var_path').'db'.DIRECTORY_SEPARATOR;
            $container->get('kernel')->addPath('db_dir', $db_dir);
            $container->get('kernel')->addPath('db_path', $db_path, true, true);
        }
//			$_altdb = $this->container->get('request')->getUrlArg('altdb');
//			return \AutoObject\AutoObjectMapper::getEntityManager( $_altdb );
        return \CarteBlanche\Library\AutoObject\AutoObjectMapper::getEntityManager('default');
/*
        $dbname = getContainer()->get('request')->getUrlArg('altdb');
        if (empty($dbname)) $dbname = 'default';
        $dbfile = $this->kernel->registry->getStackEntry('db_name', $dbname.'_db', 'config');
        return new \Lib\Database( _ROOTPATH._DBDIR.$dbfile, 0666 );
*/
    }

}

// Endfile