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

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Library\AutoObject\Field;
use \CarteBlanche\Interfaces\StorageEngineInterface;

/**
 */
class Relation extends Field
{

    protected $relation_name;
    protected $relation_model;
    protected $relation_type;

    public function __construct($field_name, $field_structure, StorageEngineInterface $storage_engine, $relation_name, $relation_model, $relation_type = 'one')
    {
        $this->relation_name = $relation_name;
        $this->relation_model = $relation_model;
        $this->relation_type = $relation_type;
        parent::__construct($field_name, $field_structure, $storage_engine);
        self::init();
    }

    protected function init()
    {
        if (preg_match('/^(.*)_id$/i', $this->name, $matches)) {
            $this->type = 'choice';
        }
    }

    protected function _buildRelationValues()
    {
        $relation = $this->getRelationModel();
    //		$db = CarteBlanche::getContainer()->get('database');
        $db = $this->getStorageEngine();

        if (!empty($relation) && 0!=$relation->count()) {
            $related_slug = $relation->getSlugField();
            $_parent_special = $relation->getSpecialFields( 'related', $this->getRelationName().':id' );
            if (count($_parent_special)) $_parent_special = $_parent_special[0];
            $query = "SELECT id";
            if ($related_slug)
                $query .= ", {$related_slug}";
            if (!empty($_parent_special))
                $query .= ", {$_parent_special}";
            $query .= " FROM ".$this->getRelationName();
            $results = $db->query($query);

    // Gestion des parents : liste les enfants sous le parent, avec un ">" devant
    // Reste à gérer les sous-enfants ... à refaire donc, avec une autre logique

            $this->values = new \Patterns\Commons\Collection;
            foreach ($results->fetchAll() as $result) {
                if (!($this->getRelationName()==getContainer()->get('request')->getUrlArg('model') &&
                    isset($this->values['id']) && $result['id']==$this->values['id']))
                {
                    $item_id = $result['id'];
                    $item = $this->values->getEntry($item_id, array());

                    if (!isset($item['children']))
                        $item['children'] = array();

                    foreach ($result as $_name=>$_value) {
                        if (is_string($_name)) {
                            $item[$_name] = $_value;
                            if (!empty($related_slug) && $_name==$related_slug)
                                $item['slug'] = $_value;
                        }
                    }

                    if (!empty($_parent_special) && !empty($result[$_parent_special])) {
                        $parent_item_id = $result[$_parent_special];
                        $parent_item = $this->values->getEntry($parent_item_id, array());

                        if (!isset($parent_item['children']))
                            $parent_item['children'] = array();

                        $parent_item['children'][$result['id']] = $item;

                        $this->values->setEntry( $parent_item_id, $parent_item );
                    } else {
                        $this->values->setEntry( $item_id, $item );
                    }
                }
            }
        }

    }

// -------------------
// Getters
// -------------------

    public function getRelationName()
    {
        return $this->relation_name;
    }

    public function getRelationModel()
    {
        return $this->relation_model;
    }

    public function getValues()
    {
        return $this->getRelationValues();
    }

    public function getRelationValues()
    {
        $this->_buildRelationValues();
        return $this->values;
    }

}

// Endfile