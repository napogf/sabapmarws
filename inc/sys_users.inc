<?php
/*
 * Created on 6-dic-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

if ($mode <> "insert" and IsSet($MENU_ID) and !is_null($MENU_ID) and strlen(trim($MENU_ID))>0 ) {
    $dbKey=' where MENU_ID='.$MENU_ID;
	$mode='modify';
}

class MyDbForm extends formExtended {
//			function FormPreValidation(){
//				if (($_POST['PASSWORD'] == $_POST['PASSWORD_CONF'])
//					and $this->passwordVerify($_POST['PASSWORD'])) {
//					if ($this->GetFormFieldValue('PASSWORD')<>$_POST['PASSWORD']){
//						$_POST['PASSDATE']=date('Y-m-d');
//					}
//					return TRUE;
//				} else {
//					$this->SetFormMessage('Password Errata!');
//					return FALSE;
//				}
//			}
}
$userTable = isSet($userTable)?strtoupper($userTable):'SYS_USERS';
$dbTable = $userTable;
if (!isSet($xlsExport) or ($xlsExport <> 'Y')) {
	include ("pageheader.inc");
	$recallPage='?dbTable='.$dbTable;
}
if ($mode=='modify'){
	// $xlsBar = 'Y';
	$midToolBar = new toolBar();
	$midToolBar->SetRightLinks('<a href="assign_userresp.php?USER_ID='.$USER_ID.'" >'.get_label('assign_responsability').'</a>','modify');
}
print('<div class="formContainer">'."\n");
include("manageDbtable.inc");
print('</div>'."\n");
include ("pagefooter.inc");

?>
