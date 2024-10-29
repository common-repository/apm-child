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

if(isset($_POST['hdn_page_social']) && $_POST['hdn_page_social'] == 'Submit')
{
	$nonce = $_REQUEST['_wpnonce'];
	
	if ( ! wp_verify_nonce( $nonce, 'frm_share_success' ) ) {
		// This nonce is not valid.
		die( 'Security check' ); 
	}
	
	$form['apm_cron_daily'] 		= isset($_POST['apm_cron_daily']) ? sanitize_text_field($_POST['apm_cron_daily']) : '';
	$form['apm_cron_weekly'] 		= isset($_POST['apm_cron_weekly']) ? sanitize_text_field($_POST['apm_cron_weekly']) : '';
	$form['apm_cron_weekly_time'] 	= isset($_POST['apm_cron_weekly_time']) ? sanitize_text_field($_POST['apm_cron_weekly_time']) : '';
	$form['share_success_category']	= isset($_POST['share_success_category']) ? sanitize_text_field($_POST['share_success_category']) : '';
	$form['apm_post_schedule'] 		= isset($_POST['apm_post_schedule']) ? sanitize_text_field($_POST['apm_post_schedule']) : '';
	$form['share_success_headline'] = isset($_POST['share_success_headline']) ? sanitize_text_field($_POST['share_success_headline']) : '';
	$form['social_share_message'] 	= isset($_POST['social_share_message']) ? wp_kses_post(stripslashes($_POST['social_share_message'])) : '';
	$form['set_success_story_amount'] 	= isset($_POST['set_success_story_amount']) ? wp_kses_post(stripslashes($_POST['set_success_story_amount'])) : '';
	$form['share_success_image'] 		= isset($_POST['share_success_image']) ? sanitize_text_field($_POST['share_success_image']) : '';
	
	update_option('apm_apm_cron_daily', $form['apm_cron_daily']);
	update_option('apm_apm_cron_weekly', $form['apm_cron_weekly']);
	update_option('apm_cron_weekly_time', $form['apm_cron_weekly_time']);
	update_option('apm_share_success_category', $form['share_success_category']);
	update_option('apm_apm_post_schedule', $form['apm_post_schedule']);
	update_option('apm_share_success_headline', $form['share_success_headline']);
	update_option('apm_social_share_message', $form['social_share_message']);
	update_option('apm_set_success_story_amount', $form['set_success_story_amount']);
	update_option('apm_share_success_image', $form['share_success_image']);
}

$apm_cron_daily  = get_option('apm_apm_cron_daily');
$apm_cron_weekly = get_option('apm_apm_cron_weekly');
$apm_cron_weekly_time = get_option('apm_cron_weekly_time');
$apm_post_schedule = get_option('apm_apm_post_schedule');
$share_success_category = get_option('apm_share_success_category');
$share_success_headline = get_option('apm_share_success_headline');
$social_share_message = get_option('apm_social_share_message');
$set_success_story_amount = get_option('apm_set_success_story_amount');
$share_success_image = get_option('apm_share_success_image');

if($egmail_error_found == TRUE && isset($egmail_errors[0]) == TRUE)
{
?>
    <div class="error fade"><p><strong><?php echo $egmail_errors[0]; ?></strong></p></div>
<?php
}
if ($egmail_error_found == FALSE && strlen($eemail_success) > 0)
{
?>
    <div class="updated fade"><p><strong><?php echo $eemail_success; ?></strong></p></div>
<?php
}


