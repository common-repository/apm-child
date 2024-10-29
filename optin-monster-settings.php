<?php

function apm_optin_monster_session_functions()
{

	global $wpdb;

	

	$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";

	$record = array();

	$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);

	

	

	if(apm_get_session_value('apm_ses_epm_magic_click_id') == '')

	{

		if(isset($_REQUEST['clickid']) && sanitize_text_field($_REQUEST['clickid']) != '')

		{

			apm_set_session_value('apm_ses_epm_magic_click_id',sanitize_text_field($_REQUEST['clickid']));

		}

	}

	

	if(isset($_REQUEST['clickid']) && sanitize_text_field($_REQUEST['clickid']) != '' && apm_get_session_value('apm_ses_epm_magic_click_id') != sanitize_text_field($_REQUEST['clickid']))

	{

		apm_set_session_value('apm_ses_epm_magic_click_id', sanitize_text_field($_REQUEST['clickid']));

	}

}

function apm_optin_monster_functions()
{



	global $wpdb;



	
	

	$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";

	$record = array();

	$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);

	$apm_ses_epm_magic_click_id = '';

	

	if(apm_get_session_value('apm_ses_epm_magic_click_id') != '')

	{

		$apm_ses_epm_magic_click_id = apm_get_session_value('apm_ses_epm_magic_click_id');

	}

	

	/** Start Google reCAPTCHA coding **/

	$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
	
	$postdata = array(
		'mode' => 'get_google_recaptcha',
		'aff_id' => $recordCrediantial['user_email_id']
	);
	$args = array(
		'body' => $postdata,
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'cookies' => array()
	);
	
	
	$response = wp_remote_post( $url, $args );
	$resultList = $response['body'];
	$resultList = json_decode($resultList); 
	
	$recaptcha_site_key = $resultList->google_recaptcha_key;
	
	$recaptcha_key_message = '';

	if($recaptcha_site_key == '')
	{
		$recaptcha_key_message = "Google reCAPTCHA key has not assigned for this affiliate member.";
	}
	
?>


<script type="text/javascript">

	function OptinMonsterCustomVariables(app)
	{
		app.setCustomVariable('val_error_message', '<?php echo $recaptcha_key_message;?>');

		app.setCustomVariable('account_id', '<?php echo esc_html($recordCrediantial['aweber_account_number'],'apm-child')?>');

		app.setCustomVariable('list_id', '<?php echo esc_html($recordCrediantial['aweber_list_id'],'apm-child')?>');

		app.setCustomVariable('clickbank_nickname', '<?php echo __($recordCrediantial['clickbank_affiliate_nickname'],'apm-child')?>');

		app.setCustomVariable('thrivecart_username', '<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>');

		app.setCustomVariable('warriorplus_aff_id', '<?php echo __($recordCrediantial['warriorplus_aff_id'],'apm-child')?>');

		app.setCustomVariable('jv_zoo_id', '<?php echo __($recordCrediantial['jv_zoo_id'],'apm-child')?>');

		app.setCustomVariable('customer_email', '<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>');

		app.setCustomVariable('click_magic_click_id', '<?php echo __($apm_ses_epm_magic_click_id,'apm-child')?>');

		app.setCustomVariable('click_magic_id', '<?php echo esc_html($apm_ses_epm_magic_click_id,'apm-child')?>');

		app.setCustomVariable('duplicate_email', '<input type="hidden" name="duplicate_email" value="http://<?php echo esc_html($_SERVER['HTTP_HOST'],'apm-child')?>/aweber-subscription-failed" />');
		
		app.setCustomVariable('recaptcha_sitekey', '<?php echo __($recaptcha_site_key, 'apm-child')?>');
		

	}

</script>

<script>

	

	function anchorTest()
	{

		jQuery(".Campaign select").prop('required', true); //--> Validation for Webinar



		jQuery('.Campaign').find('a').each(function() { 

		

			if(jQuery(this).attr('href') == "https://affiliatepromembership.com/privacy-policy/" || jQuery(this).attr('href') =="https://affiliatepromembership.com/privacy-policy")

			{

				jQuery(this).on( "click", function() {

				  jQuery(this).attr('href', 'javascript:void(0)');

				  jQuery( "#optinmonster_hidden_form" ).submit();

				});

			}

		

		});

		

		

		

		//--> Replace JvZoo ID to funnel shortcode

		

		fullQStr = jQuery("#FunnelReplaceUrl").attr("data");

		

		if(fullQStr != '' && fullQStr == "undefined")

		{

			var newFunnelURL = fullQStr.replace("replace_aid", "<?php echo __($recordCrediantial['jv_zoo_id'],'apm-child')?>");		

			jQuery("#FunnelReplaceUrl").attr("data", newFunnelURL)

		}

		

		

	}

	

	setTimeout("anchorTest()", 5000);

	setTimeout("anchorTest()", 3000);

	setTimeout("anchorTest()", 2000);

	
	


</script>

<form method="post" name="optinmonster_hidden_form" id="optinmonster_hidden_form" action="https://affiliatepromembership.com/privacy-policy" target="_blank">

<input type="hidden" name="customer_email_address" value="<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>" />

</form>

<?php

}

add_action('init', 'apm_optin_monster_session_functions',1);

add_action('wp_footer', 'apm_optin_monster_functions', 22);

?>