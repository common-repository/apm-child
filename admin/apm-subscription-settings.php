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

// Preset form fields
$form = array(
	'first_name' => '',
	'last_name' => '',
	'email' => '',
	'aweber_consumer_key' => '',
	'aweber_consumer_secret' => '',
	'aweber_account_number' => '',
);

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

$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
$record = array();
$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);

$apm_first_name = $recordCrediantial['first_name'] ? $recordCrediantial['first_name'] : '';
$apm_last_name = $recordCrediantial['last_name'] ? $recordCrediantial['last_name'] : '';
$apm_email = $recordCrediantial['email'] ? $recordCrediantial['email'] : '';
$apm_jv_zoo_id = $recordCrediantial['jv_zoo_id'] ? $recordCrediantial['jv_zoo_id'] : '';
$apm_clickbank_affiliate_nickname = $recordCrediantial['clickbank_affiliate_nickname'] ? $recordCrediantial['clickbank_affiliate_nickname'] : '';
$apm_warriorplus_aff_id = $recordCrediantial['warriorplus_aff_id'] ? $recordCrediantial['warriorplus_aff_id'] : '';
$apm_thrivecart_affiliate_username = $recordCrediantial['thrivecart_affiliate_username'] ? $recordCrediantial['thrivecart_affiliate_username'] : '';
$apm_email_service_provider = $recordCrediantial['email_service_provider'] ? $recordCrediantial['email_service_provider'] : '';

?>

<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(APM_EGMAILl_TITLE . ' - Main Settings', 'apm-child'); ?></h2>
    
    <div style="width:100%; float:left">
    
    <form name="frm_subscription" method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=apm_list_aweber">
	<input type="hidden" name="hdn_page_subscription" value="Yes" />
    <h3><?php //_e('AWeber Subscription Settings', 'apm-child'); ?> <span style="color:#F00; font-size:13px; display:block; float:right"> *All fields are mandatory.</span></h3>
        <div style="width:50%; float:left">
            
            <label for="first_name">First Name</label>
            <input class="txt-apmbox" name="first_name" type="text" id="first_name" value="<?php echo esc_html($apm_first_name);?>" size="50" />

            <label for="last_name">Last Name</label>
            <input class="txt-apmbox" name="last_name" type="text" id="last_name" value="<?php echo esc_html($apm_last_name);?>" size="50" />
            
            <label for="email">Email</label>
            <input class="txt-apmbox" name="email" type="email" id="email" value="<?php echo esc_html($apm_email);?>" size="50" /> 
            
            <label for="jv_zoo_id">JV Zoo ID</label>
            <input class="txt-apmbox" name="jv_zoo_id" type="text" id="jv_zoo_id" value="<?php echo esc_html($apm_jv_zoo_id);?>" size="50" /> <span> Open JVZoo ID  <a href="https://www.nick-james.com/jvzoo" target="_blank">click here</a></span>
            
            <label for="clickbank_affiliate_nickname">ClickBank Affiliate Username</label>
            <input class="txt-apmbox" name="clickbank_affiliate_nickname" type="text" id="clickbank_affiliate_nickname" value="<?php echo esc_html($apm_clickbank_affiliate_nickname);?>" size="50" /> <span> Open ClickBank Username <a href="https://www.nick-james.com/clickbank" target="_blank">click here</a></span>
            
            
            <label for="warriorplus_affiliate_id">WarriorPlus Affiliate ID</label>
            <input class="txt-apmbox" name="warriorplus_affiliate_id" type="text" id="warriorplus_affiliate_id" value="<?php echo esc_html($apm_warriorplus_aff_id);?>" size="50" /> <span> Open WarriorPlus ID  <a href="https://www.nick-james.com/warriorplus" target="_blank">click here</a></span>


            <label for="thrivecart_affiliate_username">ThriveCart Affiliate Username</label>
            <input class="txt-apmbox" name="thrivecart_affiliate_username" type="text" id="thrivecart_affiliate_username" value="<?php echo esc_html($apm_thrivecart_affiliate_username);?>" size="50" /> <span> Open ThriveCart Username <a href="https://www.Nick-James.com/thrivecart" target="_blank">click here</a></span>
            
            
            <label for="email_service_provider">Email Service Provider</label>
            <select name="email_service_provider" id="email_service_provider">
            	<option value="">_____</option>
                <option value="Aweber" <?php if($apm_email_service_provider == "Aweber"){?> selected="selected" <?php } ?>>Aweber</option>
                <option value="Sendeagle" <?php if($apm_email_service_provider == "Sendeagle"){?> selected="selected" <?php } ?>>Send Eagle</option>
                <option value="Infusionsoft" <?php if($apm_email_service_provider == "Infusionsoft"){?> selected="selected" <?php } ?>>Infusionsoft/Keap</option>
                <option value="GetResponse" <?php if($apm_email_service_provider == "GetResponse"){?> selected="selected" <?php } ?>>GetResponse</option>
            </select>

            <input type="hidden" name="frm_subscription_submit" value="yes"/>
            <p class="submit"><input name="publish" lang="publish" class="button button-primary add-new-h2" value="Save" type="submit" onclick="return submit_aweber_option()" /></p>
            
            <br />
           <!-- <p>Copy the listener URL and paste the value into your JVZoo Affiliate settings.</p>
    <input type="text" class="jvzoo_ipn_clickboard_txt_box" id="copy_jvzoo_ipn_clickboard" value="<?php echo site_url()?>/apm-jvzoo-ipn-listener" readonly="readonly" /> <a onclick="copyJVZooIpnClickboard();" class="button jvzoo_ipn_clickboard_btn button-primary add-new-h2"> Copy to clipboard </a>
    		<br /><br />-->
            
            
            <br />
            <p>Copy the listener URL and paste the value into your JVZoo Affiliate settings.</p>
    <input type="text" class="jvzoo_ipn_clickboard_txt_box" id="master_jvzoo_ipn_clickboard_txt_box" value="https://affiliatepromembership.com/jvzoo_affiliate_ipn.php" readonly="readonly" /> <a onclick="copyJVZooIpnMasterClickboard();" class="button jvzoo_ipn_clickboard_btn button-primary add-new-h2"> Copy to clipboard </a>
    		<br /><br />
            
            
            <p>Copy the listener URL and paste the value into your WarriorPlus Affiliate settings.</p>
            <input type="text" class="jvzoo_ipn_clickboard_txt_box" id="copy_warriorplus_affliate_ipn_clickboard" value="https://affiliatepromembership.com/warriorplus_affliate_ipn.php" readonly="readonly" /> <a onclick="copyWarriorPlusClickboard();" class="button jvzoo_ipn_clickboard_btn button-primary add-new-h2"> Copy to clipboard </a>

        </div>
        
    <?php wp_nonce_field('frm_subscription'); ?>
    </form>
    </div>
</div>
</div>