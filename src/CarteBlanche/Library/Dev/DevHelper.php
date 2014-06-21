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

namespace CarteBlanche\Library\Dev;

use \CarteBlanche\CarteBlanche;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
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