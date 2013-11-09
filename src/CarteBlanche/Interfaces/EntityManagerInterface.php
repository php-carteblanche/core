<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Interfaces;

/**
 * The EntityManager interface
 *
 * Any entity manager (such as Database) must implements this interface, and follow its constructor's rules
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
interface EntityManagerInterface
{

	/**
	 * Construction : 1 single argument
	 * @param array $options A table of options for the manager
	 */
	public function __construct(array $options = null);

}

// Endfile