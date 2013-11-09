<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library\StorageEngine\DatabaseDriver;

/**
 * Any database driver must extends this abstract class
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
abstract class AbstractDatabaseDriver
{

// ------------------------
// Statics
// ------------------------

	public static $DBADAPTER_ASSOC = null;

	public static $DBADAPTER_NUM = null;

	public static $DBADAPTER_BOTH = null;

// ------------------------
// Constants build
// ------------------------

	/**
	 * Constants constructor
	 *
	 * @param array $statics_map An array defineding every class'statics value
	 */
	protected function _buildStatics(array $statics_map)
	{
		// assoc
		if (isset($statics_map['assoc'])) {
			self::$DBADAPTER_ASSOC = $statics_map['assoc'];
		} else {
			throw new \InvalidArgumentException('You must defined the "assoc" constant value (as a static property) for a database adapter!');
		}

		// num
		if (isset($statics_map['num'])) {
			self::$DBADAPTER_NUM = $statics_map['num'];
		} else {
			throw new \InvalidArgumentException('You must defined the "num" constant value (as a static property) for a database adapter!');
		}

		// both
		if (isset($statics_map['both'])) {
			self::$DBADAPTER_BOTH = $statics_map['both'];
		} else {
			throw new \InvalidArgumentException('You must defined the "both" constant value (as a static property) for a database adapter!');
		}
	}

	/**
	 * Force the adapter classes to build a "_loadStatics()" method to execute the "_buildStatics()" method above
	 */
	abstract protected function _loadStatics();

}

// Endfile