<?php

if (empty($_app)) $_app=array();
if (empty($_globals)) $_globals=array();

$profiler_id = _getid('profiler',true,true);
$dbgfooter_id = _getid('debug_footer',true,true);

$mode = \CarteBlanche\CarteBlanche::getKernelMode();
$modes = \CarteBlanche\CarteBlanche::getKernelMode(true);
$show_profiler = isset($modes['show_profiler']) && $modes['show_profiler'];
if ($show_profiler) {
    _use('debug');
    _use('effect-blind');
// body onload="Blind('$profiler_id');" 
}

?>
<div class="forscreens">
	<a href="<?php echo get_path('root_file'); ?>"
	    title="<?php _trans_js('Go back home'); ?>"><?php _trans('Home'); ?></a>
<?php if ($show_profiler) : ?>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url('action','check_system'); ?>"
	    title="<?php _trans_js('See system versions'); ?>"><?php _trans('Versions'); ?></a>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url(array(
		'controller'=>'data', 'action'=>'tables_structure', 'altdb'=>$altdb
	)); ?>"
	    title="<?php _trans_js('See tables structure'); ?>"><?php _trans('Tables structure'); ?></a>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url(array('controller'=>'default','action'=>'doc')); ?>"
	    title="<?php _trans_js('See documentation'); ?>">
	    <?php _trans('Documentation'); ?>
	</a>
<?php endif; ?>
	&nbsp;|&nbsp;
	<a href="<?php echo build_url(array('controller'=>'default','action'=>'credits')); ?>"
	    title="<?php _trans_js('See application credits'); ?>">
	    <?php _trans('Credits'); ?>
	</a>
<?php if (!empty($_app['author']) && !empty($_app['website'])) : ?>
	&nbsp;|&nbsp;
	<a href="http://<?php echo $_app['website']; ?>" title="<?php echo $_app['name'].' '.$_app['version']; ?> by <?php echo $_app['author']; ?>" target="_blank"><?php echo $_app['author']; ?></a>
<?php endif; ?>
	&nbsp;|&nbsp;
    <?php _trans('Page rendered in %time% seconds', array('time'=>round((microtime(true) - $_SERVER['REQUEST_TIME']), 3))); ?>
<?php if ($show_profiler) : ?>

	<div class="profiler">
		<div class="profiler_handler">
			[ <a href="javascript:void(0);" onclick="Blind('<?php echo $profiler_id; ?>');" title="<?php _trans_js('Show or hide profiler'); ?>"><?php _trans('+ profiler'); ?></a> ]
		</div>
		<div id="<?php echo $profiler_id; ?>" class="profiler-content" style="display:none">
		    <h4><?php _trans('Profiler for request: %url%', array('url'=>$profiler['request']));?></h4>
			<table class="profiler_table">
			<tr>
                <td colspan="3">
                <p>
                    <a href="<?php 
                        $cur_url = current_url(true);
                        echo $cur_url.( 0===substr_count($cur_url, '?') ? '?' : '&amp;' ).'debug=1';
                    ?>" title="<?php _trans_js('Debug current request'); ?>"><?php _trans('debug'); ?></a>
                    &nbsp;|&nbsp;
                    <a href="<?php echo build_url(array(
                        'controller'=>'dev','action'=>'app_map'
                    )); ?>" title="<?php _trans_js('See application map'); ?>"><?php _trans('app map'); ?></a>
                    &nbsp;|&nbsp;
                    <a href="<?php echo build_url(array(
                        'controller'=>'dev','action'=>'cb_config'
                    )); ?>" title="<?php _trans_js('See application configuration'); ?>"><?php _trans('CB config'); ?></a>
                    &nbsp;|&nbsp;
                    <a href="<?php echo build_url(array(
                        'controller'=>'dev','action'=>'test'
                    )); ?>" title="<?php _trans_js('See application test page'); ?>"><?php _trans('test page'); ?></a>
                    &nbsp;|&nbsp;
                    <a href="<?php
                        _echo(@file_exists(_ROOTPATH.'/phpdoc/') ? '../phpdoc/' : \CarteBlanche\App\Kernel::$CARTE_BLANCHE_DOCUMENTATION);
                    ?>" title="<?php _trans_js('See application documentation'); ?>"><?php _trans('documentation'); ?></a>
                </p>
                </td>
            </tr><tr>
<?php if (!empty($db_queries)) : ?>
			<td>
			<p><strong><?php _trans(array(
    			    0=>'no DB query',
    			    1=>'1 DB query',
    			    2=>'%nb% DB queries'
			    ), count($db_queries)); ?></strong></p>
			<ul>
	<?php foreach($db_queries as $_query) : ?>
		<li><?php echo $_query; ?></li>
	<?php endforeach; ?>
			</ul>
			</td>
