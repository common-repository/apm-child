<?php
/*
Copyright 2016  APM Child Plugin.® (email : admin@nickjamesadmin.com)
This file is part of APM Child.
*/

function apm_subscription_init()
{
	/* -----load sample popup settings */
	if( get_option("apm_subscription_curr_ver") == NULL)
	{
		/* --------- initialize database */
		apm_aweber_subscription_db_init();
		apm_create_posttype();
	}
	
	/* -------- check version */

	if( $GLOBALS['APM_SUBSCRIPTION_VER'] != get_option('apm_subscription_curr_ver') )
	{
		apm_aweber_subscription_db_init();
		apm_create_posttype();
		$ver = get_option('apm_subscription_curr_ver');
		update_option('apm_subscription_prev_ver',$ver);
		update_option('apm_subscription_curr_ver', $GLOBALS['APM_SUBSCRIPTION_VER']);
	}
	
}

function apm_update_plugin()
{
	if($GLOBALS['APM_SUBSCRIPTION_VER'] > get_option('apm_subscription_curr_ver'))
	{
		apm_aweber_subscription_db_init();
		apm_create_posttype();
		
		$ver = get_option('apm_subscription_curr_ver');
		update_option('apm_subscription_prev_ver',$ver);
		update_option('apm_subscription_curr_ver', $GLOBALS['APM_SUBSCRIPTION_VER']);
	}
}



