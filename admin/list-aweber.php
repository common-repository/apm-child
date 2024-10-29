<?php 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 
if(!current_user_can('edit_others_pages'))
{
	die('You are not allowed to work.');
}
?>

<div class="wrap">
<?php

$egmail_errors = $sendeagle_lists = array();
$egmail_success = $company_unique_id = $sendeagle_company_name = '';
$egmail_error_found = FALSE;
$show_aweber_list = false;

include_once ("classes/ESP.Class.php");
$_APMESP = new APMESP;


// Form submitted and validation
if(isset($_POST['hdn_page_subscription']) && $_POST['hdn_page_subscription'] == 'Yes')
{
	$nonce = $_REQUEST['_wpnonce'];
	
	if ( ! wp_verify_nonce( $nonce, 'frm_subscription' ) ) {
		// This nonce is not valid.
		die( 'Security check' ); 
	}
	
	//--> Validation Start
	
	$form['first_name'] = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
	if ($form['first_name'] == '')
	{
		$egmail_errors[] = __('Please enter first name.', 'apm-child');
		$egmail_error_found = TRUE;
	}
	
	$form['last_name'] = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
	if ($form['last_name'] == '')
	{
		$egmail_errors[] = __('Please enter last name.', 'apm-child');
		$egmail_error_found = TRUE;
	}
	
	$form['email'] = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
	if ($form['email'] == '')
	{
		$egmail_errors[] = __('Please enter email.', 'apm-child');
		$egmail_error_found = TRUE;
	}
	
	$form['jv_zoo_id'] = isset($_POST['jv_zoo_id']) ? sanitize_text_field($_POST['jv_zoo_id']) : '';
	if ($form['jv_zoo_id'] == '')
	{
		$egmail_errors[] = __('Please enter JV Zoo ID.', 'apm-child');
		$egmail_error_found = TRUE;
	}
	
	$form['email_service_provider'] = isset($_POST['email_service_provider']) ? sanitize_text_field($_POST['email_service_provider']) : '';
	if ($form['email_service_provider'] == '')
	{
		$egmail_errors[] = __('Please Choose Email Service Provider.', 'apm-child');
		$egmail_error_found = TRUE;
	}
	
	$form['clickbank_affiliate_nickname'] = isset($_POST['clickbank_affiliate_nickname']) ? sanitize_text_field($_POST['clickbank_affiliate_nickname']) : '';
	$form['thrivecart_affiliate_username'] = isset($_POST['thrivecart_affiliate_username']) ? sanitize_text_field($_POST['thrivecart_affiliate_username']) : '';
	$form['warriorplus_affiliate_id'] = isset($_POST['warriorplus_affiliate_id']) ? sanitize_text_field($_POST['warriorplus_affiliate_id']) : '';
	
	if($egmail_error_found == FALSE)
	{
		$egmail_success = $_APMESP->apm_esp_add(array("first_name" => $form['first_name'], "last_name" => $form['last_name'], "email" => $form['email'], "jv_zoo_id" => $form['jv_zoo_id'], "email_service_provider" => $form['email_service_provider'], "clickbank_affiliate_nickname" => $form['clickbank_affiliate_nickname'], "thrivecart_affiliate_username" => $form['thrivecart_affiliate_username'], "warriorplus_aff_id" => $form['warriorplus_affiliate_id']));
	}
	
}


