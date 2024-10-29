<?php
	/* Getting the Affiliate Information to fillout the Optiun form*/

	global $wpdb;

	$egSqlCrediantial = "SELECT * FROM `".APM_OPTIONS_TABLE."` WHERE 1";

	$record = array();

	$recordCrediantial = $wpdb->get_row($egSqlCrediantial, ARRAY_A);
	
	
	$apm_ses_epm_magic_click_id = '';
	if(apm_get_session_value('apm_ses_epm_magic_click_id') != '')
	{
		$apm_ses_epm_magic_click_id = apm_get_session_value('apm_ses_epm_magic_click_id');
	}
	
	$recaptcha_site_key = apm_get_google_recaptcha_info( $recordCrediantial['user_email_id'] )
	
?>
    <link rel="stylesheet" href="<?php echo APM_PLUGIN_PATH ?>optin-forms/one-letter-from-retirement/olfr.css" media="screen">
    <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
    <link id="u-page-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i">
      
  <section class="inline-container u-section-1 d-flex flex-column align-items-center align-items-xxl-center">
    <div class="inline-container-inner">
		
		<div class="inline-row row-optin-landing-1 pt-4 pb-4">
            <div class="inline-col-lg-5 optin-aling-center">
                <img class="u-image u-image-contain u-image-default u-image-1" src="<?php echo APM_PLUGIN_PATH ?>optin-forms/one-letter-from-retirement/images/nick-kate.png" alt="" data-image-width="906" data-image-height="auto">
            </div>
            <div class="inline-col-lg-7">
                <h1 class="u-custom-font u-align-center u-font-montserrat u-text u-text-black u-text-default u-title u-text-1"> You Could Be Just One <u>Simple Letter Away</u> From The End Of All Your Financial Worries...
                </h1>
            </div> 
        </div>
		
		<div class="inline-row pt-4">
            <p class="u-align-center u-custom-font u-font-montserrat u-text u-text-grey-90 u-text-4"> ... And If You <strong><u>Enter Your Mailing Address Below</u></strong> I Can Show You Exactly How To Write It!<br>
            </p>
        </div>
		
        
      <div class="inline-row pt-4">
            
                                     <script src="https://www.google.com/recaptcha/api.js?render=<?php echo __($recaptcha_site_key, 'apm-child')?>"></script>
        				 <script>
                            function onSubmitFormolfr2(event) {
                               
							   
							    event.preventDefault();
                        
                                
                                if(document.getElementById('frm_subscribers_olfr2').elements['subscriber_first_name'].value == "")
                                {
                                    alert("Please enter the name.");
                                    return false;
                                }
                                
                                
                                var emailaddr = document.getElementById('frm_subscribers_olfr2').elements['subscriber_email'].value;
                        
                                
                                if(emailaddr == "")
                                {
                                    alert("Please enter the email address.");
                                    return false;
                                }
                                
                                if(emailaddr != '')
                                {
                                    var atpos=emailaddr.indexOf("@");
                                    var dotpos=emailaddr.lastIndexOf(".");
                                    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=emailaddr.length)
                                    {
                                        alert("Please enter the proper email address.");
                                        return false;
                                    }
                                    
                                }
                                
                                grecaptcha.ready(function() {
                                    grecaptcha.execute('<?php echo __($recaptcha_site_key, 'apm-child')?>', {action: 'submit'}).then(function(token) {
                                        // Add the token value to the form
                                        document.getElementById('frm_subscribers_olfr2').elements['recaptcha_token'].value = token;
                                        // Submit the form
                                        document.getElementById('frm_subscribers_olfr2').submit();
                                    });
                                });
                                
                                /* Disable the Submit Button after clicking and enable the Loader*/
                                var submitButton = document.getElementById("btnApmSubmitolfr2");
                                submitButton.disabled = true;
                                
                                /* Enable the Loader*/
                                var hiddenDiv = document.querySelector(".progress_olfr2");
                                hiddenDiv.style.display = "block";
                                
                            }
                        </script>
						<style>
                        .frm_subscribers .progress {
                           width: 200.8px;
                           height: 16.8px;
                           border-radius: 16.8px;
                           background: repeating-linear-gradient(135deg,#050506 0 8.4px,rgba(5,5,6,0.75) 0 16.8px) left/0%   100% no-repeat,
                                 repeating-linear-gradient(135deg,rgba(5,5,6,0.2) 0 8.4px,rgba(5,5,6,0.1) 0 16.8px) left/100% 100%;
                           animation: progress-p43u5e 5s infinite;
                           margin:0 auto !important;
                           margin-top:20px !important;
                           display:none;
                        }
                        
                        
                        @keyframes progress-p43u5e {
                           100% {
                              background-size: 100% 100%;
                           }
                        }
                        
                        
                        </style>
         
                    <form name="frm_subscribers" class="frm_subscribers" id="frm_subscribers_olfr2" method="post" action="https://affiliatepromembership.com/mi_email_subscribers.php">
                        <input type="hidden" name="mode" value="mi_subscribe">
                        
                        <input type="hidden" name="thankyou_page_url" value="https://<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>--nickjames.thrivecart.com/one-letter-from-retirement/?ref=<?php echo esc_html($apm_ses_epm_magic_click_id,'apm-child')?>">
                        
                        <input type="hidden" name="form_name" value="serious-about-six-figures">
                        <input type="hidden" name="warriorplus_aff_id" value="<?php echo __($recordCrediantial['warriorplus_aff_id'],'apm-child')?>">
                        <input type="hidden" name="clickbank_nickname" value="<?php echo __($recordCrediantial['clickbank_affiliate_nickname'],'apm-child')?>">
                        <input type="hidden" name="thrivecart_username" value="<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>">
                        
                        <input type="hidden" name="click_magic_click_id" value="<?php echo __($apm_ses_epm_magic_click_id,'apm-child')?>">
                        <input type="hidden" name="account_id" value="<?php echo esc_html($recordCrediantial['aweber_account_number'],'apm-child')?>">
                        <input type="hidden" name="list_id" value="<?php echo esc_html($recordCrediantial['aweber_list_id'],'apm-child')?>">
                        <input type="hidden" name="customer_email" value="<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>">
                        <input type="hidden" name="duplicate_email" value="http://<?php echo esc_html($_SERVER['HTTP_HOST'],'apm-child')?>/aweber-subscription-failed" />
                        
                        <input type="hidden" name="apm_aweber_tag" value="<?php echo apm_display_aweber_tag('one-letter-from-retirement');?>">
                        
                        <div class="optin-inline-form-container">
                            <input name="subscriber_first_name" id="subscriber_first_name" required="true" class="plf-fname" type="text" placeholder="Your first name...">
                            <input name="subscriber_email" id="subscriber_email" required="true" class="plf-email" type="email" placeholder="Your email address...">
                        </div>
                        
                        <input type="hidden" id="recaptcha_token" name="recaptcha_token" value="<?php echo __($recaptcha_site_key, 'apm-child')?>">
                        
                        
                        <center><input type="submit" name="submit-om" class="om-trigger-conversion plf-submit-button" id="btnApmSubmitolfr2" onclick="onSubmitFormolfr2(event)" value="Discover His Proven Formula"></center>
                        
                        <div class="progress progress_olfr2"></div>
                        <br>
                        
                </form>

            
            
         </div>
         
         <div class="inline-row">
                <p class="u-align-center u-custom-font u-font-montserrat u-text u-text-black u-text-5"><a href="https://affiliatepromembership.com/privacy-policy/?customer_email_address=<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>" target="_blank">Privacy Policy</a>: We value your privacy. You can unsubscribe from receiving future emails with 1 click at any time. <br>
    </p>
         </div>
         
    </div>
</section>

