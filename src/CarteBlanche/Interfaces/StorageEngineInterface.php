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
 * The Storage Engine interface
 *
 * Any storage engine must implement this interface and follow its constructor's rules
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface StorageEngineInterface
{

    /**
     * Construction : 1 single argument
     *
     * @param   array   $options    A table of options for the manager
     */
    public function __construct(array $options);

    /**
     * Escape a string using the internal PHP adapter function
     *
     * @param   string  $str            The string to escape
     * @param   bool    $double_quotes  If true, the double-quotes will be doubled for escaping
     * @return  string  The escaped string
     */
    public function escape($str = null, $double_quotes = false);

    /**
     * Get the current storage engine adapter
     *
     * @return  object
     */
    public function getAdapter();

}

// Endfile