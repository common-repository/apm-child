<?php
/*
Copyright 2017  APM Child® (email : admin@nickjamesadmin.com)
This file is part of Affiliate Pro Membership.

Plugin Name: APM Child
Plugin URI: http://www.pluginpixie.com/
Description: By using this plugin user can configure their details for integration with Aweber, JVZoo and the Affiliate Pro Membership Members Area.
Author: Nick James
E-mail: admin@nickjamesadmin.com
Version: 3.6.9
Author URI: http://www.pluginpixie.com
*/

global $APM_SUBSCRIPTION_VER;
$APM_SUBSCRIPTION_VER = "3.6.9";

define('APM_SUBSCRIPTION_PATH', plugins_url().'/'. basename(dirname(__FILE__)).'/');
define('APM_SUBSCRIPTION_IMAGES', plugins_url().'/'. basename(dirname(__FILE__)).'/images/');
define('APM_PHYSICAL_PATH', plugin_dir_path(__FILE__ ));
define("APM_EGMAILl_TITLE", "APM Child");
define("APM_MASTER_PLUGIN_URL", "https://affiliatepromembership.com/");
define('APM_PLUGIN_PATH', plugins_url() . '/apm-child/');

require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');


include_once ('functions/session.php');
include_once ('functions/core-function.php');
include_once ('functions/database-function.php');
include_once ('aweber_api/aweber_api.php');
include_once ('post-snippet.php');
include_once ('optin-monster-settings.php');
include_once ('affiliate-jvzoo-ipn-listener.php');
include_once ('clickbank-instant-notification.php');
include_once ('thrivecart-instant-notification.php');
include_once ('warrior-instant-notification.php');
include_once ('add-manual-commission.php');
include_once ('jvzoo-affiliate-commission-api.php');
include_once ('infusionsoft/InfusionsoftAPIUtil.php');
include_once ('getresponse/GetResponseAPI3.class.php');

global $post;

register_activation_hook( __FILE__, 'apm_subscription_init' );


function apm_subscription_options() 
{ 
    global $wpdb;
	
    $current_page = isset($_GET['page']) ? $_GET['page'] : '';
    $current_page_ac = isset($_GET['ac']) ? $_GET['ac'] : '';

	if(!apm_check_login())
	{
		$current_page = 'apm_plugin_login';
	}
	
	if($current_page_ac != ''){
		$current_page = $current_page_ac;
	}

    switch($current_page)
    {
        case 'apm_add_crediantial':
            include('admin/apm-subscription-settings.php');
            break;
		case 'apm_clickmagick':
            include('admin/clickmagick.php');
            break;
		case 'apm_list_aweber':
            include('admin/list-aweber.php');
            break;
		case 'apm_funnel_pages':
            include('admin/list-funnel-pages.php');
            break;
		case 'apm_inline_forms':
            include('admin/list-inline-pages.php');
            break;
		
		case 'apm_post_snippets':
            include('admin/post-snippets.php');
            break;
		case 'apm_plugin_login':
            include('admin/plugin-login.php');
            break;
		case 'apm_dispaly_package':
            include('admin/packages.php');
            break;
		case 'apm_thank_you':
            include('admin/thanks.php');
            break;
		case 'apm_orders':
            include('admin/orders.php');
            break;
		case 'apm_my_income':
            include('admin/my_income.php');
            break;
		case 'apm_my_solo_income':
            include('admin/solo-orders.php');
            break;
		case 'apm_direct_email_order':
            include('admin/direct-email-orders.php');
            break;
		case 'apm_info':
            include('admin/apm-info.php');
            break;
		case 'share_your_success':
            include('admin/share-your-success.php');
            break;
		case 'apm_logout':
            include('admin/logout.php');
            break;
        default:
            include('admin/apm-subscription-settings.php');
            break;
    }
}

add_action("init", "apm_update_plugin", 2);
add_action("init", "apm_set_login_session");
add_action("init", "apm_set_logout_session");
add_action("init", "apm_update_hidden_funnel_settings");
add_action('admin_menu', 'apm_admin_menu');
add_action('admin_footer', 'apm_powered_by_arpreach_aweber');

global $wp;

$current_url = $_SERVER['REQUEST_URI'];

//--> Fire JVZoo IPN Page
if(strpos($current_url, 'apm-jvzoo-ipn-listner') !== false)
{
	execute_jvzoo_ipn_listner();
}

if(strpos($current_url, 'apm-jvzoo-ipn-listener') !== false)
{
	execute_jvzoo_ipn_listner();
}


