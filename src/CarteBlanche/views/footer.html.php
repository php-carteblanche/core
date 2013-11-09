<?php
if (empty($_app)) $_app=array();
if (empty($_globals)) $_globals=array();
$profiler_id = _getid('profiler',true,true);
$dbgfooter_id = _getid('debug_footer',true,true);
?>
<div class="forscreens">
	<a href="<?php echo get_path('root_file'); ?>">home</a>
<?php if (defined('_APP_MODE') && _APP_MODE=='dev') : ?>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url('action','check_system'); ?>">versions</a>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url(array(
		'controller'=>'data', 'action'=>'tables_structure', 'altdb'=>$altdb
	)); ?>">tables structure</a>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url('altdb','int'); ?>">doc</a>
<?php endif; ?>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url(array('controller'=>'default','action'=>'credits')); ?>">credits</a>
<?php if (!empty($_app['author']) && !empty($_app['website'])) : ?>
	&nbsp;|&nbsp;
	<a href="http://<?php echo $_app['website']; ?>" title="<?php echo $_app['name'].' '.$_app['version']; ?> by <?php echo $_app['author']; ?>" target="_blank"><?php echo $_app['author']; ?></a>
<?php endif; ?>
	&nbsp;|&nbsp;
	Page rendered in <?php echo round((microtime(true) - $_SERVER['REQUEST_TIME']), 3); ?> seconds
<?php if (defined('_APP_MODE') && _APP_MODE=='dev') : ?>
	<div class="profiler">
		<div class="profiler_handler">
			[ <a href="javascript:show_hide('<?php echo $profiler_id; ?>','show','<?php echo $dbgfooter_id; ?>');" title="Show or hide profiler">+ profiler</a> ]
		</div>
		<br /><div id="<?php echo $profiler_id; ?>" class="hide">
			<p>
				<a href="<?php 
					$cur_url = current_url(true);
					echo $cur_url.( 0===substr_count($cur_url, '?') ? '?' : '&amp;' ).'debug=1';
				?>">debug</a>
				&nbsp;|&nbsp;
				<a href="<?php echo build_url(array(
					'controller'=>'dev','action'=>'app_map'
				)); ?>">app map</a>
				&nbsp;|&nbsp;
				<a href="<?php echo build_url(array(
					'controller'=>'dev','action'=>'cb_config'
				)); ?>">CB config</a>
				&nbsp;|&nbsp;
				<a href="<?php echo build_url(array(
					'controller'=>'dev','action'=>'test'
				)); ?>">test page</a>
				&nbsp;|&nbsp;
				<a href="<?php
				    _echo(@file_exists(_ROOTPATH.'/phpdoc/') ? '../phpdoc/' : \CarteBlanche\App\Kernel::$CARTE_BLANCHE_DOCUMENTATION);
				?>">documentation</a>
			</p>
			<table class="profiler_table"><tr>
<?php if (!empty($db_queries)) : ?>
			<td>
			<p><strong><?php echo count($db_queries); ?> DB queries</strong></p>
			<ul>
	<?php foreach($db_queries as $_query) : ?>
		<li><?php echo $_query; ?></li>
	<?php endforeach; ?>
			</ul>
			</td>
<?php else: ?>
			<td>
			<p><strong>No DB query</strong></p>
			</td>
<?php endif; ?>

<?php if (!empty($router_views)) : ?>
			<td>
			<p><strong><?php echo count($router_views); ?> views parsed</strong></p>
			<ul>
	<?php foreach($router_views as $_viewid=>$_view) : ?>
		<li><?php echo $_view['tpl']; ?> => <?php echo count($_view['params']); ?> params</li>
	<?php endforeach; ?>
			</ul>
			</td>
<?php endif; ?>

			<td>
			<p><strong>controller / action</strong></p>
			<ul>
			<li>
			<?php echo 
					sprintf(\DevDebug\Profiler::mask_abbr, \DevDebug\Profiler::formatClassName( \DevDebug\Profiler::buildClassInfo($controller) ), $controller); 
			?> :: <span title="Method"><?php echo $action; ?></span>
			</li>
			</ul>

<?php if (!empty($usersession)) : ?>
			<p><strong><?php echo count($usersession); ?> session entries</strong></p>
			<ul>
	<?php foreach($usersession as $_sess_var=>$_sess_val) : ?>
		<li><?php echo $_sess_var; ?> => <?php echo is_string($_sess_val) ? htmlentities($_sess_val) : var_export($_sess_val,1); ?></li>
	<?php endforeach; ?>
			</ul>
<?php else: ?>
			<p><strong>No session entry</strong></p>
<?php endif; ?>

<?php if (!empty($flashsession)) : ?>
			<p><strong><?php echo count($flashsession); ?> flash session entries</strong></p>
			<ul>
	<?php foreach($flashsession as $_sess_var=>$_sess_val) : ?>
		<li><?php echo $_sess_var; ?> => <?php echo is_string($_sess_val) ? htmlentities($_sess_val) : var_export($_sess_val,1); ?></li>
	<?php endforeach; ?>
			</ul>
<?php else: ?>
			<p><strong>No session flash entry</strong></p>
<?php endif; ?>

			</td>

			</tr><tr>
			<td colspan="3">
			<?php echo date('D'); ?> <strong><?php echo date('d M Y H:i:s'); ?></strong> 
			<?php echo date_default_timezone_get(); ?> [<abbr title="ISO date"><em><?php echo date('c'); ?></em></abbr>]
			&nbsp;&nbsp;|&nbsp;&nbsp;PHP vers. <strong><?php echo phpversion(); ?></strong> (<?php echo php_sapi_name(); ?>)
			&nbsp;&nbsp;|&nbsp;&nbsp;SQLite vers. <strong><?php echo sqlite_libversion(); ?></strong>
			<br />Webserver system :&nbsp;&nbsp;<strong><?php echo apache_get_version(); ?></strong>
			<br />Server OS :&nbsp;&nbsp;<strong><?php echo php_uname(); ?></strong>
			<br />Browser / Device :&nbsp;&nbsp;<strong><?php echo $_SERVER['HTTP_USER_AGENT']; ?></strong>
			</td>
			</tr></table>
		</div>
	</div>
	<a id="<?php echo $dbgfooter_id; ?>"></a>
<?php endif; ?>
</div>
