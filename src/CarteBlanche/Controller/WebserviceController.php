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

namespace CarteBlanche\Controller;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Abstracts\AbstractControllerCarteBlancheDefault;

/**
 * The default application controller
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class WebserviceController
    extends AbstractControllerCarteBlancheDefault
{

    /**
     * The directory where to search the views files
     */
    static $views_dir = 'system/';

    /**
     * The home page of the controller
     *
     * @param int $offset   The offset used for the tables dump
     * @param int $limit    The limit used for the tables dump
     * @param string $table     The name of a table to isolate it
     * @param mixed $show        ??
     *
     * @return string           The home page view content
     */
    public function indexAction($offset = 0, $limit = 5, $table = null, $show = null)
    {
        return array(self::$views_dir.'hello', array(
            'altdb'=>$this->getContainer()->get('request')->getUrlArg('altdb'),
            'title' => $this->trans("Hello"),
        ));
    }

    /**
     * Page for uninstalled application
     *
     * @param string $altdb     The alternative database
     * @return string           The view content
     */
    public function emptyAction($altdb = 'default')
    {
        return array('empty', array(
            'altdb'=>$this->getContainer()->get('request')->getUrlArg('altdb'),
            'title' => $this->trans("System not installed"),
        ));
    }

    /**
     * Credits Page
     *
     * @return string The view content
     */
    public function creditsAction()
    {
        $_app = $this->getContainer()->get('config')->getRegistry()->dumpStack('app');
        return array(self::$views_dir.'credits', array(
            '_app' => !empty($_app['app']) ? $_app['app'] : array(),
            'title' => $this->trans("About"),
        ));
    }

    /**
     * Page of system check
     *
     * @return string The view content
     */
    public function check_systemAction()
    {
        $errs=$ctt=array();
        $sqlite=$php=null;
        $_altdb = $this->getContainer()->get('request')->getUrlArg('altdb');

        // directories ?
        $required_dirs = CarteBlanche::getConfig('carte_blanche.required_dirs', null, true);
        foreach ($required_dirs as $_dir) {
            $_fulldir = CarteBlanche::getPath($_dir);
            if ($ok_file = file_exists($_fulldir)) {
                $ctt[] = $this->trans('Directory "%dirname%" exists.', array('dirname'=>$_dir));
            } else {
                $errs[] = $this->trans('Directory "%dirname%" does not exist!', array('dirname'=>$_dir))
                     . $this->trans('Please create it manually.');
            }
        }

        $writable_dirs = CarteBlanche::getConfig('carte_blanche.writable_dirs', null, true);
        foreach ($writable_dirs as $_dir) {
            $_fulldir = CarteBlanche::getPath($_dir);
            if (!file_exists($_fulldir)) {
                $ok_file = mkdir($_fulldir);
            } else {
                $ok_file = true;
            }
            if ($ok_file) {
                if (is_dir($_fulldir) && is_writable($_fulldir)) {
                    $ctt[] = $this->trans('Directory "%dirname%" is writable.', array('dirname'=>$_dir));
                } else {
                    $errs[] = $this->trans('Directory "%dirname%" is not writable!', array('dirname'=>$_dir))
                        . $this->trans('Please authorize system\'s web user to write in it.');
                }
            } else {
                $errs[] = $this->trans('Directory "%dirname%" doesn\'t exist and can not be created!', array('dirname'=>$_dir))
                    . $this->trans('Please create it manually and authorize system\'s web user to write in it.');
            }
        }

        // PHP ?
        if (function_exists('phpversion')) {
            $ctt[] = $this->trans('PHP version: %version%', array('version'=>phpversion()));
        } else {
            $errs[] = $this->trans('Your PHP version is not enough. Please check the <a href="http://www.php.net/manual/en/">PHP manual</a>.');
        }
        // SQLite ?
        if (function_exists('sqlite_libversion')) {
            $ctt[] = $this->trans('SQLite version: %version%', array('version'=>sqlite_libversion()));
        } else {
            $errs[] = $this->trans('SQLite is not installed. Please check the <a href="http://www.php.net/manual/en/book.sqlite.php">PHP manual</a>.');
        }

        return array(self::$views_dir.'check_system', array(
            'title' => $this->trans("System checker"),
            'errors'=>$errs,
            'contents'=>$ctt
        ));
    }

    /**
     * Page of system installation
     *
     * @return string The view content
     */
    public function installAction()
    {
        $_altdb = $this->getContainer()->get('request')->getUrlArg('altdb');
        $SQLITE = $this->getContainer()->get('database');
        $tables = \CarteBlanche\Library\AutoObject\AutoObjectMapper::getObjectsStructure( $_altdb );
        if (!empty($tables)) {
            $installed = array();
            foreach ($tables as $table) {
                $err = $SQLITE->add_table( $table['table'], $table['structure'] );
                if (!$err) {
                    trigger_error( "An error occured while creating table '{$table['table']}'!", E_USER_ERROR);
                }
                $installed[] = $table['table'];
            }
        }
        $this->getContainer()->get('session')
            ->setFlash("ok:".$this->trans('OK - Tables "%list%" created', array('list'=>join("', '", $installed))));
        $this->getContainer()->get('session')->commit();
        $this->getContainer()->get('router')->redirect(
            $this->getContainer()->get('router')->buildUrl('altdb',$_altdb)
        );
    }

    /**
     * Page of system errors
     *
     * @return string The view content
     */
    public function bootErrorAction(array $errors = null)
    {
        $session = $this->getContainer()->get('session');
        $original_errors = $session->has('boot_errors') ? $session->get('boot_errors') : $errors;
        $running_user = $this->getKernel()->whoAmI();

        return array(self::$views_dir.'errors', array(
            'title'=>$this->trans('System errors'),
            'original_errors'=>$original_errors,
            'running_user' => $running_user,
            'errors'=>$errors
        ));
    }

    /**
     * Test content page
     *
     * @return string A loremipsum content
     */
    public function loremIpsumAction($type = 'html')
    {
        $view = $type==='text' ? 'lorem_ipsum.txt' : 'lorem_ipsum.html';
        return array($view, array(
            'title'=>'Lorem Ipsum'
        ));
    }

}

// Endfile