<?php
if (empty($debug)) return '';
if (empty($_app)) $_app=array();
if (empty($_globals)) $_globals=array();
if (empty($message)) $message = $debug->renderMessages();
$debug_menu_id = _getid('debug_menu',true,true);

$_template = \CarteBlanche\CarteBlanche::getContainer()->get('template_engine');

_use('debug');
_use('tab-content');

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
// metas
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
// => no robots
$_template->getTemplateObject('MetaTag')
	->add('robots', 'none');

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
	->add($global_assets.'css/printer_styles.css','print');
	// => profiler.css
if (@file_exists(_ASSETS.'css/profiler.css'))
	$_template->getTemplateObject('CssFile')
		->add('css/profiler.css');
$_template->getTemplateObject('CssFile')
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
	->add($global_assets.'js/scripts.js');
	// => profiler.js
if (@file_exists(_ASSETS.'js/profiler.js'))
	$_template->getTemplateObject('JavascriptFile')
		->add('js/profiler.js');
	// => + old ones
$_template->getTemplateObject('JavascriptFile', 'jsfiles_footer')
	->set($old_footer_js);

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php
echo
	$_template->getTemplateObject('MetaTag')->write("\n\t %s "),
	$_template->getTemplateObject('TitleTag')->write("\n\t %s "),
	$_template->getTemplateObject('CssFile')->minify()->writeMinified("\n\t %s "),
	$_template->getTemplateObject('JavascriptFile')->minify()->writeMinified("\n\t %s "),
	$_template->getTemplateObject('LinkTag')->write("\n\t %s "),
	"\n";
?>
</head>
<body>
<?php if (!empty($title)) : ?>
<div id="<?php _getid('page_header'); ?>" class="header">
    <h1><?php echo $title; ?></h1>
</div>
<?php endif; ?>
<div id="<?php _getid('page_content'); ?>" class="content">

<?php if (!empty($flash_message)) : ?>
    <div class="<?php echo( !empty($flash_message_class) ? $flash_message_class : 'ok' ); ?>_message">
        <?php echo $flash_message; ?>
    </div>
<?php endif; ?>

    <div class="debugger">

        <br class="clear" />
        <div class="header_info">
            <?php echo $debug->profiler->renderProfilingInfo(); ?>
        </div>
        <br class="clear" />

<?php if (!empty($message)) : ?>
        <br class="clear" />
        <div class="debug_message">
            <?php echo $message; ?>
        </div>
        <br class="clear" />
<?php endif; ?>

        <div class="tab-container">
            <div id="<?php _getid('page_menu'); ?>" class="debug_menu">
                <a id="<?php echo $debug_menu_id; ?>"></a>
                <ul id="<?php _getid('debug_tabs'); ?>" class="tabs tab-list">
<?php foreach ($debug->getStacks() as $_i=>$_stack) : ?>
<?php if ($_stack->getType()!='message') : ?>
                    <li class="tab-handler"><a href="#<?php echo $_i; ?>"><?php echo $_stack->getTitle(); ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
                </ul>
            </div>
<?php foreach ($debug->getStacks() as $_i=>$_stack) : ?>
<?php if ($_stack->getType()=='object') : ?>
            <div id="<?php echo $_i; ?>" class="tab-content tabcontents">
                <h3><?php echo $_stack->getTitle(); ?></h3>
                <pre><?php print_r( $_stack->getEntity() ); ?></pre>
                <div class="back_link">[ <a href="#<?php echo $debug_menu_id; ?>">back to menu</a> ]</div>
            </div>
<?php elseif ($_stack->getType()!='message') : ?>
            <div id="<?php echo $_i; ?>" class="tab-content tabcontents">
                <h3><?php echo $_stack->getTitle(); ?></h3>
                <?php echo $_stack; ?>
                <div class="back_link">[ <a href="#<?php echo $debug_menu_id; ?>">back to menu</a> ]</div>
            </div>
<?php endif; ?>
<?php endforeach; ?>
        </div>

        <div id="<?php _getid('page_footer'); ?>" class="footer">
            <a href="<?php echo get_path('root_file'); ?>">home</a>
            &nbsp;|&nbsp;
            <a href="<?php echo build_url('action','check_system'); ?>">versions</a>
            &nbsp;|&nbsp;
            <a href="<?php echo build_url(array(
                'action'=>'tables_structure', 'altdb'=>$altdb
            )); ?>">tables structure</a>
            &nbsp;|&nbsp;
            <a href="<?php echo build_url('altdb','int'); ?>">doc</a>
            &nbsp;|&nbsp;
            Page rendered in <?php echo round((microtime(true) - $_SERVER['REQUEST_TIME']), 3); ?> seconds
            <div class="profiler">
                [ <a href="<?php echo $return_url; ?>" title='Return to normal page'>back to previous page</a> ]
            </div>
        </div>

    </div>
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
<script language="Javascript" type="text/javascript">
onDocumentLoad(function() {init_tab();});
function init_tab() {
	window.tabs = new TabContent({
		handler_list_id: '<?php _getid('debug_tabs'); ?>',
		collapsible: true,
		cookie_name: 'debug_tab'
	});
}
</script>
</body>
</html>