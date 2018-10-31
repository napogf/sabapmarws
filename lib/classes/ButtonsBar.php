<?php
/**
 *
 *
 * @version $Id: butt_c.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003
 **/

/**
 *
 *
 **/
class ButtonsBar{
	/**
     * Constructor
     * @access protected
     */
	function ButtonsBar($FormName=null){
		$_SESSION['sess_lang'] = $_SESSION['sess_lang'];

		if (empty($FormName)) {
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
									 AND (FORMSTB.FORM_NAME = '".$FormName."') )
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

	function BarButtonShow($table=TRUE){
		$ButtonsObjects=$this->GetBarButtons();
		reset($ButtonsObjects);
		if (!is_null($ButtonsObjects)) {
			if ($table) {
		        print('<!-- Start bottoniera -->');
		        print("\n");
		        print('<table width="100%" bgcolor="#FFFFFF" border="0" >');
		        print('  <tr>');
				print('<td width="50%" align="left">');
				while(!is_null($key = key($ButtonsObjects) )){
					if ($ButtonsObjects[$key]->GetButtonAlignment()=='R') {
					    print('<td width="50%" align="right">');
					}
					if ($ButtonsObjects[$key]->IsEnabled()) {
					    $ButtonsObjects[$key]->ButtonShow();
					}
					next($ButtonsObjects);
				} // while
		        print('</td>');
		        print('  </tr>');
		        print("\n");
		        print('</table>');
		        print('<!-- End bottoniera -->');
		        print("\n");
			} else {
		        print('<!-- Start bottoniera -->');
		        print("\n");
				print('<div class="buttonBarLeft">');
				while(!is_null($key = key($ButtonsObjects) )){
					if ($ButtonsObjects[$key]->GetButtonAlignment()=='R') {
					    print('</div>'."\n");
					    print('<div class="buttonBarRight">'."\n");
					}
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


	var $_BarButtons=array();
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

/**
 *
 *
 **/
class Button{
	/**
     * Constructor
     * @access protected
     */

	var $_ButtonName;
	function GetButtonName(){
		return $this->_ButtonName;
	}
	function SetButtonName($value){
		$this->_ButtonName = $value;
	}
	var $_ButtonClass='';
	function GetButtonClass(){
		return $this->_ButtonClass;
	}
	function SetButtonClass($value){
		$value=empty($value)?$value:' class="'.$value.'" ';
		$this->_ButtonClass = $value;
	}
	var $_ButtonType;
	function GetButtonType(){
		return $this->_ButtonType;
	}
	function SetButtonType($value){
		$this->_ButtonType = $value;
	}
	var $_ButtonValue;
	function GetButtonValue(){
		return $this->_ButtonValue;
	}
	function SetButtonValue($value){
		$this->_ButtonValue = $value;
	}
	var $_ButtonAlignment;
	function GetButtonAlignment(){
		return $this->_ButtonAlignment;
	}
	function SetButtonAlignment($value){
		$this->_ButtonAlignment = $value;
	}

	var $_Enabled=TRUE;
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



	function Button($button_name){
		$this->SetButtonName($button_name);
	}


	function ButtonShow(){
		 print('<input '.$this->GetButtonClass().' name="');
		 print($this->GetButtonName());
		 print('" type="');
		 print($this->GetButtonType());
		 print('" value="');
		 print($this->GetButtonValue());
		 print('" >');

	}

	function ButtonShowDojo(){

		 print('<input '.$this->GetButtonClass().' name="');
		 print($this->GetButtonName());
		 print('" type="');
		 print($this->GetButtonType());
		 print('" value="');
		 print($this->GetButtonValue());
		 print('" ');
		 if (strtoupper($this->GetButtonType())=='SUBMIT') print('onclick="dojoInput(\''.$_SERVER['PHP_SELF'].'\',\''.$this->GetButtonValue().'\')" ');
		 print('>');

	}


}

?>