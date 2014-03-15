<?php
if (empty($errors)) $errors = array();
if (empty($original_errors)) $original_errors = $errors;
?>
<div class="content">

<p><?php _trans('The following errors had been found booting the system. You MUST correct them to use CarteBlanche correctly.'); ?></p>
<?php foreach ($original_errors as $_error) : ?>

    <div class="<?php 
        echo (false!==array_search($_error, $errors) ? 'error' : 'ok');
    ?>_message">
        <?php echo $_error; ?>
    </div>

<?php endforeach; ?>

<?php if (!empty($running_user)) : ?>
    <p>
        <?php _trans('For information and debugging, the application is running using the system\'s user "%user%"', array('user'=>$running_user)); ?></var>.
    </p>
<?php endif; ?>

</div>