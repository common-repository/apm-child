<?php
class APMESP 
{
	public function __construct()
	{
		
	}
	
	public function apm_esp_add($apm_arr_request_val)
	{
		global $wpdb;
		
		$first_name	= $apm_arr_request_val['first_name'];
		$last_name	= $apm_arr_request_val['last_name'];
		$email	= $apm_arr_request_val['email'];
		$jv_zoo_id	= $apm_arr_request_val['jv_zoo_id'];
		$email_service_provider =  $apm_arr_request_val['email_service_provider'];
		$clickbank_affiliate_nickname =  $apm_arr_request_val['clickbank_affiliate_nickname'];
		$thrivecart_affiliate_username =  $apm_arr_request_val['thrivecart_affiliate_username'];
		$warriorplus_aff_id =  $apm_arr_request_val['warriorplus_aff_id'];
		
		
		$current_date = date('Y-m-d G:i:s');
		
		//--> Entry process for Crediantial
		$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
		$record = array();
		$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
		
		if(!is_array($recordCrediantial))
		{
			$egSql = $wpdb->prepare(
					"INSERT INTO `".APM_OPTIONS_TABLE."`
					(`first_name`,`last_name`, `email`, `jv_zoo_id`, `email_service_provider`, `clickbank_affiliate_nickname`, `thrivecart_affiliate_username`, `warriorplus_aff_id`, `last_updated_date`)
					VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s)",
					array($first_name, $last_name, $email, $jv_zoo_id, $email_service_provider, $clickbank_affiliate_nickname, $thrivecart_affiliate_username, $warriorplus_aff_id, $current_date));
				
				$wpdb->query($egSql);
	
				$insert_id = $wpdb->insert_id; 
				$option_id = $insert_id;
		}
		else
		{ 
			$option_id = $recordCrediantial['ID'];
	
			$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `first_name` = %s, `last_name` = %s, `email` = %s, `jv_zoo_id` = %s, `email_service_provider` = %s, `clickbank_affiliate_nickname` = %s, `thrivecart_affiliate_username` = %s, `warriorplus_aff_id` = %s, `last_updated_date` = %s WHERE ID = %d LIMIT 1", array($first_name, $last_name, $email, $jv_zoo_id, $email_service_provider, $clickbank_affiliate_nickname, $thrivecart_affiliate_username, $warriorplus_aff_id, $current_date, $option_id));
			$wpdb->query($egSql);
		}
		
