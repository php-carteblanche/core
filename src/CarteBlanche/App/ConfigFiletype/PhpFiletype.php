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

namespace CarteBlanche\App\ConfigFiletype;

use \CarteBlanche\Interfaces\ConfigFiletypeInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class PhpFiletype implements ConfigFiletypeInterface
{

    /**
     * Parse a file content
     *
     * @param   \SplFileInfo    $file   The file to parse
     * @return array
     */
    function parse(\SplFileInfo $file)
    {
        $config = include $file->getRealpath();
        return $config;
    }
    
}

// Endfile