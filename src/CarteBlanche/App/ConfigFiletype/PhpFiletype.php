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
class PhpFiletype implements ConfigFiletypeInterface
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
        $config = include $file->getRealpath();
        return $config;
    }
    
}

// Endfile