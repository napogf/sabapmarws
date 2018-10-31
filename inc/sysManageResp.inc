<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


if ($mode=='delete') {
	if (isset($_GET['RESP_ID']) and dbupdate('delete from sys_responsabilities where resp_id ='.$_GET['RESP_ID'])) {
		dbupdate('delete from sys_resp_lang_descriptions where resp_id ='.$_GET['RESP_ID']);
	}
	unset($_GET['mode']);
	unset($_GET['RESP_ID']);
}


if ($mode <> "insert" and IsSet($_GET['RESP_ID']) and !is_null($_GET['RESP_ID']) and strlen(trim($_GET['RESP_ID']))>0 ) {
    $dbKey=' where RESP_ID='.$_GET['RESP_ID'];
	$mode='modify';
}

class MyDbForm extends formExtended {

			function FormPostValidation(){
				if ($_POST['DESCRIPTION']>'') {
					if ($this->GetFormMode()=='insert') {
						$last_resp_id=$this->GetLastInsertId();
						if (!dbupdate('insert into sys_resp_lang_descriptions (resp_id, description, language_id)
														select '.$last_resp_id.', \''.$_POST['DESCRIPTION'].'\', lan.language_id from sys_languages lan')) {
							errore('Labels non inserite per menu '.$last_resp_id);
						}
						if (!dbupdate('insert into sys_menu_resp_ref (resp_id, menu_id) values (1, '.$last_resp_id.')')) {
							errore('Responsability non inserite per menu '.$last_resp_id);
						}
					}

				} else {
					errore('Responsability '.$last_resp_id.' Description '.$_POST['DESCRIPTION']);
				}
				return TRUE;
			}

}

$ManagedTable = new MyDbForm('RESPONSABILITIES',$_SESSION['sess_lang']);

$del_message=get_label('del_message');

$ManagedTable->SetFormMode($_GET['mode'],stripslashes($_GET['dbKey']));

include("pageheader.inc");
print('<div class="formContainer">'."\n");
$ManagedTable->ShowForm();

if ($ManagedTable->GetFormMode()=="modify") {
	print('<hr>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>'."\n");
//	print('<td valign="top" width="10" class="lista2" ><img src="graphics/merge_join.gif" ></td>'."\n");
//	print('<td valign="middle" class="lista2" nowrap >&nbsp;&nbsp;<a href="assign_menuresp.php?RESP_ID='.$ManagedTable->GetFormFieldValue('RESP_ID').'" >'.get_label('assign_responsability').'</a></td>'."\n");
	$languages_result=dbselect("select LANGUAGE_ID, DESCRIPTION, FLAG_GIF from languages");
	print('<td valign="top" class="lista2" nowrap width="25" align="center" >&nbsp;</td>');
	if ($languages_result['NROWS']>0) {
		for($i = 0; $i < $languages_result['NROWS']; $i++){
			print('<td valign="middle" class="lista2" nowrap width="50" align="center" >
					<img src="'.$languages_result['ROWS'][$i]['FLAG_GIF'].'" alt="'.get_label('manage_languages').' - '.$languages_result['ROWS'][$i]['DESCRIPTION'].'" STYLE="cursor: pointer"
					onclick="javascript:location.href=\'sys_manage_resp_lang.php?mode=modify&RESP_ID='.$ManagedTable->GetFormFieldValue('RESP_ID').'&LANGUAGE_ID='.$languages_result['ROWS'][$i]['LANGUAGE_ID'].'\';"></td>'."\n");
		} // for

	}

	print('<td valign="middle" width="100%" class="lista2" nowrap >&nbsp;&nbsp;</td>'."\n");
	print('</tr></table>'."\n");
}

?>


<hr>

<TABLE width="100%">
	<TR><TD class="TableTitle" >
<?php print(get_label('selectkey'));?>
	</TD></TR>
	<TR><TD align="center">

<?php

		$selectMenuQuery="select CONCAT('<a href=\"".$_SERVER['PHP_SELF']."?mode=modify&dbKey=where RESP_ID=\'',resp.resp_id,'\'\">',lbl.description,'</a>') as ".'Responsability'.",
									resp.resp_id as Id,
									lbl.description as Description,
									concat('<center><img src=\"graphics/webapp/deleted.gif\" STYLE=\"cursor: pointer\" onClick=\"submit_del(\'".$_SERVER['PHP_SELF']."?mode=delete&RESP_ID=',resp.RESP_ID,'\',\'$del_message\')\"></center>') AS cancel
									From sys_responsabilities resp
									left join sys_resp_lang_descriptions lbl on ((lbl.resp_id = resp.resp_id)
																and (lbl.language_id = '".$_SESSION['sess_lang']."'))
									Order by lbl.description ";

		$selectTable = new htmlTable($selectMenuQuery);
		$selectTable->SetWidth('80%');
		$selectTable->show();
print('</div>'."\n");
include("pagefooter.inc");

?>
