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
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
interface CarteBlancheExceptionInterface
{

    /**
     * Render of a production error
     *
     * @return void
     */
    function productionRendering();

    /**
     * Log the exception in `error.log`
     *
     * @return void
     */
    function log();

    /**
     * Get the CarteBlanche information string about an Exception
     *
     * @return string
     */
    function getAppMessage();

}

// Endfile