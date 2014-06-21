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

namespace CarteBlanche\Library;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Exception\InvalidArgumentException;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * File object extending PHP SplFileInfo
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class File
    extends \SplFileInfo
{

    protected $client_file_name;

// -----------------
// Constructor
// -----------------

    public function __construct( $file_name, $client_file_name=null )
    {
        parent::__construct( $file_name );
        if (!is_null($client_file_name)) $this->setClientFilename( $client_file_name );
    }

// -----------------
// Getters / Setters
// -----------------

    /**
     * Set the client filename
     *
     * @param   string  $file_name  The client filename for the file
     * @return  void
     * @throws   \CarteBlanche\Exception\InvalidArgumentException if the file name is not a string
     */
    public function setClientFilename($file_name)
    {
        if (is_string($file_name)) {
            $this->client_file_name = $file_name;
            $_ext = $this->guessExtension();
            if (!empty($_ext) && !strstr($file_name, $_ext))
                $this->client_file_name .= '.'.$_ext;
        } else {
            throw new InvalidArgumentException(
                sprintf('Client name of a file must be a string (got "%s")!', gettype($file_name))
            );
        }
    }

    /**
     * Get the client filename if so, the filename if not
     *
     * @return  string  The client filename
     */
    public function getClientFilename()
    {
        return !empty($this->client_file_name) ? $this->client_file_name : $this->getFilename();
    }

    /**
     * Get the filename without extension
     *
     * @return  string  The isolated filename
     * @see     \SplFileInfo::getBasename()
     */
    public function getFilenameWithoutExtension()
    {
        return $this->getBasename( '.'.$this->getExtension() );
    }

    /**
     * Get the file extension or guess it from MIME type if so ...
     *
     * @return  string  The guessed extension
     * @see     \SplFileInfo::getExtension()
     */
    public function guessExtension()
    {
        $_ext = $this->getExtension();
        if (empty($_ext) && $this->getRealPath()) {
            $finfo = new \finfo();
            $mime = $finfo->file( $this->getRealPath(), FILEINFO_MIME_TYPE );
            $_ext = str_replace('image/', '', $mime);
        }
        return $_ext;
    }

    /**
     * Get the file path (web accessible)
     *
     * @return  string  The target filepath
     */
    public function getWebPath()
    {
        return str_replace(CarteBlanche::getPath('web_dir'), '', $this->getRealPath());
    }

    /**
     * Get the file miem string if possible
     *
     * @return  string  The mime type info
     */
    public function getMime()
    {
        if ($this->getRealPath()) {
            $finfo = new \finfo();
            return $finfo->file( $this->getRealPath(), FILEINFO_MIME_TYPE );
        }
        return null;
    }

    /**
     * Get the file size in human readable string
     *
     * @param   int     $decimals   The decimals number to use in calculated file size
     * @return  string  A human readable string describing a file size
     */
    public function getHumanSize( $decimals=2 )
    {
        $sz = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $factor = floor((strlen($this->getSize()) - 1) / 3);
        if ($factor >= count($sz)) $factor = count($sz)-1;
        return sprintf("%.{$decimals}f", $this->getSize() / pow(1024, $factor)) . $sz[$factor];
    }

    /**
     * Get the stat infos of the file
     *
     * @return  array   The stat infos array about the file
     */
    public function getStat()
    {
        return $this->openFile()->fstat();
    }

    /**
     * Get the last access time on the file and return it as a DateTime object
     *
     * @return  null|object     The datetime object if so
     */
    public function getATimeAsDatetime()
    {
        $_date = $this->getATime();
        if (!empty($_date))
            return \DateTime::createFromFormat( 'U', $_date );
        return null;
    }

    /**
     * Get the creation time on the file and return it as a DateTime object
     *
     * @return  null|object     The datetime object if so
     */
    public function getCTimeAsDatetime()
    {
        $_date = $this->getCTime();
        if (!empty($_date))
            return \DateTime::createFromFormat( 'U', $_date );
        return null;
    }

    /**
     * Get the last modification time on the file and return it as a DateTime object
     *
     * @return  null|object     The datetime object if so
     */
    public function getMTimeAsDatetime()
    {
        $_date = $this->getMTime();
        if (!empty($_date))
            return \DateTime::createFromFormat( 'U', $_date );
        return null;
    }

    /**
     * Check if a file seems to be an image, based on its mime type signature
     *
     * @return  bool    True if the file seems to be an image, false otherwise
     */
    public function isImage()
    {
        $finfo = new \finfo();
        return (0!=preg_match('#^image/(.*)$#', $finfo->file( $this->getRealPath(), FILEINFO_MIME_TYPE )));
    }

// ---------------------
// Create a file from a content string
// ---------------------

    /**
     * Create a file with a content string and returns the created filename
     *
     * @param   string  $file_content   The source file content
     * @param   string  $filename       The target filename (optional - automatic if not set)
     * @return  \CarteBlanche\Library\File
     */
    public static function createFromContent( $file_content, $filename=null, $client_file_name=null )
    {
        $finfo = new \finfo();
        $mime = $finfo->buffer( $file_content, FILEINFO_MIME_TYPE );
        $extension = end(explode('/', $mime));
        if (is_null($filename)) {
            $_tmp_filename = md5( $file_content ).'.'.$extension;
            $filename = CarteBlanche::getFullPath('web_tmp_dir').$_tmp_filename;
            if (file_exists($filename)) {
                return new File( $filename, $client_file_name );
            }
        } elseif (end(explode('.', $filename))!=$extension) {
            $filename .= '.'.$extension;
        }

        $_tf = fopen($filename, 'a+');
        if ($_tf) {
            fwrite($_tf, $file_content);
            fclose($_tf);
            return new File( $filename, $client_file_name );
        }
        return null;
    }

}

/*
$test_img = 'tmp/test_img.png';
$test_noext = 'tmp/10504-F1';

echo '<pre>';
foreach( array($test_img, $test_noext) as $_f)
{
  $a = new \CarteBlanche\Library\File( $_f );
  echo '<br />object : '.var_export($a,1);
  echo '<br />filename : '.$a->getFilename();
  echo '<br />file basename : '.$a->getBasename();
  echo '<br />file extension : '.$a->getExtension();
  echo '<br />file guess extension : '.$a->guessExtension();
  echo '<br />file basename without extension : '.$a->getFilenameWithoutExtension();
  echo '<br />file real path : '.$a->getRealPath();
  echo '<br />file size : '.$a->getSize();
  echo '<br />file size human readable : '.$a->getHumanSize();
  echo '<br />file stats : '.var_export($a->getStat(),1);
  echo '<br />file aTime as DateTime : '.var_export($a->getATimeAsDatetime(),1);
  echo '<br />file cTime as DateTime : '.var_export($a->getCTimeAsDatetime(),1);
  echo '<br />file mTime as DateTime : '.var_export($a->getMTimeAsDatetime(),1);
  echo '<br />file is image ? : '.var_export($a->isImage(),1);
  echo '<br />file mime type ? : '.$a->getMime();
  echo '<hr />';
}
echo '<br />';
//exit('yo');
*/
// Endfile