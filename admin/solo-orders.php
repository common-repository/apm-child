<?php 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 
if(!current_user_can('edit_others_pages'))
{
	die('You are not allowed to work.');
}
?>
<div class="wrap">
<?php

$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";

$postdata = array(
	'mode' => 'get_solo_orders',
	'aff_id' => apm_get_session_value('apm_ses_user_login')
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
?>

<link rel='stylesheet' href='<?php echo APM_SUBSCRIPTION_PATH?>css/apm-style.css' type='text/css' media='all' />
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
     <h2><?php _e(APM_EGMAILl_TITLE . " - Solo Orders", 'apm-child'); ?></h2>
    <div style="width:100%; float:left">
	<?php echo $resultList;?>    

    </div>
    
</div>
</div>