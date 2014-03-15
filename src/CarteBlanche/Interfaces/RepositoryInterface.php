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

namespace CarteBlanche\Interfaces;

use \CarteBlanche\Interfaces\ModelInterface;
use \CarteBlanche\Interfaces\StorageEngineInterface;

/**
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
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