?>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(APM_EGMAILl_TITLE . ' - Share Your Success', 'apm-child'); ?></h2>
    
    <div style="width:100%; float:left">
    
    <form name="frm_share_success" class="frm_share_success" method="post">
	<input type="hidden" name="hdn_page_social" value="Submit" />
    <h3><?php _e('Share Your Success Settings', 'apm-child'); ?> </h3>
        <div class="form-row">
            
            <div class="row-full-width">
                <label for="apm_post_schedule">Post Schedule</label>
                <label class="apm_radio_container">Instant<input type="radio" <?php if ($apm_post_schedule == 'Instant'){?> checked="checked" <?php } ?> name="apm_post_schedule" value="Instant" onclick="apm_post_schedule_cron('Instant');" /><span class="checkmark"></span></label>
                <label class="apm_radio_container">Daily<input type="radio" <?php if ($apm_post_schedule == 'Daily'){?> checked="checked" <?php } ?> name="apm_post_schedule" value="Daily" onclick="apm_post_schedule_cron('daily');" /><span class="checkmark"></span></label>
                <label class="apm_radio_container">Weekly<input type="radio" <?php if ($apm_post_schedule == 'Weekly'){?> checked="checked" <?php } ?> name="apm_post_schedule" value="Weekly" onclick="apm_post_schedule_cron('weekly');" /><span class="checkmark"></span></label>
                <label class="apm_radio_container">Fix Price<input type="radio" <?php if ($apm_post_schedule == 'fixprice'){?> checked="checked" <?php } ?> name="apm_post_schedule" value="fixprice" onclick="apm_post_schedule_cron('fixprice');" /><span class="checkmark"></span></label>
                <label class="apm_radio_container">Turn Off<input type="radio" <?php if ($apm_post_schedule == 'TurnOff'){?> checked="checked" <?php } ?> name="apm_post_schedule" value="TurnOff" onclick="apm_post_schedule_cron('TurnOff');" /><span class="checkmark"></span></label>
            </div>
			
            <div class="row-full-width apm_schedule_cron" id="apm_cron_daily" style="display:none">
            	<label for="apm_cron_daily">Set Time</label>
                <!--<input style="width:100px;" class="txt-apmbox" id="apm-filter-time" type="text" name="apm_cron_daily" value="<?php echo $apm_cron_daily; ?>" />-->
                 <select class="txt-apmbox" style="width:100px; margin-top: 5px;" name="apm_cron_daily">
                    <?php
                    for($hours=0; $hours < 24; $hours++) // the interval for hours is '1'
					{
                        for($mins=0; $mins < 60; $mins += 30) //the interval for mins is '30'
						{
							$time_stamp_1 = str_pad($hours, 2, '0', STR_PAD_LEFT).':' .str_pad($mins, 2, '0', STR_PAD_LEFT);
					?>
                    		<option value="<?php echo $time_stamp_1; ?>" <?php if($time_stamp_1 == $apm_cron_daily) {?> selected="selected" <?php } ?>><?php echo $time_stamp_1; ?></option>
                    <?php
						}
					}
                    ?>
                 </select>
                 <p>Choose as per GMT Time Zone</p>
            </div>
            
            <div class="row-full-width apm_schedule_cron" id="apm_cron_weekly" style="display:none">
             	<label for="apm_cron_daily">Set Date/Time</label>
                <select class="txt-apmbox" style="width:100px; margin-top: 5px;" name="apm_cron_week">
                	<option value="Mon" <?php if($apm_cron_week == 'Mon'){?> selected="selected" <?php } ?>>Mon</option>
                    <option value="Tue" <?php if($apm_cron_week == 'Tue'){?> selected="selected" <?php } ?>>Tue</option>
                    <option value="Wed" <?php if($apm_cron_week == 'Wed'){?> selected="selected" <?php } ?>>Wed</option>
                    <option value="Thu" <?php if($apm_cron_week == 'Thu'){?> selected="selected" <?php } ?>>Thu</option>
                    <option value="Fri" <?php if($apm_cron_week == 'Fri'){?> selected="selected" <?php } ?>>Fri</option>
                    <option value="Sat" <?php if($apm_cron_week == 'Sat'){?> selected="selected" <?php } ?>>Sat</option>
                    <option value="Sun" <?php if($apm_cron_week == 'Sun'){?> selected="selected" <?php } ?>>Sun</option>
                </select>
             
               <!--<input style="width:100px;" class="txt-apmbox" id="apm-filter-date" type="text" name="apm_cron_weekly" value="<?php echo $apm_cron_weekly; ?>" />-->
                 
                 <select class="txt-apmbox" style="width:100px; margin-top: 5px;" name="apm_cron_weekly_time">
                    <?php
                    for($hours=0; $hours < 24; $hours++) // the interval for hours is '1'
					{
                        for($mins=0; $mins < 60; $mins += 30) //the interval for mins is '30'
						{
							$time_stamp_1 = str_pad($hours, 2, '0', STR_PAD_LEFT).':' .str_pad($mins, 2, '0', STR_PAD_LEFT);
					?>
                    		<option value="<?php echo $time_stamp_1; ?>" <?php if($time_stamp_1 == $apm_cron_weekly_time) {?> selected="selected" <?php } ?>><?php echo $time_stamp_1; ?></option>
                    <?php
						}
					}
                    ?>
                 </select>
                 <p>Choose as per GMT Time Zone</p>
            </div>
           
           
           <div class="row-full-width apm_schedule_cron" id="apm_cron_fixprice" style="display:none;"> 
           		<label for="share_success_headline">Set Target Amount</label>		
            	<strong style="font-size: 16px;">$</strong><input type="number" class="txt-apmbox" min="0" id="set_success_story_amount" name="set_success_story_amount" value="<?php echo $set_success_story_amount;?>" />
            	<span style="display: block; font-style: italic;">When the commission amount reaches this threshold, a new post will be triggered.</span>
           </div>
           
           <div class="row-full-width hide-on-turn-off"> 
           <label for="share_success_category">Choose Category</label>
            <select name="share_success_category" class="txt-apmbox"> 
                <option se value=""><?php echo esc_attr(__('Select Category')); ?></option> 
            
                <?php 
                    $categories = get_categories(); 
                    foreach ($categories as $category) {
						
						$selected = '';
						
						if($share_success_category == $category->term_id) {
							$selected = 'selected';	
						}
						
                        $option .= '<option value="'.$category->term_id.'" '.$selected.'>';
                        $option .= $category->cat_name;
                        $option .= '</option>';
                    }
					
                    echo $option;
                ?>
            </select>
           </div>
           
           <div class="row-full-width hide-on-turn-off"> 
           		<label for="share_success_headline">Post Headline</label>		
            	<input type="text" class="txt-apmbox" id="share_success_headline" name="share_success_headline" value="<?php echo $share_success_headline;?>" />
           </div>
           
           <div class="row-full-width hide-on-turn-off"> 
                <label for="share_success_headline">Post Feature Image URL</label>		
            	<input type="text" class="txt-apmbox" id="share_success_image" name="share_success_image" value="<?php echo $share_success_image;?>"  style="max-width:600px" />
           </div>
            
           <div class="row-full-width hide-on-turn-off">
                <label for="notification_message">Post Message <?php //echo date("H:i");?></label>
				<?php
                    wp_editor($social_share_message, $id = 'social_share_message', $prev_id = 'title', $media_buttons = true, $tab_index = 2)
                ?>
                <p>[Platform] [Vendor] [CustomerName] [CustomerEmail] [AffiliateName] [AffiliateID] [ItemName] [SalesAmount] [CommissionAmount] [Clickid]</p>
            </div>
            
            <div class="row-full-width">
            <p class="submit"><input name="publish" lang="publish" class="button button-primary add-new-h2" value="Save" type="submit" onclick="return submit_aweber_option()" /></p>
            </div>
            <br />

        </div>
        
    <?php wp_nonce_field('frm_share_success'); ?>
    </form>
    </div>
</div>
</div>			
<script>

	function apm_post_schedule_cron(val)
	{
		if(val == "TurnOff" || val == "turnoff")
		{
			jQuery(".apm_schedule_cron").hide();
			jQuery(".hide-on-turn-off").hide();
		}
		else
		{
			jQuery(".apm_schedule_cron").hide();
			jQuery("#apm_cron_"+val).show(200);
			jQuery(".hide-on-turn-off").show(200);
		}
		
	}

	apm_post_schedule_cron('<?php echo strtolower($apm_post_schedule);?>');
	
</script>