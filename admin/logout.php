<?php 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 
if(!current_user_can('edit_others_pages'))
{
	die('You are not allowed to work.');
}
?>
<?php
apm_logout();
?>
<div class="wrap">
<link rel='stylesheet' href='<?php echo APM_SUBSCRIPTION_PATH?>css/apm-style.css' type='text/css' media='all' />
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>Logout</h2>
    <div style="width:100%; float:left">
    	<h3>You have successfully logout</h3>
    </div>
</div>
</div>