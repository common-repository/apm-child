<?php
//uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {exit ();}
else
{
	global $wpdb;
	$wpdb->prefix;
	
	include_once ('functions/database-function.php');
	
	delete_option('apm_subscription_curr_ver');
	delete_option('apm_subscription_prev_ver');
	
	$wpdb->query("DROP TABLE `".APM_OPTIONS_TABLE."`");
	$wpdb->query("DROP TABLE `".APM_POST_SNIPPET."`");
	$wpdb->query("DROP TABLE `".APM_MY_INCOME."`");
}
?>