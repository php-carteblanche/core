<?php
if (empty($_app)) $_app=array();
if (empty($_globals)) $_globals=array();

if (!isset($merge_css)) $merge_css = false;
if (!isset($minify_css)) $minify_css = false;
if (!isset($merge_js)) $merge_js = false;
if (!isset($minify_js)) $minify_js = false;

// --------------------------------
// the "classic" assets web accessible directory
if (empty($assets)) {
    $assets = $_template->getAssetsLoader()->getAssetsWebPath();
    //trim(str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__.'/../'), '/');
}
if (strlen($assets)) {
    $assets = rtrim($assets, '/').'/';
}

// --------------------------------
// the global internal assets web accessible directory
if (empty($global_assets)) {
    $global_assets = $assets.\CarteBlanche\CarteBlanche::getPath('assets_dir');
}
if (strlen($global_assets)) {
    $global_assets = rtrim($global_assets, '/').'/';
}

// --------------------------------
// the "template engine" assets web accessible directory
if (empty($tple_assets)) {
    $tple_assets = $_template->getAssetsLoader()->findInPackage('', 'atelierspierrot/templatengine');
}
if (empty($tple_assets)) {
    $tple_assets = $assets;
}
if (strlen($tple_assets)) {
    $tple_assets = rtrim($tple_assets, '/').'/';
}

// --------------------------------
// the Boilerplate assets web accessible directory
if (empty($boilerplate_assets)) {
    $boilerplate_assets = $_template->getAssetsLoader()->findInPackage('html5boilerplate', 'atelierspierrot/templatengine');
}
if (empty($boilerplate_assets)) {
    $boilerplate_assets = $_template->getAssetsLoader()->findInPath('html5boilerplate', $assets);
}
if (strlen($boilerplate_assets)) {
    $boilerplate_assets = rtrim($boilerplate_assets, '/').'/';
}

// ------------------
// METAS
$old_metas = $_template->getTemplateObject('MetaTag')->get();
$_template->getTemplateObject('MetaTag')->reset();

// => charset and others
$_template->getTemplateObject('MetaTag')
	->add('charset', (isset($charset) ? $charset : 'UTF-8'))
	->add('X-UA-Compatible', 'IE=edge,chrome=1', true)
	->add('viewport', 'width=device-width');

// => description
if (!empty($_globals['meta_description']))
{
	$_template->getTemplateObject('MetaTag')
		->add('description', $_globals['meta_description']);
}
// => keywords
if (!empty($_globals['meta_keywords']))
{
	$_template->getTemplateObject('MetaTag')
		->add('keywords', $_globals['meta_keywords']);
}
// => robots
if (!empty($_globals['robots']))
{
	$_template->getTemplateObject('MetaTag')
		->add('robots', $_globals['robots']);
}
// => author
if (!empty($_app['author']))
{
	$_template->getTemplateObject('MetaTag')
		->add('author', $_app['author']);
}
// => generator
if (!empty($_app['name']))
{
	$_template->getTemplateObject('MetaTag')
		->add('generator', $_app['name'].(!empty($_app['version']) ? ' '.$_app['version'] : ''));
}
// => + old ones
$_template->getTemplateObject('MetaTag')->set($old_metas);

// ------------------
// LINKS
$old_links = $_template->getTemplateObject('LinkTag')->get();
$_template->getTemplateObject('LinkTag')->reset();