//--> Edit Funnel and AWeber/SendEagle Mapping
if (isset($_POST['hdnPageAction']) && $_POST['hdnPageAction'] == 'Edit')
{
	$esp_list_id = '';
	$nonce = $_REQUEST['_wpnonce'];
	
	if ( ! wp_verify_nonce( $nonce, 'frm_apm_list' ) ) {
		// This nonce is not valid.
		die( 'Security check' ); 
	}
	
	$form['option_id'] = isset($_POST['option_id']) ? sanitize_text_field($_POST['option_id']) : '';
	$form['funnel_id'] = isset($_POST['funnel_id']) ? $_POST['funnel_id'] : '';
	$form['aweber_auth_code'] = isset($_POST['aweber_auth_code']) ? sanitize_text_field($_POST['aweber_auth_code']) : '';
	$form['sendeagle_api_key'] = isset($_POST['sendeagle_api_key']) ? sanitize_text_field($_POST['sendeagle_api_key']) : '';
	$form['esp_list_id']	   = isset($_POST['esp_list_id']) ? $_POST['esp_list_id'] : '';
	
	$form['jvz_infusionsoft_api_key'] = isset($_POST['jvz_infusionsoft_api_key']) ? sanitize_text_field($_POST['jvz_infusionsoft_api_key']) : '';
	$form['jvz_infusionsoft_app_name'] = isset($_POST['jvz_infusionsoft_app_name']) ? sanitize_text_field($_POST['jvz_infusionsoft_app_name']) : '';
	
	$form['getresponse_api_key'] = isset($_POST['getresponse_api_key']) ? sanitize_text_field($_POST['getresponse_api_key']) : '';
	$form['getresponse_custom_field'] = isset($_POST['getresponse_custom_field']) ? sanitize_text_field($_POST['getresponse_custom_field']) : '';
	
	$arr_return_val = $_APMESP->apm_esp_edit(
						array(
							"option_id" => $form['option_id'], 
							"funnel_id" => $form['funnel_id'],
							"aweber_auth_code" => $form['aweber_auth_code'],
							"sendeagle_api_key" => $form['sendeagle_api_key'],
							"esp_list_id" => $form['esp_list_id'],
							"jvz_infusionsoft_api_key" => $form['jvz_infusionsoft_api_key'],
							"jvz_infusionsoft_app_name" => $form['jvz_infusionsoft_app_name'],
							"getresponse_api_key" => $form['getresponse_api_key'],
							"getresponse_custom_field" => $form['getresponse_custom_field'],
						 )
					 );
						 
	$egmail_errors[]	= $arr_return_val['egmail_errors'];
	$egmail_success 	= $arr_return_val['egmail_success'];
	$egmail_error_found = $arr_return_val['egmail_error_found'];
} 


//--> Get Funnel list from master website for funnel and Email Service provider 
$resultFunnelList = $_APMESP->apm_get_funnel_list();


//--> Get Affiliate Settings details
$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
$record = array();
$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
$option_id = $recordCrediantial['ID'];


/** Funnel and AWeber Mapping and to get AWeber List if affiliate chooses AWeber **/
if($recordCrediantial['email_service_provider'] == "" || $recordCrediantial['email_service_provider'] == "Aweber")
{
	$mapping_type = "Aweber";
	//$mapping_list_id = "aweber_list_id";
	
	$arr_return_val = $_APMESP->apm_get_aweber_list_by_api($recordCrediantial);
	
	$account 			= $arr_return_val['account'];
	$egmail_errors[]	= $arr_return_val['egmail_errors'];
	$show_aweber_list 	= $arr_return_val['show_aweber_list'];
	$egmail_error_found = $arr_return_val['egmail_error_found'];
}


/** Funnel and SendEagle Mapping and to get SendEagle List if affiliate choose SendEagle **/
if($recordCrediantial['email_service_provider'] == "Sendeagle")
{
	$mapping_type = "Sendeagle";
	//$mapping_list_id = "sendeagle_list_id";
	$sendeagle_lists = $_APMESP->apm_get_sendeagle_list_by_api($recordCrediantial);
	
}


/** Funnel and Infusionsoft Mapping and to get Infusionsoft List if affiliate choose Infusionsoft **/
if($recordCrediantial['email_service_provider'] == "Infusionsoft")
{
	
	$mapping_type = "Infusionsoft";
	//$mapping_list_id = "infusionsoft_key";
	$infusionsoft_lists = '';
	
	$INFUSIONSOFT_APP_NAME = $recordCrediantial['jvz_infusionsoft_app_name'];
	$INFUSIONSOFT_API_KEY = $recordCrediantial['jvz_infusionsoft_api_key'];
	$getresponse_custom_field = $recordCrediantial['getresponse_custom_field'];
	
	if($INFUSIONSOFT_APP_NAME != '' && $INFUSIONSOFT_API_KEY != '')
	{
		$InfusionsoftAPIUtil = new InfusionsoftAPIUtil($INFUSIONSOFT_APP_NAME, $INFUSIONSOFT_API_KEY);
		
		$infusionsoft_lists = $InfusionsoftAPIUtil->getInfusionsoftTags();
		$custom_field_list = $InfusionsoftAPIUtil->getInfusionsoftDataFieldHTML();
		
	}
}


/** Funnel and GetResponse Mapping and to get GetResponse List if affiliate choose GetResponse **/
if($recordCrediantial['email_service_provider'] == "GetResponse")
{
	$mapping_type = "GetResponse";
	$getresponse_lists = $getresponse_response = '';
	
	$getresponse_api_key = $recordCrediantial['getresponse_api_key'];
	$getresponse_custom_field = $recordCrediantial['getresponse_custom_field'];

	if($getresponse_api_key != '')
	{
		$getresponse 		= new GetResponse($getresponse_api_key);
		
		$getresponse_list 	= $getresponse->getCampaigns();
		$custom_field_list 	= $getresponse->getCustomFields();

		if(isset($getresponse_list->codeDescription))
		$getresponse_response = $getresponse_list->codeDescription;
		
	}
}


