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
class Table
{

	protected $table_structure;

	protected $name;
	protected $database;

	protected $editable;

	public function __construct($table_name, $db = null, $structure = null)
	{
		$this->name = $table_name;
		$this->database = !is_null($db) ? $db : 'default';
		$this->table_structure = !is_null($structure) ? $structure : array();
		$this->init();
	}

	protected function init()
	{
		if (isset($this->table_structure['editable']))
			$this->editable = (bool) $this->table_structure['editable'];
	}

// -------------------
// Getters
// -------------------

	public function getName()
	{
		return $this->name;
	}
	
	public function getDatabase()
	{
		return $this->database;
	}
	
	public function isEditable()
	{
		return true===$this->editable;
	}
	
}

// Endfile