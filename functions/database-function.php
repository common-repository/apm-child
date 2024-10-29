<?php
/*
Copyright 2013  APM Child Plugin.® (email : admin@nickjamesadmin.com)
This file is part of APM Child.
*/

global $wpdb;

define ('APM_OPTIONS_TABLE', $wpdb->prefix.'cso_options');
define ('APM_POST_SNIPPET', $wpdb->prefix.'cso_post_snippets');
define ('APM_MY_INCOME', $wpdb->prefix.'cso_my_income');


/* -------Create plugin tables */
function apm_aweber_subscription_db_init()
{
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	
	/* ------------------Option table */
	$options_table_name = APM_OPTIONS_TABLE;
	$sql = "CREATE TABLE $options_table_name 
	(
		ID int NOT NULL AUTO_INCREMENT,
		PRIMARY KEY  (ID),
		first_name varchar(255),
		last_name varchar(255),
		email varchar(255),
		aweber_consumer_key  varchar(255),
		aweber_consumer_secret varchar(255),
		aweber_account_number varchar(255),
		aweber_access_token varchar(255),
		aweber_access_token_secret varchar(255),
		aweber_request_token_secret varchar(255),
		clickmagick_clickid varchar(255),
		aweber_auth_code TEXT,
		clickmagick_tracking_code TEXT,
		aweber_list_id int(11),
		jv_zoo_id varchar(255),
		last_updated_date DATETIME, 
		user_email_id varchar(255), 
		clickbank_affiliate_nickname varchar(255),
		thrivecart_affiliate_username varchar(255),
		warriorplus_aff_id varchar(255), 
		extra_field_1 varchar(255),
		email_service_provider ENUM('Aweber', 'Sendeagle', 'Infusionsoft', 'GetResponse') NOT NULL DEFAULT 'Aweber',
		sendeagle_api_key varchar(255),
		sendeagle_company_unique_id varchar(255),
		sendeagle_company_name varchar(255),
		jvz_infusionsoft_api_key varchar(255),
		jvz_infusionsoft_app_name varchar(255),
		getresponse_api_key varchar(255),
		getresponse_custom_field varchar(255),
		jvzoo_notification ENUM('Yes', 'No') NOT NULL DEFAULT 'Yes',
		clickbank_notification ENUM('Yes', 'No') NOT NULL DEFAULT 'Yes',
		thrivecart_notification ENUM('Yes', 'No') NOT NULL DEFAULT 'Yes',
		warriorplus_notification ENUM('Yes', 'No') NOT NULL DEFAULT 'Yes'
		
	)$charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	/* --------------My Income Table */
	$my_income_table_name = APM_MY_INCOME;

	$sql = "CREATE TABLE $my_income_table_name 
	(
		ID int NOT NULL AUTO_INCREMENT,
		PRIMARY KEY  (ID),
		inc_caffitid varchar(255),
		inc_ccustcc varchar(255),
		inc_ccustemail varchar(255),
		inc_ccustname  varchar(255),
		inc_ccuststate varchar(255),
		inc_cproditem varchar(255),
		inc_cprodtitle varchar(255),
		inc_cprodtype varchar(255),
		inc_ctransaction varchar(255),
		inc_ctransaffiliate varchar(255),
		inc_ctransamount DECIMAL(10,2),
		inc_ctranspaymentmethod varchar(255),
		inc_ctransreceipt varchar(255),
		inc_ctranstime TEXT,
		inc_ctransvendor TEXT,
		inc_cupsellreceipt TEXT,
		inc_cvendthru TEXT,
		inc_cverify TEXT,
		inc_commision_percent varchar(255),
		inc_commision_amt DECIMAL(10,2),
		inc_processed varchar(255),
		inc_ipaddress TEXT,
		income_date DATETIME,
		magic_postback_date DATETIME,
		cron_date DATETIME,
		buyer_tag_identifier TEXT,
		cron_running_status ENUM('Running', 'Executed') NOT NULL DEFAULT 'Executed',
		cron_status ENUM('Yes', 'No') NOT NULL DEFAULT 'No',
		inc_income_type ENUM('JvZoo', 'ClickBank', 'Warrior', 'ThriveCart') NOT NULL DEFAULT 'JvZoo',
		income_source ENUM('Master', 'Child') NOT NULL DEFAULT 'Master',
		social_post_created ENUM('Yes', 'No') NOT NULL DEFAULT 'No'
	)$charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	/* --------------Post Snippet Table */
	
	$snippet_table_name = APM_POST_SNIPPET;
	
	$sql = "CREATE TABLE $snippet_table_name 
	(
		ID int NOT NULL AUTO_INCREMENT,
		PRIMARY KEY  (ID),
		post_snippet_variables varchar(255),
		post_snippet_value TEXT
	)$charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	
	$egEmptySql = "SELECT * FROM $snippet_table_name WHERE 1";
	$egEmptyRecord = $wpdb->get_results($egEmptySql,ARRAY_A);
	
	if(count($egEmptyRecord) == 0)
	{
		$sqlPostSnippet = "INSERT INTO $snippet_table_name (`ID`, `post_snippet_variables`, `post_snippet_value`) VALUES 
							(NULL, 'Sitename', NULL), (NULL, 'CompanyName', NULL), 
							(NULL, 'CompanyAdd', NULL), (NULL, 'Phone', NULL), 
							(NULL, 'ContactUs', NULL), (NULL, 'Country', NULL), 
							(NULL, 'SiteOwner', NULL), (NULL, 'SiteURL', NULL), 
							(NULL, 'jvzoo', NULL), (NULL, 'first_name', NULL), 
							(NULL, 'last_name', NULL), (NULL, 'year_est', NULL),
							(NULL, 'thrivecart', NULL), 
							(NULL, 'avatar1', NULL), 
							(NULL, 'avatar2', NULL)";
		dbDelta( $sqlPostSnippet );
	}
	else
	{
		global $wpdb;
	
		$ethriveSql 	= "SELECT * FROM $snippet_table_name WHERE post_snippet_variables = 'thrivecart'";
		$egThriveRecord  = $wpdb->get_results($ethriveSql,ARRAY_A);
		
		if(count($egThriveRecord) == 0)
		{
			$sqlPostSnippet = "INSERT INTO $snippet_table_name (`ID`, `post_snippet_variables`, `post_snippet_value`) VALUES (NULL, 'thrivecart', NULL)";
			$wpdb->query($sqlPostSnippet);	
		}
	}
	
}

