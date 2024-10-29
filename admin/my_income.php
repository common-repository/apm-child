<?php 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } 
if(!current_user_can('edit_others_pages'))
{
	die('You are not allowed to work.');
}
?>
<?php
	
	$where_clause = '';
	$my_income_from_date = '';
	$my_income_to_date = '';
	$my_income_search = '';
	
	
	if (isset($_GET['mode']) && $_GET['mode'] == 'retry' && isset($_GET['mode']) && $_GET['mode'] != '')
	{	    
		   
		$page_name = APM_PHYSICAL_PATH."/logs/debug_clickmagic.txt";   
		$fp = fopen($page_name, "a");
		$dataArr = print_r($_REQUEST, TRUE);
		fwrite($fp, $dataArr);
		
		$id = $_GET['sid'];
		
		if(isset($id) && $id == '')
		{
			echo "Unauthorized Access";die;
		}
		
		$egSql = "SELECT * FROM ".APM_MY_INCOME." WHERE ID = '".$id."'";
		$egRecord = $wpdb->get_row($egSql, ARRAY_A); 
		
		
		$egSqlCrediantial = "SELECT * FROM `".$wpdb->prefix.'cso_options'."` WHERE 1";
		$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
		
		//--> Setup CURL for ClickMagick
		$url = "http://www.clkmg.com/api/s/post/";
		
		$postdata = array(
				'uid' => __($recordCrediantial['clickmagick_clickid'], 'apm-child'),
				's1' => __($egRecord['inc_caffitid']),
				'amt' => $egRecord['inc_commision_amt'],
				'ref' => __($egRecord['inc_ctransreceipt']),
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
		
		$new_data2 = "\n\n".'Array Send to to CLICK MAGIC:'.print_r($postdata, TRUE);
		fwrite($fp, $new_data2);
	
		//--> Tracking CLICK MAGIC Response for debug.
		$new_data3 = "\n\n".'CLICK MAGIC Response = '.print_r($response, TRUE);
		fwrite($fp, $new_data3);
		
		//--> Update My Income
		if($response['body'] == 'OK')
		{
				$TodaysDate = date('Y-m-d H:i:s');
			   
				$egSql = $wpdb->prepare("UPDATE `".$wpdb->prefix.'cso_my_income'."` SET `inc_processed` = %s, `magic_postback_date` = %s WHERE ID = %d LIMIT 1", array('Yes', $TodaysDate, addslashes_gpc(sanitize_text_field($id))));
			   
				$wpdb->query($egSql);
			   
				$clickmagic_response = "ClickMagic has proccessed your entry successfully.";
		}
		else
		{
				$clickmagic_response = "ClickMagic has not proccessed your entry.";
		}
		$new_data4 = "\n\n".'======================================================================='."\n\n";
		fwrite($fp, $new_data4);
	}
	   
	
	if(isset($_REQUEST['set_income_notification']) && $_REQUEST['set_income_notification'] == 'Save')
	{
		
		include_once ("classes/ESP.Class.php");
		$_APMESP = new APMESP;

		$nonce = $_REQUEST['_wpnonce'];
		
		if ( ! wp_verify_nonce( $nonce, 'frm_notification_settings' ) ) {
			// This nonce is not valid.
			die( 'Security check' ); 
		}
		
		$jvzoo_notification 		= sanitize_text_field($_REQUEST['jvzoo_notification']);
		$clickbank_notification 	= sanitize_text_field($_REQUEST['clickbank_notification']);
		$thrivecart_notification 	= sanitize_text_field($_REQUEST['thrivecart_notification']);
		$warriorplus_notification 	= sanitize_text_field($_REQUEST['warriorplus_notification']);
		
		
		$return_val = $_APMESP->apm_set_income_notification(array("jvzoo_notification" => $jvzoo_notification, "clickbank_notification" => $clickbank_notification, "thrivecart_notification" => $thrivecart_notification, "warriorplus_notification" => $warriorplus_notification));
		
		if($return_val == 'success')
		{
			$egmail_success = 'Notification has been saved.';
		}
		
	}

	
	if(isset($_REQUEST['my_income_search']) && $_REQUEST['my_income_search'] != '')
	{
		/*if(isset($_REQUEST['_wpnonce']) && $_REQUEST['_wpnonce'])
		{
			$nonce = $_REQUEST['_wpnonce'];
		}
		if ( ! wp_verify_nonce( $nonce, 'frm_my_income_search' ) ) {
			// This nonce is not valid.
			die( 'Security check' ); 
		}*/
		
		$my_income_from_date = sanitize_text_field($_REQUEST['my_income_from_date']);
		$my_income_to_date = sanitize_text_field($_REQUEST['my_income_to_date']);
		
		if($my_income_from_date != '' && $my_income_to_date != '')
		{
			$where_clause = " AND DATE(income_date) >= '".$my_income_from_date."' AND DATE(income_date) <= '".$my_income_to_date."'";
		}
		
		$my_income_search = $_REQUEST['my_income_search'];
	}
	
	
	
	$egSql = "SELECT * FROM ".APM_MY_INCOME." WHERE 1$where_clause";
	
	$egSql = $egSql . " ORDER BY ID DESC";
	$egRecord = array();
	$total_record = count($wpdb->get_results($egSql, ARRAY_A));
	
	
	//--> Pagination Section
	$items_per_page = 50;
	$page 			= isset( $_GET['cpage'] ) ? abs( (int) sanitize_text_field($_GET['cpage']) ) : 1;
	$offset 		= ( $page * $items_per_page ) - $items_per_page;
	$limit_query    =   " LIMIT ".$items_per_page." OFFSET ".$offset;   
	
	$egRecord 		=   $wpdb->get_results($egSql.$limit_query,OBJECT); // return OBJECT
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

<?php

if (isset($egmail_success) != ''){
?>
  <div style="width:100%; float:left" class="updated fade"><p><strong><?php echo $egmail_success; ?> </strong></p></div>
<?php
}
?>

<div class="wrap">
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(APM_EGMAILl_TITLE . " - My Income", 'apm-child'); ?></h2>
    <div class="row-full-width">
        <div class="form-col-1">
            <form name="frm_my_income_search" id="frm_my_income_search" method="post">
            <?php wp_nonce_field('frm_my_income_search'); ?>
            <table class="frm_my_income_search">
                <tr><td colspan="3"><h3 style="margin:0">Filter your income</h3></td></tr>
                <tr>
                    <td><input type="date" name="my_income_from_date" id="my_income_from_date" value="<?php echo esc_html($my_income_from_date);?>" /></td>
                    <td><input type="date" name="my_income_to_date" id="my_income_to_date" value="<?php echo esc_html($my_income_to_date);?>" /></td>
                    <td>
                    	<input name="my_income_search" lang="search" class="button jvzoo_ipn_clickboard_btn button-primary add-new-h2" value="Search" type="submit" />
                    </td>
                </tr>
                <tr>
                <td colspan="3"><br /><a class="button button-primary add-new-h2" href="<?php echo APM_PLUGIN_PATH?>income_export.php?mode=export&fdate=<?php echo $my_income_from_date;?>&tdate=<?php echo $my_income_to_date;?>">Export Income</a></td>
                </tr>
            </table>
            </form>
        </div>
        <div class="form-col-2">
        	<form name="frm_notification_settings" id="frm_notification_settings" method="post">
                
				<?php
					$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";
					$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
					$jvzoo_notification = $recordCrediantial['jvzoo_notification'];
					$clickbank_notification = $recordCrediantial['clickbank_notification'];
					$thrivecart_notification = $recordCrediantial['thrivecart_notification'];
					$warriorplus_notification = $recordCrediantial['warriorplus_notification'];
				?>
            	 <table class="frm_notification_settings">
                 	<tr><td colspan="5"><h3 style="margin:0">Do you want to receive notification of the income?</h3></td></tr>
                    <tr>
                    	<td>
                        	<label class="notification-lable" for="jvzoo_notification">JVZoo Notification</label>
                            <label class="apm_radio_container">Yes<input type="radio" <?php if ($jvzoo_notification == 'Yes'){?> checked="checked" <?php } ?> name="jvzoo_notification" value="Yes"><span class="checkmark"></span></label>
                            <label class="apm_radio_container">No<input type="radio" <?php if ($jvzoo_notification == 'No'){?> checked="checked" <?php } ?> name="jvzoo_notification" value="No"><span class="checkmark"></span></label>

                        </td>
                        
                        <td>
                        	<label class="notification-lable" for="jvzoo_notification">ThriveCart Notification</label>
                            <label class="apm_radio_container">Yes<input type="radio" <?php if ($thrivecart_notification == 'Yes'){?> checked="checked" <?php } ?> name="thrivecart_notification" value="Yes"><span class="checkmark"></span></label>
                            <label class="apm_radio_container">No<input type="radio" <?php if ($thrivecart_notification == 'No'){?> checked="checked" <?php } ?> name="thrivecart_notification" value="No"><span class="checkmark"></span></label>

                        </td>
                        
						<td>
                        	<label class="notification-lable" for="clickbank_notification">ClickBank Notification</label>
                            <label class="apm_radio_container">Yes<input type="radio" <?php if ($clickbank_notification == 'Yes'){?> checked="checked" <?php } ?> name="clickbank_notification" value="Yes"><span class="checkmark"></span></label>
                            <label class="apm_radio_container">No<input type="radio" <?php if ($clickbank_notification == 'No'){?> checked="checked" <?php } ?> name="clickbank_notification" value="No"><span class="checkmark"></span></label>

                        </td>
                        <td>
                        	<label class="notification-lable" for="warriorplus_notification">WarriorPlus Notification</label>
                            <label class="apm_radio_container">Yes<input type="radio" <?php if ($warriorplus_notification == 'Yes'){?> checked="checked" <?php } ?> name="warriorplus_notification" value="Yes"><span class="checkmark"></span></label>
                            <label class="apm_radio_container">No<input type="radio" <?php if ($warriorplus_notification == 'No'){?> checked="checked" <?php } ?> name="warriorplus_notification" value="No"><span class="checkmark"></span></label>

                        </td>   
                    	<td>
                        <input name="set_income_notification" lang="Save" class="button jvzoo_ipn_clickboard_btn button-primary add-new-h2" value="Save" style="margin-top: 26px !important;" type="submit" /></td>

                     </tr>
                 </table>
            <?php wp_nonce_field('frm_notification_settings'); ?>
            </form>
        </div>
        <form name="frm_income_list" method="post">
          <table width="100%" class="widefat" id="straymanage">
            <thead>
              <tr>
                <th class="check-column" scope="col" style="padding:0; vertical-align:middle"></th>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Item</th>
                <th scope="col">Clickid</th>
                <th scope="col">Type</th>
                <th scope="col">Transaction</th>
                <th scope="col">Payment Method</th>
                <th scope="col">Amount</th>
                <th scope="col">Commission Amount</th>
                <th scope="col">Processed</th>
                <th scope="col">Sale Captured Date</th>
                <th scope="col">ClickMagick Proceed Date</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th class="check-column" scope="col"></th>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Item</th>
                <th scope="col">Clickid</th>
                <th scope="col">Type</th>
                <th scope="col">Transaction</th>
                <th scope="col">Payment Method</th>
                <th scope="col">Amount</th>
                <th scope="col">Commission  Amount</th>
                <th scope="col">Processed</th>
                <th scope="col">Sale Captured Date</th>
                <th scope="col">ClickMagick Proceed Date</th>
              </tr>
            </tfoot>
            <tbody>
              <?php
                $i = 0;
                $displayisthere = FALSE;
                $url = "http://affiliatepromembership.com/wp-content/plugins/mi-email-subscribers/outside_requests.php";
				
                if(count($egRecord) > 0)
                {
                    $i = 1;
					$total_amount = 0;
					$total_ccommission_amount = 0;
                    foreach ($egRecord as $record)
                    {
						$bgColor = $redColor = $append = '';
						
						$commision_amount = $record->inc_commision_amt;
						
						$total_amount += $record->inc_ctransamount;
						
						$arr_inc_ctransaction = array("RFND", "CGBK", "INSF", "TEST_RFND");
						
						if (in_array($record->inc_ctransaction, $arr_inc_ctransaction))
						{
							$bgColor = ' style="boarder:1px solid #F00"';
							$redColor = ' style="color: #F00"';
							$append = '-';
							
							$total_ccommission_amount += $commision_amount;
						}
						else
						{
							$total_ccommission_amount += $commision_amount;
						}
						
						
						if(is_numeric($record->inc_caffitid))
						{
							$inc_caffitid = $record->inc_caffitid;
						}
						else
						{
							$inc_caffitid = "";
						}
                ?>
                  <tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
                        <td align="left"></td>
                        <td><?php echo $i; ?></td>
                        <td><?php echo esc_html($record->inc_ccustname); ?></td>
                        <td><?php echo esc_html($record->inc_ccustemail); ?></td>
                        <td><?php echo esc_html($record->inc_cproditem); ?></td>        
                        <td><?php echo esc_html($inc_caffitid); ?></td>        
                        <td><?php echo esc_html($record->inc_cprodtype); ?></td>
                        <td><?php echo esc_html($record->inc_ctransaction); ?></td>
                        <td><?php echo esc_html($record->inc_ctranspaymentmethod); ?></td>
                        <td><?php echo $append; ?>$<?php echo str_replace("-", "", number_format(esc_html($record->inc_ctransamount), 2, '.', '')); ?> </td>
                        <td<?php echo $redColor;?><?php echo $bgColor;?>>
							<?php if(esc_html($record->cron_status) == 'No'){ echo "Pending"; } else { ?> <?php echo $append; ?>$<?php echo str_replace("-", "", number_format($commision_amount, 2, '.', '')); }?>
						</td>
                        <td>
							<?php if(esc_html($record->cron_status) == 'No'){ echo "Pending"; } else { ?>
							<?php if(esc_html($record->inc_processed) == 'Yes'){ echo "Yes"; } else { ?> 
                            	<a onClick="javascript:RetryClickMagicSubmission('<?php echo esc_html($record->ID); ?>')" href="javascript:void(0);">Resend To ClickMagic</a>
							<? }} ?></td>
                        <td width="80"><?php echo date("m-d-Y  H:i:s", strtotime(esc_html($record->income_date))); ?></td>
                        <td width="80"><?php if($record->magic_postback_date != "0000-00-00 00:00:00" && $record->magic_postback_date != ""){ echo date("m-d-Y H:i:s", strtotime(esc_html($record->magic_postback_date))); }?></td>
                  </tr>
              <?php
                        $i = $i+1;
                    } 
?>
				<tr>
                    <td style="border-top: 1px solid #e1e1e1;" colspan="8" align="right"><strong>Totals</strong></td>
                    <td style="border-top: 1px solid #e1e1e1;" align="left"><strong>$<?php echo number_format($total_amount, 2, '.', '');?></strong></td>
                    <td style="border-top: 1px solid #e1e1e1;" align="left"><strong>$<?php echo number_format($total_ccommission_amount, 2, '.', '');?></strong></td>
                    <td colspan="3" style="border-top: 1px solid #e1e1e1;" align="left"></td>
                </tr>		
<?php					
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="12" align="center"><?php _e('No records available.', 'apm-child'); ?></td>
                    </tr>
                    <?php 
                }
                ?>
            </tbody>
          </table>
          
          <?php wp_nonce_field('eemail_form_show'); ?>
          <input type="hidden" name="frm_income_list" value="yes" />
          <input type="hidden" name="frm_income_bulkaction" value=""/>
          <input type="hidden" name="mi_plugin_url" id="mi_plugin_url" value="<?php echo APM_SUBSCRIPTION_PATH;?>"/>
          
        <div style="padding-top:10px;"></div>
        <div class="tablenav">
            <div class="alignleft">
                
            </div>
            <div class="alignright">
                <?php 
                    if($egRecord): 
                ?>
                   <div class="navigation eg-navigation">
                    <?php 
                        echo $total_record. " items &nbsp;&nbsp;&nbsp;";
                        echo paginate_links( array(
                            'base' => add_query_arg( 'cpage', '%#%' ),
                            'format' => '',
                            'prev_text' => __('&laquo;'),
                            'next_text' => __('&raquo;'),
                            'total' => ceil($total_record / $items_per_page),
                            'current' => $page,
							'add_args' => array( 'my_income_from_date' => sanitize_text_field($my_income_from_date), 'my_income_to_date' => sanitize_text_field($my_income_to_date), 'my_income_search' => sanitize_text_field($my_income_search)),

                        ));
                    ?>
                   </div>
                <?php endif; ?>
            </div>
        </div>
        </form>
    </div>
</div>
</div>

<div class="hover_bkgr_fricc">
    <span class="helper"></span>
    <div>
        <div class="popupCloseButton">X</div>
        <p id="clickMagicResponse">This income cannot be traced back to Clickmagick perhaps customer originated from an organic traffic source</p>
    </div>
</div>
 <script>
	jQuery(window).load(function () {
		jQuery('.hover_bkgr_fricc').click(function(){
			jQuery('.hover_bkgr_fricc').hide();
		});
		jQuery('.popupCloseButton').click(function(){
			jQuery('.hover_bkgr_fricc').hide();
		});
		
		/*jQuery('.Untraceable_clickmagic').mouseover(function(){
			jQuery('.hover_bkgr_fricc').show();
		});*/
	});
</script>