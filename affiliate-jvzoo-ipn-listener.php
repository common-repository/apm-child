<?php
function execute_jvzoo_ipn_listner()
{
	global $wpdb;
	
	$TodaysDate = date('Y-m-d H:i:s');
	
	//--> Tracking jvZoo response.
	$page_name = APM_PHYSICAL_PATH."/logs/debug_".date("d-m-Y").".txt";	
	$fp = fopen($page_name, "a");
	$dataArr = print_r($_REQUEST, TRUE);
	fwrite($fp, $dataArr);
	
	if(isset($_REQUEST['cprodtype']) && isset($_REQUEST['ctransaction']) && sanitize_text_field($_REQUEST['cprodtype']) != '' && sanitize_text_field($_REQUEST['ctransaction']) != '')
	{
		//--> Debug Tracking Code
		$new_data_start = "\n\n".'IPN Execution start on : '.$TodaysDate;
		fwrite($fp, $new_data_start);
		
		$customer_ip = '';
		$commision_amt = '0.00';
		$income_source = 'Child';
		
		//---> This Query Does Duplicate Checking of Same Transaction ID // This was added by kailash because JVZoo IPN was sending duplicate data for same transaction
		$egSql = "SELECT * FROM `".$wpdb->prefix."cso_my_income` WHERE `inc_ctransreceipt` = '".addslashes_gpc(sanitize_text_field($_REQUEST['ctransreceipt']))."'";
		$egRecord = array();
		$egRecord = $wpdb->get_results($egSql, ARRAY_A);
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'jvzoo_ipn_listner')
		{
			$TodaysDate 	= sanitize_text_field($_REQUEST['record_date']);
			$customer_ip 	= sanitize_text_field($_REQUEST['ipaddress']);
			$commision_amt 	= sanitize_text_field($_REQUEST['commision_amt']);
			$income_source = 'Master';
		}
		
		
		if(count($egRecord) <= 0)
		{
			$sql = $wpdb->prepare("INSERT INTO `".$wpdb->prefix."cso_my_income`
							 (`inc_caffitid`, `inc_ccustcc`, `inc_ccustemail`, `inc_ccustname`, `inc_ccuststate`, `inc_cproditem`, `inc_cprodtitle`, `inc_cprodtype`, `inc_ctransaction`, `inc_ctransaffiliate`, `inc_ctransamount`, `inc_commision_amt`, `inc_ctranspaymentmethod`, `inc_ctransreceipt`, `inc_ctranstime`, `inc_ctransvendor`, `inc_cupsellreceipt`, `inc_cvendthru`, `inc_cverify`, `inc_processed`, `income_date`, `inc_ipaddress`, `income_source`, `cron_status`)
							 VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'No')",
							 array(addslashes_gpc(sanitize_text_field($_REQUEST['caffitid'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustcc'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustemail'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustname'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccuststate'])), addslashes_gpc(sanitize_text_field($_REQUEST['cproditem'])), addslashes_gpc(sanitize_text_field($_REQUEST['cprodtitle'])), addslashes_gpc(sanitize_text_field($_REQUEST['cprodtype'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransaction'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransaffiliate'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransamount'])), addslashes_gpc(sanitize_text_field($commision_amt)), addslashes_gpc(sanitize_text_field($_REQUEST['ctranspaymentmethod'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransreceipt'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctranstime'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransvendor'])), addslashes_gpc(sanitize_text_field($_REQUEST['cupsellreceipt'])), addslashes_gpc(sanitize_text_field($_REQUEST['cvendthru'])), addslashes_gpc(sanitize_text_field($_REQUEST['cverify'])), 'No', $TodaysDate, $customer_ip, $income_source));
			
			$wpdb->query($sql);
			$insert_id = $wpdb->insert_id; 
		}
		
		
		if(!isset($_REQUEST['mode']) && $_REQUEST['mode'] != 'jvzoo_ipn_listner')
		{

			//--> Send email to update master based ZvZoo IPN

			$email_sender = get_bloginfo('admin_email');
			$email_subject = "Urgent Action Required: Please Update Your JVZoo Listener URL";

			$sqlUser = "SELECT * FROM `".$wpdb->prefix.'users'."` WHERE user_email = '".$email_sender."'";
			$recordUser = $wpdb->get_row($sqlUser, ARRAY_A);

			$email_body = "Dear ". $recordUser['display_name'] .", <br /><br />

							We've noticed that you still have an 'old' JVZoo Listener URL entered into your JVZoo account for the following product:
							".$_REQUEST['cproditem'].": ".$_REQUEST['cprodtitle']."<br /><br />
							
							Please click here to log into JVZoo.com and update this listener URL to be:<br />
							<a href=\"https://affiliatepromembership.com/jvzoo_affiliate_ipn.php\">https://affiliatepromembership.com/jvzoo_affiliate_ipn.php</a><br /><br />
							
							If you wish to watch a step-by-step video of the process please click the link below:<br />
							<a href=\"https://affiliatepromembership.com/level-3/ongoing-maintenance-and-management/how-to-update-your-jvzoo-listener-url\">https://affiliatepromembership.com/level-3/ongoing-maintenance-and-management/how-to-update-your-jvzoo-listener-url</a><br /><br />
							
							Updating this listener link will give you more information about every sale you generate, so please do this ASAP.<br /><br />
							
							Thanks<br />
							APM Support Team";
			

			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: Affiliate Pro Membership <support@affiliatepromembership.com>' . "\r\n";

			@mail( $email_sender, $email_subject, $email_body, $headers);
		}
	}
	
	$new_data4 = "\n\n".'======================================================================='."\n\n";
	fwrite($fp, $new_data4);
	
	//---> This page will be called through IPN so, we can die it.
	echo "Successful";die;
	exit();
}
?>