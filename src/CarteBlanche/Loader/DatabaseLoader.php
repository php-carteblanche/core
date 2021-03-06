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

namespace CarteBlanche\Loader;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Interfaces\DependencyLoaderInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class DatabaseLoader implements DependencyLoaderInterface
{

    /**
     * Instance loader
     *
     * @param   array                       $config
     * @param   \CarteBlanche\App\Container $container
     * @return  object
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
//      $_altdb = $this->container->get('request')->getUrlArg('altdb');
//      return \AutoObject\AutoObjectMapper::getEntityManager( $_altdb );
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