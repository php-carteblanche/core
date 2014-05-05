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

namespace CarteBlanche\Interfaces;

/**
 * @author  Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
interface ConfigFiletypeInterface
{

    /**
     * Parse a file content
     *
     * @param   \SplFileInfo $file  The file to parse
     * @return  array
     */
    function parse(\SplFileInfo $file);

}

// Endfile