function create_post_for_social_media($arr_request_val)
{
	require_once( ABSPATH . "wp-includes/pluggable.php" );

	global $wpdb;
	
	$flag_publish_post = false;
	
	$income_id = $arr_request_val['income_id'];
	
	$apm_post_schedule = get_option('apm_apm_post_schedule');
	$apm_cron_daily  = get_option('apm_apm_cron_daily');
	$apm_cron_weekly = get_option('apm_apm_cron_weekly');
	$apm_cron_weekly_time = get_option('apm_cron_weekly_time');
	$share_success_category = get_option('apm_share_success_category');
	$social_share_message = get_option('apm_social_share_message');
	$share_success_headline = get_option('apm_share_success_headline');
	$share_success_imageURL = get_option('apm_share_success_image');
	
	//--> create category when it is not selected at settings page 
	if($share_success_category == '')
	{
		$parent_term = term_exists( 'social' );
		
		if(!$parent_term)
		{
			wp_insert_term(
				'Social',
				'category',
				array(
				  'description'	=> 'This category will be used for APM Social Media Posting.',
				  'slug' 		=> 'social'
				)
			);
		}
		
		$social_category = get_category_by_slug('social'); 
		$share_success_category = $social_category->term_id;
	}
	
	//--> Create Post Title and Body
	
	if($apm_post_schedule == 'Instant')
	$post_title = date("H:i");
	
	if($apm_post_schedule == 'Daily')
	$post_title = $apm_cron_daily;
	
	if($apm_post_schedule == 'Weekly')
	$post_title = $apm_cron_weekly.'-'.$apm_cron_weekly_time;
	
	
	
	$post_title = $share_success_headline.' '.$post_title;
	
	
	//--> Affiliate Information 
	
	
	$egSqlCrediantial = "SELECT * FROM `".$wpdb->prefix."cso_options` WHERE 1";
	$record = array();
	$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
	
	
	//--> Get Information of income.
	if($income_id > 0)
	{
		
		//--> Instant
		$egSql = "SELECT * FROM `".$wpdb->prefix."cso_my_income` WHERE ID = '".$income_id."'";
		$egRecord = $wpdb->get_row($egSql, ARRAY_A); 
		
		$inc_ctransamount = $egRecord['inc_ctransamount'];
		$inc_commision_amt = $egRecord['inc_commision_amt'];
		
		$egUpdate = $wpdb->prepare("UPDATE ".$wpdb->prefix."cso_my_income SET `social_post_created` = 'Yes' WHERE ID = %s ", array($income_id));
		$wpdb->query($egUpdate);
		$flag_publish_post = true;
			
	}
	elseif($apm_post_schedule == 'fixprice')
	{
		$flag_publish_post = false;
		
		$egSql = "SELECT * FROM `".$wpdb->prefix."cso_my_income` WHERE social_post_created = 'No'";
		$egRecords =   $wpdb->get_results($egSql, ARRAY_A);
		
		$inc_ctransamount = 0;
		$inc_commision_amt = 0;
		
		foreach ($egRecords as $egRecord)
		{
			$inc_ctransamount  += $egRecord['inc_ctransamount'];
			$inc_commision_amt += $egRecord['inc_commision_amt'];
			
			$arr_income_ids[] = $egRecord['ID'];
			
			if( $inc_commision_amt >= $set_success_story_amount )
			{
				$flag_publish_post = true;
				break;
			}
		}
	}
	else
	{
		//--> Daily and Weekly
		
		$success_story_last_execution_time = get_option('success_story_last_execution_time');
		
		$egSql = "SELECT * FROM `".$wpdb->prefix."cso_my_income` WHERE social_post_created = 'No' AND income_date > '".$success_story_last_execution_time."' ";
		$egRecords =   $wpdb->get_results($egSql, ARRAY_A);
		
		$inc_ctransamount = 0;
		$inc_commision_amt = 0;
		
		foreach ($egRecords as $egRecord)
        {
			$inc_ctransamount += $egRecord['inc_ctransamount'];
			$inc_commision_amt += $egRecord['inc_commision_amt'];
			
			$egUpdate = $wpdb->prepare("UPDATE ".$wpdb->prefix."cso_my_income SET `social_post_created` = 'Yes' WHERE ID = %s ", array($record->ID));
			$wpdb->query($egUpdate);	
			$flag_publish_post = true;	
		}
	}
	
	/* This is for the fix amount set */
	if( ( $inc_commision_amt >= $set_success_story_amount ) && count($arr_income_ids) > 0)
	{
		foreach($arr_income_ids as $income_ids)
		{
			$egUpdate = $wpdb->prepare("UPDATE ".$wpdb->prefix."cso_my_income SET `social_post_created` = 'Yes' WHERE ID = %s ", array($income_ids));
			$wpdb->query($egUpdate);		
		}
	}
	
		
	$inc_ctransamount  = number_format($inc_ctransamount, 2, '.', '');
	$inc_commision_amt = number_format($inc_commision_amt, 2, '.', '');
	
	
	if($apm_post_schedule == 'TurnOff')
	{
		$flag_publish_post = false;	
	}
	
	//--> Replace Title short code
	if($flag_publish_post == true)
	{
		$post_title = str_replace("[Platform]", $egRecord['inc_income_type'], $post_title);
		$post_title = str_replace("[Vendor]", $egRecord['inc_ctransvendor'], $post_title);
		$post_title = str_replace("[CustomerName]", $egRecord['inc_ccustname'], $post_title);
		$post_title = str_replace("[CustomerEmail]", $egRecord['inc_ccustemail'], $post_title);
		$post_title = str_replace("[AffiliateName]", $recordCrediantial['first_name'].' '.$recordCrediantial['last_name'], $post_title);
		$post_title = str_replace("[AffiliateID]", $egRecord['inc_ctransaffiliate'], $post_title);
		$post_title = str_replace("[Clickid]", $egRecord['inc_caffitid'], $post_title);
		$post_title = str_replace("[ItemName]", $egRecord['inc_cprodtitle'], $post_title);
		$post_title = str_replace("[SalesAmount]", $inc_ctransamount, $post_title);
		$post_title = str_replace("[CommissionAmount]", $inc_commision_amt, $post_title);
		
		
		
		//--> Post Body
		$social_share_message = wpautop($social_share_message);
		
		$social_share_message = str_replace("[Platform]", $egRecord['inc_income_type'], $social_share_message);
		$social_share_message = str_replace("[Vendor]", $egRecord['inc_ctransvendor'], $social_share_message);
		$social_share_message = str_replace("[CustomerName]", $egRecord['inc_ccustname'], $social_share_message);
		$social_share_message = str_replace("[CustomerEmail]", $egRecord['inc_ccustemail'], $social_share_message);
		$social_share_message = str_replace("[AffiliateName]", $recordCrediantial['first_name'].' '.$recordCrediantial['last_name'], $social_share_message);
		$social_share_message = str_replace("[AffiliateID]", $egRecord['inc_ctransaffiliate'], $social_share_message);
		$social_share_message = str_replace("[Clickid]", $egRecord['inc_caffitid'], $social_share_message);
		$social_share_message = str_replace("[ItemName]", $egRecord['inc_cprodtitle'], $social_share_message);
		$social_share_message = str_replace("[SalesAmount]", $inc_ctransamount, $social_share_message);
		$social_share_message = str_replace("[CommissionAmount]", $inc_commision_amt, $social_share_message);
	
		if($inc_ctransamount > 0)
		{
			$page_data = array(
					'post_status'    => 'publish',
					'post_type'      => 'post',
					'post_author'    => 1,
					'post_name'      => $post_title,
					'post_title'     => $post_title,
					'post_content'   => $social_share_message,
					'post_parent'    => '',
					'post_category' => array( $share_success_category ),
					'comment_status' => 'closed',
			);
			
			$post_id = wp_insert_post( $page_data );
			
			
			
			$image = media_sideload_image( $share_success_imageURL, $post_id, $post_title, 'id' );
			set_post_thumbnail( $post_id, $image );

			
		}
			
		$TodaysDate = date('Y-m-d H:i:s');
		update_option('success_story_last_execution_time', $TodaysDate);
	}
	
	return true;
}