// => favicon.ico
if (file_exists($global_assets.'icons/favicon.ico'))
{
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'icon',
			'href'=>$global_assets.'icons/favicon.ico',
			'type'=>'image/x-icon'
		) )
		->add( array(
			'rel'=>'shortcut icon',
			'href'=>$global_assets.'icons/favicon.ico',
			'type'=>'image/x-icon'
		) );
}
// the followings are taken from <http://mathiasbynens.be/notes/touch-icons>
// => For third-generation iPad with high-resolution Retina display: apple-touch-icon-144x144-precomposed.png
if (file_exists($global_assets.'icons/apple-touch-icon-144x144-precomposed.png'))
{
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$global_assets.'icons/apple-touch-icon-144x144-precomposed.png',
			'sizes'=>'144x144'
		) );
}
// => For iPhone with high-resolution Retina display: apple-touch-icon-114x114-precomposed.png
if (file_exists($global_assets.'icons/apple-touch-icon-114x114-precomposed.png'))
{
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$global_assets.'icons/apple-touch-icon-114x114-precomposed.png',
			'sizes'=>'114x114'
		) );
}
// => For first- and second-generation iPad: apple-touch-icon-72x72-precomposed.png
if (file_exists($global_assets.'icons/apple-touch-icon-72x72-precomposed.png'))
{
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$global_assets.'icons/apple-touch-icon-72x72-precomposed.png',
			'sizes'=>'72x72'
		) );
}
// => For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: apple-touch-icon-precomposed.png
if (file_exists($global_assets.'icons/apple-touch-icon-precomposed.png'))
{
	$_template->getTemplateObject('LinkTag')
		->add( array(
			'rel'=>'apple-touch-icon-precomposed',
			'href'=>$global_assets.'icons/apple-touch-icon-precomposed.png'
		) );
}
// => + old ones
$_template->getTemplateObject('LinkTag')->set($old_links);

// ------------------
// TITLE
$old_titles = $_template->getTemplateObject('TitleTag')->get();
$_template->getTemplateObject('TitleTag')->reset();

// => $page_title or $title
if (!empty($page_title))
{
	$_template->getTemplateObject('TitleTag')
		->add( $page_title );
}
elseif (!empty($title))
{
	$_template->getTemplateObject('TitleTag')
		->add( $title );
}
// => + old ones
$_template->getTemplateObject('TitleTag')->set($old_titles);
// => meta_title last
if (!empty($meta_title))
{
	$_template->getTemplateObject('TitleTag')
		->add( $meta_title );
}

// ------------------
// CSS
$old_css = $_template->getTemplateObject('CssFile')->get();
$_template->getTemplateObject('CssFile')->reset();

$_template->getTemplateObject('CssFile')
	->add($boilerplate_assets.'css/normalize.css')
	->add($boilerplate_assets.'css/main.css')
	->add($tple_assets.'css/styles.css')
	// => styles.css
	->add($global_assets.'css/styles.css')
	// => print : printer_styles.css
	->add($global_assets.'css/printer_styles.css','print')
	// => + old ones
	->set($old_css);

// ------------------
// JS in header
$old_header_js = $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->get();
$_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->reset();

$_template->getTemplateObject('JavascriptFile', 'jsfiles_header')
	->addMinified($tple_assets.'vendor_assets/modernizr-2.6.2.min.js')
	// => + old ones
	->set($old_header_js);

// ------------------
// JS in footer
$old_footer_js = $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->get();
$_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->reset();

$_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')
	->addMinified($tple_assets.'vendor_assets/jquery-last.min.js')
	->add($boilerplate_assets.'js/plugins.js')
	->add($tple_assets.'js/scripts.js')
	// => scripts.js
	->add($global_assets.'js/scripts.js')
	// => + old ones
	->set($old_footer_js);

// --------------------------------
// the content
if (empty($content)) $content = '<p>Test content</p>';

//echo '<pre>';var_dump($_template);exit('yo');

// lang info
$lang_info = isset($lang) ? ' lang="'.$lang.'"' : '';

?><!DOCTYPE html>
<!--[if lt IE 7]>      <html<?php echo $lang_info; ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html<?php echo $lang_info; ?> class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html<?php echo $lang_info; ?> class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html<?php echo $lang_info; ?> class="no-js"> <!--<![endif]-->
<head>
<?php
echo
	$_template->getTemplateObject('MetaTag')->write("\n\t\t %s "),
	$_template->getTemplateObject('TitleTag')->write("\n\t\t %s "),
	$_template->getTemplateObject('LinkTag')->write("\n\t\t %s ");

