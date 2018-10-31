<?php
/*
 * Created on 17/giu/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class djButtonsBar {
	function __construct($formName=null){
		$_SESSION['sess_lang']=is_null($_SESSION['sess_lang'])?1:$_SESSION['sess_lang'];
		if (is_null($formName)) {
			$button_query="SELECT SYS_FORM_BUTTONS.BUTTON_NAME, SYS_FORM_BUTTONS.BUTTON_TYPE, SYS_FORM_BUTTONS.CLASS,
									SYS.BUTTON_VALUE, SYS.LANGUAGE_ID, SYS_FORM_BUTTONS.ALIGNMENT,
							    	SYS_FORM_BUTTONS.BUTTON_SEQ
							FROM sys_form_buttons SYS_FORM_BUTTONS
							LEFT JOIN sys_form_button_labels SYS ON ( (SYS_FORM_BUTTONS.BUTTON_ID = SYS.BUTTON_ID)
																	AND (SYS.LANGUAGE_ID = '".$_SESSION['sess_lang']."'))
								WHERE (SYS_FORM_BUTTONS.FORM_ID IS NULL)
								ORDER BY SYS_FORM_BUTTONS.ALIGNMENT ASC, SYS_FORM_BUTTONS.BUTTON_SEQ ASC";
		} else {
			$button_query="SELECT SYS_FORM_BUTTONS.BUTTON_NAME, SYS_FORM_BUTTONS.BUTTON_TYPE, SYS_FORM_BUTTONS.CLASS,
								 SYS.BUTTON_VALUE, SYS.LANGUAGE_ID, FORMSTB.FORM_NAME, SYS_FORM_BUTTONS.ALIGNMENT,
							    	SYS_FORM_BUTTONS.BUTTON_SEQ

							FROM sys_form_buttons SYS_FORM_BUTTONS,
							sys_forms FORMSTB
							LEFT JOIN sys_form_button_labels SYS ON ( (SYS_FORM_BUTTONS.BUTTON_ID = SYS.BUTTON_ID)
																	AND (SYS.LANGUAGE_ID = '".$_SESSION['sess_lang']."'))
							WHERE ( (SYS_FORM_BUTTONS.FORM_ID = FORMSTB.FORM_ID)
									 AND (FORMSTB.FORM_NAME = '".$formName."') )
								ORDER BY SYS_FORM_BUTTONS.ALIGNMENT ASC, SYS_FORM_BUTTONS.BUTTON_SEQ ASC";
		}
		$DbButtons=dbselect($button_query);
		for($i = 0; $i < $DbButtons['NROWS']; $i++){
			$button_object = new Button($DbButtons['ROWS'][$i]['BUTTON_NAME']);
			$button_object->SetButtonClass($DbButtons['ROWS'][$i]['CLASS']);
			$button_object->SetButtonType($DbButtons['ROWS'][$i]['BUTTON_TYPE']);
			$button_object->SetButtonValue($DbButtons['ROWS'][$i]['BUTTON_VALUE']);
			$button_object->SetButtonAlignment($DbButtons['ROWS'][$i]['ALIGNMENT']);
			$this->AddBarButtons($DbButtons['ROWS'][$i]['BUTTON_NAME'], $button_object);
		} // for
	}

	function barButtonShow($table=TRUE){
		$ButtonsObjects=$this->GetBarButtons();
		reset($ButtonsObjects);
		if (!is_null($ButtonsObjects)) {
	        print('<!-- Start bottoniera -->');
	        print("\n");
			print('<div class="barButtons">');
			while(!is_null($key = key($ButtonsObjects) )){
				if ($ButtonsObjects[$key]->IsEnabled()) {
				    $ButtonsObjects[$key]->ButtonShow();
				}
				next($ButtonsObjects);
			} // while
	        print('</div>');
	        print("\n");
	        print('<!-- End bottoniera -->');
	        print("\n");
		}
	}

	function BarButtonDisable($key){
		$this->_BarButtons[$key]->Disable();
	}

	function BarButtonEnable($key){
		$this->_BarButtons[$key]->Enable();
	}


	function BarButtonIsPressed($key){
		if ($this->_BarButtons[$key]->IsPressed($_POST[$key])) {
		    return TRUE;
		} else {
			return FALSE;
		}

	}


	private $_BarButtons=array();
	function GetBarButtons(){
		return $this->_BarButtons;
	}
	function AddBarButtons($key,$value){
		$this->_BarButtons[$key] = $value;
	}

	function GetBarButtonByName($key){
		return $this->_BarButtons[$key];
	}




}

class Button{
	/**
     * Constructor
     * @access protected
     */
	function __construct($buttonName=null,$formSendDestination=null){
		$this->SetButtonName($buttonName);
		$this->setSendFormDestination($formSendDestination);
	}

	private $_ButtonName;
	function GetButtonName(){
		return $this->_ButtonName;
	}
	function SetButtonName($value){
		$this->_ButtonName = $value;
	}
	private $_ButtonClass='';
	function GetButtonClass(){
		 if ($this->GetButtonAlignment()=='R'){
		 	return ($this->_ButtonClass = 'class="djButtonBarRight" ');
		 } else {
		 	return($this->_ButtonClass = 'class="djButtonBarLeft" ');
		 }
	}
	function SetButtonClass($value){
			$value=empty($value)?$value:' class="'.$value.'" ';
			$this->_ButtonClass = $value;
	}
	private $_ButtonType;
	function GetButtonType(){
		return $this->_ButtonType;
	}
	function SetButtonType($value){
		$this->_ButtonType = $value;
	}
	private $_ButtonValue;
	function GetButtonValue(){
		return $this->_ButtonValue;
	}
	function SetButtonValue($value){
		$this->_ButtonValue = $value;
	}
	private $_ButtonAlignment;
	function GetButtonAlignment(){
		return $this->_ButtonAlignment;
	}
	function SetButtonAlignment($value){
		$this->_ButtonAlignment = $value;
	}
	private $_Enabled=TRUE;
	function IsEnabled(){
		return $this->_Enabled;
	}
	function Enable(){
		$this->_Enabled = true;
	}

	function Disable(){
		$this->_Enabled=FALSE;
	}


	function IsPressed($button_value){
		$button_name=$this->GetButtonName();
		if ($button_value==$this->GetButtonValue()) {
		    return TRUE;
		} else {
			return FALSE;
		}
	}
	private $_sendFormDestination;
	function setSendFormDestination($value){
		$this->_sendFormDestination=$value;
	}
	function getSendFormDestination(){
		return $this->_sendFormDestination;
	}


	function ButtonShow(){
		 print('<input '.$this->GetButtonClass().' name="');
		 print($this->GetButtonName());
		 print('" type="');
		 print($this->GetButtonType());
		 // print('button');
		 print('" value="');
		 print($this->GetButtonValue());
		 print('" ');
		 if (strtoupper($this->GetButtonType())=='SUBMIT') print('onclick="sendIt(\''.$this->getSendFormDestination().'\');" ');
		 print('>');

	}


}






?>
