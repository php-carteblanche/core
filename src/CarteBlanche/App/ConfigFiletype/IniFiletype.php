<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\App\ConfigFiletype;

use \CarteBlanche\Interfaces\ConfigFiletypeInterface;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class IniFiletype implements ConfigFiletypeInterface
{

    /**
     * Parse a file content
     *
     * @param object SplFileInfo for the file to parse
     *
     * @return array
     */
    function parse(\SplFileInfo $file)
    {
        $config = parse_ini_file($file->getRealpath(), true);
        return $config;
    }

}

// Endfile