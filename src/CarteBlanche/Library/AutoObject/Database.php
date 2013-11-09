<?php
/**
 * CarteBlanche - PHP framework package - AutoObject bundle
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library\AutoObject;

/**
 */
class Database
{

	protected $name;

	public function __construct( $database_name )
	{
		$this->name = $database_name;
		$this->init();
	}

	protected function init()
	{
	}

// -------------------
// Getters
// -------------------

	public function getName()
	{
		return $this->name;
	}
	
}

// Endfile