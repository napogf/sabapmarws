<?php
/*
 * Created on 19-ott-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("fdataentry.php");
//require_once("formExt.inc");





class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
     */

	function showButtonBar(){
		print('<div>' .
			' <button dojoType="dijit.form.Button" ' .
				'onClick="insForm(\''.$this->GetFormName().'\')">Aggiorna</button>' .
			  '</div>');
	}



	function ShowForm() {
		/* Form container */

 		print('<div id="'.$this->GetFormName().'" class="dbFormContainer" >'."\n");

		print ('<!-- Form open -->');
		print ("\n");


		print('<form dojoType="dijit.form.Form" id="form_'.$this->GetFormName().'">');
		$this->showButtonBar(FALSE);

		print ('<fieldset style="border:none">' . "\n");
		foreach ($this->_FormFields as $fieldName => $fieldObj) {
			if (array_search($fieldName,$this->_recursiveFields)===FALSE) {
				$fieldObj->showDivField();
			} else {
				if ((strlen(trim($_POST[$fieldName]))>0)) {
					$fieldObj->dispDivField();
				} else {
					$fieldObj->showDivField();
				}
			}
		}
		print ("\n");
		print ("<br />\n");
		print('</fieldset>'."\n");
		$this->showButtonBar(FALSE);
		print ('</form>' . "\n");

		print ('</div>');


	}






}



if ($_GET['PERIZIA_ID']>''){
	$mode='modify';
	$dbKey=' WHERE PERIZIA_ID ='.$_GET['PERIZIA_ID'];
} else {
	$mode='insert';
}


$ManagedTable = new MyDbForm('LAV_PERIZIE', $_SESSION['sess_lang']);
$ManagedTable->SetFormMode($mode, stripslashes($dbKey));

$ManagedTable->ShowForm();

?>