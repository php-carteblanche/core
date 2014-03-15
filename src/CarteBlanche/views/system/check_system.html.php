<?php

if (!isset($errors)) $errors = array();
if (!isset($contents)) $contents = array();


if (empty($errors)): ?>

<div class="ok_message">
    <?php _trans('OK - Your system seems to be enough to let this application work correctly.'); ?>
<?php
/*
				.(CarteBlanche::getKernel()->isInstalled($_altdb) ? '' : '<a href="'.$this->getContainer()->get('router')->buildUrl(array(
					'action'=>'install', 'altdb'=>$_altdb
				)).'">clic here to install it</a>')
*/
?>
</div>
<ul>
    <?php foreach ($contents as $ctt): ?>
        <li><?php echo $ctt; ?></li>
    <?php endforeach; ?>
</ul>

<?php else: ?>

<div class="error_message">
    <?php _trans('! - Your system doesn\'t seem to be enough to let this application work correctly! (<em>see errors below</em>).'); ?>
</div>
<ul>
    <?php foreach ($errors as $err): ?>
        <li><?php echo $err; ?></li>
    <?php endforeach; ?>
</ul>
<ul>
    <?php foreach ($contents as $ctt): ?>
        <li><?php echo $ctt; ?></li>
    <?php endforeach; ?>
</ul>

<?php endif; ?>
