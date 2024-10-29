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
	
	if ( ! wp_verify_nonce( $nonce, 'frm_apm_list' ) ) {
		//--> This nonce is not valid.
		die( 'Security check' ); 
	}
	
	$form['funnel_id'] = isset($_POST['funnel_id']) ? $_POST['funnel_id'] : '';
	$form['apm_funnel_term_id'] = isset($_POST['apm_funnel_term_id']) ? $_POST['apm_funnel_term_id'] : '';
	$form['apm_funnel_slug'] = isset($_POST['apm_funnel_slug']) ? $_POST['apm_funnel_slug'] : '';
	$form['apm_funnel_prospect_tags'] = isset($_POST['apm_funnel_prospect_tags']) ? $_POST['apm_funnel_prospect_tags'] : '';
	
	if(is_array( $form['apm_funnel_term_id'] ) && count($form['apm_funnel_term_id']) > 0)
	{
		$arr_funnel_termid_mapping = array();
		$arr_funnel_slug_mapping = array();
		$arr_funnel_prospect_tags = array();
		
		
		//-->  Filter out empty values
		$apm_filtered_funnel_term_ids = array_filter($form['apm_funnel_term_id'], function($value) {
			return !empty($value);
		});
		
		//--> Count the occurrences of each value
		$apm_funnel_term_id_count = array_count_values($apm_filtered_funnel_term_ids);
		
		//--> Filter the values that occur more than once
		$apm_duplicates_term_ids = array_filter($apm_funnel_term_id_count, function($count) {
			return $count > 1;
		});
		
		
		if ( !empty($apm_duplicates_term_ids) ) 
		{
			$egmail_errors[] = "You can't choose same category for two different funnels.";
		} 
		else 
		{
			
		
			foreach($form['apm_funnel_term_id'] as $key => $term_id)
			{
				$arr_funnel_termid_mapping[$key] = $term_id;
			}
			
			foreach($form['apm_funnel_slug'] as $key => $funnel_slug)
			{
				$arr_funnel_slug_mapping[$key] = $funnel_slug;
			}
			
			foreach($form['apm_funnel_prospect_tags'] as $key => $funnel_prospect_tags)
			{
				$arr_funnel_prospect_tags[$key] = $funnel_prospect_tags;
			}
			
			
			$funnel_termid_mapping = json_encode($arr_funnel_termid_mapping);
			$funnel_slug_mapping = json_encode($arr_funnel_slug_mapping);
			$funnel_prospect_tags = json_encode($arr_funnel_prospect_tags);
			
			
			update_option('apm_funnel_termid_mapping_data', $funnel_termid_mapping);
			update_option('apm_funnel_slug_term_mapping_data', $funnel_slug_mapping);
			update_option('apm_funnel_prospect_tags', $funnel_prospect_tags);
			
			$egmail_success = "Record Successfully Updated!";
		}
		
	}
	
	
} 

$funnel_termid_mapping_data =  get_option('apm_funnel_termid_mapping_data');

$funnel_termid_mapping_data = json_decode($funnel_termid_mapping_data);
$funnel_termid_mapping_data = (array)$funnel_termid_mapping_data;


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
	<h2><?php _e(APM_EGMAILl_TITLE . ' - Optin Forms', 'apm-child'); ?></h2>
    
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
            
                    if (check_directory_exists_inline_optin($apm_page_slug)) 
                    {          
?>
                        <div style="width:100%; margin:0; padding:0; clear:both"></div>
                        <div style="width:100%; display:inline-block; max-width:785px; margin-bottom:5px;">
                            <div style="width:49%; display:inline-block">
                                <span><input type="hidden" name="funnel_id[]" value="<?php echo $funnelList->ID;?>" /> <?php echo $funnelList->funnel_name;?></span>
                                <input type="hidden" name="apm_funnel_slug[<?php echo $funnelList->ID;?>]" value="<?php echo $funnelList->funnel_slug;?>" />
                                <input type="hidden" name="apm_funnel_prospect_tags[<?php echo $funnelList->funnel_slug;?>]" value="<?php echo $funnelList->optin_prospect_tags;?>" />
                            </div>
                            <div style="width:49%; display:inline-block">
                    
<?php
                                $category_array = apm_get_category_list_for_optin_form();
								
                                //--> Create a filtered list of pages that haven't been used yet
                                $filtered_category_array = array_diff_key($category_array, array_flip($used_term_ids));
								
?>
                                <select name="apm_funnel_term_id[<?php echo $funnelList->ID;?>]">
                                    <option value="">Select a Category</option>
<?php
									foreach ( $filtered_category_array as $term_id => $category_name ) 
									{
?>
                                    <option value="<?php echo esc_attr( $term_id );?>" <?php if(isset($funnel_termid_mapping_data[$funnelList->ID]) && $funnel_termid_mapping_data[$funnelList->ID] == $term_id){ ?> selected="selected" <?php } ?>><?php echo esc_html( $category_name ) ?></option>
<?php
									}
?>
                                </select>
                            </div>
                        </div>
<?php	
                        //--> Add the current mapped page ID to the list of used IDs if it exists
                        if(isset($funnel_termid_mapping_data[$funnelList->ID])) {
                            $used_term_ids[] = $funnel_termid_mapping_data[$funnelList->ID];
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
