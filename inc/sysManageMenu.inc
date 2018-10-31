<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

if ($_GET['mode']=='delete') {
	if (isset($_GET['MENU_ID']) and dbupdate('delete from sys_menu where menu_id ='.$_GET['MENU_ID'])) {
		dbupdate('delete from sys_menu_labels where menu_id ='.$_GET['MENU_ID']);
		dbupdate('delete from sys_menu_resp_ref where menu_id='.$_GET['MENU_ID']);
	}
	header('Location: ' . $_SERVER['PHP_SELF'] . '?menu_id=' . $_GET['menu_id']);
}

if ($mode <> "insert" and IsSet($_GET['MENU_ID']) and !is_null($_GET['MENU_ID']) and strlen(trim($_GET['MENU_ID']))>0 ) {
    $dbKey=' where MENU_ID='.$_GET['MENU_ID'];
	$_GET['mode']='modify';
}

class MyDbForm extends formExtended {

			function FormPostValidation(){
				if ($_POST['DESCRIPTION']>'') {
					if ($this->GetFormMode()=='insert') {
						$last_menu_id=$this->GetLastInsertId();
						if (!dbupdate('insert into sys_menu_labels (menu_id, description, language_id)
														select '.$last_menu_id.', \''.$_POST['DESCRIPTION'].'\', lan.language_id from languages lan')) {
							errore('Labels non inserite per menu '.$last_menu_id);
						}
						if (!dbupdate('insert into sys_menu_resp_ref (resp_id, menu_id) values (1, '.$last_menu_id.'), (31,'.$last_menu_id.');')) {
							errore('Responsability non inserite per menu '.$last_menu_id);
						}
					}

				} else {
					errore('Menu '.$last_menu_id.' Description '.$_POST['DESCRIPTION']);
				}
				return TRUE;
			}

}

$ManagedTable = new MyDbForm('SYS_MENU',$_SESSION['sess_lang']);

$del_message=get_label('del_message');


$ManagedTable->SetFormMode($_GET['mode'],stripslashes($_GET['dbKey']));
$ManagedTable->setAfterInsertLocation(true);
include("pageheader.inc");
print('<div class="formContainer">'."\n");

$ManagedTable->ShowForm();

if ($ManagedTable->GetFormMode()=="modify") {
	print('<hr>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>'."\n");
	print('<td valign="top" width="10" class="lista2" ><img src="graphics/merge_join.gif" ></td>'."\n");
	print('<td valign="middle" class="lista2" nowrap >&nbsp;&nbsp;<a href="sys_assign_menuresp.php?MENU_ID='.$ManagedTable->GetFormFieldValue('MENU_ID').'" >'.get_label('assign_responsability').'</a></td>'."\n");
	$languages_result=dbselect("select LANGUAGE_ID, DESCRIPTION, FLAG_GIF from languages");
	print('<td valign="top" class="lista2" nowrap width="25" align="center" >&nbsp;</td>');
	if ($languages_result['NROWS']>0) {
		for($i = 0; $i < $languages_result['NROWS']; $i++){
			print('<td valign="middle" class="lista2" nowrap width="50" align="center" >
					<img src="'.$languages_result['ROWS'][$i]['FLAG_GIF'].'" alt="'.get_label('manage_languages').' - '.$languages_result['ROWS'][$i]['DESCRIPTION'].'" STYLE="cursor: pointer"
					onclick="javascript:location.href=\'sys_manage_menu_lang.php?mode=modify&MENU_ID='.$ManagedTable->GetFormFieldValue('MENU_ID').'&LANGUAGE_ID='.$languages_result['ROWS'][$i]['LANGUAGE_ID'].'\';"></td>'."\n");
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

		$selectMenuQuery="select CONCAT('<a href=\"".$_SERVER['PHP_SELF']."?mode=modify&dbKey=where MENU_ID=\'',menu.menu_id,'\'\">',lbl.description,'</a>') as ".'Menu'.",
									menu.menu_id as Id,
									menu.menu_sequence as seq,
									lbl.alt_tag as Description,
									menu.menu_name as Type,
									concat('<center><img src=\"graphics/webapp/deleted.gif\" STYLE=\"cursor: pointer\" onClick=\"submit_del(\'".$_SERVER['PHP_SELF']."?menu_id=" . $_GET['menu_id'] . "&mode=delete&MENU_ID=',menu.MENU_ID,'\',\'$del_message\')\"></center>') AS cancel
									From sys_menu menu
									left join sys_menu_labels lbl on ((lbl.menu_id = menu.menu_id)
																and (lbl.language_id = '" .$_SESSION['sess_lang']. "'))
									Order by menu.menu_name, menu.menu_sequence	";

		$selectTable = new htmlTable($selectMenuQuery);

		$selectTable->SetColumnHeader(0,'Menu');
		$selectTable->SetColumnHeader(1,'Id');
		$selectTable->SetColumnHeader(2,'Seq');
		$selectTable->SetColumnHeader(3,'Description');
		$selectTable->SetColumnHeader(4,'Type');
		$selectTable->SetColumnHeader(5,'#');

		$selectTable->SetWidth('80%');
		$selectTable->show();
print('</div>'."\n");

include("pagefooter.inc");

?>
