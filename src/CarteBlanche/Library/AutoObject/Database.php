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