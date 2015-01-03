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

namespace CarteBlanche\Model;

/**
 */
class DirectoryModel
{

    /**
     * The path of the directories root path
     */
    var $path='';

    /**
     * The name of the current directory
     */
    var $dirname=null;

    /**
     * The content of the current directory
     */
    var $data=null;

    /**
     * Directories names to ignore
     */
    var $ignore = array( '.', '..', '.DS_Store' );

    /**
     * Returns TRUE if the directory exists
     */
    public function exists()
    {
        return isset($this->dirname) &&
            file_exists( $this->getPath().$this->dirname );
    }

    /**
     * Returns TRUE if the root path exists
     */
    public function pathExists()
    {
        return file_exists( $this->getPath() ) && is_dir( $this->getPath() );
    }

// ------------------------------------------
// Setters / Getters
// ------------------------------------------

    /**
     * Sets the global path
     *
     * @param string $path The root path
     */
    public function setPath($path = null)
    {
        if (!empty($path)) $this->path = rtrim($path, '/').'/';
    }

    /**
     * Gets the global path
     *
     * @return string The root path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the directory name
     *
     * @param string $dirname The directory name
     */
    public function setDirname($dirname = null)
    {
        if (!empty($dirname)) $this->dirname = $dirname;
    }

    /**
     * Gets the directory name
     *
     * @return string The directory name
     */
    public function getDirname()
    {
        return $this->dirname;
    }

    /**
     * Gets the directory name transformed to be displayed as a title
     *
     * @return string The directory name transformed
     */
    public function getDisplayDirname()
    {
        $_dirname = $this->dirname;
        if (empty($_dirname)) $_dirname = basename($this->getPath());
        return ucwords( str_replace( '_', ' ', $_dirname ) );
    }

    /**
     * Sets the object data
     *
     * @param array $data An array of the object contents
     */
    public function setData($data = null)
    {
        $this->data = $data;
    }

    /**
     * Gets the object data
     *
     * @return array The object data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Scan the current directory
     *
     * @param bool $recursive Is the scan recursive ?
     */
    public function scanDir($recursive = false)
    {
        if (!$this->exists()) return false;
        $_dir = opendir($this->getPath().$this->dirname);
        $data = array();
        while ($entry = @readdir($_dir)) {
            if (!in_array($entry, $this->ignore)) {
                if (is_dir($this->getPath().$this->dirname.'/'.$entry)) {
                    if (true === $recursive){
                        $_new_dir = new DirectoryModel;
                        $_new_dir->setPath( $this->getPath().$this->dirname );
                        $_new_dir->setDirname( $entry );
                        $data[] = $_new_dir->scanDir( $recursive );
                    } else {
                        $data[] = $entry;
                    }
                } else {
                    $data[] = $entry;
                }
            }
        }
        closedir($_dir);
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
     *
     * @param int $id The primary ID of the object
     */
    public function read( $id=null )
    {
    }

    /**
     * Create a new object
     *
     * @param array $data An array of the object contents
     */
    public function create( $data=null )
    {
    }

    /**
     * Update an existing object
     *
     * @param int $id The primary ID of the object
     * @param array $data An array of the object contents
     */
    public function update( $id=null, $data=null )
    {
    }

    /**
     * Delete an object
     *
     * @param int $id The primary ID of the object
     */
    public function delete( $id=null )
    {
    }

}

// Endfile