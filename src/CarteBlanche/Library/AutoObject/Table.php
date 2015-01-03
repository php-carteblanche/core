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