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


//--> Edit Funnel and AWeber/SendEagle Mapping
if (isset($_POST['hdnPageAction']) && $_POST['hdnPageAction'] == 'Edit')
{
	$esp_list_id = '';
	$nonce = $_REQUEST['_wpnonce'];
	
	if ( ! wp_verify_nonce( $nonce, 'frm_apm_list' ) ) 
	{
		//--> This nonce is not valid.
		die( 'Security check' ); 
	}
	
	$form['funnel_id'] = isset($_POST['funnel_id']) ? $_POST['funnel_id'] : '';
	$form['apm_funnel_template_status'] = isset($_POST['apm_funnel_template_status']) ? $_POST['apm_funnel_template_status'] : '';
	
	if(is_array( $form['apm_funnel_template_status'] ) && count($form['apm_funnel_template_status']) > 0)
	{
		$arr_funnel_status_mapping = array();
		
		foreach($form['apm_funnel_template_status'] as $key => $status_value)
		{
			$arr_funnel_status_mapping[$key] = $status_value;
		}
		
		//print_r($arr_funnel_status_mapping);die;
		
		$funnel_status_mapping = json_encode($arr_funnel_status_mapping);
		
		update_option('apm_funnel_status_mapping_data', $funnel_status_mapping);
		
		$egmail_success = "Record Successfully Updated!";
		
	}
	
	
} 

$funnel_status_mapping_data =  get_option('apm_funnel_status_mapping_data');

$funnel_status_mapping_data = json_decode($funnel_status_mapping_data);
$funnel_status_mapping_data = (array)$funnel_status_mapping_data;

//print_r($funnel_status_mapping_data);


//--> Get Funnel list from master website for funnel and Email Service provider 
$resultFunnelList = $_APMESP->apm_get_funnel_list();


//--> Get Funnel mapping from master website
$user_email_id = apm_get_session_value('apm_ses_user_login');


if(isset($egmail_errors[0]) == TRUE)
{
?>
	<div class="error fade"><p><strong><?php echo $egmail_errors[0]; ?></strong></p></div>
<?php
}


if ($egmail_error_found == FALSE && strlen($egmail_success) > 0)
{
?>
	  <div class="updated fade">
		<p><strong><?php echo $egmail_success; ?></strong></p>
	  </div>
<?php
}
?>



<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(APM_EGMAILl_TITLE . ' - Active/Inactive Optin Forms', 'apm-child'); ?></h2>
    <h3>Disable the optin form</h3>
    <div style="width:100%; float:left">
    <form name="frm_apm_list" method="post" action="#">
    <input type="hidden" name="hdnPageAction" value="Edit" />

            <div style="width:100%; margin:0; padding:0; clear:both"></div>
<?php 
            
            if( is_array($resultFunnelList ))
            {
                if(count($resultFunnelList) > 0)
                {
?>
                    <div style="width:100%; display:inline-block; margin-top:20px; margin-bottom:10px; max-width:785px">
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">Funnel Name</label></div>
                        <div style="width:49%; display:inline-block;"><label style="font-weight:700; font-size:15px;">Page List</label></div>
                    </div>
<?php			
                }
            }
            
            $resultFunnelList = json_decode($resultFunnelList);
            
            if( is_array($resultFunnelList ) && count($resultFunnelList) > 0)
            {
                $used_term_ids = array(); //--> This array will keep track of used page IDs
            
                foreach($resultFunnelList as $funnelList)
                {
                    $apm_page_slug =  $funnelList->funnel_slug;
            
                    if (check_directory_exists($apm_page_slug)) 
                    {          
?>
                        <div style="width:100%; margin:0; padding:0; clear:both"></div>
                        <div class="apm-checkbox-style" style="width:100%; display:inline-block; max-width:785px; margin-bottom:5px;">
                        
                            <div style="width:49%; display:inline-block">
                                <span><input type="hidden" name="funnel_id[]" value="<?php echo $funnelList->ID;?>" /> <?php echo $funnelList->funnel_name;?></span>
                                <input type="hidden" name="apm_funnel_slug[<?php echo $funnelList->ID;?>]" value="<?php echo $funnelList->funnel_slug;?>" />
                            </div>
                            
                            <div style="width:49%; display:inline-block">
                                <label class="apm_radio_container">Yes<input type="radio" <?php if(isset($funnel_status_mapping_data[$funnelList->ID]) && $funnel_status_mapping_data[$funnelList->ID] == "Yes"){ ?> checked="checked" <?php } ?> name="apm_funnel_template_status[<?php echo $funnelList->ID;?>]" value="Yes"><span class="checkmark"></span></label>
                                <label class="apm_radio_container">No<input type="radio" <?php if(isset($funnel_status_mapping_data[$funnelList->ID]) && $funnel_status_mapping_data[$funnelList->ID] == "No"){ ?> checked="checked" <?php } ?> name="apm_funnel_template_status[<?php echo $funnelList->ID;?>]" value="No"><span class="checkmark"></span></label>
                            </div>
                            
                        </div>
<?php	
                        
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
