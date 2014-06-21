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

namespace CarteBlanche\App\ConfigFiletype;

use \CarteBlanche\Interfaces\ConfigFiletypeInterface;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class JsonFiletype implements ConfigFiletypeInterface
{

    /**
     * Parse a file content
     *
     * @param   \SplFileInfo    $file   The file to parse
     * @return  array
     * @throws  \RuntimeException for any JSON error
     */
    function parse(\SplFileInfo $file)
    {
        $content = file_get_contents($file->getRealpath());
        $config = json_decode($content, true);

        $json_error = json_last_error();
        if ($json_error && $json_error!==JSON_ERROR_NONE) {
            switch (json_last_error()) {
                case JSON_ERROR_NONE: default: break;
                case JSON_ERROR_DEPTH:
                    $type = 'JSON_ERROR_DEPTH';
                    $error = 'Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $type = 'JSON_ERROR_STATE_MISMATCH';
                    $error = 'Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $type = 'JSON_ERROR_CTRL_CHAR';
                    $error = 'Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $type = 'JSON_ERROR_SYNTAX';
                    $error = 'Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    $type = 'JSON_ERROR_UTF8';
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            }
            throw new \RuntimeException(
                sprintf('JSON decoding error with message "%s: %s"!', $type, $error)
            );
        }

        return $config;
    }

}

// Endfile