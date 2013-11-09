<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Controller;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Abstracts\AbstractController;

/**
 * The default application controller
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class DefaultController extends AbstractController
{

	/**
	 * The directory where to search the views files
	 */
	static $views_dir = 'system/';

	/**
	 * The home page of the controller
	 *
	 * @param numeric $offset The offset used for the tables dump
	 * @param numeric $limit The limit used for the tables dump
	 * @param string $table The name of a table to isolate it
	 * @param misc $show ??
	 * @return string The home page view content
	 */
	public function indexAction($offset = 0, $limit = 5, $table = null, $show = null)
	{
		return array(self::$views_dir.'hello', array(
            'altdb'=>$this->getContainer()->get('request')->getUrlArg('altdb'),
            'title' => $this->trans("Hello")
		));
	}

	/**
	 * Page for uninstalled application
	 *
	 * @param string $altdb The alternative database
	 * @return string The view content
	 */
	public function emptyAction($altdb = 'default')
	{
		return array('empty', array(
            'altdb'=>$this->getContainer()->get('request')->getUrlArg('altdb'),
            'title' => "System not installed"
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
            'title' => "About"
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
		$writable_dirs = CarteBlanche::getConfig('carte_blanche.writable_dirs', null, true);
		foreach ($writable_dirs as $_dir) {
			$_fulldir = CarteBlanche::getPath('root_path').$_dir;
			if (!file_exists($_fulldir))
				$ok_file = mkdir($_fulldir);
			else
				$ok_file = true;
			if ($ok_file) {
				if (is_dir($_fulldir) && is_writable($_fulldir))
					$ctt[] = 'Directory "'.$_dir.'" is writable';
				else
					$errs[] = 'The directory "'.$_dir.'" is not writable! Please authorize web user to write in it.';
			}
			else
				$errs[] = 'The directory "'.$_dir.'" doesn\'t exist and can not be created! Please create it manually and authorize web user to write in it.';
		}
		// PHP ?
		if (function_exists('phpversion')) $php = phpversion();
		if (!empty($php))
			$ctt[] = 'PHP version: '.$php;
		else
			$errs[] = 'Your PHP is not enough. Please check the <a href="http://www.php.net/manual/en/">PHP manual</a>.';
		// SQLite ?
		if (function_exists('sqlite_libversion')) $sqlite = sqlite_libversion();
		if (!empty($sqlite))
			$ctt[] = 'SQLite version: '.$sqlite;
		else
			$errs[] = 'SQLite is not installed. Please check the <a href="http://www.php.net/manual/en/book.sqlite.php">PHP manual</a>.';

		// Content
		$output='';
		if (empty($errs))
			$output .= '<div class="ok_message">OK - Your system seems to be enough to let this application work correctly.'
				.(CarteBlanche::getKernel()->isInstalled($_altdb) ? '' : '<a href="'.$this->getContainer()->get('router')->buildUrl(array(
					'action'=>'install', 'altdb'=>$_altdb
				)).'">clic here to install it</a>')
				.'</div>'
				.'<ul><li>'.implode('</li><li>', $ctt).'</li></ul>';
		else
			$output .= '<div class="error_message">! - Your system doesn\'t seem to be enough to let this application work correctly! (<em>see errors below</em>)</div>'
				.'<ul><li>'.implode('</li><li>', $errs).'</li></ul>'
				.'<ul><li>'.implode('</li><li>', $ctt).'</li></ul>';

		$this->render(array(
			'output'=> $output,
			'title' => "System checker"
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

		if (!empty($tables)) 
		{
			$installed=array();
			foreach($tables as $table) 
			{
				$err = $SQLITE->add_table( $table['table'], $table['structure'] );
				if (!$err)
					trigger_error( "An error occured while creating table '{$table['table']}'!", E_USER_ERROR);
				$installed[] = $table['table'];
			}
		}
		$this->getContainer()->get('session')->setFlash("ok:OK - Tables '".join("', '", $installed)."' created");
		$this->getContainer()->get('session')->commit();
		$this->getContainer()->get('router')->redirect( $this->getContainer()->get('router')->buildUrl('altdb',$_altdb) );
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
            'title'=>'System errors',
            'original_errors'=>$original_errors,
            'running_user' => $running_user,
            'errors'=>$errors
        ));
	}

}

// Endfile