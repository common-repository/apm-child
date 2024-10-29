<?php
function execute_warriorplus_instant_notification()
{
	global $wpdb;
	
	$TodaysDate = date('Y-m-d H:i:s');
	$responseMsg = "Failed";
	
	//--> Tracking warrior response.
	$page_name = APM_PHYSICAL_PATH."/logs/warrior_debug_".date("d-m-Y").".txt";	
	$fp = fopen($page_name, "a");
	$dataArr = print_r($_REQUEST, TRUE);
	fwrite($fp, $dataArr);
	
	
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'warriorplus_notification')
	{
		//--> Debug Tracking Code
		$new_data_start = "\n\n".'Execution start on : '.$TodaysDate;
		fwrite($fp, $new_data_start);
		
		$sql = $wpdb->prepare("INSERT INTO `".$wpdb->prefix."cso_my_income`
						 (`inc_caffitid`, `inc_ccustcc`, `inc_ccustemail`, `inc_ccustname`, `inc_ccuststate`, `inc_cproditem`, `inc_cprodtitle`, `inc_cprodtype`, `inc_ctransaction`, `inc_ctransaffiliate`, `inc_ctransamount`, `inc_commision_amt`, `inc_ctranspaymentmethod`, `inc_ctransreceipt`, `inc_ctranstime`, `inc_ctransvendor`, `inc_cverify`, `inc_processed`, `income_date`, `cron_status`, `inc_income_type`)
						 VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'Yes', 'Warrior')",
						 array(addslashes_gpc(sanitize_text_field($_REQUEST['caffitid'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustcc'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustemail'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccustname'])), addslashes_gpc(sanitize_text_field($_REQUEST['ccuststate'])), addslashes_gpc(sanitize_text_field($_REQUEST['cproditem'])), addslashes_gpc(sanitize_text_field($_REQUEST['cprodtitle'])), addslashes_gpc(sanitize_text_field($_REQUEST['cprodtype'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransaction'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransaffiliate'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransamount'])), addslashes_gpc(sanitize_text_field($_REQUEST['affiliatePayout'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctranspaymentmethod'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransreceipt'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctranstime'])), addslashes_gpc(sanitize_text_field($_REQUEST['ctransvendor'])), addslashes_gpc(sanitize_text_field($_REQUEST['cverify'])), 'No', addslashes_gpc(sanitize_text_field($_REQUEST['record_date'])) ));
		$wpdb->query($sql);
		
		fwrite($fp, $sql);
		
		$commision_insert_id = $wpdb->insert_id; 
		
		if($commision_insert_id > 0)
		{
			$responseMsg = "Successful";
		}
	}
	
	
	//--> Send Values to ClickMagic
	
	if($_REQUEST['caffitid'] != '' && is_numeric($_REQUEST['caffitid']))
	{
		
		//--> Fetch Information from Settings Table
		$egSqlCrediantial = "SELECT * FROM `".$wpdb->prefix.'cso_options'."` WHERE 1";
		$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
		
		
		//--> Setup CURL for ClickMagick
		$url = "http://www.clkmg.com/api/s/post/";
		
		$postdata = array(
			'uid' => __($recordCrediantial['clickmagick_clickid'], 'apm-child'),
			's1' => __($_REQUEST['caffitid']),
			'amt' => $_REQUEST['affiliatePayout'],
			'ref' => __($_REQUEST['ctransreceipt']),
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