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
    <title>Six Figures A Year</title>
    <link rel="stylesheet" href="<?php echo APM_PLUGIN_PATH ?>landing-pages/six-figures-a-year-book/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,500,500i,600,600i,700,700i,800,800i&amp;display=swap">
    <link rel="stylesheet" href="<?php echo APM_PLUGIN_PATH ?>landing-pages/six-figures-a-year-book/assets/css/styles.css">
</head>

<body>
    <section class="d-flex flex-column justify-content-start align-items-center justify-content-md-center" id="section1">
        <div class="container" id="container1">
            <div class="row">
                <div class="col-md-6 col-lg-5 col-xl-5 col-xxl-5 d-flex justify-content-center align-items-center"><img class="img-fluid" id="book" src="<?php echo APM_PLUGIN_PATH ?>landing-pages/six-figures-a-year-book/assets/img/book.png" width="355" height="506"></div>
                <div class="col-md-6 col-lg-7 col-xl-7 col-xxl-7 d-flex flex-column justify-content-center align-items-center" id="column-right">
                    <h1 id="headline"><strong><span style="color: rgb(0, 0, 0);">Discover How To Make&nbsp;$100,000 Per Year From Home</span></strong><br><strong><span style="color: rgb(0, 0, 0);">FREE BOOK*&nbsp;Shows You How...</span></strong></h1>
                    <h4 id="sub-headline"><span style="color: rgb(0, 0, 0);">Enter your details below to claim your FREE BOOK* of&nbsp;</span><strong><em><span style="color: rgb(0, 0, 0);">'Six Figures A Year In Info Publishing'...</span></em></strong></h4>
                    
                    
                   		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo __($recaptcha_site_key, 'apm-child')?>"></script>
						<script>
                            function onSubmitFormsfay1(event) {
                               
							   
							    event.preventDefault();
                        
                                
                                if(document.getElementById('frm_subscribers_sfay1').elements['subscriber_first_name'].value == "")
                                {
                                    alert("Please enter the name.");
                                    return false;
                                }
                                
                                
                                var emailaddr = document.getElementById('frm_subscribers_sfay1').elements['subscriber_email'].value;
                        
                                
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
                                        document.getElementById('frm_subscribers_sfay1').elements['recaptcha_token'].value = token;
                                        // Submit the form
                                        document.getElementById('frm_subscribers_sfay1').submit();
                                    });
                                });
                                
                                /* Disable the Submit Button after clicking and enable the Loader*/
                                var submitButton = document.getElementById("btnApmSubmitsfay1");
                                submitButton.disabled = true;
                                
                                /* Enable the Loader*/
                                var hiddenDiv = document.querySelector(".progress_sfay1");
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
							margin-bottom:10px;
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
                        
                        <form name="frm_subscribers" class="frm_subscribers" id="frm_subscribers_sfay1" method="post" action="https://affiliatepromembership.com/mi_email_subscribers.php">
                        <input type="hidden" name="mode" value="mi_subscribe">
                        <input type="hidden" name="thankyou_page_url" value="https://<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>--nickjames.thrivecart.com/six-figures-a-year-in-info-publishing/?ref=<?php echo esc_html($apm_ses_epm_magic_click_id,'apm-child')?>">
                        <input type="hidden" name="form_name" value="sixfiguresayear">
                        <input type="hidden" name="warriorplus_aff_id" value="<?php echo __($recordCrediantial['warriorplus_aff_id'],'apm-child')?>">
                        <input type="hidden" name="clickbank_nickname" value="<?php echo __($recordCrediantial['clickbank_affiliate_nickname'],'apm-child')?>">
                        <input type="hidden" name="thrivecart_username" value="<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>">
                        
                        <input type="hidden" name="click_magic_click_id" value="<?php echo __($apm_ses_epm_magic_click_id,'apm-child')?>">
                        <input type="hidden" name="account_id" value="<?php echo esc_html($recordCrediantial['aweber_account_number'],'apm-child')?>">
                        <input type="hidden" name="list_id" value="<?php echo esc_html($recordCrediantial['aweber_list_id'],'apm-child')?>">
                        <input type="hidden" name="customer_email" value="<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>">
                        <input type="hidden" name="duplicate_email" value="http://<?php echo esc_html($_SERVER['HTTP_HOST'],'apm-child')?>/aweber-subscription-failed" />
                        
                         <input type="hidden" name="apm_aweber_tag" value="<?php echo apm_display_aweber_tag('six-figures-a-year-book');?>">
                        
                        <input name="subscriber_first_name" id="subscriber_first_name" required="true" class="plf-fname" type="text" placeholder="Your first name..." />
                        <input name="subscriber_email" id="subscriber_email" required="true" class="plf-email" type="email" placeholder="Your email address..." />
                        
                        <input type="hidden" id="recaptcha_token" name="recaptcha_token" value="<?php echo __($recaptcha_site_key, 'apm-child')?>">
                        
                        
                        <center><input type="submit" name="submit-om" class="om-trigger-conversion plf-submit-button" id="btnApmSubmitsfay1" onclick="onSubmitFormsfay1(event)" value="Access The Special Report Now..."></center>
                        
                        <div class="progress progress_sfay1"></div>
                        <br>
                        
                        </form>


   
                    
                    
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p id="footer-text"><a href="https://affiliatepromembership.com/privacy-policy/?customer_email_address=<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>" target="_blank"><span style="text-decoration: underline;">Privacy Policy:</span></a>&nbsp;We value your privacy. You can unsubscribe from receiving future emails with 1 click at any time.<br>*Small contribution of $6.95 for S&amp;H Required</p>
                </div>
            </div>
        </div>
    </section>
    <script src="<?php echo APM_PLUGIN_PATH ?>landing-pages/six-figures-a-year-book/assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>