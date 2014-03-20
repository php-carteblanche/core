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

namespace CarteBlanche\Controller;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Abstracts\AbstractController;
use \CarteBlanche\Library\Form\Field;
use \CarteBlanche\Library\Helper;
use \CarteBlanche\Exception\NotFoundException;
use \CarteBlanche\Exception\AccessForbiddenException;
use \CarteBlanche\Exception\InternalServerErrorException;
use \CarteBlanche\Exception\Exception;
use \CarteBlanche\Exception\ErrorException;
use \CarteBlanche\Exception\DomainException;
use \CarteBlanche\Exception\RuntimeException;
use \CarteBlanche\Exception\InvalidArgumentException;
use \CarteBlanche\Exception\UnexpectedValueException;


/**
 * Default dvelopment controller extending abstract \CarteBlanche\Abstracts\AbstractController class
 *
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class Dev extends AbstractController
{

	/**
	 * The directory where to search the views files
	 */
	static $views_dir = 'dev/';

	/**
	 * The home page of the controller
	 */
	public function indexAction()
	{
		$ctt = '';

		
		$this->render(array(
			'output'=> $ctt
		));
	}

	/**
	 * Special page to clear user data (such as session)
	 */
	public function clearAction()
	{
		$this->getContainer()->get('session')->clear();
		$this->getContainer()->get('router')->redirect( $this->getContainer()->get('router')->buildUrl() );
	}

