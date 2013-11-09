<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Interfaces;

use \CarteBlanche\Interfaces\ModelInterface;
use \CarteBlanche\Interfaces\StorageEngineInterface;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
interface RepositoryInterface
{

	/**
	 */
	public function __construct(array $options = null, ModelInterface $model, StorageEngineInterface $storage_engine);

    public function find($field_value, $field_name = 'id');
    
    public function findAll($field_value = null, $field_name = null);

}

// Endfile