<div class="content">

<?php echo @$content; ?>

<br />

<button onclick="document.location.href = '<?php echo $sitemap_url; ?>'"
    title="<?php _trans_js('Complete list of website contents'); ?>">
        <?php _trans('Go to sitemap'); ?>
</button>

<button onclick="document.location.href = '<?php echo $report_error; ?>'"
    title="<?php _trans_js('Transmit an email to the website administrators with your error informations and environement'); ?>">
        <?php _trans('Transmit a report'); ?>
</button>

<br />
<br />

</div>