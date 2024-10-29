<?php 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 
if(!current_user_can('edit_others_pages'))
{
	die('You are not allowed to work.');
}
?>
<div class="wrap">
    <div class="form-wrap">
        <div id="icon-plugins" class="icon32"></div>
        <h2>APM Plugin Information</h2>
        <div class="row-full-width">
        
        <p>&nbsp;</p>
        <p>You can also run the income update manually. Please be patient and do not refresh the page, it may take some time.</p>
        <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=apm-child&ac=manual-income" target="_blank">RUN CRON MANUALLY</a>
        
        </div>
    </div>
</div>