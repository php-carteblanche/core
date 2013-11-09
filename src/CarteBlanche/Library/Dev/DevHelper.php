<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library\Dev;

use \CarteBlanche\CarteBlanche;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class DevHelper
{
	
    public static function getDocUrl($class_name)
    {
        return CarteBlanche::getContainer()->get('config')->get('app.documentation')
            .str_replace('\\', '/', $class_name).'.html';
    }

}

// Endfile