<?php else: ?>
			<td>
			<p><strong><?php _trans('no DB query'); ?></strong></p>
			</td>
<?php endif; ?>

<?php if (!empty($router_views)) : ?>
			<td>
			<p><strong><?php _trans(array(
    			    0=>'no view parsed',
    			    1=>'1 view parsed',
    			    2=>'%nb% views parsed'
			    ), count($router_views)); ?></strong></p>
			<ul>
	<?php foreach($router_views as $_viewid=>$_view) : ?>
		<li>
		    <?php echo ($_view['iterations']>1 ? $_view['iterations'].'x ' : ''),
    		        $_view['tpl'], ' => ', count($_view['params']), ' params'; ?>
		</li>
	<?php endforeach; ?>
			</ul>
			</td>
<?php else: ?>
			<td>
			<p><strong><?php _trans('no view parsed'); ?></strong></p>
			</td>
<?php endif; ?>

			<td>
			<p><strong><?php _trans('controller / action'); ?></strong></p>
			<ul>
			<li>
			<?php echo 
					sprintf(\DevDebug\Profiler::mask_abbr, \DevDebug\Profiler::formatClassName( \DevDebug\Profiler::buildClassInfo($controller) ), $controller); 
			?> :: <span title="Method"><?php echo $action; ?></span>
			</li>
			</ul>

<?php if (!empty($usersession)) : ?>
			<p><strong><?php _trans(array(
    			    0=>'no session entry',
    			    1=>'1 session entry',
    			    2=>'%nb% session entries',
			    ), count($usersession)); ?></strong></p>
			<ul>
	<?php foreach($usersession as $_sess_var=>$_sess_val) : ?>
		<li><?php echo $_sess_var; ?> => <?php echo is_string($_sess_val) ? htmlentities($_sess_val) : var_export($_sess_val,1); ?></li>
	<?php endforeach; ?>
			</ul>
<?php else: ?>
			<p><strong><?php _trans('no session entry'); ?></strong></p>
<?php endif; ?>

<?php if (!empty($flashsession)) : ?>
			<p><strong><?php _trans(array(
    			    0=>'no session flash entry',
    			    1=>'1 session flash entry',
    			    2=>'%nb% session flash entries',
			    ), count($flashsession)); ?></strong></p>
			<ul>
	<?php foreach($flashsession as $_sess_var=>$_sess_val) : ?>
		<li><?php echo $_sess_var; ?> => <?php echo is_string($_sess_val) ? htmlentities($_sess_val) : var_export($_sess_val,1); ?></li>
	<?php endforeach; ?>
			</ul>
<?php else: ?>
			<p><strong><?php _trans('no session flash entry'); ?></strong></p>
<?php endif; ?>

			</td>

			</tr><tr>
			<td colspan="3" class="profiler-list">
                <ul>
                    <li><?php _trans('app:'); ?> <strong><?php echo $_app['name'] . ' ' . $_app['version']
                        . ' ' .(isset($profiler['git_clone']) && $profiler['git_clone'] ? ' - '._T('clone') : ''); ?></strong></li>
                    <li><?php _trans('date:'); ?> 
                        <?php echo $profiler['date']->format('D'); ?>
                        <strong><?php echo $profiler['date']->format('d M Y H:i:s'); ?></strong> 
                        <?php echo $profiler['timezone']; ?>
                         [<abbr title="<?php echo $profiler['date']->format('c'); ?>"><?php _trans('ISO date'); ?></abbr>]
                    </li>
                    <li>
                        <?php _trans('PHP vers.:'); ?> <strong><?php echo $profiler['php_version']; ?></strong> (<?php echo $profiler['php_sapi_name']; ?>)
                        &nbsp;-&nbsp;
                        <?php _trans('SQLite vers.:'); ?> <strong><?php echo $profiler['sqlite_version']; ?></strong>
                    </li>
                    <li><?php _trans('Webserver system:'); ?> <strong><?php echo $profiler['server_version']; ?></strong></li>
                    <li><?php _trans('Server OS:'); ?> <strong><?php echo $profiler['php_uname']; ?></strong></li>
                    <li><?php _trans('Browser/Device:'); ?> <strong><?php echo $profiler['user_agent']; ?></strong></li>
                    <li><?php _trans('Page rendered in %time% seconds', array('time'=>round((microtime(true) - $_SERVER['REQUEST_TIME']), 3))); ?></li>
                </ul>
			</td>
			</tr></table>
		</div>
	</div>
	<a id="<?php echo $dbgfooter_id; ?>"></a>
<?php endif; ?>
</div>
