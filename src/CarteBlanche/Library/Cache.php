<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library;

use \CarteBlanche\CarteBlanche;

/**
 * The global Cache class
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
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