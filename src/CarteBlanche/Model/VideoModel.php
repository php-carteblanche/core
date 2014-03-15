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

namespace CarteBlanche\Model;

use \CarteBlanche\Model\DirectoryModel;
use \CarteBlanche\Model\ImageModelHelper;

/**
 */
class VideoModel
{

	/**
	 * The path of the directories root path
	 */
	var $path='';

	/**
	 * The name of the current directory
	 */
	var $filename=null;

	/**
	 * The content of the current directory
	 */
	var $data=null;

	static $allowed_extensions = array('mp4');

	/**
	 * Returns TRUE if the directory exists
	 */
	public function exists()
	{
		return isset($this->filename) && 
			file_exists( $this->getPath().$this->filename );
	}

	/**
	 * Returns TRUE if the root path exists
	 */
	public function pathExists()
	{
		return file_exists( $this->getPath() );
	}

	/**
	 * Returns TRUE if the object is a video file
	 */
	public function isVideo()
	{
		return $this->pathExists() && $this->exists() && 
			in_array(strtolower(end(explode('.', $this->filename))), self::$allowed_extensions);
	}

// ------------------------------------------
// Setters / Getters
// ------------------------------------------

	/**
	 * Sets the global path
	 * @param string $path The root path
	 */
	public function setPath( $path=null )
	{
		if (!empty($path)) $this->path = rtrim($path, '/').'/';
	}

	/**
	 * Gets the global path
	 * @return string The root path
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Sets the directory name
	 * @param string $filename The directory name
	 */
	public function setFilename( $filename=null )
	{
		if (!empty($filename)) $this->filename = $filename;
	}

	/**
	 * Gets the directory name
	 * @return string The directory name
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Sets the object data
	 * @param array $data An array of the object contents
	 */
	public function setData( $data=null )
	{
		$this->data = $data;
	}

	/**
	 * Gets the object data
	 * @return array The object data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get video infos
	 */
	public function getInfos()
	{
		if (!$this->exists() || !$this->isVideo()) return false;
		return $this->getVideoInfos();
	}
	
	/**
	 * Scan video infos
	 */
	public function getVideoInfos()
	{
		if (!$this->exists()) return false;

		$data = finfo_file($this->getPath().$this->filename);

		return $data;
	}

// ------------------------------------------
// CRUD Methods
// ------------------------------------------

	/**
	 * Must return the counting of all model objects
	 */
	public function count()
	{
		$_new_dir = new DirectoryModel;
		$_new_dir->setPath( dirname($this->getPath()) );
		$_new_dir->setDirname( basename($this->getPath()) );
		return count($_new_dir->scanDir());
	}

	/**
	 * 
	 */
	public function findFirst()
	{
		$_new_dir = new DirectoryModel;
		$_new_dir->setPath( dirname($this->getPath()) );
		$_new_dir->setDirname( basename($this->getPath()) );
		$_dat = $_new_dir->scanDir();
		return $_dat[0];
	}

	/**
	 * Must return the list of all model objects
	 */
	public function dump()
	{
		$_new_dir = new DirectoryModel;
		$_new_dir->setPath( dirname($this->getPath()) );
		$_new_dir->setDirname( basename($this->getPath()) );
		return $_new_dir->scanDir();
	}

	/**
	 * Must return the content of the object
	 * @param int $id The primary ID of the object
	 */
	public function read( $id=null )
	{
	}

	/**
	 * Create a new object
	 * @param array $data An array of the object contents
	 */
	public function create( $data=null )
	{
	}

	/**
	 * Update an existing object
	 * @param int $id The primary ID of the object
	 * @param array $data An array of the object contents
	 */
	public function update( $id=null, $data=null )
	{
	}

	/**
	 * Delete an object
	 * @param int $id The primary ID of the object
	 */
	public function delete( $id=null )
	{
	}

}

// Endfile