function apm_get_page_listing_for_landing_page() 
{
    $pages = get_pages();
    $pages_array = array();

    foreach ( $pages as $page ) {
        $pages_array[$page->ID] = $page->post_title;
    }

    return $pages_array;
}


function apm_get_category_list_for_optin_form() 
{
    $categories = get_categories( array(
		'orderby' => 'name',
		'order'   => 'ASC'
	) );
	
    $category_array = array();

    foreach ( $categories as $category ) {
        $category_array[$category->term_id] = esc_html( $category->name ) ;
    }

    return $category_array;
}



//--> Function to check if a directory exists for the gunnel landing page
function check_directory_exists($slug) {

    $directory_path = APM_PHYSICAL_PATH . 'landing-pages/' . $slug;

    if (is_dir($directory_path)) 
	{
        return true;
    } 
	else 
	{
        return false;
    }
}



function check_directory_exists_inline_optin($slug) {

    $directory_path = APM_PHYSICAL_PATH . 'optin-forms/' . $slug;

    if (is_dir($directory_path)) 
	{
        return true;
    } 
	else 
	{
        return false;
    }
}


/*** Get the Google reCAPTCHA info from the master website ***/
function apm_get_google_recaptcha_info( $user_email_id )
{
	/** Start Google reCAPTCHA coding **/

	$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
	
	$postdata = array(
		'mode' => 'get_google_recaptcha',
		'aff_id' => $user_email_id
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
		return $recaptcha_key_message = "Google reCAPTCHA key has not assigned for this affiliate member.";
	}
	
	return $recaptcha_site_key;
}


/** Get Aweber Tags **/

function apm_display_aweber_tag( $funnel_slug )
{
	$apm_funnel_prospect_tags =  get_option('apm_funnel_prospect_tags');

	$apm_funnel_prospect_tags = json_decode($apm_funnel_prospect_tags);
	$apm_funnel_prospect_tags = (array)$apm_funnel_prospect_tags;
	
	
	
	return $apm_funnel_prospect_tags[$funnel_slug];
}


