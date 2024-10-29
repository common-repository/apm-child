<?php 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 
if(!current_user_can('edit_others_pages'))
{
	die('You are not allowed to work.');
}
?>
<div class="wrap">
<?php

/** Get Packages **/

$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
	
$postdata = array(
	'mode' => 'get_packages',
	'return_url' => get_option('siteurl')."/wp-admin/admin.php?page=apm-child&ac=apm_thank_you",
	'cancel_url' => get_option('siteurl')."/wp-admin/admin.php?page=apm_dispaly_package",
	'aff_id' => apm_get_session_value('apm_ses_user_login'),
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


/** Check Post Snippet is updated into Master website or not **/




/** Get Banner Ads **/

$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
	
$postdata = array(
	'mode' => 'get_banner_ads',
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
$resultBannerList = $response['body'];


/** Get Solo Ads **/

$apm_snippet_updated = get_option("apm_snippet_updated_in_master");

if($apm_snippet_updated != "Yes")
{
	$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";	
	
	$user_email_id = apm_get_session_value('apm_ses_user_login');
	
	
	$affiliateSql 	 = " SELECT * FROM `".APM_POST_SNIPPET."` where 1";
	$affiliateRecord =   $wpdb->get_results($affiliateSql, ARRAY_A); // return Array
	
	if(count($affiliateRecord) > 0)
	{
		$arr_post_snippet_variables = $arr_post_snippet_value = array();
		foreach($affiliateRecord as $affiliate)
		{
			$arr_post_snippet_variables[] = $affiliate['post_snippet_variables'];
			$arr_post_snippet_value[] = $affiliate['post_snippet_value'];
		}
	}
	
	$postdata = array(
		'mode' => 'update_post_snippet',
		'user_email_id' => $user_email_id,
		'post_snippet_variables' => $arr_post_snippet_variables,
		'post_snippet_value' => $arr_post_snippet_value,
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
	$affiliate_updated = $response['body'];
	
	update_option('apm_snippet_updated_in_master', "Yes");
}




$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
	
$postdata = array(
	'mode' => 'get_solo_ads',
	'server_type' => 'live',
	'return_url' => get_option('siteurl')."/wp-admin/admin.php?page=apm-child&ac=apm_thank_you",
	'cancel_url' => get_option('siteurl')."/wp-admin/admin.php?page=apm_dispaly_package",
	'aff_id' => apm_get_session_value('apm_ses_user_login'),
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
$soloResultList = $response['body'];


/*  Direct Email Start */

$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
	
$postdata = array(
	'mode' => 'get_direct_email_package',
	'server_type' => 'live',
	'return_url' => get_option('siteurl')."/wp-admin/admin.php?page=apm-child&ac=apm_thank_you",
	'cancel_url' => get_option('siteurl')."/wp-admin/admin.php?page=apm_dispaly_package",
	'aff_id' => apm_get_session_value('apm_ses_user_login'),
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
$directEmailResultList = $response['body'];

/*  Direct Email End */


?>

<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(APM_EGMAILl_TITLE . " - Recommended Resources", 'apm-child'); ?></h2>
    <div class="package-result-area">
	<?php echo $resultBannerList;?>    
    </div>
    
    
    
    <h2 class="package-headline">Targeted Direct Mail Campaign Packages</h2>
    <div style="clear:both">&nbsp;</div>
   <!-- <center><img src="https://internetbusinessplan.co.uk/wp-content/uploads/2019/11/TD-logo-RedGlowing.png" style="height:200px;width:25%;"/></center>-->
    <div class="package-result-area">
    	<?php echo $directEmailResultList;?>
    </div>
    
    
    <h2 class="package-headline">Targeted Solo Ad Campaign Packages</h2>
    <div class="package-result-area">
		<?php echo $resultList;?>    
    </div>
    
    <div style="clear:both">&nbsp;</div>
    <!--<center><img src="https://internetbusinessplan.co.uk/wp-content/uploads/2019/11/TD-logo-RedGlowing.png" style="height:200px;width:25%;"/></center>-->
    <div class="package-result-area">
    	<?php echo $soloResultList;?>
    </div>
</div>
</div>
   
