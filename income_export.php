<?php
ini_set("display_error", 1);

global $confroot;

FindWPConfig(dirname(dirname(__FILE__)));

include_once $confroot."/wp-load.php";
require_once ('mc-main.php');

//--> Export
	
if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'export')
{
	$my_income_from_date = sanitize_text_field($_GET['fdate']);
	$my_income_to_date = sanitize_text_field($_GET['tdate']);

	if($my_income_from_date != '' && $my_income_to_date != '')
	{
		$where_clause = " AND DATE(income_date) >= '".$my_income_from_date."' AND DATE(income_date) <= '".$my_income_to_date."'";
	}
	
	$egSql = "SELECT * FROM ".APM_MY_INCOME." WHERE 1 $where_clause";
	$exportRecords = $wpdb->get_results($egSql, OBJECT);

	if(count($exportRecords) > 0)
	{
		//--> Generating CSV
		$page_name = APM_PHYSICAL_PATH."export/export_my_income.txt";
		$fp = fopen($page_name, "w");
		$header = "Name, Email, Product\n";
		fwrite($fp, $header);
		
		$replace_special_char = array("\t", "#", "-", "^", "?", "<", ">",'"',";","|","/","(","*","'");
		
		$serverFile = "export_my_income.txt";
		$downloadFile = "my_income_".date('m_d_Y').".csv";
		
		foreach ($exportRecords as $exportRecord)
		{
			$body = $exportRecord->inc_ccustname.",\"".$exportRecord->inc_ccustemail."\",\"".$exportRecord->inc_cprodtitle."\"\n";
			fwrite($fp, $body);
		}
	}
	
	
	fclose($fp);
	header('Content-Type: application/x-msdownload');
	header("Content-Disposition: attachment; filename=".$downloadFile.".csv");
	readfile(APM_PHYSICAL_PATH."export/".$serverFile);
	header("Pragma: no-cache");
	header("Expires: 0");
	die;
	
}



//==> locate wp-load.php dynamically

function FindWPConfig($dirrectory){

global $confroot;
foreach(glob($dirrectory."/*") as $f){

    if (basename($f) == 'wp-load.php' ){

        $confroot = str_replace("\\", "/", dirname($f));
        return true;
    }

    if (is_dir($f)){
        $newdir = dirname(dirname($f));
    }
}

if (isset($newdir) && $newdir != $dirrectory){

    if (FindWPConfig($newdir)){
        return false;
    }   
}
return false;
}
?>