//--> Get Funnel mapping from master website
$user_email_id = apm_get_session_value('apm_ses_user_login');
$postdata = array(
	'mode' => 'get_funnel_option_mapping_info',
	'type' => $mapping_type,
	'user_email_id' => $user_email_id,
	'version' => $GLOBALS['APM_SUBSCRIPTION_VER'],
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

$url = APM_MASTER_PLUGIN_URL."wp-content/plugins/mi-email-subscribers/outside_requests.php";

$response = wp_remote_post( $url, $args );

$resultFunnelOptionMapping = $response['body'];

$resultFunnelOptionMapping = json_decode($resultFunnelOptionMapping);
$MappingFunnelIndexArr = array();

if(count($resultFunnelOptionMapping) > 0)
{
	foreach($resultFunnelOptionMapping as $Mapping)
	{
		$MappingFunnelIndexArr[$Mapping->funnel_id] = $Mapping->esp_list_id;
	}
}


if($egmail_error_found == TRUE && isset($egmail_errors[0]) == TRUE)
{
?>
	<div class="error fade"><p><strong><?php echo $egmail_errors[0]; ?></strong></p></div>
<?php
}

if ($egmail_error_found == FALSE && strlen($egmail_success) > 0)
{
?>
	  <div class="updated fade">
		<p><strong><?php echo $egmail_success; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=apm-child"><?php _e('Click here', 'apm-child'); ?></a><?php _e(' to go to edit page', 'apm-child'); ?></strong></p>
	  </div>
<?php
}
	

?>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(APM_EGMAILl_TITLE . ' - Autoresponder Settings', 'apm-child'); ?></h2>
    
    <div style="width:100%; float:left">
    <form name="frm_apm_list" method="post" action="#">
    <input type="hidden" name="hdnPageAction" value="Edit" />
    <input type="hidden" name="option_id" value="<?php echo esc_html($option_id);?>" />
<?php
		
		//-->  As per selected Email service provider either Aweber or Sendeagle will be displayed
		if($recordCrediantial['email_service_provider'] == "" || $recordCrediantial['email_service_provider'] == "Aweber")
		{
?>          
          
            <label for="tag-link"><a target="_blank" href="https://auth.aweber.com/1.0/oauth/authorize_app/751e18ef">Click here to get the Authcode and paste it into the text area below</a></label>
            
            <label for="tag-link">AWeber Auth Code</label>
            <textarea class="txt-apmboxarea" name="aweber_auth_code" id="aweber_auth_code" style="max-width:600px;"><?php echo esc_html($recordCrediantial['aweber_auth_code']);?></textarea>
            
            <div style="width:100%; margin:0; padding:0; clear:both"></div>
<?php 

			if( is_array($resultFunnelList ))
			{
				if(count($resultFunnelList) > 0 && $show_aweber_list == true)
				{
?>
                    <div style="width:100%; display:inline-block; margin-top:20px; margin-bottom:10px; max-width:785px">
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">Funnel Name</label></div>
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">AWeber List</label></div>
                    </div>
<?php			
				}
			}
		
		
			$resultFunnelList = json_decode($resultFunnelList);
			
			if( is_array($resultFunnelList ))
			{
				if(count($resultFunnelList) > 0 && $show_aweber_list == true)
				{
					foreach($resultFunnelList as $funnelList)
					{
?>          
                        <div style="width:100%; margin:0; padding:0; clear:both"></div>
                        <div style="width:100%; display:inline-block; max-width:785px; margin-bottom:5px;">
                            <div style="width:49%; display:inline-block">
                             <span><input type="hidden" name="funnel_id[]" value="<?php echo $funnelList->ID;?>" /> <?php echo $funnelList->funnel_name;?></span>
                            </div>
                            <div style="width:49%; display:inline-block">
<?php
							if($show_aweber_list == true)
							{
?>          
                            
                               <select name="esp_list_id[]">
                                    <option value="">_____________</option>
<?php 
								foreach ($account->lists as $list) 
								{
?>
                                    <option value="<?php echo $list->id?>"  <?php if(count($MappingFunnelIndexArr) > 0){ if(@$MappingFunnelIndexArr[$funnelList->ID] ==  $list->id){?> selected="selected" <?php } }?>><?php echo esc_html($list->name)?></option>
<?php 
								}
?>
                            
                           </select>
<?php
							}
?>
                        </div>
                 </div>
<?php			
					}	
				}
			}
		}
		elseif($recordCrediantial['email_service_provider'] == "Sendeagle")
		{
?>  
          
            <label for="tag-link">SendEagle API Key</label>
            <textarea class="txt-apmboxarea" name="sendeagle_api_key" id="sendeagle_api_key" style="max-width:600px;"><?php echo esc_html($recordCrediantial['sendeagle_api_key']);?></textarea>
            
            <div style="width:100%; margin:0; padding:0; clear:both"></div>
<?php 

			if( is_array( $resultFunnelList )  && is_array( $sendeagle_lists ) )
			{
				if(count($resultFunnelList) > 0 && count($sendeagle_lists) > 0)
				{
?>
                    <div style="width:100%; display:inline-block; margin-top:20px; margin-bottom:10px; max-width:785px">
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">Funnel Name</label></div>
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">SendEagle List</label></div>
                    </div>
<?php			
				}
			}
		
		
			$resultFunnelList = json_decode($resultFunnelList);
	
			if( is_array( $resultFunnelList ) && is_array( $sendeagle_lists ) )
			{
				if(count($resultFunnelList) > 0 && count($sendeagle_lists) > 0)
				{
					foreach($resultFunnelList as $funnelList)
					{
?>          
						<div style="width:100%; margin:0; padding:0; clear:both"></div>
						<div style="width:100%; display:inline-block; max-width:785px; margin-bottom:5px;">
							<div style="width:49%; display:inline-block">
							 <span><input type="hidden" name="funnel_id[]" value="<?php echo $funnelList->ID;?>" /> <?php echo $funnelList->funnel_name;?></span>
							</div>
							<div style="width:49%; display:inline-block">
<?php
							if(count($sendeagle_lists) > 0)
							{
?>          
							   <select name="esp_list_id[]">
									<option value="">_____________</option>
<?php 
									foreach($sendeagle_lists as $sendeagle_list) 
									{
?>
									<option value="<?php echo $sendeagle_list['list_unique_id']?>" <?php if(@$MappingFunnelIndexArr[$funnelList->ID] ==  $sendeagle_list['list_unique_id']){?> selected="selected" <?php }?>><?php echo esc_html($sendeagle_list['list_name'])?></option>
<?php 
									}
?>
								</select>
<?php
							}
?>
							</div>
					 </div>
<?php			
					}	
				}
			}
		}
		elseif($recordCrediantial['email_service_provider'] == "Infusionsoft")
		{
?>  
          
            <label for="tag-link">Infusionsoft APP Name</label>
          	<input type="text" class="txt-apmbox"  name="jvz_infusionsoft_app_name" id="jvz_infusionsoft_app_name" required="true" value="<?php echo esc_html($recordCrediantial['jvz_infusionsoft_app_name']);?>" style="max-width:350px; width:100%;" />
            <br />
            <label for="tag-link">Infusionsoft API Key</label>
            <input type="text" class="txt-apmbox"  name="jvz_infusionsoft_api_key" id="jvz_infusionsoft_api_key" required="true" value="<?php echo esc_html($recordCrediantial['jvz_infusionsoft_api_key']);?>" style="max-width:350px; width:100%;" />
            
            <div style="width:100%; margin:0; padding:0; clear:both"></div>
<?php 
			if(is_array($infusionsoft_lists) && count($infusionsoft_lists) > 0)
			{
			
				if(count($resultFunnelList) > 0)
				{
					if(isset($custom_field_list) > 0)
					{
	?>
					<div style="width:100%; display:block; max-width:785px; margin-bottom:15px;">
						<label for="getresponse_custom_field">Choose Custom field for clickid mapping.</label>
						<select id="getresponse_custom_field" name="getresponse_custom_field" required>
							<option value="">___</option>
	<?php
								foreach($custom_field_list as $custom_list)
								{
	?>
									<option value="<?php echo $custom_list->Name;?>" <?php if($getresponse_custom_field == $custom_list->Name){?> selected="selected" <?php } ?>><?php echo $custom_list->Label;?></option>
	<?php
								}
	?>
						</select>
					</div>
	<?php
					}
	?>                
					<div style="width:100%; display:inline-block; margin-top:20px; margin-bottom:10px; max-width:785px">
						<div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">Funnel Name</label></div>
						<div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">Infusionsoft Tags</label></div>
					</div>
	<?php			
				}
		
			}
			
			$resultFunnelList = json_decode($resultFunnelList);
			if(is_array($infusionsoft_lists) && count($infusionsoft_lists) > 0)
			{
				if(count($resultFunnelList))
				{
					foreach($resultFunnelList as $funnelList)
					{
	?>          
						<div style="width:100%; margin:0; padding:0; clear:both"></div>
						<div style="width:100%; display:inline-block; max-width:785px; margin-bottom:15px;">
							<div style="width:49%; display:inline-block">
							 <span><input type="hidden" name="funnel_id[]" value="<?php echo $funnelList->ID;?>" /> <?php echo $funnelList->funnel_name;?></span>
							</div>
							<div style="width:49%; display:inline-block">
	<?php
							if(is_array($infusionsoft_lists))
							{
	?>          
							   <select class='infusionsoft_lists' name="esp_list_id[]">
									<option value="">_____________</option>
	<?php 
									foreach($infusionsoft_lists as $infusionsoft_list) 
									{
	?>
									<option value="<?php echo $infusionsoft_list->tagId; ?>" <?php if(@$MappingFunnelIndexArr[$funnelList->ID] ==  $infusionsoft_list->tagId){?> selected="selected" <?php }?>><?php echo esc_html($infusionsoft_list->tagName)?></option>
							
	<?php 
									}
	?>
								</select><br />
	<?php
							}
	?>
							 <div id='result'></div> 
							</div>
					 </div>
	<?php			
					}	
				}
			}
		}
		elseif($recordCrediantial['email_service_provider'] == "GetResponse")
		{
?>  
          
            <label for="tag-link">GetResponse API Key</label>
          	<input type="text" class="txt-apmbox"  name="getresponse_api_key" id="getresponse_api_key" required="true" value="<?php echo esc_html($recordCrediantial['getresponse_api_key']);?>" style="max-width:350px; width:100%;" />
            <br />
            
            <div style="width:100%; margin:0; padding:0; clear:both"></div>
<?php 

			if(isset($getresponse_list) && $getresponse_response == '')
			{
				if(count($resultFunnelList) > 0)
				{
					if(isset($custom_field_list) > 0)
					{
?>
					 <div style="width:100%; display:block; max-width:785px; margin-bottom:15px;">
                        <label for="getresponse_custom_field">Choose Custom field for clickid mapping.</label>
                        <select id="getresponse_custom_field" name="getresponse_custom_field" required>
                        	<option value="">___</option>
<?php
                            	foreach($custom_field_list as $custom_list)
								{
?>
                                    <option value="<?php echo $custom_list->customFieldId;?>" <?php if($getresponse_custom_field == $custom_list->customFieldId){?> selected="selected" <?php } ?>><?php echo $custom_list->name;?></option>
<?php
								}
?>
                        </select>
                     </div>
<?					
					}
?>
                    <div style="width:100%; display:block; margin-top:20px; margin-bottom:10px; max-width:785px">
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">Funnel Name</label></div>
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">GetResponse List</label></div>
                    </div>
<?php			
				}
			}
		
			$resultFunnelList = json_decode($resultFunnelList);
	
			if(isset($getresponse_list) && $getresponse_response == '')
			{
				if(count($resultFunnelList) > 0)
				{
					foreach($resultFunnelList as $funnelList)
					{
?>          
                        <div style="width:100%; margin:0; padding:0; clear:both"></div>
                        <div style="width:100%; display:inline-block; max-width:785px; margin-bottom:15px;">
                            <div style="width:49%; display:inline-block">
                             <span><input type="hidden" name="funnel_id[]" value="<?php echo $funnelList->ID;?>" /> <?php echo $funnelList->funnel_name;?></span>
                            </div>
                            <div style="width:49%; display:inline-block">
                               <select name="esp_list_id[]">
                                    <option value="">_____________</option>
<?php 
									foreach($getresponse_list as $getresponse) 
									{
?>
                                    <option value="<?php echo $getresponse->campaignId; ?>" <?php if(@$MappingFunnelIndexArr[$funnelList->ID] ==  $getresponse->campaignId){?> selected="selected" <?php }?>><?php echo esc_html($getresponse->name)?></option>
                            
<?php 
									}
?>
                                </select>
                                <br />
                            </div>
                     </div>
<?php			
						}	
					}
				}
			}
?>

        <p class="submit"><input name="publish" lang="publish" class="button button-primary add-new-h2" value="Update" type="submit" onclick="return submit_aweber_list()" /></p>
        <?php wp_nonce_field('frm_apm_list'); ?>
            
        </form>
    </div>
</div>
</div>
