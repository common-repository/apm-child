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
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Full System Trial Package</title>
    <link rel="stylesheet" href="<?php echo APM_PLUGIN_PATH ?>landing-pages/free-system-trial-package/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fjalla+One&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,500,500i,600,600i,700,700i,800,800i&amp;display=swap">
    <link rel="stylesheet" href="<?php echo APM_PLUGIN_PATH ?>landing-pages/free-system-trial-package/assets/css/styles.css">
</head>

<body>
    <section class="d-lg-flex d-xl-flex d-xxl-flex justify-content-center align-items-center align-items-lg-center align-items-xl-center align-items-xxl-center" id="section1">
        <div class="container d-flex flex-column align-items-center align-items-xxl-center" id="container1">
            <div class="row">
                <div class="col-md-6 col-lg-5 align-self-center" style="text-align:center"><img id="nick-kate" src="<?php echo APM_PLUGIN_PATH ?>landing-pages/free-system-trial-package/assets/img/nick.png"></div>
                <div class="col-md-6 col-lg-7 align-self-center">
                    <h1 id="headline">When you join <span style="color: rgb(108, 84, 46);">Nick</span> in his new <span style="color: rgb(108, 84, 46);">Se</span><span style="color: rgb(108, 84, 46);">rious About Six Figures A Year™</span> training programme, he'll show you how <span style="text-decoration: underline; color: rgb(108, 84, 46);">YOU</span> too could be making <span style="color: rgb(108, 84, 46);">$10,000 to $85,000 every month</span>, within the next 6 to 12 months ...</h1>
                </div>
            </div>
            <p id="text-bottom">But first, he needs to send you a <strong><em>Serious About Six Figures A Year™ - Full System Trial Package</em></strong>, right away, so that you can decide if this is something you want to take further ...&nbsp;</p>
            <p id="text-bottom-2"><span style="font-weight: normal !important;">Enter Your Details Below To Claim Your </span><strong><span style="text-decoration: underline; color: rgb(108, 84, 46);">Full System Trial Package</span></strong><span style="font-weight: normal !important;"> On </span><strong><span style="text-decoration: underline; color: rgb(108, 84, 46);">A COMPLETELY RISK FREE</span></strong><span style="font-weight: normal !important;"> Trial Basis...&nbsp;</span></p>
            <div class="row d-xl-flex justify-content-xl-center" id="form-container">
                <div class="col align-self-center" style="width: 100%;">
                
                
                		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo __($recaptcha_site_key, 'apm-child')?>"></script>
						<script>
                            function onSubmitFormfstp(event) {
                                event.preventDefault();
                        
                                
                                if(document.getElementById('frm_subscribers_fstp').elements['subscriber_first_name'].value == "")
                                {
                                    alert("Please enter the name.");
                                    return false;
                                }
                                
                                
                                var emailaddr = document.getElementById('frm_subscribers_fstp').elements['subscriber_email'].value;
                        
                                
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
                                        document.getElementById('frm_subscribers_fstp').elements['recaptcha_token'].value = token;
                                        // Submit the form
                                        document.getElementById('frm_subscribers_fstp').submit();
                                    });
                                });
                                
                                /* Disable the Submit Button after clicking and enable the Loader*/
                                var submitButton = document.getElementById("btnApmSubmitfstp");
                                submitButton.disabled = true;
                                
                                /* Enable the Loader*/
                                var hiddenDiv = document.querySelector(".progress_fstp");
                                hiddenDiv.style.display = "block";
                                
                            }
                        </script>
                        
                        <style>
						.frm_subscribers {
							max-width:500px;
							width:100%;
							margin:0 auto;
						}
						
						.frm_subscribers .plf-fname,
						.frm_subscribers .plf-email{
							width:100%;
							margin-bottom:5px;
							padding:6px;
						}
						
						.frm_subscribers .plf-submit-button{
							width:100%;
							padding:6px;
						}
						
                        .frm_subscribers .progress {
                           width: 200.8px;
                           height: 16.8px;
                           border-radius: 16.8px;
                           background: repeating-linear-gradient(135deg,#fff 0 8.4px,rgb(5 5 6 / 10%) 0 7.8px) left/0%   100% no-repeat,
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

                        <form name="frm_subscribers" class="frm_subscribers" id="frm_subscribers_fstp" method="post" action="https://affiliatepromembership.com/mi_email_subscribers.php">
                        <input type="hidden" name="mode" value="mi_subscribe">
                        <input type="hidden" name="thankyou_page_url" value="https://<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>--nickjames.thrivecart.com/free-system-trial-package/?ref=<?php echo esc_html($apm_ses_epm_magic_click_id,'apm-child')?>">
                        <input type="hidden" name="form_name" value="free-system-trial-package">
                        <input type="hidden" name="warriorplus_aff_id" value="<?php echo __($recordCrediantial['warriorplus_aff_id'],'apm-child')?>">
                        <input type="hidden" name="clickbank_nickname" value="<?php echo __($recordCrediantial['clickbank_affiliate_nickname'],'apm-child')?>">
                        <input type="hidden" name="thrivecart_username" value="<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>">
                        
                        <input type="hidden" name="click_magic_click_id" value="<?php echo __($apm_ses_epm_magic_click_id,'apm-child')?>">
                        <input type="hidden" name="account_id" value="<?php echo esc_html($recordCrediantial['aweber_account_number'],'apm-child')?>">
                        <input type="hidden" name="list_id" value="<?php echo esc_html($recordCrediantial['aweber_list_id'],'apm-child')?>">
                        <input type="hidden" name="customer_email" value="<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>">
                        <input type="hidden" name="duplicate_email" value="http://<?php echo esc_html($_SERVER['HTTP_HOST'],'apm-child')?>/aweber-subscription-failed" />
                        
                        
                        <input type="hidden" name="apm_aweber_tag" value="<?php echo apm_display_aweber_tag('free-system-trial-package');?>">
                        
                        <input name="subscriber_first_name" id="subscriber_first_name" required="true" class="plf-fname" type="text" placeholder="Your first name..." />
                        <input name="subscriber_email" id="subscriber_email" required="true" class="plf-email" type="email" placeholder="Your email address..." />
                        
                        <input type="hidden" id="recaptcha_token" name="recaptcha_token" value="<?php echo __($recaptcha_site_key, 'apm-child')?>">
                        
                        
                        <center><input type="submit" name="submit-om" class="om-trigger-conversion plf-submit-button" id="btnApmSubmitfstp" onclick="onSubmitFormfstp(event)" value="Access The Special Report Now..."></center>
                        
                        <div class="progress progress_fstp"></div>
                        <br>
                        
                        </form>
                    <p id="privacy-text"><a href="https://affiliatepromembership.com/privacy-policy/?customer_email_address=<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>" target="_blank"><span style="text-decoration: underline;">Privacy Policy</span></a> - We value your data, you can unsubscribe with 1-click at any time.</p>
                </div>
            </div>
        </div>
    </section>
    <script src="<?php echo APM_PLUGIN_PATH ?>landing-pages/free-system-trial-package/assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>