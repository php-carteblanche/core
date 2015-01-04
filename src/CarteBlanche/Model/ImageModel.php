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

use \CarteBlanche\Model\DirectoryModel;
use \CarteBlanche\Model\ImageModelHelper;

/**
 */
class ImageModel
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

    static $iptc_fields = array(
        '005' => 'object name',
        '007' => 'edition status',
        '010' => 'priority',
        '015' => 'category',
        '020' => 'category added',
        '022' => 'id',
        '025' => 'keywords',
        '026' => 'location',
        '030' => 'out date',
        '035' => 'out hour',
        '040' => 'specials',
        '055' => 'creation date',
        '060' => 'creation hour',
        '065' => 'software',
        '070' => 'software version',
        '075' => 'cycle',
        '080' => 'creator',
        '085' => 'creator status',
        '090' => 'city',
        '092' => 'region',
        '095' => 'state',
        '100' => 'country code',
        '101' => 'country',
        '103' => 'reference',
        '105' => 'title',
        '110' => 'credits',
        '115' => 'source',
        '116' => 'copyright',
        '118' => 'contact',
        '120' => 'legend',
        '122' => 'author',
    );

    static $img_extensions = array('jpg', 'jpeg', 'tif', 'tiff');
    static $exif_extensions = array('jpg', 'jpeg', 'tif', 'tiff', 'png', 'giff', 'gif');

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
     * Returns TRUE if the object is an image
     */
    public function isImage( $exif=null )
    {
        $_ext   = empty($exif) ? self::$img_extensions : self::$exif_extensions;
        $tbl    = explode('.', $this->filename);
        $last   = end($tbl);
        return (bool) ($this->pathExists() && $this->exists() && in_array(strtolower($last), $_ext));
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
     * Scan image infos
     */
    public function getInfos()
    {
        if (!$this->exists() || !$this->isImage()) return false;
        if ($this->isImage(true))
            return array_merge( self::getIptcInfos(), self::getExifInfos() );
        else
            return self::getIptcInfos();
    }

    /**
     * Scan image infos
     */
    public function getIptcInfos()
    {
        if (!$this->exists()) return false;
        GetImageSize( $this->getPath().$this->filename, $data );
        $_data = empty($data["APP13"]) ? array() : iptcparse($data["APP13"]);
        $iptcdata = array();
        if (!empty($data)) {
            foreach($_data as $tag => $tab) {
               $tag = substr($tag, 2);
               if (array_key_exists($tag, self::$iptc_fields))
                   $iptcdata[self::$iptc_fields[$tag]] = join($tab, ', ');
            }
        }
        return $iptcdata;
    }

    /**
     * Scan image infos
     */
    public function getExifInfos()
    {
        if (!$this->exists()) return false;
        $exifdata = array();
        if ($data = exif_read_data($this->getPath().$this->filename, defined('EXIF') ? EXIF : 'EXIF', true)) {
            foreach ($data as $key => $section) {
                foreach ($section as $name => $value) {
                    if (is_string($value)) {
                        if (!isset($exifdata[$name])) $exifdata[$name] = '';
                        $exifdata[$name] .= $value;
                    } elseif (is_array($value)) {
                        if (!isset($exifdata[$name])) $exifdata[$name] = array();
                        $exifdata[$name][] = $value;
                    }
                }
            }

            if (!empty($exifdata["GPSLongitude"]) && !empty($exifdata['GPSLongitudeRef']))
                $exifdata["GPSLongitudeTransformed"] = ImageModelHelper::getGps($exifdata["GPSLongitude"][0], $exifdata['GPSLongitudeRef'][0]);
            if (!empty($exifdata["GPSLatitude"]) && !empty($exifdata['GPSLatitudeRef']))
                $exifdata["GPSLatitudeTransformed"] = ImageModelHelper::getGps($exifdata["GPSLatitude"][0], $exifdata['GPSLatitudeRef'][0]);
            if (!empty($exifdata["DateTimeOriginal"]))
                $exifdata["DateTimeOriginalTransformed"] = ImageModelHelper::getDateFromExif($exifdata["DateTimeOriginal"]);

        }
        return $exifdata;
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