//--> Fire ClickBank INP page
if(strpos($current_url, 'clickbank-instant-notification') !== false)
{
	execute_clickbank_instant_notification();
}

//--> Fire ClickBank INP page
if(strpos($current_url, 'thrivecart-instant-notification') !== false)
{
	execute_thrivecart_instant_notification();
}

//--> Fire WarriorPlus INP page
if(strpos($current_url, 'warriorplus-instant-notification') !== false)
{
	execute_warriorplus_instant_notification();
}

//--> Fire Add Manual Commission page
if(strpos($current_url, 'add-manual-commission') !== false)
{
	execute_add_manual_commission();
}

add_filter( 'cron_schedules', 'affiliate_commission_cron_intervals', 10, 1 );
function affiliate_commission_cron_intervals( $schedules ) {
	// $schedules stores all recurrence schedules within WordPress
	$schedules['five_minutes'] = array(
		'interval'	=> 300,	// Number of seconds, 300 in 5 minutes
		'display'	=> 'Once Every 5 Minutes'
	);

	// Return our newly added schedule to be merged into the others
	return (array)$schedules; 
}

add_action( 'ws_cron_hook', 'execute_jvzoo_affiliate_commission' );
if ( ! wp_next_scheduled( 'ws_cron_hook' ) ) {
	wp_schedule_event( time(), 'five_minutes', 'ws_cron_hook' );
}


/* Social Media posting CRON */

function apm_social_media_cron_schedule( $schedules ) {
	
	$apm_post_schedule = get_option('apm_apm_post_schedule');
	
	
	if($apm_post_schedule == 'Weekly')
	{
		$apm_cron_weekly = get_option('apm_apm_cron_weekly');
		$apm_cron_weekly = strtotime("next ".$apm_cron_weekly);
		
		$schedules['weekly'] = array(
			'interval' => $apm_cron_weekly,
			'display'  => __( 'Every Weekly' ),
		);
		
	}
	
    return $schedules;
}
add_filter( 'cron_schedules', 'apm_social_media_cron_schedule' );


//--> Schedule an action if it's not already scheduled
$apm_post_schedule = get_option('apm_apm_post_schedule');

if($apm_post_schedule == 'Daily')
{
	wp_clear_scheduled_hook( 'apm_social_post_weekly_cron_hook' );
	wp_clear_scheduled_hook( 'apm_social_post_regular_cron_hook' );
	
	if ( ! wp_next_scheduled( 'apm_social_post_daily_cron_hook' ) ) 
	{
		$apm_cron_daily  = get_option('apm_apm_cron_daily');
		wp_schedule_event( strtotime($apm_cron_daily), 'daily', 'apm_social_post_daily_cron_hook' );
	}
}

if($apm_post_schedule == 'Weekly')
{
	wp_clear_scheduled_hook( 'apm_social_post_daily_cron_hook' );
	wp_clear_scheduled_hook( 'apm_social_post_regular_cron_hook' );
	
	if ( ! wp_next_scheduled( 'apm_social_post_weekly_cron_hook' ) ) 
	{
		$apm_cron_weekly_time = get_option('apm_cron_weekly_time');
		wp_schedule_event( strtotime($apm_cron_weekly_time), 'weekly', 'apm_social_post_weekly_cron_hook' );
	}
}

if($apm_post_schedule == 'Fix Price')
{
	wp_clear_scheduled_hook( 'apm_social_post_weekly_cron_hook' );
	wp_clear_scheduled_hook( 'apm_social_post_daily_cron_hook' );
	
	if ( ! wp_next_scheduled( 'apm_social_post_regular_cron_hook' ) ) 
	{
		wp_schedule_event( strtotime("23:30"), 'daily', 'apm_social_post_regular_cron_hook' );
	}
}



//--> Hook into that action that'll fire every day
add_action( 'apm_social_post_daily_cron_hook', 'apm_social_post_daily_cron' );
function apm_social_post_daily_cron() {
    create_post_for_social_media(array("income_id" => 0));
}


//--> Hook into that action that'll fire every week
add_action( 'apm_social_post_weekly_cron_hook', 'apm_social_post_weekly_cron' );
function apm_social_post_weekly_cron() {
    create_post_for_social_media(array("income_id" => 0));
}


//--> Hook into that action that'll fire every day to target fix price
add_action( 'apm_social_post_regular_cron_hook', 'apm_social_post_daily_cron_fix_price' );
function apm_social_post_daily_cron_fix_price() {
    create_post_for_social_media(array("income_id" => 0));
}




function apm_powered_by_arpreach_aweber() {
  $content = '<div class="powered-by-alert">Powered By Aweber.com and ARPreach.com</div>';
  echo $content;
}