// Our custom post type function
function apm_create_posttype() 
{
	
	global $wpdb;
	$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", 'aweber-subscription-failed' ) );
	
	if ( $valid_page_found ) {
	} else {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => 'aweber-subscription-failed',
			'post_title'     => 'Subscription Failed',
			'post_content'   => 'Subscriber email is already exist in our database.',
			'post_parent'    => '',
			'comment_status' => 'closed',
		);
		$page_id = wp_insert_post( $page_data );
	}

	//--> Creating page for JVZoo IPN Page if it's not exist
	
	$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", 'apm-jvzoo-ipn-listener' ) );
	
	if ( $valid_page_found ) {
	} else {
		$ipn_page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => 'apm-jvzoo-ipn-listener',
			'post_title'     => 'Affiliate jvZoo IPN Listener',
			'post_content'   => 'Do not delete this page.',
			'post_parent'    => '',
			'comment_status' => 'closed',
		);
		$ipn_page_id = wp_insert_post( $ipn_page_data );
	}
	
	//--> Create page for ClickBank Instant Notification
	
	$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", 'clickbank-instant-notification' ) );
	
	if ( $valid_page_found ) {
	} else {
		$ipn_page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => 'clickbank-instant-notification',
			'post_title'     => 'Clickbank Instant Notification',
			'post_content'   => 'Do not delete this page.',
			'post_parent'    => '',
			'comment_status' => 'closed',
		);
		$ipn_page_id = wp_insert_post( $ipn_page_data );
	}
	
	
	//--> Create page for WarriorPlus Instant Notification
	
	$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", 'warriorplus-instant-notification' ) );
	
	if ( $valid_page_found ) {
	} else {
		$ipn_page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => 'warriorplus-instant-notification',
			'post_title'     => 'WarriorPlus Instant Notification',
			'post_content'   => 'Do not delete this page.',
			'post_parent'    => '',
			'comment_status' => 'closed',
		);
		$ipn_page_id = wp_insert_post( $ipn_page_data );
	}
	
	
	
	//--> Create page for Thrivecart Instant Notification
	
	$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", 'thrivecart-instant-notification' ) );
	
	if ( $valid_page_found ) {
	} else {
		$ipn_page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => 'thrivecart-instant-notification',
			'post_title'     => 'Thrivecart Instant Notification',
			'post_content'   => 'Do not delete this page.',
			'post_parent'    => '',
			'comment_status' => 'closed',
		);
		$ipn_page_id = wp_insert_post( $ipn_page_data );
	}
	
	
	return true;
}
?>