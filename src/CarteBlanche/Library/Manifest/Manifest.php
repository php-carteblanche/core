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

namespace Lib\Manifest;

use \SplFileInfo, \Exception;

class Manifest extends SplFileInfo
{

    protected $entries;

    public function __construct($filename)
    {
        parent::__construct($filename);
        if (!$this->isFile()) {
            throw new Exception(
                sprintf('Manifest file "%s" is not a file!', $filename)
            );
        }
        if (!$this->isReadable()) {
            throw new Exception(
                sprintf('Manifest file "%s" is not readable!', $filename)
            );
        }
        $this->parse();
    }

    public function getEntries()
    {
        return (array) $this->entries;
    }

    public function get($name, $default = null)
    {
        return (isset($this->entries[$name]) ? $this->entries[$name] : $default);
    }

    public function parse()
    {
        $ext = $this->getExtension();
        $_meth = '_parse'.ucfirst($ext);
        if (method_exists($this, $_meth)) {
            $this->{$_meth}( file_get_contents($this->getRealPath()) );
        } else {
            throw new Exception(
                sprintf('Unknown manifest file type (got extension "%s")!', $ext)
            );
        }
    }

    protected function _parseJson($content)
    {
        $this->entries = json_decode($content);
        if (is_null($this->entries)) {
            $error_str='';
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH: $error_str = 'Max JSON depth reached'; break;
                case JSON_ERROR_STATE_MISMATCH: $error_str = 'Invalid JSON content'; break;
                case JSON_ERROR_CTRL_CHAR: $error_str = 'Charset error while parsing JSON content'; break;
                case JSON_ERROR_SYNTAX: $error_str = 'JSON syntax error'; break;
                case JSON_ERROR_UTF8: $error_str = 'UTF-8 JSON error'; break;
                default: $error_str = 'Unkown JSON error'; break;
                case JSON_ERROR_NONE:break;
            }
            throw new Exception(
                sprintf('An error occured while parsing a JSON content: "%s"', $error_str)
            );
        }
    }

    protected function _parsePhp($content)
    {
        $this->entries = eval( str_replace(array('<?php', '?>'), '', $content) );
        if (is_null($this->entries) || !is_array($this->entries)) {
            throw new Exception(
                'An error occured while parsing a PHP content: "PHP manifest must return an array"'
            );
        }
    }

    protected function _parseIni($content)
    {
        $this->entries = parse_ini_string($content, true);
        if (is_null($this->entries) || !is_array($this->entries)) {
            throw new Exception(
                'An error occured while parsing an INI content: "INI manifest must return an array"'
            );
        }
    }

}

// Endfile