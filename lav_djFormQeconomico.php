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
						'onClick="insFormQe(\''.$this->GetFormName().'\')">Inserisci</button>' .
					  '</div>');
		} else {
				print('<div>' .
					' <button dojoType="dijit.form.Button" ' .
						'onClick="modFormQe(\''.$this->GetFormName().'\');">Aggiorna</button>' .
					  '</div>');
		}
	}



	function ShowForm() {
		/* Form container */

 		print('<div id="'.$this->GetFormName().'" class="dbFormContainer" >'."\n");

		print ('<!-- Form open -->');
		print ("\n");
		print('<div style="float:right;"><img src="graphics/close.png" style="cursor: pointer;" onclick="resetQe();"></div>');


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
$ManagedTable = new MyDbForm('LAV_QUADRO_ECONOMICO', $_SESSION['sess_lang']);

switch ($_GET['aggiorna']) {
	case 'insert':
		$insQueryFields='insert into lav_quadro_economico (';
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
		$updQueryFields='update lav_quadro_economico set ';
		$whereQueryValues=' where QECONOMICO_ID='.$_GET['QECONOMICO_ID'];
		$token='';
		foreach($_GET as $key => $value){
			if($key != 'aggiorna' and $key != 'QECONOMICO_ID' and $value>'' ){
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




if ($_GET['QECONOMICO_ID']>''){
	$mode='modify';
	$dbKey=' WHERE QECONOMICO_ID ='.$_GET['QECONOMICO_ID'];
} else {
	$mode='insert';
}


$ManagedTable->SetFormMode($mode, stripslashes($dbKey));
$ManagedTable->SetFormFieldValue('PERIZIA_ID',$_GET['PERIZIA_ID']);
$ManagedTable->HideFormField('PERIZIA_ID');
$ManagedTable->ShowForm();

?>