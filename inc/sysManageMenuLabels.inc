<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class MyDbForm extends formExtended {



}




$dbKey =  isSet($_GET['dbKey']) ? $_GET['dbKey'] :  ' where MENU_ID='.$_GET['MENU_ID'].' and LANGUAGE_ID='.$_GET['LANGUAGE_ID'];

$ManagedTable = new MyDbForm('SYS_MENU_LABELS',$_SESSION['sess_lang']);

$del_message=get_label('del_message');



if (dbselect("select * from sys_menu_labels $dbKey")){
	$ManagedTable->SetFormMode("modify",stripslashes($dbKey));
} else {
	// extract key to valorize for insert mode
	$insExp='|(?P<FIELD>[a-zA-Z_]+)=(?P<VALUE>[0-9]+)|U';
	if (preg_match_all($insExp,$dbKey,$a)) {
		$ManagedTable->SetFormMode("insert");
		for ($index = 0; $index < sizeof($a['FIELD']); $index++) {
			$ManagedTable->SetFormFieldValue($a['FIELD'][$index],$a['VALUE'][$index]);
		}
	}

}

if (isset($_POST['buttapp'])) {

	header("Location: sys_manage_menu.php?MENU_ID=".$_GET['MENU_ID']);
}



include('pageheader.inc');
print('<div class="formContainer">'."\n");

$ManagedTable->ShowForm();

print('<hr>');
print('<table width="100%" border="0" cellspacing="0" cellpadding="0">');
print('<tr>');
print('<td valign="top" width="10" class="lista2" ><img src="graphics/back.gif" ></td>');
print('<td valign="middle" class="lista2" nowrap >&nbsp;&nbsp;<a href="sys_manage_menu.php?MENU_ID='.$MENU_ID.'" >'.get_label('go_back').'</a></td>');
print('<td valign="middle" width="100%" class="lista2" nowrap >&nbsp;&nbsp;</td>');
print('</tr></table>'."\n");
print('</TD></TR>'."\n");
print('<hr>');
print('</TABLE>');
print('</div>'."\n");

include('pagefooter.inc');
?>
