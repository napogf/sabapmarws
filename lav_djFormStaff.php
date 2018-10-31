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
if($_GET['mode']=='void') exit;

class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
     */

	function showButtonBar(){
		if($this->GetFormMode()=='insert'){
				print('<div>' .
					' <button dojoType="dijit.form.Button" ' .
						'onClick="insFormSt(\''.$this->GetFormName().'\')">Inserisci</button>' .
					  '</div>');
		} else {
				print('<div>' .
					' <button dojoType="dijit.form.Button" ' .
						'onClick="modFormSt(\''.$this->GetFormName().'\');">Aggiorna</button>' .
					  '</div>');
		}
	}



	function ShowForm() {
		/* Form container */

 		print('<div id="'.$this->GetFormName().'" class="dbFormContainer" >'."\n");

		print ('<!-- Form open -->');
		print ("\n");
		print('<div style="float:right;"><img src="graphics/close.png" style="cursor: pointer;" onclick="resetSt();"></div>');


		print('<form dojoType="dijit.form.Form" id="form_'.$this->GetFormName().'">');
		$this->showButtonBar(FALSE);

		print ('<fieldset style="margin: 10px;">' . "\n");
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
$ManagedTable = new MyDbForm('LAV_STAFF', $_SESSION['sess_lang']);


switch ($_GET['aggiorna']) {
	case 'insert':
		$insQueryFields='insert into lav_staff (';
		$insQueryValues=' values (';
		$token='';
		foreach($_GET as $key => $value){
			if($key != 'aggiorna' and $value>'' ){
				if ($ManagedTable->_FormFields[$key]->GetDataType()=='NUMBER') $value=str_replace(',','.',$value);
				$insQueryFields.= $token.$key;
				$insQueryValues.= $token.'\''.$value.'\'';
				$token=',';
			}
		}
		$insQueryFields.=')';
		$insQueryValues.=')';
		if (dbupdate($insQueryFields.$insQueryValues)){
			print('ok');
			exit;
		}
		break;
	case 'modify':
		$updQueryFields='update lav_staff set ';
		$whereQueryValues=' where STAFF_ID='.$_GET['STAFF_ID'];
		$token='';
		foreach($_GET as $key => $value){
			if($key != 'aggiorna' and $key != 'STAFF_ID' and $value>'' ){
				if ($ManagedTable->_FormFields[$key]->GetDataType()=='NUMBER') $value=str_replace(',','.',$value);
				$updQueryFields.= $token.$key.'='.'\''.$value.'\'';
				$token=',';
			}
		}
		if (dbupdate($updQueryFields.$whereQueryValues)){
			print('ok');
			exit;
		}
		break;
}



if ($_GET['STAFF_ID']>''){
	$mode='modify';
	$dbKey=' WHERE STAFF_ID ='.$_GET['STAFF_ID'];
} else {
	$mode='insert';
}


$ManagedTable->SetFormMode($mode, stripslashes($dbKey));
$ManagedTable->SetFormFieldValue('PERIZIA_ID',$_GET['PERIZIA_ID']);
$ManagedTable->HideFormField('PERIZIA_ID');
$ManagedTable->ShowForm();

?>