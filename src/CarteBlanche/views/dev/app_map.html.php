<?php

if (empty($app_controllers)) $app_controllers=array();
if (empty($bundles_controllers)) $bundles_controllers=array();
if (!isset($debug)) $debug=false;

?>

<h3>Controllers list</h3>

<ul>
<?php if (0!=count($app_controllers)) : ?>

	<?php foreach ($app_controllers as $_ctrl) : ?>
	<li>
		<a href="<?php echo build_url('controller', $_ctrl->short_name); ?>" title="Test the default action of this controller"><strong><?php echo $_ctrl->short_name; ?></strong></a>
		&nbsp;<a href="<?php echo build_url('controller', $_ctrl->short_name); ?>" title="Test the default action of this controller in a new window" target="_blank" class="external">&nbsp;</a>
		<?php if ($debug): ?> (<?php echo $_ctrl->name; ?>)<?php endif; ?>
		<?php if (0!=count($_ctrl->methods)) : ?>
		<?php $_ctrl_id = _getid(null,null,true); ?>
		<br /><small>[<a href="javascript:show_hide('<?php echo $_ctrl_id; ?>','show');" title="See controller's methods list">methods</a>]</small>
		<ul id="<?php echo $_ctrl_id; ?>" class="hide">
			<?php foreach ($_ctrl->methods as $_meth) : ?>
			<li><a href="<?php echo build_url(array(
				'controller'=>$_ctrl->short_name, 'action'=>$_meth->short_name
			)); ?>" title="Test this method"><strong><?php echo $_meth->name; ?></strong></a>
			&nbsp;<a href="<?php echo build_url(array(
				'controller'=>$_ctrl->short_name, 'action'=>$_meth->short_name
			)); ?>" title="Test this method in a new window" target="_blank" class="external">&nbsp;</a>
			<?php if ($_meth->expect_arguments): ?> (<em>arguments expected</em>)<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>

<?php endif; ?>

<?php if (0!=count($bundles_controllers)) : ?>

	<?php foreach ($bundles_controllers as $_bundle=>$_ctrls) : ?>
	<?php if (is_array($_ctrls) && 0!=count($_ctrls)) : ?>
	<li>In bundle "<strong><?php echo $_bundle; ?></strong>":
	<ul>
		<?php if (is_array($_ctrls)) foreach ($_ctrls as $_ctrl) : ?>
		<li>
			<a href="<?php echo build_url('controller', $_ctrl->short_name); ?>" title="Test the default action of this controller"><strong><?php echo $_ctrl->short_name; ?></strong></a>
			&nbsp;<a href="<?php echo build_url('controller', $_ctrl->short_name); ?>" title="Test the default action of this controller in a new window" target="_blank" class="external">&nbsp;</a>
			<?php if ($debug): ?> (<?php echo $_ctrl->name; ?>)<?php endif; ?>
		<?php if (0!=count($_ctrl->methods)) : ?>
		<?php $_ctrl_id = _getid(null,null,true); ?>
		<br /><small>[<a href="javascript:show_hide('<?php echo $_ctrl_id; ?>','show');" title="See controller's methods list">methods</a>]</small>
		<ul id="<?php echo $_ctrl_id; ?>" class="hide">
			<?php foreach ($_ctrl->methods as $_meth) : ?>
			<li><a href="<?php echo build_url(array(
				'controller'=>$_ctrl->short_name, 'action'=>$_meth->short_name
			)); ?>" title="Test this method"><strong><?php echo $_meth->name; ?></strong></a>
			&nbsp;<a href="<?php echo build_url(array(
				'controller'=>$_ctrl->short_name, 'action'=>$_meth->short_name
			)); ?>" title="Test this method in a new window" target="_blank" class="external">&nbsp;</a>
			<?php if ($_meth->expect_arguments): ?> (<em>arguments excpected</em>)<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
	</li>
	<?php endif; ?>
	<?php endforeach; ?>

<?php endif; ?>

</ul>