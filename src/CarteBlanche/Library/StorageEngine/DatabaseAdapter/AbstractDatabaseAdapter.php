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

namespace CarteBlanche\Library\StorageEngine\DatabaseAdapter;

use \CarteBlanche\App\Kernel;

/**
 * Any database driver must extend this abstract class
 *
 * @author 		Piero Wbmstr <me@e-piwi.fr>
 */
abstract class AbstractDatabaseAdapter
{

	/**
	 * Construction of the database driver
	 *
	 * @param array $params
	 *
	 * @return object \CarteBlanche\Library\StorageEngine\DatabaseDriver\DatabaseDriverInterface
	 */
	public function getDriver(array $params = null)
	{
	    return $this->driver;
	}

    protected function _buildDriver($name, array $params = null)
    {
        $factory = \Library\Factory::create()
            ->factoryName(__CLASS__)
            ->mustImplementAndExtend(array(
                Kernel::DATABASE_DRIVER_ABSTRACT,
                Kernel::DATABASE_DRIVER_INTERFACE
            ))
            ->defaultNamespace(Kernel::DATABASE_DRIVER_DEFAULT_NAMESPACE)
            ->classNameMask(array('%s', '%s'.Kernel::DATABASE_DRIVER_SUFFIX))
            ;
        return $factory->build($name, $params);
    }

    public function __call($name, array $params)
    {
        if (method_exists($this->getDriver(), $name)) {
            return call_user_func_array(array($this->getDriver(), $name), $params);
        } else {
            throw new \RuntimeException(
                sprintf('Unknown method "%s" in database driver "%s"!', $name, get_class($this->getDriver()))
            );
        }
    }

}

// Endfile