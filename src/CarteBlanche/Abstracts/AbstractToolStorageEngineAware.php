<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * The default tool abstract class
 */

namespace CarteBlanche\Abstracts;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\App\FrontController;
use \CarteBlanche\Abstracts\AbstractTool;
use \CarteBlanche\Interfaces\StorageEngineInterface;
use \CarteBlanche\Library\StorageEngine\StorageEngineAwareInterface;

/**
 * Any tool class must extend this abstract one
 */
abstract class AbstractToolStorageEngineAware
    extends AbstractTool
    implements StorageEngineAwareInterface
{

	/**
	 * @var \CarteBlanche\Interfaces\StorageEngineInterface
	 */
    protected $__storage_engine;

	/**
	 * Set the storage engine
	 *
	 * @param object $storage_engine \CarteBlanche\Interfaces\StorageEngineInterface
	 */
	public function setStorageEngine(StorageEngineInterface $storage_engine)
	{
	    $this->__storage_engine = $storage_engine;
	    return $this;
	}

	/**
	 * Get the storage engine
	 *
	 * @return object \CarteBlanche\Interfaces\StorageEngineInterface
	 */
	public function getStorageEngine()
	{
	    return $this->__storage_engine;
	}

}

// Endfile