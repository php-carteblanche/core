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

namespace CarteBlanche\Abstracts;

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
     * @param \CarteBlanche\Interfaces\StorageEngineInterface $storage_engine
     * @return self
     */
    public function setStorageEngine(StorageEngineInterface $storage_engine)
    {
        $this->__storage_engine = $storage_engine;
        return $this;
    }

    /**
     * Get the storage engine
     *
     * @return \CarteBlanche\Interfaces\StorageEngineInterface
     */
    public function getStorageEngine()
    {
        return $this->__storage_engine;
    }

}

// Endfile