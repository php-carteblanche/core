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
use \Library\Converter\Xml2Array;

/**
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class XmlFiletype implements ConfigFiletypeInterface
{

    /**
     * Parse a file content
     *
     * @param   \SplFileInfo    $file   The file to parse
     * @return array
     */
    function parse(\SplFileInfo $file)
    {
        $content = file_get_contents($file->getRealpath());
        libxml_clear_errors();
        $xml_config = Xml2Array::convert($content);
        $config = $this->rebuild($xml_config);

/*
        libxml_clear_errors();
        libxml_use_internal_errors(true);
        try {
            $xml = new \SimpleXMLElement($content);
        } catch (\Exception $e) {
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                $last_error = libxml_get_last_error();
                throw new \RuntimeException(
                    sprintf('XML parsing errors with message "%s"!', $last_error->message)
                );
            }
        }
var_export($xml);        
        $config = array();

        libxml_clear_errors();
*/

        return $config;
    }

    public function rebuild($xml)
    {
        $array = array();
        foreach ($xml as $index=>$element) {
            if ($index==='@root') {
                continue;
            }
            if (is_array($element) && !isset($element['@content'])) {
                if (in_array($index, array('item', 'items'))) {
                    return $this->rebuild($element);
                } else {
                    $array[$index] = $this->rebuild($element);
                }
            } else {
                $index_rebuilt = $index;
                $content_rebuilt = null;
                if (is_array($element)) {
                    if (isset($element['@attributes']) && isset($element['@attributes']['id'])) {
                        $index_rebuilt = $element['@attributes']['id'];
                    }
                    if (isset($element['@content'])) {
                        $content_rebuilt = $element['@content'];
                    }
                } else {
                    $content_rebuilt = $element;
                }
                $array[$index_rebuilt] = $content_rebuilt;
            }
        }
        return $array;
    }
    
}

// Endfile