		$this->apm_update_affiliates("", "", "add_aff_setting");
		$egmail_success = "List has been updated";
		return $egmail_success;
	}
	
	
	public function apm_esp_edit($apm_arr_request_val)
	{
		global $wpdb;
		
		$option_id 			= $apm_arr_request_val['option_id'];
		$funnel_id 			= $apm_arr_request_val['funnel_id'];
		$current_date 		= date('Y-m-d G:i:s');
		$esp_list_id		= $apm_arr_request_val['esp_list_id'];
		$aweber_auth_code 	= $apm_arr_request_val['aweber_auth_code'];
		$sendeagle_api_key 	= $apm_arr_request_val['sendeagle_api_key'];
		$jvz_infusionsoft_api_key 	= $apm_arr_request_val['jvz_infusionsoft_api_key'];
		$jvz_infusionsoft_app_name 	= $apm_arr_request_val['jvz_infusionsoft_app_name'];
		$getresponse_api_key 	= $apm_arr_request_val['getresponse_api_key'];
		$getresponse_custom_field 	= $apm_arr_request_val['getresponse_custom_field'];

		$egmail_error_found = FALSE;	
		$egmail_success = $egmail_errors = '';	
		
		//--> Entry process for Crediantial
		$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
		$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
		
		
		//--> When Affiliate choose Aweber
		if($recordCrediantial['email_service_provider'] == "" || $recordCrediantial['email_service_provider'] == "Aweber")
		{	
			if ($aweber_auth_code == '')
			{
				$egmail_errors 		= __('Please enter aweber auth code.', 'apm-child');
				$egmail_error_found = TRUE;
			}
			
			$auth_code = trim($aweber_auth_code);
			
			if($aweber_auth_code != $recordCrediantial['aweber_auth_code'])
			{
				try {
					
					$credentials = AWeberAPI::getDataFromAweberID($auth_code);
					list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $credentials;
				
				}
				catch(AWeberAPIException $exc) {
					$AWeberAPIException = "<h3>AWeberAPIException:</h3>";
					$AWeberAPIException .= " <li> Type: $exc->type              <br>";
					$AWeberAPIException .= " <li> Msg : $exc->message           <br>";
					$AWeberAPIException .= " <li> Docs: $exc->documentation_url <br>";
					$AWeberAPIException .= "<hr>";
					$egmail_errors[] = $AWeberAPIException;
					$consumerKey = $consumerSecret = $accessKey = $accessSecret = '';
					
					$egmail_errors 		= __($exc->message, 'apm-child');
					$egmail_error_found = TRUE;
				}
			}
			else
			{
				$consumerKey 	=  $recordCrediantial['aweber_consumer_key'];
				$consumerSecret =  $recordCrediantial['aweber_consumer_secret'];
				$accessKey 		=  $recordCrediantial['aweber_access_token'];
				$accessSecret 	=  $recordCrediantial['aweber_access_token_secret'];
			}
			
			$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `last_updated_date` = %s, `aweber_auth_code` = %s, `aweber_consumer_key` = %s , `aweber_consumer_secret` = %s, `aweber_access_token` = %s, `aweber_access_token_secret` = %s WHERE ID = %d LIMIT 1", array($current_date, $auth_code, $consumerKey, $consumerSecret, $accessKey, $accessSecret, $option_id));
			$wpdb->query($egSql);
		}
		
		
		//--> When Affiliate choose Sendeagle
		if($recordCrediantial['email_service_provider'] == "Sendeagle")
		{
			$company_unique_id =  $sendeagle_company_name  = '';
			
			if ($sendeagle_api_key == '')
			{
				$egmail_errors  = __('Please enter Sendeagle API Key.', 'apm-child');
				$egmail_error_found = TRUE;
			}
	
			$sendeagle_api_key = trim($sendeagle_api_key);
			
			if($sendeagle_api_key != $recordCrediantial['sendeagle_api_key'])
			{
				//--> If Company Info not exist then fetch it via CURL
				$url_company_info = "https://my.sendeagle.com/api/web/api/getmycompanies";
	
				$postdata = array(
					'api_key' => __($sendeagle_api_key),
				);
				$args = array(
					'body' => $postdata,
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'sslverify' => false,
					'blocking' => true,
					'headers' => array(),
					'cookies' => array()
				);
					
				$response_company_info = wp_remote_post( $url_company_info, $args );
				$response_arr_company_info = json_decode($response_company_info['body'], true);
				
				if($response_arr_company_info['message'] == 'Success')
				{
					$company_info = $response_arr_company_info['companies'];
					$sendeagle_company_name = $company_info[0]['company_name'];
					$company_unique_id = $company_info[0]['company_unique_id'];
				}
				else
				{
					$egmail_errors = __('SendEagle API Key is invalid.', 'apm-child');
					$egmail_error_found = TRUE;
				}
			}
			else
			{
					$sendeagle_company_name = $recordCrediantial['sendeagle_company_name'];
					$company_unique_id = $recordCrediantial['sendeagle_company_unique_id'];
			}
				
				$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `last_updated_date` = %s, `sendeagle_api_key` = %s, `sendeagle_company_unique_id` = %s, `sendeagle_company_name` = %s WHERE ID = %d LIMIT 1", array($current_date, $sendeagle_api_key, $company_unique_id, $sendeagle_company_name, $option_id));
				$wpdb->query($egSql);
		}
		
		
		//--> When Affiliate choose Infusionsoft
		if($recordCrediantial['email_service_provider'] == "Infusionsoft")
		{
			if ($jvz_infusionsoft_api_key == '')
			{
				$egmail_errors  = __('Please enter Infusionsoft API Key.', 'apm-child');
				$egmail_error_found = TRUE;
			}
			
			if ($jvz_infusionsoft_app_name == '')
			{
				$egmail_errors  = __('Please enter Infusionsoft APP Name.', 'apm-child');
				$egmail_error_found = TRUE;
			}
	
			$jvz_infusionsoft_api_key = trim($jvz_infusionsoft_api_key);
			
			if($jvz_infusionsoft_api_key != $recordCrediantial['jvz_infusionsoft_api_key'])
			{
				
				$INFUSIONSOFT_APP_NAME = $jvz_infusionsoft_app_name;
				$INFUSIONSOFT_API_KEY = $jvz_infusionsoft_api_key;
				
				$InfusionsoftAPIUtil = new InfusionsoftAPIUtil($INFUSIONSOFT_APP_NAME, $INFUSIONSOFT_API_KEY);
				
				$infusionsoft_lists = $InfusionsoftAPIUtil->getInfusionsoftTags();

				
				if(sizeof($infusionsoft_lists) > 0)
				{
					$jvz_infusionsoft_app_name = $jvz_infusionsoft_app_name;
					$jvz_infusionsoft_api_key  = $jvz_infusionsoft_api_key;
				}
				else
				{
					$egmail_errors = __('Something is wrong with Infusionsoft API Key', 'apm-child');
					$egmail_error_found = TRUE;
				}
			}
			else
			{
					$jvz_infusionsoft_app_name = $recordCrediantial['jvz_infusionsoft_app_name'];
					$jvz_infusionsoft_api_key  = $recordCrediantial['jvz_infusionsoft_api_key'];
			}
				
				$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `last_updated_date` = %s, `jvz_infusionsoft_app_name` = %s, `jvz_infusionsoft_api_key` = %s, `getresponse_custom_field` = %s WHERE ID = %d LIMIT 1", array($current_date, $jvz_infusionsoft_app_name, $jvz_infusionsoft_api_key, $getresponse_custom_field, $option_id));
				$wpdb->query($egSql);
		}
		
		
		//--> When Affiliate choose GetResponse
		if($recordCrediantial['email_service_provider'] == "GetResponse")
		{
			if ($getresponse_api_key == '')
			{
				$egmail_errors  = __('Please enter GetResponse API Key.', 'apm-child');
				$egmail_error_found = TRUE;
			}
			
			/*if ($getresponse_custom_field == '')
			{
				$egmail_errors  = __('Please choose Custom field to for clickid mapping.', 'apm-child');
				$egmail_error_found = TRUE;
			}*/
			
			
			$getresponse_api_key = trim($getresponse_api_key);
			
			if($getresponse_api_key != $recordCrediantial['getresponse_api_key'])
			{
				$getresponse = new GetResponse($getresponse_api_key);
				$getresponse_list =  $getresponse->getCampaigns();

				if(isset($getresponse_list->codeDescription))
				{
					$egmail_errors = __($getresponse_list->codeDescription, 'apm-child');
					$egmail_error_found = TRUE;
					$getresponse_api_key  = "";
				}
				else
				{
					$getresponse_api_key  = $getresponse_api_key;
				}
				
			}
			else
			{
					$getresponse_api_key = $recordCrediantial['getresponse_api_key'];
			}
				
				$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `last_updated_date` = %s, `getresponse_api_key` = %s , `getresponse_custom_field` = %s WHERE ID = %d LIMIT 1", array($current_date, $getresponse_api_key, $getresponse_custom_field, $option_id));
				$wpdb->query($egSql);
		}
	
		
		if($egmail_error_found == FALSE)
		{
			$this->apm_update_affiliates($esp_list_id, $funnel_id, "update_aff_setting");
			$egmail_success = "List has been updated";
		}
		
		$arr_return_val = array("egmail_success" => $egmail_success, "egmail_error_found" => $egmail_error_found, "egmail_errors" => $egmail_errors);
		return $arr_return_val;
	}
	
	
	/** To get Aweber List if affiliate choose Aweber **/
	public function apm_get_aweber_list_by_api($apm_arr_request_val)
	{
		global $wpdb;
		
		$option_id		 	= $apm_arr_request_val['ID'];
		$consumerKey    	= $apm_arr_request_val['aweber_consumer_key'];
		$consumerSecret 	= $apm_arr_request_val['aweber_consumer_secret'];
		$aweber_auth_code 	= $apm_arr_request_val['aweber_auth_code'];
		$egmail_errors = '';
		$egmail_error_found = false;
		$show_aweber_list = $account = '';
		
		if($aweber_auth_code != '')
		{
			$aweber = new AWeberAPI($consumerKey, $consumerSecret); 
			
			if($aweber->consumerKey != '')
			{
				
				try {
				
					$account = $aweber->getAccount($apm_arr_request_val['aweber_access_token'], $apm_arr_request_val['aweber_access_token_secret']);
					$account_id = $account->id;
					
					if($account_id != '')
					{
						$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `aweber_account_number` = %d WHERE ID = %d LIMIT 1", array( $account_id, $option_id));
						$wpdb->query($egSql);
					}
					$show_aweber_list = true;
					
				} catch(AWeberAPIException $exc) {
					
					$AWeberAPIException = "<h3>AWeberAPIException:</h3>";
					$AWeberAPIException .= " <li> Type: $exc->type              <br>";
					$AWeberAPIException .= " <li> Msg : $exc->message           <br>";
					$AWeberAPIException .= " <li> Docs: $exc->documentation_url <br>";
					$AWeberAPIException .= "<hr>";
					$egmail_errors = $AWeberAPIException;
				}
			}
			else
			{
				$show_aweber_list = false;
				$egmail_error_found = true;
				$egmail_errors = "Please use correct AWeber Auth Code";
			}
		}
		
		$arr_return_val = array("show_aweber_list" => $show_aweber_list,  "account" => $account, "egmail_error_found" => $egmail_error_found, "egmail_errors" => $egmail_errors);
		return $arr_return_val;
	}
	
	
	/** To get SendEagle List if affiliate choose SendEagle **/
	public function apm_get_sendeagle_list_by_api($apm_arr_request_val)
	{
		$sendeagle_api_key = $apm_arr_request_val['sendeagle_api_key'];
		$sendeagle_company_unique_id = $apm_arr_request_val['sendeagle_company_unique_id'];
		$sendeagle_company_name = $apm_arr_request_val['sendeagle_company_name'];
		$sendeagle_api_key = $apm_arr_request_val['sendeagle_api_key'];
		
		if($sendeagle_api_key != '' && $sendeagle_company_unique_id != '')
		{
			$url_list = "https://my.sendeagle.com/api/web/api/getusergroups";
	
			$postdata = array(
				'api_key' => __($sendeagle_api_key),
				'company_unique_id' => __($sendeagle_company_unique_id)
			);
			$args = array(
				'body' => $postdata,
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'blocking' => true,
				'headers' => array(),
				'cookies' => array()
			);
				
			$response_list = wp_remote_post( $url_list, $args );
			$response_arr_list = json_decode($response_list['body'], true);
			$sendeagle_lists = $response_arr_list['lists'];
			
			return $sendeagle_lists;
		}
	}
	
	
	/** To get SendEagle List if affiliate choose SendEagle **/
	public function apm_get_infusion_list_by_api($apm_arr_request_val)
	{
		$sendeagle_api_key = $apm_arr_request_val['sendeagle_api_key'];
		$sendeagle_company_unique_id = $apm_arr_request_val['sendeagle_company_unique_id'];
		$sendeagle_company_name = $apm_arr_request_val['sendeagle_company_name'];
		$sendeagle_api_key = $apm_arr_request_val['sendeagle_api_key'];
		
		if($sendeagle_api_key != '' && $sendeagle_company_unique_id != '')
		{
			$url_list = "https://my.sendeagle.com/api/web/api/getusergroups";
	
			$postdata = array(
				'api_key' => __($sendeagle_api_key),
				'company_unique_id' => __($sendeagle_company_unique_id)
			);
			$args = array(
				'body' => $postdata,
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'sslverify' => false,
				'blocking' => true,
				'headers' => array(),
				'cookies' => array()
			);
				
			$response_list = wp_remote_post( $url_list, $args );
			$response_arr_list = json_decode($response_list['body'], true);
			$sendeagle_lists = $response_arr_list['lists'];
			
			return $sendeagle_lists;
		}
	}
	
	
	//--> Function For get Funnel List from master plugin
	public function apm_get_funnel_list()
	{
		$url = APM_MASTER_PLUGIN_URL."wp-content/plugins/mi-email-subscribers/outside_requests.php";
			
		$postdata = array(
			'mode' => 'get_funnel_list',
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
		$resultFunnelList = $response['body'];
		
		return $resultFunnelList;
	}
	
	
	public function apm_update_affiliates($esp_list_id, $funnel_id, $type)
	{
		global $wpdb;
		
		$user_email_id	= apm_get_session_value('apm_ses_user_login');

		//--> Setting CURL to update information in master website
		$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
		$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
		
		$url = APM_MASTER_PLUGIN_URL."wp-content/plugins/mi-email-subscribers/outside_requests.php";
		
		$user_email_id 		= apm_get_session_value('apm_ses_user_login');
		$first_name 		= $recordCrediantial['first_name'];
		$last_name 			= $recordCrediantial['last_name'];
		$jv_zoo_id 			= $recordCrediantial['jv_zoo_id'];
		$email 				= $recordCrediantial['email'];
		$clickmagick_tracking_code	= stripslashes($recordCrediantial['clickmagick_tracking_code']);
		$email_service_provider	= $recordCrediantial['email_service_provider'];
		$clickbank_affiliate_nickname	= $recordCrediantial['clickbank_affiliate_nickname'];
		$thrivecart_affiliate_username	= $recordCrediantial['thrivecart_affiliate_username'];
		$warriorplus_aff_id	= $recordCrediantial['warriorplus_aff_id'];
		
		//--> AWeber API		
		$aweber_account_number 	= $recordCrediantial['aweber_account_number'];
		$aweber_auth_code 	= $recordCrediantial['aweber_auth_code'];
		$consumerKey 		= $recordCrediantial['aweber_consumer_key'];
		$consumerSecret 	= $recordCrediantial['aweber_consumer_secret'];
		$accessKey 			= $recordCrediantial['aweber_access_token'];
		$accessSecret 		= $recordCrediantial['aweber_access_token_secret'];
		
		//--> SendEagle API Information
		$sendeagle_api_key			 = $recordCrediantial['sendeagle_api_key'];
		$sendeagle_company_unique_id = $recordCrediantial['sendeagle_company_unique_id'];
		$sendeagle_company_name		 = $recordCrediantial['sendeagle_company_name'];
		$esp_list_id			 	 = isset($esp_list_id) ? $esp_list_id : '';
		
		//--> Infusionsoft API
		$jvz_infusionsoft_app_name 	 = $recordCrediantial['jvz_infusionsoft_app_name'];
		$jvz_infusionsoft_api_key	 = $recordCrediantial['jvz_infusionsoft_api_key'];
		
		//--> GetResponse API
		$getresponse_api_key  = $recordCrediantial['getresponse_api_key'];
		$getresponse_custom_field  = $recordCrediantial['getresponse_custom_field'];
		
		
		$postdata = array(
			'mode' => 'update_affiliates',
			'user_email_id' => $user_email_id,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'jv_zoo_id' => $jv_zoo_id,
			'aweber_auth_code' => $aweber_auth_code,
			'clickmagick_tracking_code' => $clickmagick_tracking_code,
			'consumerKey' => $consumerKey,
			'consumerSecret' => $consumerSecret,
			'accessKey' => $accessKey,
			'accessSecret' => $accessSecret,
			'aweber_account_number' => $aweber_account_number,
			'email' => $email,
			'version' => $GLOBALS['APM_SUBSCRIPTION_VER'],
			'email_service_provider' => $email_service_provider,
			'clickbank_affiliate_nickname' => $clickbank_affiliate_nickname,
			'thrivecart_affiliate_username' => $thrivecart_affiliate_username,
			'warriorplus_aff_id' => $warriorplus_aff_id,
			'sendeagle_api_key' => $sendeagle_api_key,
			'sendeagle_company_unique_id' => $sendeagle_company_unique_id,
			'sendeagle_company_name' => $sendeagle_company_name,
			'jvz_infusionsoft_app_name' => $jvz_infusionsoft_app_name,
			'jvz_infusionsoft_api_key' => $jvz_infusionsoft_api_key,
			'getresponse_api_key' => $getresponse_api_key,
			'getresponse_custom_field' => $getresponse_custom_field,
		);
		
		if($type ==  "update_aff_setting")
		{
			$postdata_updae = array(
				'esp_list_id' => $esp_list_id,
				'funnel_id' => $funnel_id,
			);
			
			$postdata = array_merge($postdata, $postdata_updae);
		}
	
	
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
		
		$egmail_success = "List has been updated";
	}

	function apm_set_income_notification($arr_request_val)
	{
		global $wpdb;
		$current_date = date('Y-m-d G:i:s');
		
		//--> Request Values
		$jvzoo_notification = $arr_request_val['jvzoo_notification'];
		$clickbank_notification = $arr_request_val['clickbank_notification'];
		$thrivecart_notification = $arr_request_val['thrivecart_notification'];
		$warriorplus_notification = $arr_request_val['warriorplus_notification'];

		//--> Get Affiliate detail
		$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
		$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
		$option_id = $recordCrediantial['ID'];
		$user_email_id = $recordCrediantial['user_email_id'];
		
		
		$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `last_updated_date` = %s, `jvzoo_notification` = %s, `clickbank_notification` = %s, `thrivecart_notification` = %s,  `warriorplus_notification` = %s WHERE ID = %d LIMIT 1", array($current_date, $jvzoo_notification, $clickbank_notification, $thrivecart_notification, $warriorplus_notification, $option_id));
	
		if($wpdb->query($egSql))
		{
			//--> Send Notification request to Master Website
			$url = APM_MASTER_PLUGIN_URL."wp-content/plugins/mi-email-subscribers/outside_requests.php";
			$postdata = array(
				'mode' => 'set_income_notification',
				'jvzoo_notification' => $jvzoo_notification,
				'clickbank_notification' => $clickbank_notification,
				'thrivecart_notification' => $thrivecart_notification,
				'warriorplus_notification' => $warriorplus_notification,
				'user_email_id' => $user_email_id
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
			
			return "success";
		}

	}
	
}
?>