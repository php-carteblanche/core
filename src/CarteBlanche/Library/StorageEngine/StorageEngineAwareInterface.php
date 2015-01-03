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

namespace CarteBlanche\Library\StorageEngine;

use \CarteBlanche\Interfaces\StorageEngineInterface;

/**
 * The Database Adpater interface
 *
 * Any database driver must implements this interface, and follow its constructor's rules
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
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