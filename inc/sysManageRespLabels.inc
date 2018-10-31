<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


if (!empty($buttapp)) {
    header("Location: sys_manage_resp.php?RESP_ID=$RESP_ID");
}

class MyDbForm extends formExtended {



}

$dbKey=' where RESP_ID='.$RESP_ID.' and LANGUAGE_ID='.$LANGUAGE_ID;

$ManagedTable = new MyDbForm('RESP_LANG_DESCRIPTIONS',$_SESSION['sess_lang']);

$del_message=get_label('del_message');



if (dbselect("select * from resp_lang_descriptions $dbKey")){
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





include('pageheader.inc');
print('<div class="formContainer">'."\n");

$ManagedTable->ShowForm();

print('<hr>');
print('<table width="100%" border="0" cellspacing="0" cellpadding="0">');
print('<tr>');
print('<td valign="top" width="10" class="lista2" ><img src="graphics/back.gif" ></td>');
print('<td valign="middle" class="lista2" nowrap >&nbsp;&nbsp;<a href="sys_manage_resp.php?RESP_ID='.$RESP_ID.'" >'.get_label('go_back').'</a></td>');
print('<td valign="middle" width="100%" class="lista2" nowrap >&nbsp;&nbsp;</td>');
print('</tr></table>'."\n");
print('</TD></TR>'."\n");
print('<hr>');
print('</TABLE>');
print('</div>'."\n");

include('pagefooter.inc');
?>
