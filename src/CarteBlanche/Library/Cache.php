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

namespace CarteBlanche\Library;

use \CarteBlanche\CarteBlanche;

/**
 * The global Cache class
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class Cache 
{

    protected $tmp_dir;

    public function __construct()
    {
        $this->tmp_dir = CarteBlanche::getPath('cache_path');
    }

    public function cacheFile($filename, $content, $encode_filename = true)
    {
        if (empty($filename)) {
            $filename = uniqid();
        }
        if (true==$encode_filename) {
            $filename = md5( $filename );
        }
        $_fc =
            rtrim($this->tmp_dir, '/').'/'
            .str_replace($this->tmp_dir, '', $filename);
    //var_export($_fc);
        $cached = fopen($_fc, "w");
        if ($cached) {
            fwrite($cached, $content);
            fclose($cached);
            return $_fc;
        }
        return false;
    }

    public function getCachedFile( $filename )
    {
    }

}

// Endfile