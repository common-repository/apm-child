<?php
function execute_thrivecart_instant_notification()
{
	global $wpdb;
	
	$TodaysDate = date('Y-m-d H:i:s');
	$responseMsg = "Failed";
	
	//--> Tracking jvZoo response.
	$page_name = APM_PHYSICAL_PATH."/logs/thrivecart_debug_".date("d-m-Y").".txt";	
	$fp = fopen($page_name, "a");
	$dataArr = print_r($_REQUEST, TRUE);
	fwrite($fp, $dataArr);
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'thrivecart_notification')
	{
		//--> Debug Tracking Code
		$new_data_start = "\n\n".'Execution start on : '.$TodaysDate;
		fwrite($fp, $new_data_start);
		
		$sql = $wpdb->prepare("INSERT INTO `".$wpdb->prefix."cso_my_income`
						 (`inc_caffitid`, `inc_ccustcc`, `inc_ccustemail`, `inc_ccustname`, `inc_ccuststate`, `inc_cproditem`, `inc_cprodtitle`, `inc_cprodtype`, `inc_ctransaction`, `inc_ctransaffiliate`, `inc_ctransamount`, `inc_commision_amt`, `inc_ctranspaymentmethod`, `inc_ctransreceipt`, `inc_ctranstime`, `inc_ctransvendor`, `inc_cverify`, `inc_processed`, `income_date`, `cron_status`, `inc_income_type`, `buyer_tag_identifier`)
						 VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'Yes', 'ThriveCart', %s)",
						 array(addslashes_gpc(sanitize_text_field($_REQUEST['caffitid'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustcc'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustemail'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustname'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccuststate'])), addslashes_gpc(sanitize_text_field($_REQUEST['cproditem'])), addslashes_gpc(sanitize_text_field($_REQUEST['cprodtitle'])), addslashes_gpc(sanitize_text_field($_REQUEST['cprodtype'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransaction'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransaffiliate'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransamount'])), addslashes_gpc(sanitize_text_field($_REQUEST['affiliatePayout'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctranspaymentmethod'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransreceipt'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctranstime'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransvendor'])), addslashes_gpc(sanitize_text_field($_REQUEST['cverify'])), 'No', addslashes_gpc(sanitize_text_field($_REQUEST['record_date'])), addslashes_gpc(sanitize_text_field($_REQUEST['buyer_tag_identifier'])) ));
		
		$wpdb->query($sql);
		
		fwrite($fp, $sql);
		
		$commision_insert_id = $wpdb->insert_id; 
		
		if($commision_insert_id > 0)
		{
			$responseMsg = "Successful";
		}
	}
	
	
	//--> Fetch Information from Settings Table
		$egSqlCrediantial = "SELECT * FROM `".$wpdb->prefix.'cso_options'."` WHERE 1";
		$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
		
		
		
		//--> Send Data to master website subscriber table  <--\\
		
		$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
		
		if(isset($_REQUEST['caffitid']) && $_REQUEST['caffitid'] != '')
		{
			$_REQUEST['caffitid'] = $_REQUEST['caffitid'];
		}
		else
		{
			$_REQUEST['caffitid'] = '';
		}
		
		
		//--> Get the Thrivecart buyer tags.
		
		$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
		
		$postdata = array(
			'mode' => 'get_funnel_detail',
			'product_id' => addslashes_gpc(sanitize_text_field($_REQUEST['buyer_tag_identifier'])),
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
		
		$resultFunnelDetail = $response['body'];
		
		$resultFunnelDetail = json_decode($resultFunnelDetail); 
	
		$thrive_buyer_tags = '';
	
		if( is_array($resultFunnelDetail) && count($resultFunnelDetail) > 0 )
		{
			$thrive_buyer_tags = implode(",",$resultFunnelDetail);
		}
		
		$postdata = array(
			'mode' => 'income_subscribers',
			'user_email_id' => $recordCrediantial['user_email_id'],
			'ccustemail' => addslashes_gpc(sanitize_text_field($_REQUEST['ccustemail'])),
			'ccustname' => addslashes_gpc(sanitize_text_field($_REQUEST['ccustname'])),
			'customer_ip' => '',
			'clickmagick_clickid' => addslashes_gpc(sanitize_text_field($_REQUEST['caffitid'])),
			'form_tags' => $thrive_buyer_tags,
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
		
		//--> Tracking income_subscribers request value.
		$new_data3 = "\n\n".'ThriveCart Income Subscribers Request \n\n'.print_r($postdata, TRUE);
		fwrite($fp, $new_data3);
		
		$response = wp_remote_post( $url, $args );
		
		$new_data3 = "\n\n".'ThriveCart Income Subscribers Response\n\n'.print_r($response, TRUE);
		fwrite($fp, $new_data3);
		
		//--> End the execution of the script. 
		
	
	
	//--> Send Values to ClickMagic
	
	if($_REQUEST['caffitid'] != '' && is_numeric($_REQUEST['caffitid']))
	{
		
		
		//--> Setup CURL for ClickMagick
		$url = "http://www.clkmg.com/api/s/post/";
		
		$postdata = array(
			'uid' => __($recordCrediantial['clickmagick_clickid'], 'apm-child'), //--> Click magic Account ID
			's1' => __($_REQUEST['caffitid']), //--> This field represent Click ID
			'amt' => $_REQUEST['affiliatePayout'], //--> This field is represent commision amount from thrivecart
			'ref' => __($_REQUEST['ctransreceipt']), //--> invoice_id from thrivecart
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
	
		$new_data2 = "\n\n".' Array Send to to CLICK MAGIC: '.print_r($postdata, TRUE);
		fwrite($fp, $new_data2);
	
		//--> Tracking CLICK MAGIC Response for debug.
		$new_data3 = "\n\n".'CLICK MAGIC Response = '.print_r($response, TRUE);
		fwrite($fp, $new_data3);
	
		//--> Update My Income
		if($response['body'] == 'OK')
		{
			$TodaysDate = date('Y-m-d H:i:s');
			
			$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s, `magic_postback_date` = %s WHERE ID = %d LIMIT 1", array('Yes', $TodaysDate, addslashes_gpc(sanitize_text_field($commision_insert_id))));
			$wpdb->query($egSql);
			
			fwrite($fp, "Response OK => inc_processed = Yes");
		}
		else
		{
			$TodaysDate = date('Y-m-d H:i:s');
			
			$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s, `magic_postback_date` = %s WHERE ID = %d LIMIT 1", array('No', $TodaysDate, addslashes_gpc(sanitize_text_field($commision_insert_id))));
			$wpdb->query($egSql);
			
			fwrite($fp, "Response OK => inc_processed = Yes [Else]");
		}
	}
	else
	{
		$TodaysDate = date('Y-m-d H:i:s');
		
		$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s, `magic_postback_date` = %s WHERE ID = %d LIMIT 1", array('No', $TodaysDate, addslashes_gpc(sanitize_text_field($commision_insert_id))));
		$wpdb->query($egSql);
		
		fwrite($fp, "Response OK => inc_processed = Yes [Else 2]");
	}
	
	
	$new_data = "\n\n".'======================================================================='."\n\n";
	fwrite($fp, $new_data);

	echo $responseMsg;die;
	
	//---> This page will be called through IPN so, we can die it.
	exit();
}
?>