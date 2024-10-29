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
    <title>Product Licensing Formula</title>
    <link rel="stylesheet" href="<?php echo APM_PLUGIN_PATH ?>landing-pages/product-licensing-formula/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fjalla+One&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,500,500i,600,600i,700,700i,800,800i&amp;display=swap">
    <link rel="stylesheet" href="<?php echo APM_PLUGIN_PATH ?>landing-pages/product-licensing-formula/assets/css/styles.css">
</head>

<body>
    <section class="d-lg-flex d-xl-flex d-xxl-flex justify-content-center align-items-center align-items-lg-center align-items-xl-center align-items-xxl-center" id="section1">
        <div class="container d-flex flex-column align-items-center align-items-xxl-center" id="container1">
            <p class="fw-normal" id="text-top"><strong>Let Me Introduce You To Nick James. He Is 46 Years Old, Married And Has Two Grown-Up Children.</strong><br><strong>In Fact, You Could Say <em><span style="text-decoration: underline;">He Is Just An Average Kind Of Guy</span></em>…</strong><br><strong>… But What He Stumbled Across By Accident Nearly <span style="text-decoration: underline;">TWENTY YEARS AGO</span> Is Anything But Average.</strong><br><strong>It’s <span style="text-decoration: underline;">A Proven Way Of Making Money From Home</span> Which No One Else Is Talking About... That Anyone Can Do Without Prior Personal Experience Or Skills, Or Much Time...</strong></p>
            <div class="row">
                <div class="col-md-6 col-lg-5" style="text-align:center"><img id="nick-kate" src="<?php echo APM_PLUGIN_PATH ?>landing-pages/product-licensing-formula/assets/img/nick-kate.png"></div>
                <div class="col-md-6 col-lg-7 align-self-center">
                    <h1 id="headline">Yet He Is Still Banking <span style="color: rgb(248, 187, 22);">£25,000 - £35,000</span> EVERY SINGLE MONTH!</h1>
                </div>
            </div>
            <p id="text-bottom">It takes Nick around <strong><span style="text-decoration: underline;">1 hour a day</span></strong> to do this, just 2 or 3 days a week.<br>If you’d like, he can show <strong><span style="text-decoration: underline;">YOU</span></strong> what he does to <span style="text-decoration: underline;">make this kind of money 'from scratch'</span> in exactly the same way too.<br>Just <strong><span style="text-decoration: underline;">enter your details below</span></strong> to read the special report now:</p>
            <div class="row d-xl-flex justify-content-xl-center" id="form-container">
                <div class="col align-self-center" style="width: 100%;">
                
                

                
                		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo __($recaptcha_site_key, 'apm-child')?>"></script>
						<script>
                            function onSubmitFormplf1(event) {
                                event.preventDefault();
                        
                                
                                if(document.getElementById('frm_subscribers_plf1').elements['subscriber_first_name'].value == "")
                                {
                                    alert("Please enter the name.");
                                    return false;
                                }
                                
                                
                                var emailaddr = document.getElementById('frm_subscribers_plf1').elements['subscriber_email'].value;
                        
                                
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
                                        document.getElementById('frm_subscribers_plf1').elements['recaptcha_token'].value = token;
                                        // Submit the form
                                        document.getElementById('frm_subscribers_plf1').submit();
                                    });
                                });
                                
                                /* Disable the Submit Button after clicking and enable the Loader*/
                                var submitButton = document.getElementById("btnApmSubmitplf1");
                                submitButton.disabled = true;
                                
                                /* Enable the Loader*/
                                var hiddenDiv = document.querySelector(".progress_plf1");
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
                        <form name="frm_subscribers" class="frm_subscribers" id="frm_subscribers_plf1" method="post" action="https://affiliatepromembership.com/mi_email_subscribers.php">
                        <input type="hidden" name="mode" value="mi_subscribe">
                        <input type="hidden" name="thankyou_page_url" value="https://<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>--nickjames.thrivecart.com/product-licensing-formula/?ref=<?php echo esc_html($apm_ses_epm_magic_click_id,'apm-child')?>">
                        <input type="hidden" name="form_name" value="product-licensing-formula">
                        <input type="hidden" name="warriorplus_aff_id" value="<?php echo __($recordCrediantial['warriorplus_aff_id'],'apm-child')?>">
                        <input type="hidden" name="clickbank_nickname" value="<?php echo __($recordCrediantial['clickbank_affiliate_nickname'],'apm-child')?>">
                        <input type="hidden" name="thrivecart_username" value="<?php echo __($recordCrediantial['thrivecart_affiliate_username'],'apm-child')?>">
                        
                        <input type="hidden" name="click_magic_click_id" value="<?php echo __($apm_ses_epm_magic_click_id,'apm-child')?>">
                        <input type="hidden" name="account_id" value="<?php echo esc_html($recordCrediantial['aweber_account_number'],'apm-child')?>">
                        <input type="hidden" name="list_id" value="<?php echo esc_html($recordCrediantial['aweber_list_id'],'apm-child')?>">
                        <input type="hidden" name="customer_email" value="<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>">
                        <input type="hidden" name="duplicate_email" value="http://<?php echo esc_html($_SERVER['HTTP_HOST'],'apm-child')?>/aweber-subscription-failed" />
                       
                       <input type="hidden" name="apm_aweber_tag" value="<?php echo apm_display_aweber_tag('product-licensing-formula');?>">
                        
                        <input name="subscriber_first_name" id="subscriber_first_name" required="true" class="plf-fname" type="text" placeholder="Your first name..." />
                        <input name="subscriber_email" id="subscriber_email" required="true" class="plf-email" type="email" placeholder="Your email address..." />
                        
                        <input type="hidden" id="recaptcha_token" name="recaptcha_token" value="<?php echo __($recaptcha_site_key, 'apm-child')?>">
                        
                        
                        <center><input type="submit" name="submit-om" class="om-trigger-conversion plf-submit-button" id="btnApmSubmitplf1" onclick="onSubmitFormplf1(event)" value="Access The Special Report Now..."></center>
                        
                        <div class="progress progress_plf1"></div>
                        <br>
                        
                        </form>



                    
                    
                    <p id="privacy-text"><a href="https://affiliatepromembership.com/privacy-policy/?customer_email_address=<?php echo esc_html($recordCrediantial['user_email_id'],'apm-child')?>" target="_blank"><span style="text-decoration: underline;">Privacy Policy</span></a> - We value your data, you can unsubscribe with 1-click at any time.</p>
                </div>
            </div>
            <hr id="line">
        </div>
    </section>
    <script src="<?php echo APM_PLUGIN_PATH ?>landing-pages/product-licensing-formula/assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>