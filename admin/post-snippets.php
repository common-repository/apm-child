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

if(isset($_POST['hdn_page_post_snippets']) && $_POST['hdn_page_post_snippets'] == 'Yes')
{
	$nonce = $_REQUEST['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'frm_post_snippets' ) ) {
		// This nonce is not valid.
		die( 'Security check' ); 
	}
	$arr_post_snippet_variables = isset($_POST['post_snippet_variables']) ? $_POST['post_snippet_variables'] : ''; // This is an array, so sanitize will be applied later
	$arr_post_snippet_value = isset($_POST['post_snippet_variables']) ? $_POST['post_snippet_value'] : '';	// This is an array, so sanitize will be applied later
	
	$sql = "TRUNCATE TABLE ".APM_POST_SNIPPET."";
	$wpdb->get_results($sql);
	
	for($i = 0; $i < sizeof($arr_post_snippet_variables); $i++)
	{
		if($arr_post_snippet_variables[$i] != '')
		{
			$egSql = $wpdb->prepare(
				"INSERT INTO `".APM_POST_SNIPPET."`
				(`post_snippet_variables`,`post_snippet_value`) VALUES(%s, %s)", array(sanitize_text_field($arr_post_snippet_variables[$i]), wp_kses_post($arr_post_snippet_value[$i])));
			$wpdb->query($egSql);
		}
	}
	
	//--> Save post snippet into master plugin.
	$url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";	
	$user_email_id = apm_get_session_value('apm_ses_user_login');
	
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
	
}

if ($egmail_error_found == TRUE && isset($egmail_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $egmail_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($egmail_error_found == FALSE && strlen($eemail_success) > 0)
{
	?>
	  <div class="updated fade">
		<p><strong><?php echo $eemail_success; ?></strong></p>
	  </div>
	  <?php
}

$egSqlCrediantial = "SELECT * FROM `".APM_POST_SNIPPET."` WHERE 1";
$record = array();
$egRecord =   $wpdb->get_results($egSqlCrediantial,ARRAY_A); // return OBJECT
$total_record = count($wpdb->get_results($egSqlCrediantial, ARRAY_A));
?>

<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(APM_EGMAILl_TITLE . " - Post Snippets", 'apm-child'); ?></h2>
    
    <div style="width:100%; float:left">
    
    <form name="frm_post_snippets" method="post">
	<input type="hidden" name="hdn_page_post_snippets" value="Yes" />
    
        <div class="form-area">
          
          <?php
		  	$post_snippet_variables = $post_snippet_value = '';
          	for($i = 0; $i < 10 + $total_record; $i++)
			{
				if($total_record > 0)
				{
					$post_snippet_variables = @$egRecord[$i]['post_snippet_variables'];
					$post_snippet_value 	= @$egRecord[$i]['post_snippet_value'];
				}
		  ?>
              <div class="form-row">
                <div class="form-col">
                    <label for="tag-image">Variable</label>
                    <input class="txt-apmbox" name="post_snippet_variables[<?=$i?>]" value="<?=esc_html($post_snippet_variables)?>" style="font-size:14px; font-weight:500" type="text" size="40" <?php if($i < 15){?> readonly="readonly" <?php } ?> />
                </div>
               
                <div class="form-col">
                    <label for="tag-image">Value</label>
                    <textarea class="txt-apmboxarea" name="post_snippet_value[<?=$i?>]"><?=esc_html($post_snippet_value)?></textarea>
                </div>
              </div>
         <?
			}
		 ?> 
          
           <div class="form-row">
            <input type="hidden" name="frm_subscription_submit" value="yes"/>
            <p class="submit"><input name="publish" lang="publish" class="button button-primary add-new-h2" value="Update Snippets" type="submit" /></p>
           </div>
           
        </div>
        
    <?php wp_nonce_field('frm_post_snippets'); ?>
    </form>
    </div>
</div>
</div>