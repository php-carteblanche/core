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
class JsonFiletype implements ConfigFiletypeInterface
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