// -------------------
// Special method for application full map
// -------------------

	/**
	 * Page of application map
	 *
	 * @return string The view content
	 */
	public function app_mapAction()
	{
	    $root_path = CarteBlanche::getPath('root_path');
		$base_path = $root_path;
		$bundles_base_path = $root_path
		    .CarteBlanche::getPath('bundles_dir');
		$controller_dirname = 'Controller';
		
		$app_controllers = $bundles_controllers = array();
/*
        $classMap = require __DIR__ . '/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }
*/
		// the global app dir
		$_dir = new \CarteBlanche\Model\DirectoryModel;
		$_dir->setPath(CarteBlanche::getPath('carte_blanche_core'));
		$_dir->setDirname( $controller_dirname );
		$_app_ctrls = $_dir->scanDir();
		if ($_app_ctrls) {
			foreach ($_app_ctrls as $_ctrl) {
				$controller = new \stdClass;
				$controller->short_name = str_replace('.php', '', $_ctrl);
				$controller->name = \CarteBlanche\App\Locator::locateController($controller->short_name);
				$controller->path = $base_path.$controller_dirname.'/'.$_ctrl;
				$controller->methods = array();
				if (\CarteBlanche\App\Loader::classExists($controller->name)) {
					$_cls = new \ReflectionClass( $controller->name );
					foreach ($_cls->getMethods(\ReflectionMethod::IS_PUBLIC) as $_meth) {
						if (strstr($_meth->name, 'Action')) {
							$method = new \stdClass;
							$method->name = $_meth->name;
							$method->short_name = str_replace('Action', '', $_meth->name);
							$method->expect_arguments = false;
							foreach ($_meth->getParameters() as $_param) {
								if (!$_param->isOptional()) {
									$method->expect_arguments = true;
									continue;
								}
							}
							$controller->methods[] = $method;
						}
					}
				}
				$app_controllers[] = $controller;
			}
		}

		// the bundles dirs
        $known_bundles = $this->getContainer()->get('bundles');
        if (is_null($known_bundles)) $known_bundles = array();
        $known_bundles_lower = $known_bundles;
        foreach ($known_bundles_lower as $key=>$k) {
            $known_bundles_lower[strtolower($key)] = $k;
        }
		$_dir->setPath(dirname($bundles_base_path));
		$_dir->setDirname(basename($bundles_base_path));
		$_bundles = $_dir->scanDir();
		if ($_bundles) {
			$_bundle_dir = new \CarteBlanche\Model\DirectoryModel;
			$_bundle_dir->setPath( $bundles_base_path );
			foreach ($_bundles as $_bundle) {
			    if (!array_key_exists($_bundle, $known_bundles) && !array_key_exists($_bundle, $known_bundles_lower)) {
			        foreach ($known_bundles as $nm=>$bdl) {
			            if ($bdl->getShortname()==$_bundle) {
			                $_bundle = $nm;
			            }
			        }
    			    if (!array_key_exists($_bundle, $known_bundles) && !array_key_exists($_bundle, $known_bundles_lower)) {
	    		        continue;
	    		    }
			    }
			    if (array_key_exists($_bundle, $known_bundles)) {
			        $this_bundle_path = $known_bundles[$_bundle]->getDirectory()
			            .'/'.$known_bundles[$_bundle]->getNamespace();
			    }
			    if (array_key_exists($_bundle, $known_bundles_lower)) {
			        $this_bundle_path = $known_bundles_lower[$_bundle]->getDirectory()
			            .'/'.$known_bundles_lower[$_bundle]->getNamespace();
			    }
				$bundles_controllers[$_bundle] = array();
				$_bundle_dir->setDirname( str_replace($_bundle_dir->getPath(), '', $this_bundle_path.'/'.$controller_dirname) );
				$_bundles_ctrls = $_bundle_dir->scanDir();

				if ($_bundles_ctrls)
				foreach ($_bundles_ctrls as $_ctrl) {
					$controller = new \stdClass;
					$controller->short_name = str_replace('.php', '', $_ctrl);
					$controller->name = '\\'.$_bundle.'\\Controller\\'.$controller->short_name;
					$controller->path = $bundles_base_path.$_bundle.'/'.$controller_dirname.'/'.$_ctrl;
					$controller->methods = array();
					if (\CarteBlanche\App\Loader::classExists($controller->name)) {
						$_cls = new \ReflectionClass( $controller->name );
						if ($_cls->isAbstract()) {
						    continue;
						}
						foreach ($_cls->getMethods(\ReflectionMethod::IS_PUBLIC) as $_meth) {
							if (strstr($_meth->name, 'Action')) {
								$method = new \stdClass;
								$method->name = $_meth->name;
								$method->short_name = str_replace('Action', '', $_meth->name);
								$method->expect_arguments = false;
								foreach ($_meth->getParameters() as $_param) {
									if (!$_param->isOptional()) {
										$method->expect_arguments = true;
										break;
									}
								}
								$controller->methods[] = $method;
							}
						}
					}
					$bundles_controllers[$_bundle][] = $controller;
				}
			}
		}

        $mode_data = \CarteBlanche\CarteBlanche::getKernelMode(true);
		return array(self::$views_dir.'app_map', array(
            'title' => $this->trans('Application full map'),
            'debug' => isset($mode_data['debug']) ? $mode_data['debug'] : false,
            'app_controllers'=>$app_controllers,
            'bundles_controllers' => $bundles_controllers,
		));
	}

	/**
	 * Page of application configuration
	 *
	 * @return string The view content
	 */
	public function cb_configAction()
	{
	    $config = $this->getContainer()->get('config')->dump();
	    $reflection_cb = new \ReflectionClass('\CarteBlanche\App\Kernel');
	    $constants = $reflection_cb->getConstants();

        $mode_data = \CarteBlanche\CarteBlanche::getKernelMode(true);
		return array(self::$views_dir.'app_config', array(
            'title' => $this->trans('Application full config'),
            'debug' => isset($mode_data['debug']) ? $mode_data['debug'] : false,
            'constants'=>$constants,
            'config' => $config,
		));
	}

	/**
	 * Page of application administration (test)
	 *
	 * @return string The view content
	 */
	public function adminAction()
	{
	    $fields = $values = array();

/*
TODO

- clear web cache
- clear app cache
- clear i18n cache
- clear generated images
- disable caches
*/


		$fields[] = new Field('clear_web_cache', array(
		    'type'=>'submit',
            'action'=>'test',
            'value'=>'ok',
            'label'=>'Clear web cache',
		));
	    
        $form = new \Tool\Form(array(
            'form_id'=>'app_admin',
            'fields'=>$fields, 
            'values'=>$values
        ));

		if ($this->getContainer()->get('request')->isPost()) {
		    $_posted = $this->getContainer()->get('request')->getData();
		    
		    if (isset($_posted['clear_web_cache']) && $_posted['clear_web_cache']==='ok') {
		        $path = \Library\Helper\Directory::slashDirname(CarteBlanche::getPath('cache_path'));
                $ok = \Library\Helper\Directory::purge($_path);
		    }
		    
		    var_export($_posted);
		    exit('yo');
		}
		
		return array(self::$views_dir.'app_admin', array(
            'title' => 'App admin',
			'form'=>$form,
		));
	}

