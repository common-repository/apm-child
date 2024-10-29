<?php 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 
if(!current_user_can('edit_others_pages'))
{
	die('You are not allowed to work.');
}
?>
<div class="wrap">
<?php

$egmail_errors = array();
$eemail_success = '';
$egmail_error_found = FALSE;


if(isset($_POST['frm_plugin']) && $_POST['frm_plugin'] != '')
{
	if(apm_check_login())
	{
		echo '<script type="text/javascript"> window.location = \''.get_option('siteurl').'/wp-admin/admin.php?page=apm-child\'</script>';
		exit();
		
	} else {
		$eemail_success = 'Authentication failed. You are logged in from invalid website name.';
	}
}


if ($egmail_error_found == TRUE && isset($egmail_errors[0]) == TRUE)
{
	?>
    
	<div class="error fade">
		<p><strong><?php echo $egmail_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($egmail_error_found == FALSE && strlen($eemail_success) > 0)
{
	?>
	  <div class="updated fade">
		<p><strong><?php echo $eemail_success; ?></strong></p>
	  </div>
	  <?php
}
?>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>First Login to access this plugin.</h2>
    <p>Please log into your Affiliate Pro Membership Partner Plugin by clicking on below button.</p>
    <div style="width:100%; float:left">
    
    <form name="frm_plugin_login" method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=apm-child">
	<input type="hidden" name="hdn_page_subscription" value="Yes" />
    <h3><span style="font-size:13px; display:block; float:left; width:100%; margin:10px 0;"> </span></h3>
        <div style="width:50%; float:left;">
            <input type="hidden" name="frm_plugin" value="yes"/>
            <p class="submit"><input name="publish" lang="publish" class="button button-primary add-new-h2" value="Login to Affiliate Pro Membership" type="submit" onclick="return submit_login_form()" /></p>
        </div>
        
    <?php wp_nonce_field('frm_subscription'); ?>
    </form>
    </div>
</div>
</div>