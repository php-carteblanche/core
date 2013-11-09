<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library\StorageEngine;

use \CarteBlanche\Interfaces\StorageEngineInterface;

/**
 * The Database Adpater interface
 *
 * Any database driver must implements this interface, and follow its constructor's rules
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
interface StorageEngineAwareInterface
{

	/**
	 * Set the storage engine
	 *
	 * @param object $storage_engine \CarteBlanche\Interfaces\StorageEngineInterface
	 */
	public function setStorageEngine(StorageEngineInterface $storage_engine);

	/**
	 * Get the storage engine
	 *
	 * @return object \CarteBlanche\Interfaces\StorageEngineInterface
	 */
	public function getStorageEngine();

}

// Endfile