function apm_child_scripts() 
{
	wp_register_script('apm_child_script', plugins_url('scripts/apm-settings.js', __FILE__), array('jquery'),'1.1', true);
	wp_enqueue_script('apm_child_script');
	
	wp_register_script('apm_child_autosearch', plugins_url('scripts/autosearch.min.js', __FILE__), array('jquery'),'1.1', true);
	wp_enqueue_script('apm_child_autosearch');

}

add_action( 'admin_enqueue_scripts', 'apm_child_scripts' );  

function apm_child_styles() 
{
	wp_register_style('amp_child_stylesheet', plugins_url('css/apm-style.css', __FILE__));
	wp_enqueue_style('amp_child_stylesheet');
	
	wp_register_style('amp_child_autosearch', plugins_url('css/autosearch.min.css', __FILE__));
	wp_enqueue_style('amp_child_autosearch');
	
}
add_action( 'admin_enqueue_scripts', 'apm_child_styles' );



/** Functionality to load Gunnel Templates to selected pages. **/
function apm_include_funnel_templates($template) 
{
	include_once ("classes/ESP.Class.php");
	$_APMESP = new APMESP;

	//--> Get the Funnel ID and Page ID Mapping data
	$funnel_pageid_mapping_data =  get_option('apm_funnel_pageid_mapping_data');
	$funnel_pageid_mapping_data = json_decode($funnel_pageid_mapping_data);
	$funnel_pageid_mapping_data = (array)$funnel_pageid_mapping_data;
	
	
	//--> Get the Funnel Slug and Page ID Mapping data
	$funnel_slug_mapping_data =  get_option('apm_funnel_slug_mapping_data');
	$funnel_slug_mapping_data = json_decode($funnel_slug_mapping_data);
	$funnel_slug_mapping_data = (array)$funnel_slug_mapping_data;
	
	//print_r($funnel_slug_mapping_data);die;
	
	
	//--> Get the Funnel Status to check it's enable to affiliate or not
	$funnel_status_mapping_data =  get_option('apm_funnel_status_mapping_data');

	$funnel_status_mapping_data = json_decode($funnel_status_mapping_data);
	$funnel_status_mapping_data = (array)$funnel_status_mapping_data;
	
	//--> Current WordPress Page ID
	$apm_curr_page_id = get_the_ID();
	

	if( in_array($apm_curr_page_id, $funnel_pageid_mapping_data) )
	{
		$apm_funnel_id = array_search($apm_curr_page_id, $funnel_pageid_mapping_data);

		if(isset($funnel_slug_mapping_data[$apm_funnel_id]))
		{
			$apm_funnel_slug = $funnel_slug_mapping_data[$apm_funnel_id];
			
			$apm_funnel_status = $funnel_status_mapping_data[$apm_funnel_id];
			
			if($apm_funnel_status == 'Yes')
			{
				 return $template;	
			}
			
			if ($apm_funnel_id !== false) 
			{
				return plugin_dir_path(__FILE__) . 'landing-pages/'.$apm_funnel_slug.'/'.$apm_funnel_slug.'.php';
			}
			
		}
	}
	
    return $template;
}

add_filter('template_include', 'apm_include_funnel_templates');


/** Add optin form after the post content **/

function apm_add_optin_form_after_post_content( $content )
{
	//--> Get the Funnel ID and Page ID Mapping data
	$funnel_termid_mapping_data =  get_option('apm_funnel_termid_mapping_data');
	$funnel_termid_mapping_data = json_decode($funnel_termid_mapping_data);
	$funnel_termid_mapping_data = (array)$funnel_termid_mapping_data;
	
	
	//--> Get the Funnel Slug and Page ID Mapping data
	$funnel_slug_mapping_data =  get_option('apm_funnel_slug_mapping_data');
	$funnel_slug_mapping_data = json_decode($funnel_slug_mapping_data);
	$funnel_slug_mapping_data = (array)$funnel_slug_mapping_data;
	
	
	//--> Get the Funnel Status to check it's enable to affiliate or not
	$funnel_status_mapping_data =  get_option('apm_funnel_status_mapping_data');

	$funnel_status_mapping_data = json_decode($funnel_status_mapping_data);
	$funnel_status_mapping_data = (array)$funnel_status_mapping_data;
	
	
	if (is_single()) 
	{
		//--> Current post category ID
		
		global $post;
		
		$categories = get_the_category($post->ID);
		if (!empty($categories)) 
		{
			$first_category = $categories[0];
            $apm_curr_category_id = $first_category->term_id;
		}

		if( in_array($apm_curr_category_id, $funnel_termid_mapping_data) )
		{
			$apm_funnel_id = array_search($apm_curr_category_id, $funnel_termid_mapping_data);
			$apm_funnel_slug = $funnel_slug_mapping_data[$apm_funnel_id];
			
			
			$apm_funnel_status = $funnel_status_mapping_data[$apm_funnel_id];
		
			if($apm_funnel_status == 'Yes')
			{
				 return $content;	
			}
			
			if ($apm_funnel_id !== false) 
			{
				ob_start();
				include plugin_dir_path(__FILE__) . 'optin-forms/'.$apm_funnel_slug.'/'.$apm_funnel_slug.'.php';
				$extra_content = ob_get_clean();
				
				$content .= $extra_content;
			}
		}
	}
	
    return $content;
}

