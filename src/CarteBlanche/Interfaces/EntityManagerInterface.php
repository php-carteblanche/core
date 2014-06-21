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
 * The EntityManager interface
 *
 * Any entity manager (such as Database) must implements this interface, and follow its constructor's rules
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface EntityManagerInterface
{

    /**
     * Construction : 1 single argument
     * @param array $options A table of options for the manager
     */
    public function __construct(array $options = null);

}

// Endfile