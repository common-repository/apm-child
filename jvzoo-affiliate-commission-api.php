<?php
function execute_jvzoo_affiliate_commission()
{
	global $wpdb;
	
	$TodaysDate = date('Y-m-d H:i:s');
	
	//--> Debug Tracking Code
	$page_name = APM_PHYSICAL_PATH."/logs/debug_".date("d-m-Y").".txt";	
	$fp = fopen($page_name, "a");
	$new_data_start = "\n\n".'CRON Execution start on : '.$TodaysDate;
	fwrite($fp, $new_data_start);
	
	$customer_ip = '';
	
	//--> Check staus of income before running calculation
	
	$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `cron_running_status` = 'Executed', `cron_date` = NOW() WHERE cron_date > date_sub(now(), interval 60 minute) AND cron_running_status = 'Running'", array());
	$wpdb->query($egSql);
	
	//$sql_commsission = "SELECT * FROM ".$wpdb->prefix.'cso_my_income'." WHERE income_date > date_sub(now(), interval 5 minute)";

	//---> Delete All Duplicate Records Before Processing Transactions
	$sql_commsission = "SELECT * FROM ".$wpdb->prefix.'cso_my_income'." WHERE cron_status = 'No' AND cron_running_status = 'Executed' AND inc_ctransreceipt <> '' AND inc_income_type = 'JvZoo' GROUP BY inc_ctransreceipt";
	$commsission_record = $wpdb->get_results($sql_commsission, ARRAY_A);

	if(count($commsission_record) > 0)
	{
		foreach($commsission_record as $commsission)
		{
			$egSql = $wpdb->prepare("DELETE FROM `".$wpdb->prefix.'cso_my_income'."` WHERE `inc_ctransreceipt` = %s and `ID` <> %d and cron_status = 'No'", array(addslashes_gpc(sanitize_text_field($commsission['inc_ctransreceipt'])), addslashes_gpc(sanitize_text_field($commsission['ID']))));
			$wpdb->query($egSql);
		}
	}

	//---> Start Processing Transaction Record to Fetch Affiliate Commission Data From JVZoo
	$sql_commsission = "SELECT * FROM ".$wpdb->prefix.'cso_my_income'." WHERE cron_status = 'No' AND cron_running_status = 'Executed' AND inc_ctransreceipt <> '' AND inc_income_type = 'JvZoo' ";
	$commsission_record = $wpdb->get_results($sql_commsission, ARRAY_A);
	
	
	if(count($commsission_record) > 0)
	{
		
		//--> Get JVZoo API Key From Master Plugin
		$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
		$caffitid = '';
		
		$postdata = array(
			'mode' => 'get_jvzoo_api_key',
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
		$APIKey = $response['body'];

		
		foreach($commsission_record as $commsission)
		{
			$inc_processed = $commsission['inc_processed'];
			
			//--> Update Cron running status
			$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `cron_running_status` = 'Running', `cron_date` = NOW() WHERE ID = %d LIMIT 1", array(addslashes_gpc(sanitize_text_field($commsission['ID']))));
			$wpdb->query($egSql);
			
			//--> Execute JVZoo API for Commision amount
			if($APIKey != '' && $commsission['income_source'] == 'Child')
			{
				$postdata = array();
				$auth_api_key = base64_encode( $APIKey . ':' . 'x' );
				$response = wp_remote_get("https://api.jvzoo.com/v2.1/transaction/summary/".$commsission['inc_ctransreceipt'], array(
					'timeout' => 45,
					'httpversion' => '1.0',
					'sslverify' => false,
					'headers' => array(
						'Authorization' => "Basic $auth_api_key",
					),
					'body' => $postdata,
					)
				);
				
				$response = wp_remote_retrieve_body( $response );
				
				$jsonResponse = json_decode( $response ); //Convert the result from JSON format to a PHP array 
			
				//--> Debug Tracking Code
				$debug_code = "Requested URL: https://api.jvzoo.com/v2.1/transaction/summary/".$commsission['inc_ctransreceipt'];
				fwrite($fp, $debug_code);
	
				$debug_code = "\n\n".'JVZoo API Response: '.$response.', IP ADDR:'. $jsonResponse->results[0]->customer_ip;
				fwrite($fp, $debug_code);
			
				$customer_ip = $jsonResponse->results[0]->customer_ip;
				
			}
			
			$caffitid = $commsission['inc_caffitid'];

			if($commsission['income_source'] == 'Master')
			{
				$customer_ip = $commsission['inc_ipaddress'];
			}
			
			
			//---> Pick Recently done transaction's ClickID From database for IPN records coming with BLANK ClickID. This was happening for UPSALE transactions. Nick suggested this solution.
			if(trim($caffitid) == '')
			{
				$egSql = "SELECT * FROM ".$wpdb->prefix.'cso_my_income'." WHERE inc_caffitid <> '' AND inc_ccustemail = '".sanitize_email($commsission['inc_ccustemail'])."' AND inc_ctransaffiliate = '".addslashes_gpc(sanitize_text_field($commsission['inc_ctransaffiliate']))."' AND inc_ctransvendor = '".addslashes_gpc(sanitize_text_field($commsission['inc_ctransvendor']))."' ORDER BY inc_ctranstime DESC LIMIT 0, 1";
		
				$egRecord = array();
				$egRecord = $wpdb->get_results($egSql, ARRAY_A);
		
				if(count($egRecord) > 0)
				{
					foreach ($egRecord as $record)
					{
						$caffitid = $record['inc_caffitid'];
					}
					
					//--> Tracking code for debug.
					$new_data = "\n\n".'NEW caffitid by Email ==> '.$caffitid;
					fwrite($fp, $new_data);
				}
			}
			
			if(trim($caffitid) == '')
			{
				$egSql = "SELECT * FROM ".$wpdb->prefix.'cso_my_income'." WHERE inc_caffitid <> '' AND inc_ipaddress = '".addslashes_gpc(sanitize_text_field($customer_ip))."' ORDER BY inc_ctranstime DESC LIMIT 0, 1";
			
				$egRecord = array();
				$egRecord = $wpdb->get_results($egSql, ARRAY_A);
				if(count($egRecord) > 0)
				{
					foreach ($egRecord as $record)
					{
						$caffitid = $record['inc_caffitid'];
					}
					
					//--> Tracking code for debug.
					$new_data = "\n\n".'inc_caffitid Added by IP'.$caffitid;
					fwrite($fp, $new_data);
				}
			}			
			
			
			//--> Fetch Information from Settings Table
			$egSqlCrediantial = "SELECT * FROM `".$wpdb->prefix.'cso_options'."` WHERE 1";
			$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
			
			//--> Send Data to master website subscriber table  <--\\
			//$user_email_id = apm_get_session_value('apm_ses_user_login');
		
			$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
			
			$postdata = array(
				'mode' => 'income_subscribers',
				'user_email_id' => $recordCrediantial['user_email_id'],
				'ccustemail' => $commsission['inc_ccustemail'],
				'ccustname' => $commsission['inc_ccustname'],
				'customer_ip' => $customer_ip,
				'clickmagick_clickid' => $commsission['inc_caffitid'],
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
			$new_data3 = "\n\n".'income_subscribers = '.print_r($postdata, TRUE);
			fwrite($fp, $new_data3);
			$response = wp_remote_post( $url, $args );
			
			
			
			//--> Update Customer IP in My Income table
			if($customer_ip != '')
			{
				$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_ipaddress` = %s WHERE ID = %d LIMIT 1", array($customer_ip, addslashes_gpc(sanitize_text_field($commsission['ID']))));
				$wpdb->query($egSql);
			}
			
			
			$ctransaction = $commsission['inc_ctransaction'];
		
			if($ctransaction == 'CANCEL-REBILL' || $ctransaction == 'UNCANCEL-REBILL')
			{
				$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s WHERE inc_ctransreceipt = %s LIMIT 1", array('No', addslashes_gpc(sanitize_text_field($commsission['inc_ctransreceipt']))));
				$wpdb->query($egSql);
			}
			else
			{
				
				if($commsission['income_source'] == 'Child')
				{
					$commision_amount = $commision_amt = '0.00';
					foreach($jsonResponse->results[0]->transactionPayouts as $key => $val)
					{
						if($key == "Payout #:3")
						{
							$commision_amount = $val->payee_amount;
							
							if($ctransaction == 'SALE' || $ctransaction == 'BILL')
							{
								$commision_amt =  $commision_amount; 
							}
							elseif($ctransaction == 'RFND' || $ctransaction == 'CGBK' || $ctransaction == 'INSF')
							{
								$commision_amt = "-".$commision_amount; 
							}
							else
							{
								$commision_amt = $commision_amount; 
							}
							break;
						}
					}
				}
				
				if($commsission['income_source'] == 'Master')
				{
					$commision_amt = $commsission['inc_commision_amt']; 
					if($ctransaction == 'RFND' || $ctransaction == 'CGBK' || $ctransaction == 'INSF')
					{
						$commision_amount = str_replace("-", "", $commision_amount);
					}
				}
					
					
				//--> Update Info into Database and run Click Magic API
				
				$upSQL = "UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_commision_amt` = '".addslashes_gpc(sanitize_text_field($commision_amt))."', `cron_status` = 'Yes' WHERE inc_ctransreceipt = '".addslashes_gpc(sanitize_text_field($commsission['inc_ctransreceipt']))."'";
				fwrite($fp, $upSQL);
				
				$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_commision_amt` = %s, `cron_status` = 'Yes' WHERE ID = %d LIMIT 1", array(addslashes_gpc(sanitize_text_field($commision_amt)), addslashes_gpc(sanitize_text_field($commsission['ID']))));
				$wpdb->query($egSql);	
				
				//--> If caffitid is null then request will not made for ClickMagick
				if($inc_processed == 'No') //---> This condition was added to stop duplicate processing into click magic for old records past dec 2018
				{
					if($caffitid != '' && is_numeric($caffitid))
					{
						$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_caffitid` = %s WHERE ID = %d LIMIT 1", array($caffitid, addslashes_gpc(sanitize_text_field($commsission['ID']))));
						$wpdb->query($egSql);
						
						//--> Setup CURL for ClickMagick
						$url = "http://www.clkmg.com/api/s/post/";
						
						$postdata = array(
							'uid' => __($recordCrediantial['clickmagick_clickid'],'apm-child'),
							's1' => __($caffitid),
							'amt' => $commision_amount,
							'ref' => __($commsission['inc_ctransreceipt']),
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
					
						$new_data2 = "\n\n".'Array Send to to CLICK MAGIC:'.print_r($postdata, TRUE);
						fwrite($fp, $new_data2);
					
						//--> Tracking CLICK MAGIC Response for debug.
						$new_data3 = "\n\n".'CLICK MAGIC Response = '.print_r($response, TRUE);
						fwrite($fp, $new_data3);
					
						//--> Update My Income
						if($response['body'] == 'OK')
						{
							$TodaysDate = date('Y-m-d H:i:s');
							
							$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s, `magic_postback_date` = %s WHERE ID = %d LIMIT 1", array('Yes', $TodaysDate, addslashes_gpc(sanitize_text_field($commsission['ID']))));
							$wpdb->query($egSql);
							
							fwrite($fp, "Response OK => inc_processed = Yes");
						}
						else
						{
							$TodaysDate = date('Y-m-d H:i:s');
							
							$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s, `magic_postback_date` = %s WHERE ID = %d LIMIT 1", array('No', $TodaysDate, addslashes_gpc(sanitize_text_field($commsission['ID']))));
							$wpdb->query($egSql);
							
							fwrite($fp, "Response OK => inc_processed = Yes [Else]");
						}
					}
					else
					{
						$TodaysDate = date('Y-m-d H:i:s');
						
						$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s, `magic_postback_date` = %s WHERE ID = %d LIMIT 1", array('No', $TodaysDate, addslashes_gpc(sanitize_text_field($commsission['ID']))));
						$wpdb->query($egSql);
						
						fwrite($fp, "Response OK => inc_processed = Yes [Else 2]");
					}
				}

			}
		
			//--> Update CRON running status
			$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `cron_running_status` = 'Executed' WHERE ID = %d LIMIT 1", array(addslashes_gpc(sanitize_text_field($commsission['ID']))));
			$wpdb->query($egSql);
		}
	}
	
	$new_data4 = "\n\n".'======================================================================='."\n\n";
	fwrite($fp, $new_data4);

	//This page will be called through IPN so, we can die it.
	exit();
}

?>