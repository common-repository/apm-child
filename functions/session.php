<?php
/*
Copyright 2016  APM Child.® (email : admin@nickjamesadmin.com)
This file is part of APM Child.
*/

function apm_check_login()
{
    return isset($_COOKIE['apm_logged_in']) && $_COOKIE['apm_logged_in'] == 'login';
}

function apm_set_login_session()
{
	if(isset($_POST['frm_plugin']) && $_POST['frm_plugin'] != '')
	{
		global $APM_SUBSCRIPTION_VER, $wpdb;
		
		$siteURL = site_url();
		$siteURL = str_replace("www.","", $siteURL);
		$siteURL = str_replace("http://","", $siteURL);
		$siteURL = str_replace("https://","", $siteURL);
		
	
		$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
		$postdata = array(
			'mode' => 'affiliates_login',
			'version' => $APM_SUBSCRIPTION_VER,
			'url' => $siteURL,
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
		$loginID = $resultList->ID;
		if($loginID > 0)
		{
			apm_set_session_value('apm_logged_in','login');
			$user_login = $resultList->user_login;
			
			apm_set_session_value('apm_ses_user_login', $user_login);
	
			$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
			$record = array();
			$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
			
			$siteURLParent = str_replace("www.","", $resultList->user_url);
			$siteURLParent = str_replace("http://","", $siteURLParent);
			$siteURLParent = str_replace("https://","", $siteURLParent);

			if(is_array( $recordCrediantial ))
			{
				if(count($recordCrediantial) > 0 && strtolower($siteURLParent) == $siteURL)
				{
					$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `user_email_id` = %s WHERE ID = %d LIMIT 1", array(apm_get_session_value('apm_ses_user_login'), $recordCrediantial['ID']));
					$wpdb->query($egSql);
				}
			}
		}
	}
}
function apm_set_session_value($variable, $value)
{
	setcookie( $variable, $value, 0, COOKIEPATH, COOKIE_DOMAIN );
	$_COOKIE[$variable] = $value;
}

function apm_get_session_value($variable)
{
	return isset($_COOKIE[$variable])?$_COOKIE[$variable]:'';
}

function apm_set_logout_session()
{
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'apm_logout')
	{
		apm_logout();
	}
}

function apm_logout()
{
	@setcookie( $_COOKIE['apm_logged_in'], '', time() - ( 15 * 60 ) );
	@setcookie( $_COOKIE['apm_ses_user_login'], '', time() - ( 15 * 60 ) );
	unset($_COOKIE['apm_logged_in']);
	unset($_COOKIE['apm_ses_user_login']);
}

?>