add_filter('the_content', 'apm_add_optin_form_after_post_content');


function apm_update_hidden_funnel_settings()
{
	$funnel_status_mapping_data =  get_option('apm_funnel_status_mapping_data');

	$funnel_status_mapping_data = json_decode($funnel_status_mapping_data);
	$funnel_status_mapping_data = (array)$funnel_status_mapping_data;

	if(is_array($funnel_status_mapping_data) && count($funnel_status_mapping_data) <= 0)
	{
		
		$resultFunnelList = apm_get_funnel_list_hidden();
		$resultFunnelList = json_decode($resultFunnelList);	
		
		$arr_funnel_status_mapping = array();
				
			

		if( is_array($resultFunnelList ) && count($resultFunnelList) > 0)
		{
			foreach($resultFunnelList as $funnelList)
			{
				
				if($funnelList->ID == 13)
				{
					$status_value = 'Yes';
				}
				else
				{
					$status_value = 'No';
				}
				
				$arr_funnel_status_mapping[$funnelList->ID] = $status_value;
			}
			
			
			$funnel_status_mapping = json_encode($arr_funnel_status_mapping);
			
			update_option('apm_funnel_status_mapping_data', $funnel_status_mapping);
		}
	}
	
	
	
	/** If Optin form's tags not set in affliate database then first time it will set. **/
	$apm_funnel_prospect_tags =  get_option('apm_funnel_prospect_tags');

	$apm_funnel_prospect_tags = json_decode($apm_funnel_prospect_tags);
	$apm_funnel_prospect_tags = (array)$apm_funnel_prospect_tags;
	
	if(is_array($apm_funnel_prospect_tags) && count($apm_funnel_prospect_tags) <= 0)
	{

		$resultFunnelList = apm_get_funnel_list_hidden();
		$resultFunnelList = json_decode($resultFunnelList);	
			
		if( is_array($resultFunnelList ) && count($resultFunnelList) > 0)
		{
			$arr_funnel_prospect_tags = array();
			
			foreach($resultFunnelList as $funnelList)
			{
				$arr_funnel_prospect_tags[$funnelList->funnel_slug] = $funnelList->optin_prospect_tags;
			}
			
			$funnel_prospect_tags = json_encode($arr_funnel_prospect_tags);
			update_option('apm_funnel_prospect_tags', $funnel_prospect_tags);
		}
		
	}
	
	
	/** If Buyer Tag not set in affliate database then first time it will set. **/
	
	
	/*$apm_funnel_thrive_buyer_tags =  get_option('apm_funnel_thrive_buyer_tags');

	$apm_funnel_thrive_buyer_tags = json_decode($apm_funnel_thrive_buyer_tags);
	$apm_funnel_thrive_buyer_tags = (array)$apm_funnel_thrive_buyer_tags;
	
	if(is_array($apm_funnel_thrive_buyer_tags) && count($apm_funnel_thrive_buyer_tags) <= 0)
	{
		$resultFunnelList = apm_get_funnel_list_hidden();
		$resultFunnelList = json_decode($resultFunnelList);	
			
		if( is_array($resultFunnelList ) && count($resultFunnelList) > 0)
		{
			$arr_funnel_buyer_tags = array();
			
			foreach($resultFunnelList as $funnelList)
			{
				$arr_funnel_buyer_tags[$funnelList->funnel_slug] = $funnelList->thrive_buyer_tags;
			}
			
			$arr_funnel_buyer_tags = json_encode($arr_funnel_buyer_tags);
			update_option('apm_funnel_buyer_tags', $arr_funnel_buyer_tags);
		}
		
	}*/

}



/** Put Click Magic ID in Session **/

add_action('init', 'apm_optin_monster_session_functions',1);

function apm_custom_form_session_functions()
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





?>