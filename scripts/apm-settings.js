// JavaScript Document
function submit_aweber_option()
{
	if(document.frm_subscription.first_name.value=="")
	{
		alert("Please enter the first name.")
		document.frm_subscription.first_name.focus();
		return false;
	}
	else if(document.frm_subscription.last_name.value=="")
	{
		alert("Please enter the last name.")
		document.frm_subscription.last_name.focus();
		return false;
	}
	else if(document.frm_subscription.email.value=="")
	{
		alert("Please enter the email.")
		document.frm_subscription.email.focus();
		return false;
	}
	
	else if(document.frm_subscription.jv_zoo_id.value=="")
	{
		alert("Please enter JV Zoo ID.")
		document.frm_subscription.jv_zoo_id.focus();
		return false;
	}
	
	else if(document.frm_subscription.email_service_provider.value=="")
	{
		alert("Please Choose Email Service Provider.")
		document.frm_subscription.email_service_provider.focus();
		return false;
	}
}

function submit_login_form()
{
	if(document.frm_plugin_login.user_login.value=="")
	{
		alert("Please enter the username.")
		document.frm_plugin_login.user_login.focus();
		return false;
	}
	else if(document.frm_plugin_login.user_pass.value=="")
	{
		alert("Please enter the password.")
		document.frm_plugin_login.user_pass.focus();
		return false;
	}
}


function submit_aweber_list()
{
	if(document.frm_apm_list.aweber_auth_code.value == "")
	{
		alert("Please enter the AWeber auth code.")
		document.frm_subscription.aweber_auth_code.focus();
		return false;
	}
	
	
}

function copyJVZooIpnClickboard()
{
	
	var url = document.getElementById("copy_jvzoo_ipn_clickboard");
	url.select();
	document.execCommand("Copy");
}


function copyWarriorPlusClickboard()
{
	
	var url = document.getElementById("copy_warriorplus_affliate_ipn_clickboard");
	url.select();
	document.execCommand("Copy");
}

function copyJVZooIpnMasterClickboard()
{
	
	var url = document.getElementById("master_jvzoo_ipn_clickboard_txt_box");
	url.select();
	document.execCommand("Copy");
}


function RetryClickMagicSubmission(id)
{
	if(confirm("Do you want to process this entry for ClickMagic?"))
	{
		document.frm_income_list.action="admin.php?page=apm_my_income&mode=retry&sid="+id;
		document.frm_income_list.submit();
	}
}



jQuery(document).ready(function(){
	
	// Initialize select2
	jQuery(".infusionsoft_lists").select2();
	
	/*jQuery("#apm-filter-date").datetimepicker({
		datepicker:false,
		format:	'H:i',
		formatTime:	'H:i',
	});
	
	jQuery("#apm-filter-time").datetimepicker({
		datepicker:false,
		format:	'H:i',
		formatTime:	'H:i',
	});*/
	
	
});
 