// -------------------
// Special method for tests
// -------------------

	/**
	 * Page of test
	 *
	 * @param misc $arg1 Test arg 1
	 * @param misc $arg2 Test arg 2
	 * @return string The view content
	 */
	public function testAction($arg1 = null, $arg2 = 'value')
	{
//				throw new InternalServerErrorException("Capture l'exception par défaut", 12);
				throw new NotFoundException("Capture l'exception par défaut", 12);

        $lang_sel = new \Tool\LanguageSelector(array(
            'home'=>$this->getContainer()->get('router')->buildUrl(array('controller'=>'cms', 'altdb'=>$_altdb)),
            'current'=>$title,
        ));

		return array('test', array(
            'title' => 'Test page',
            'language_selector' => $lang_sel,
		));
/*
echo '<pre>';

$session = CarteBlanche::getContainer()->get('session');

//$session->set('test', array('one', 'two'));
if ($session->has('test')) {
    echo '<br />test :: '.var_export($session->get('test'),1);
} else {
    echo '<br />test is empty';
}

//$session->setFlash('test qsdf qsdfqsdf', 'ok');
//$session->setFlash('test qsdf qsdfqsdf', 'error');
if ($session->hasFlash()) {
    echo '<br />flashes :: '.var_export($session->allFlashes(),1);
} else {
    echo '<br />flashes are empty';
}

echo '<br /><br />'.var_export($session,1);
echo '<br /><br />'.var_export($_SESSION,1);
echo '<br />';
exit('yo');
*/
        CarteBlanche::log('test');

		$ctt='';
		$ctt .= '<br />Premier argument reçu: '.var_export($arg1,1);
		$ctt .= '<br />Second argument reçu: '.var_export($arg2,1);
		$ctt .= '<br />Tous les arguments reçus: '.var_export(func_get_args(),1);

/*
     $to      = 'piwi@ateliers-pierrot.fr';
     $subject = 'le sujet';
     $message = 'Bonjour !';
     $headers = 'From: webmaster@example.com' . "\r\n" .
     'Reply-To: webmaster@example.com' . "\r\n" .
     'X-Mailer: PHP/' . phpversion();
     $ok = mail($to, $subject, $message, $headers);
     var_export($ok);

exit('yo');

		// test envoi de mail
		$txt_message_iso = "Hello dude !\n\nMy line 1 with special chars : é à\nLine 2\nLine 3";
		$html_message_iso = "Hello dude !\n\n<br /><table><tr><td><b>My line 1 with special chars : é à</b></td><td>Line 2</td><td>Line 3</tr></table>";

$html_lorem_ipsum = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style type="text/css">
		.posted { color: #cccccc; }
	</style>
</head>
<body>
<h3>Lorem Ipsum "Dummy Text" Gets Spam Filtered</h3>


<p>Was working on a quick HTML Email trick to post here on the blog, and while I was sending myself test emails, I noticed they were getting thrown into my junk folder. I tweaked my HTML a little (it was admittedly a little sloppy) but it didn't help. Everytime I sent my test messages, <a target="_blank" href="http://www.mozilla.com/thunderbird/">Mozilla Thunderbird's</a> built-in spam filter threw every single one into the junk folder. After half a dozen more tests, I finally figured it out. I was using a paragraph of &quot;<a href="http://www.lipsum.com" target="_blank">Lorem Ipsum</a>&quot; placeholder text in my mockup. Once I switched it out with &quot;real&quot; copy (I pulled the first paragraph from the MailChimp home page) it got past the spam filter. So if you're a designer sending yourself some HTML email tests, find some &quot;real&quot; copy to use. Spam filters (Thunderbird's, at least) don't like lorem ipsum. Et tu, Mozilla? </p>

<p><a href="http://mailchimp.blogs.com/blog/files/loremipsum-spam.gif">Here's what the email that was getting blocked looked like</a></p>

<p><a href="http://mailchimp.blogs.com/blog/files/loremipsum-spam2.gif">And here's the one that got past the spam filter</a></p>

<a id="more"></a>


<p class="posted">
January 16, 2006 in <a href="http://mailchimp.blogs.com/blog/tips_tricks_best_practices/">Tips, Tricks, Best Practices</a>  | <a href="http://mailchimp.blogs.com/blog/2006/01/lorem_ipsum_get.html">Permalink</a>
</p>

<h2><a id="trackback"></a>TrackBack</h2>
<p>TrackBack URL for this entry:<br />http://www.typepad.com/services/trackback/6a00d8341d5a5053ef00d834a260d469e2</p>

<p>Listed below are links to weblogs that reference <a href="http://mailchimp.blogs.com/blog/2006/01/lorem_ipsum_get.html">Lorem Ipsum "Dummy Text" Gets Spam Filtered</a>:</p>

<h2><a id="comments"></a>Comments</h2>
<a id="c13022823"></a>
<p>Probably that&#39;s because Thunderbird uses a filtering method called &quot;Bayesian&quot; filtering.  Without going into the details of it, the filter checks the words in your message against the words in your inbox.  If there are many in common, it is not junk and if there are few in common, it is.  Thus, since you never receive emails with the words &quot;lorem&quot;, &quot;ipsum&quot;, or &quot;dolor&quot; in them, the message was marked as spam.  Any mail client that uses this method of filtering will likely have similar problems for similar reasons.</p>
<p class="posted">Posted by: Alan Underwood | Jan 19, 2006 5:09:24 PM</p>
<p>The comments to this entry are closed.</p>
</body>
</html>
EOT;

$mailchimp = new \MimeEmail\Lib\EmailTemplate\MailChimpEmailMarkupLayout();

$mailchimp
	->setVariables(array(
		'MC:SUBJECT'=>'Mail de test',
	))
	->parse();

		$file = _ROOTPATH.'CHANGELOG.md';
//		$mail = new \MimeEmail\Lib\MimeEmail('Piero', 'piwi@ateliers-pierrot.fr', 'pierre.cassat@gmail.com', 'Mail de test', $txt_message_iso);
//		$mail = new \MimeEmail\Lib\MimeEmail('Piero', 'piwi@ateliers-pierrot.fr', null, 'Mail de test', $txt_message_iso);
		$mail = new \MimeEmail\Lib\MimeEmail();

		$mail
			->setTo('piwi@ateliers-pierrot.fr', 'PieroWbmstr')
			->setFrom( 'pierre.cassat@gmail.com', 'Piero' )
//			->setCc( array( 'Piero'=>'pierre.cassat@gmail.com' ) )
//			->setCc( array( 'Piero'=>'pierre.cassat@gmail.com', 'oim'=>'oim@gmail.com' ) )
//			->setCc( array( 'Piero'=>'pierre.cassat@gmail.com', 'oim'=>'oim@gmail.com', 'test@mlkj.com' ) )
//			->setCc( array( 'Piero'=>'pierre.cassat@gmail.com', 'oim'=>'oim@mail.com', 'test@mlkj.com','oiu' ) )
//			->setCc('atelierspierrot@ail.com', 'yo')
//			->setBcc('ateliers.pierrot@gmail.com')

			->setSubject('Mail de test')
			->setText($txt_message_iso)
//			->setHtml($html_lorem_ipsum)
			->setHtml( $mailchimp->getContent() )
			->setText('auto')
			->setAttachment($file)
//			->setDryRun(true)
//			->setSpoolDir( _ROOTPATH.'cache/spool/' )
			;
		$ok = $mail->send(1);
//		$ok = $mail->spool(1);
		echo "<pre>"; var_export($mail); echo "</pre>";
exit('yo');
*/

CarteBlanche::getContainer()->get('i18n')->setLanguage('en');
$date = new \DateTime;

echo '<p>Tests in english</p>';

echo _D($date);

_trans('test');

echo '<br /><br />';
_trans('test_args', array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter=0;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<hr />';
echo '<p>Same tests in french</p>';

echo _D($date, null, 'UTF-8', 'fr');

_trans('test', array(), 'fr');

echo '<br /><br />';
_trans('test_args', array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter=0;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<hr />';
exit('yo');

echo '<pre>';

$config = new \CarteBlanche\App\Config;

$test_conf_ini = CarteBlanche::getPath('config_path').'tests/test.ini';
$config->load($test_conf_ini, true, 'ini');

$test_conf_json = CarteBlanche::getPath('config_path').'tests/test.json';
$config->load($test_conf_json, true, 'json');

$test_conf_xml = CarteBlanche::getPath('config_path').'tests/test.xml';
$config->load($test_conf_xml, true, 'xml');

$test_conf_php = CarteBlanche::getPath('config_path').'tests/test.php';
$config->load($test_conf_php, true, 'php');


echo "<br />config->get('scope_1.scope_2.my_bit_value_int'));\n";
var_export($config->get('scope_1.scope_2.my_bit_value_int'));
echo "<br />config->get('reference_1'));\n";
var_export($config->get('reference_1'));
echo "<br />config->get('ini.scope_1.my_index'));\n";
var_export($config->get('ini.scope_1.my_index'));
echo "<br />config->get('ini.reference_1'));\n";
var_export($config->get('ini.reference_1'));
echo "<br />config->get('reference_2'));\n";
var_export($config->get('reference_2'));
echo "<br />config->get('abcdef'));\n";
var_export($config->get('abcdef'));
echo "<br />config->get('abcdef', \CarteBlanche\App\Config::NOT_FOUND_ERROR));\n";
//var_export($config->get('abcdef', \CarteBlanche\App\Config::NOT_FOUND_ERROR));

echo "<br />config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'php'));\n";
var_export($config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'php'));
echo "<br />config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'json'));\n";
var_export($config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'json'));
echo "<br />config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'xml'));\n";
var_export($config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'xml'));
echo "<br />config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'ini'));\n";
var_export($config->get('closure_setting', \CarteBlanche\App\Config::NOT_FOUND_ERROR, null, 'ini'));

