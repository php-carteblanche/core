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

namespace CarteBlanche\Interfaces;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface RepositoryInterface
{

    /**
     */
    public function __construct(
        array $options = null,
        \CarteBlanche\Interfaces\ModelInterface $model,
        \CarteBlanche\Interfaces\StorageEngineInterface $storage_engine
    );

    public function find($field_value, $field_name = 'id');
    
    public function findAll($field_value = null, $field_name = null);

}

// Endfile