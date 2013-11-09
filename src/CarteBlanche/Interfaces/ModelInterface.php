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
 * The model interface
 *
 * Any model must implements this interface, of follow its constructor's rules
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
interface ModelInterface
{

	/**
	 * Construction : defines the table name and structure and the object relations
	 * @param string $_table_name The table name
	 * @param array $_table_fields The table fields array
	 * @param array $_table_structure The table structure array
	 * @param bool $auto_populate The value for the auto_populate class flag (default is TRUE)
	 * @param bool $advanced_search The value for the advanced_search class flag (default is FALSE)
	 */
	public function __construct($_table_name = null, $_table_fields = null, $_table_structure = null, $auto_populate = true, $advanced_search = false);

}

// Endfile