$cb_cfg = \CarteBlanche\App\Locator::locateConfig('carteblanche.ini');
$config->load($cb_cfg);

var_export($config);
echo '</pre>';
exit('yo');

$ok = \CarteBlanche::log(\App\Logger::DEBUG, 'my message');

exit('yo');
echo '<pre>';

echo '<br />ORIGINAL FILE : '.__DIR__.'/../../../composer.json<br />';
$file = new \Lib\Manifest\Manifest(__DIR__.'/../../../composer.json');
var_export($file);

echo '<hr />Package from file : <br />';
$manifest = new \Lib\Manifest\Package($file);
echo '(array access) TITLE => '.$manifest->title.'<br />';
var_export($manifest);

echo '<hr />Composer from package : <br />';
$composer = new \Lib\Manifest\Vendor\Composer($manifest);
var_export($composer);

echo '<hr />Node from package : <br />';
$node = new \Lib\Manifest\Vendor\Node($manifest);
var_export($node);

echo '<Hr />Bower from package : <br />';
$bower = new \Lib\Manifest\Vendor\Bower($manifest);
var_export($bower);

exit('yo');
/*
CarteBlanche::getContainer()->get('i18n')->setLanguage('en');
$date = new \DateTime;

echo '<p>Tests in english</p>';

echo _D($date);

_trans('test');

echo '<br /><br />';
_trans('test_args', array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter=0;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657));

echo '<hr />';
echo '<p>Same tests in french</p>';

echo _D($date, null, 'UTF-8', 'fr');

_trans('test', array(), 'fr');

echo '<br /><br />';
_trans('test_args', array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter=0;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<br /><br />';
$counter++;
echo _P(array(
    0=>'test_item_zero',
    1=>'test_item_one',
    2=>'test_item_two',
    3=>'test_item_multi'
), $counter, array('arg1'=>'AZERTY', 'arg2'=>4.657), 'fr');

echo '<hr />';
exit('yo');
*/
/*
		$js_dir = _ROOTPATH._WEBDIR.'js/scripts/';
		$js_files = array(
			_ROOTPATH._WEBDIR.'js/scripts.js',
			$js_dir.'array/array_remove.js',
			$js_dir.'array/join.js',
			$js_dir.'array/dump.js',
		);

		foreach($js_files as $_file)
		{
			getContainer()->get('template_engine')
				->getTemplateObject('JavascriptFile')
				->add( $_file );
		}

		$js_min = $this->getContainer()->get('template_engine')
				->getTemplateObject('JavascriptFile')
				->minify()
				->writeMinified(  );

		$ctt .= '<p>Minified javascripts : '.htmlentities($js_min).'</p>';
*/
/*
		$js_minifier = new \App\Minifier;
		$js_minifier
			->setSilent( false )
			->setFilesStack( $js_files )
			->setWebRootPath( __DIR__.'/../../www/' )
			->setDestinationDir( __DIR__.'/../../www/tmp/cache/' )
			->process();
		
*/		

