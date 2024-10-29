<?php
include_once('isdk.php');

class InfusionsoftAPIUtil{
    public $INFUSIONSOFT_APP_NAME;
    public $INFUSIONSOFT_API_KEY;
    public $infusionApp;
    
    public function __construct($INFUSIONSOFT_APP_NAME,$INFUSIONSOFT_API_KEY) {
        $this->INFUSIONSOFT_APP_NAME = $INFUSIONSOFT_APP_NAME;
        $this->INFUSIONSOFT_API_KEY = $INFUSIONSOFT_API_KEY;

        $this->infusionApp = new iSDK;
        $this->infusionApp->cfgCon($this->INFUSIONSOFT_APP_NAME, $this->INFUSIONSOFT_API_KEY);
        
    }

    public function isConnectedSuccessfully(){
        //api connection check
        $connected = $this->infusionApp->dsGetSetting("Contact","optiontypes");
        error_log("strpos=".(int)strpos($connected, "ERROR:"));
        $isError = substr($connected, 0, strlen("ERROR:")) === "ERROR:";
        if($isError){
            return false;
        }
        return true;
    }
    
    
    public function addUpdateIFSContactAndAddTag($user_email,$first_name,$last_name, $tagId){
        $contact = array();
        $contact['Email'] = $user_email;

        
        // Query the IFS to see if the contact email exists.
        $queryEmail = array('Email' => $contact['Email']);
        $returnFields = array('Id');
        $contactExists = $this->infusionApp->dsQuery("Contact", 1, 0, $queryEmail, $returnFields);

        if (!empty($contactExists)) {
            $contactID = $contactExists[0]['Id'];  // Get the contact ID. 
            $response = $this->infusionApp->updateCon($contactID, $contact); // Update the contact details with the information from the form.
        } else {
            $contact['FirstName'] = $first_name;
            $contact['LastName'] = $last_name;            
            $contactID = $this->infusionApp->addCon($contact); // Add a new contact to the system.
			$response = $contactID;
        }
        
        $this->infusionApp->grpAssign($contactID, $tagId);
        
        return $response;
    }
	
	public function updateGroupTag($contactID, $tagId){
        return $this->infusionApp->grpAssign($contactID, $tagId);
    }
    
    public function getInfusionsoftTags(){
        $tagsArray = array();
        
        //get All TagCategories and Tags
        $queryTagCats = array('Id' => '%');
        $returnFields = array('Id','CategoryName');
        $tagCats = $this->infusionApp->dsQuery("ContactGroupCategory", 1000, 0, $queryTagCats, $returnFields);
        foreach($tagCats as $tagCat){
            $queryTags = array('GroupCategoryId' => $tagCat['Id']);
            $returnTagFields = array('Id','GroupName');
            $tags = $this->infusionApp->dsQuery("ContactGroup", 1000, 0, $queryTags, $returnTagFields);
            foreach($tags as $tag){
                $tagObj = new stdClass();
                $tagObj->tagId = $tag['Id'];
                $tagObj->tagName = $tagCat['CategoryName'].' -> '.$tag['GroupName'];
                $tagsArray[] = $tagObj;
            }
        }
        return $tagsArray;
    }

    public function getInfusionsoftOptionHTML($INFUSIONOFT_List_NameArray = array()){
        $listArray = $this->getInfusionsoftTags();
        $selected = "";
        $html .="";
        foreach($listArray as $listObj){
            if($listObj->status == 0){
                $selected = in_array($listObj->tagId,$INFUSIONOFT_List_NameArray) ? 'selected' : '';
                $html .='      <option value="'.$listObj->tagId.'" '.$selected.'>'.$listObj->tagName.'</option>';
            }
        }
        return $html;
    }

    public function getInfusionsoftDataFieldHTML(){
        $queryCustomFields = array('FormId' => -1);
        $returnFields = array('Name','Label');
        $customFldsDetail = $this->infusionApp->dsQuery("DataFormField", 1000, 0, $queryCustomFields, $returnFields);
        $customFldObjArray = array();
        foreach($customFldsDetail as $customFld){
            $customFldObj = new stdClass();
            $customFldObj->Name = "_".$customFld['Name'];
            $customFldObj->Label = $customFld['Label'];
            $customFldObjArray[] = $customFldObj;
        }
        return $customFldObjArray;
    }
	
	public function returnUpdateIFSContact($user_email){
			$returnFields = array('Id');
			$queryEmail = array('Email' => $user_email);
			$contactExists = $this->infusionApp->dsQuery("Contact", 1, 0,  $queryEmail, $returnFields);
			return $contactExists;
	}
    
    public function updateIFSContact($user_email, $contactArray){
        // Query the IFS to see if the contact email exists.
        $queryEmail = array('Email' => $user_email);
        $returnFields = array('Id');
        $contactExists = $this->infusionApp->dsQuery("Contact", 1, 0, $queryEmail, $returnFields);
        if (!empty($contactExists)) {
            $contactID = $contactExists[0]['Id'];  // Get the contact ID. 
            $response = $this->infusionApp->updateCon($contactID, $contactArray); // Update the contact details with the information from the form.
            error_log("JVZooIpn InfusionsoftAPIUtil updateIFSContact contactArray=".print_r($contactArray, true));
            error_log("JVZooIpn InfusionsoftAPIUtil updateIFSContact Result=".print_r($response, true));            
        } else{
            error_log("JVZooIpn InfusionsoftAPIUtil updateIFSContact empty(contactExists)");            
        }       

    }
}
?>