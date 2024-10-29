<?php 

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 

if(!current_user_can('edit_others_pages'))

{

	die('You are not allowed to work.');

}

?>

<div class="wrap">

<?php



$egmail_errors = array();

$eemail_success = '';

$egmail_error_found = FALSE;





if (isset($_POST['hdn_page_subscription']) && $_POST['hdn_page_subscription'] == 'Yes')

{

	$nonce = $_REQUEST['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'frm_subscription_clickmagic' ) ) {

		// This nonce is not valid.

		die( 'Security check' ); 

	}



	//--> Validation Start

	$form['clickmagick_clickid'] = isset($_POST['clickmagick_clickid']) ? sanitize_text_field($_POST['clickmagick_clickid']) : '';

	if ($form['clickmagick_clickid'] == '')

	{

		$egmail_errors[] = __('Please enter ClickMagick clickid.', 'apm-child');

		$egmail_error_found = TRUE;

	}

	

	$current_date  = date('Y-m-d G:i:s');

	$user_email_id = apm_get_session_value('apm_ses_user_login');


								  
	$clickmagick_tracking_code = '<img src="https://www.clkmg.com/api/a/pixel/?uid='.$form['clickmagick_clickid'].'&att=2&ref=&dup=1" height="1" width="1" />';

	

	if($egmail_error_found == FALSE)

	{

		$option_id = $_POST['option_id'];

		

		$egSql = $wpdb->prepare("UPDATE `".APM_OPTIONS_TABLE."` SET `clickmagick_clickid` = %s, `clickmagick_tracking_code` = %s, `last_updated_date` = %s WHERE ID = %d LIMIT 1", array($form['clickmagick_clickid'], $clickmagick_tracking_code, $current_date, $option_id));

		$wpdb->query($egSql);

		

		//--> CURL Setup to update record into 

		$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";

		$postdata = array(

			'mode' => 'clickmagic',

			'user_email_id' => $user_email_id,

			'clickmagick_clickid' => $form['clickmagick_clickid'],

			'clickmagick_tracking_code' => $clickmagick_tracking_code,

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

		

		$egmail_success = "ClickMagick information has been submitted successfully.";

	}

}



if ($egmail_error_found == TRUE && isset($egmail_errors[0]) == TRUE)

{

?>

	<div class="error fade"><p><strong><?php echo $egmail_errors[0]; ?></strong></p></div><?php

}

if ($egmail_error_found == FALSE && strlen($eemail_success) > 0)

{

?>

	  <div class="updated fade"><p><strong><?php echo $eemail_success; ?></strong></p></div>

<?php

}



$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";

$record = array();

$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);



?>



<div class="form-wrap">

	<div id="icon-plugins" class="icon32"></div>

	<h2><?php _e(APM_EGMAILl_TITLE . '- ClickMagick Settings', 'apm-child'); ?></h2>

    

    <div style="width:100%; float:left; margin-top:25px;">

<?php

    if(count($recordCrediantial) > 0)

	{

?>

    <form name="frm_subscription" method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=apm_clickmagick">

	<input type="hidden" name="hdn_page_subscription" value="Yes" />

    <input type="hidden" name="option_id" value="<?php echo esc_html($recordCrediantial['ID']);?>" />



    <div style="width:50%; float:left">

        <label for="tag-image">ClickMagick.com Account Number</label>

        <input class="txt-apmbox" name="clickmagick_clickid" type="text" id="clickmagick_clickid" value="<?php echo esc_html($recordCrediantial['clickmagick_clickid']);?>" size="50" />



		<!--

			<label for="tag-link">ClickMagick Pixel code </label>

			<textarea class="txt-apmboxarea" name="clickmagick_tracking_code" id="clickmagick_tracking_code"><?php echo esc_html(stripslashes($pixel_code));?></textarea>

		-->

		

        <input type="hidden" name="frm_subscription_submit" value="yes"/>

        <p class="submit"><input name="publish" lang="publish" class="button button-primary add-new-h2" value="Save" type="submit" /></p>

        

    </div>

        

    <?php wp_nonce_field('frm_subscription_clickmagic'); ?>

    </form>

<?php

	}

	else

	{

?> 

		<h4>Please fill up <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=apm-child">APM Child Plugin</a> form first.</h4>

<?

	}

?>   

    </div>

</div>

</div>