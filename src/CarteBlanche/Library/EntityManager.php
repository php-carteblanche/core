<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\Interfaces\EntityManagerInterface;
use \Library\Helper\Text as TextHelper;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class EntityManager implements EntityManagerInterface
{

	/**
	 * Construction : 1 single argument
	 * @param array $options A table of options for the manager
	 */
	public function __construct(array $options = null)
	{
	}

// -------------------------
// Repositories management
// -------------------------
	
    const REPOSITORIES_CONTAINER_NAME = 'repositories';

    public function getRepository($entity_name)
    {
        $_name = self::buildIndex($entity_name);

        $existing = CarteBlanche::getContainer()->get(self::REPOSITORIES_CONTAINER_NAME.'.'.$_name);
        if (!empty($existing)) {
            return $existing;
        }

        $repo = $this->_buildRepository($_name);
        CarteBlanche::getContainer()
            ->set(self::REPOSITORIES_CONTAINER_NAME.'.'.$_name, $repo);
        return $repo;
    }
	
    protected function _buildRepository($name)
    {
		$em_cfg = CarteBlanche::getConfig($name);

		if (isset($em_cfg['class'])) {
    		$repo_class = $em_cfg['class'];
		} else {
			throw new \RuntimeException(
				sprintf('A repository configuration must define a class name ("%s")!', $name)
			);
		}

    	$repo_options = isset($em_cfg['options']) ? $em_cfg['options'] : array();

        if (!empty($repo_class)) {
            $factory = \Library\Factory::create()
                ->factoryName('EntityManager::Repository')
                ->mustImplement(Kernel::REPOSITORY_INTERFACE)
                ;
            return $factory->build($repo_class, $repo_options);
        }
        return null;
    }
    
// -------------------------
// Storage Engines management
// -------------------------
	
    const STORAGE_ENGINES_CONTAINER_NAME = 'storage_engines';

    public function getStorageEngine($name = 'default')
    {
        $fullname = $name.'_storage_engine';
        $_name = self::buildIndex($fullname);

        $existing = CarteBlanche::getContainer()->get(self::STORAGE_ENGINES_CONTAINER_NAME.'.'.$_name);
        if (!empty($existing)) {
            return $existing;
        }

        $se = $this->_buildStorageEngine($_name);
        CarteBlanche::getContainer()
            ->set(self::STORAGE_ENGINES_CONTAINER_NAME.'.'.$_name, $se);
        return $se;
    }

    protected function _buildStorageEngine($name)
    {
		$em_cfg = CarteBlanche::getConfig($name);

		if (isset($em_cfg['class'])) {
    		$em_class = $em_cfg['class'];
		} else {
			throw new \RuntimeException(
				sprintf('A storage engine configuration must define a class name (for "%s")!', $name)
			);
		}

		if (isset($em_cfg['options'])) {
    		$em_options = $em_cfg['options'];
		} else {
			throw new \RuntimeException(
				sprintf('A storage engine configuration must define an options array (for "%s")!', $name)
			);
		}

        if (!empty($em_class) && !empty($em_options)) {
            $factory = \Library\Factory::create()
                ->factoryName('EntityManager::StorageEngine')
                ->mustImplement(Kernel::STORAGE_ENGINE_INTERFACE)
                ;
            return $factory->build($em_class, $em_options);
        }
        return null;
    }
    
// -------------------------
// Commons
// -------------------------
	
    public function buildIndex($name)
    {
        return str_replace('-', '_', TextHelper::slugify($name));
    }

}

// Endfile