// ASSERTIONS
$a=10;
//assert('$a<0', 'mlkjmlkj'); // only since PHP5.4.8
//assert('$a==0');
//assert('$a<0; // my comment for this assertion');

//throw new \Exception("Capture l'exception par défaut");

trigger_error('qmlskdjfmqlksj', E_USER_WARNING);
trigger_error('qmlskdjfmqlksj2', E_USER_WARNING);
			@fopen(); // error not written
			fopen(); // error

		try{
//			fopen(); // error
			if (2 != 4) // false
				throw new \CarteBlanche\Exception\Exception("Capture l'exception par défaut", 12);
		} catch(\CarteBlanche\Exception\Exception $e) {
			echo $e;
		}

//trigger_error('qmlskdjfmqlksj', E_USER_ERROR);

		trigger_error('Test error string', E_USER_ERROR);

/*		
		$my_table = \CarteBlanche\Library\AutoObject\AutoObjectMapper::getAutoObject('article');
		$ctt .= '<p>Structure de la table "article" :</p><pre>'.var_export($my_table,1).'</pre>';

		$artid=2;
		$my_article = new \CarteBlanche\Model\BaseModel( 'article', $my_table );
		$my_article->read( $artid );
		$ctt .= "<p>Contenu de l'article $artid :</p><pre>".var_export($my_article,1).'</pre>';
		
		$my_article->getRelations( false );
		$ctt .= "<p>Contenu de l'article $artid avec relations :</p><pre>".var_export($my_article,1).'</pre>';
*/		
		
		$this->render(array(
			'output'=> $ctt,
			'title' => 'Test page'
		));
	}

	/**
	 * Page of test for ajax requests
	 * @return string The view content
	 */
	public function testajaxAction()
	{
	    self::$template = 'empty.txt';
		return array('lorem_ipsum', array(
            'title' => 'Test page',
		));
	}
	
}

// Endfile