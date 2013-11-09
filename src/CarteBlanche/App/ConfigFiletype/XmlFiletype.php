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
use \Library\Converter\Xml2Array;

/**
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class XmlFiletype implements ConfigFiletypeInterface
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