/**** Plugin Menu *****/

function apm_admin_menu() 
{
	if ( current_user_can( 'apm_subscription_view' ) ) 
	{
		$role = "apm_subscription_view";
	} 
	else 
	{
		$role = "manage_options";
	}

	$queue = '';
	
	$plugin_name = __( 'APM Child', 'aweber_subscription_option' );

	if ( defined( 'AweberSubscriptionOption_PRO_VERSION' ) ) 
	{
		$plugin_name .= " " . __( 'Pro', 'aweber_subscription_option' );
	}
	
	add_menu_page( $plugin_name, $plugin_name, 'administrator', 'apm-child', 'apm_subscription_options', 'dashicons-email' );
	

	add_submenu_page( 'apm-child', __( 'Main Settings', 'apm-child' ), __( 'Main Settings', 'apm-child' ), $role, 'apm_add_crediantial', 'apm_subscription_options' );

	add_submenu_page( 'apm-child', __( 'ClickMagick Settings', 'apm-child' ), __( 'ClickMagick Settings', 'apm-child' ), $role, 'apm_clickmagick', 'apm_subscription_options' );

	add_submenu_page( 'apm-child', __( 'Autoresponder Settings', 'apm-child' ), __( 'Autoresponder Settings', 'apm-child' ), $role, 'apm_list_aweber', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'Landing Pages', 'apm-child' ), __( 'Landing Pages', 'apm-child' ), $role, 'apm_funnel_pages', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'Optin Forms', 'apm-child' ), __( 'Optin Forms', 'apm-child' ), $role, 'apm_inline_forms', 'apm_subscription_options' );
	
	//add_submenu_page( 'apm-child', __( 'Optin Hidden Page', 'apm-child' ), __( 'Optin Hidden Page', 'apm-child' ), $role, 'apm_optin_hidden_page', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'Post Snippets', 'apm-child' ), __( 'Post Snippets', 'apm-child' ), $role, 'apm_post_snippets', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'Buy Traffic', 'apm-child' ), __( 'Buy Traffic', 'apm-child' ), $role, 'apm_dispaly_package', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'My Income', 'apm-child' ), __( 'My Income', 'apm-child' ), $role, 'apm_my_income', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'List Lead Orders', 'apm-child' ), __( 'List Lead Orders', 'apm-child' ), $role, 'apm_orders', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'List Solo Ad Orders', 'apm-child' ), __( 'List Solo Ad Orders', 'apm-child' ), $role, 'apm_my_solo_income', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'List Direct Mail Orders', 'apm-child' ), __( 'List Direct Mail Orders', 'apm-child' ), $role, 'apm_direct_email_order', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'Share Your Success', 'apm-child' ), __( 'Share Your Success', 'apm-child' ), $role, 'share_your_success', 'apm_subscription_options' );
	
	add_submenu_page( 'apm-child', __( 'Logout', 'apm-child' ), __( 'Logout', 'apm-child' ), $role, 'apm_logout', 'apm_subscription_options' );
	
	add_submenu_page(
        null, // No parent menu slug, making it a hidden page
        __('Optin Hidden Page', 'apm-child'), // Page title
        __('Optin Hidden Page', 'apm-child'), // Menu title (won't be shown)
        'manage_options', // Capability
        'apm_optin_hidden_page', // Menu slug
        'apm_optin_hidden_page_callback' // Callback function
    );
	
}

function apm_optin_hidden_page_callback() {
    // Check if the current user has the required capability
    if (!current_user_can('manage_options')) {
        wp_die(__('Sorry, you are not allowed to access this page.'));
    }
    
     include(APM_PHYSICAL_PATH.'/admin/list-optin-hidden-pages.php');
}



//--> Function For get Funnel List from master plugin
function apm_get_funnel_list_hidden()
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


?>