if (true===$minify_css)
	echo $_template->getTemplateObject('CssFile')->minify()->writeMinified("\n\t\t %s ");
elseif (true===$merge_css)
	echo $_template->getTemplateObject('CssFile')->merge()->writeMerged("\n\t\t %s ");
else
	echo $_template->getTemplateObject('CssFile')->write("\n\t\t %s ");

if (true===$minify_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->minify()->writeMinified("\n\t\t %s ");
elseif (true===$merge_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->merge()->writeMerged("\n\t\t %s ");
else
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_header')->write("\n\t\t %s ");

echo "\n";
?>
</head>
<body>
<div id="page-wrapper">
    <!--[if lt IE 7]>
        <p class="chromeframe">
            <?php _trans('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.'); ?>
        </p>
    <![endif]-->
    <a id="top"></a>

<?php if (!empty($title)) : ?>
	<div id="page_header" class="header">
    	<div class="float-left">
    		<h1><?php _echo($title); ?></h1>
    	</div>

    <?php if (!empty($language_selector)) : ?>
    	<div class="float-right">
            <?php echo $language_selector; ?>
    	</div>
    <?php endif; ?>

    	<br class="clearer" />
	</div>
<?php endif; ?>

<?php if (!empty($menu)) : ?>
	<div id="page_menu" class="menu"><ul>
	<?php foreach($menu as $entry_link=>$entry_text) : ?>
		<li><a href="<?php _echo($entry_link); ?>"><?php _echo($entry_text); ?></a></li>
	<?php endforeach; ?>
	</ul></div>
<?php endif; ?>

	<div id="page_content" class="content">

<?php if (!empty($flash_messages)) : ?>
    <?php foreach ($flash_messages as $_flash) : 
        $msg_id = _getid(null, null, true);
    ?>
        <div class="<?php _echo( !empty($_flash['class']) ? $_flash['class'] : 'ok' ); ?>_message" id="<?php echo $msg_id; ?>">
            <a href="#0" onclick="closeMessageBox('<?php echo $msg_id; ?>');" class="message_closer"
                title="<?php _trans_js('Close this message box'); ?>">&nbsp;x&nbsp;</a>
            <?php _echo($_flash['content']); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

	<?php _echo($output); ?>

	</div>

	<div id="page_footer" class="footer">
<?php if (!empty($footer)) : ?>
	<?php _echo($footer); ?>
<?php else: ?>
  <?php view( 'footer', array(
  	'_app'=>$_app,
  	'_config'=>$_globals,
  	'altdb'=>$altdb,
  	'db_queries'=>isset($db_queries) ? $db_queries : array(),
  	'router_views'=>$router_views,
  	'usersession'=>$usersession,
  	'flashsession'=>$flashsession,
	'controller'=>$controller,
	'action'=>$action,
  ) ); ?>
<?php endif; ?>
        <div class="forprinters">
            <?php _trans('Original content on the Internet :'); ?>
            <br />
            <strong><?php _echo(current_url(true)); ?></strong>
            <br />
            <?php _trans('Page rendered in') . ' ' . _echo(round((microtime(true) - $_SERVER['REQUEST_TIME']), 3)) . ' ' . _T('seconds'); ?>
            &nbsp;|&nbsp;
            <?php _echo(date('D d M Y H:i:s')); ?>
        </div>
	</div>

    <a id="bottom"></a>
</div>

<?php
if (true===$minify_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->minify()->writeMinified("\n\t %s ");
elseif (true===$merge_js)
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->merge()->writeMerged("\n\t\t %s ");
else
	echo $_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')->write("\n\t %s ");

echo
	$_template->getTemplateObject('JavascriptTag')->write("%s"),
	$_template->getTemplateObject('CssTag')->write("%s"),
	"\n";
?>
</body>
</html>