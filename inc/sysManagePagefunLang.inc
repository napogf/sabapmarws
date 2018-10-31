<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


if (!empty($buttapp)) {
    header("Location: sysManagePageFunctions.php?FUNCTION_ID=$FUNCTION_ID");
}

class MyDbForm extends formExtended {



}

$dbKey=' where FUNCTION_ID='.$FUNCTION_ID.' and LANGUAGE_ID='.$LANGUAGE_ID;

$ManagedTable = new MyDbForm('SYS_PAGES_FUNCTIONS_LABELS',$_SESSION['sess_lang']);

$del_message=get_label('del_message');



if (dbselect("select * from sys_pages_functions_labels $dbKey")){
	$ManagedTable->SetFormMode("modify",stripslashes($dbKey));
} else {
	// extract key to valorize for insert mode
	dbupdate('insert into sys_pages_functions_labels (function_id, description, language_id) ' .
			'select '.$FUNCTION_ID.', f.function_url, l.language_id from sys_pages_functions f, languages l ' .
					'where f.function_id = '.$FUNCTION_ID);
	$ManagedTable->SetFormMode("modify",stripslashes($dbKey));
}





include('pageheader.inc');
print('<div class="formContainer">'."\n");

$ManagedTable->ShowForm();

print('<hr>');
print('<table width="100%" border="0" cellspacing="0" cellpadding="0">');
print('<tr>');
print('<td valign="top" width="10" class="lista2" ><img src="graphics/back.gif" ></td>');
print('<td valign="middle" class="lista2" nowrap >&nbsp;&nbsp;<a href="sysManagePageFunctions.php?FUNCTION_ID='.$FUNCTION_ID.'" >'.get_label('go_back').'</a></td>');
print('<td valign="middle" width="100%" class="lista2" nowrap >&nbsp;&nbsp;</td>');
print('</tr></table>'."\n");
print('</TD></TR>'."\n");
print('<hr>');
print('</TABLE>');
print('</div>'."\n");

include('pagefooter.inc');
?>
