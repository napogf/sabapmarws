<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


$dbTable='SYS_PAGES_FUNCTIONS';

if ($mode=='delete' and $dbKey > '') {
	if (isset($FUNCTION_ID) and dbupdate('delete from sys_pages_functions '.$dbKey)) {
		dbupdate('delete from sys_pages_functions_labels '.$dbKey);
		dbupdate('delete from sys_pagfun_resp_ref '.$dbKey);
	}
	unset($mode);
	unset($MENU_ID);
}
$recallPage='?dbTable=SYS_PAGES_FUNCTIONS';

class MyDbForm extends formExtended {

			function FormPostValidation(){
				$last_function_id=$this->GetLastInsertId();
				if ($_POST['FUNCTION_URL']>'') {
					if ($this->GetFormMode()=='insert') {
						if (!dbupdate('insert into sys_pages_functions_labels (function_id, description, language_id)
														select '.$last_function_id.', \''.$_POST['FUNCTION_URL'].'\', lan.language_id from languages lan')) {
							errore('Labels non inserite per menu '.$last_function_id);
						}
						if (!dbupdate('insert into sys_pagfun_resp_ref (resp_id, function_id) values (1, '.$last_function_id.')')) {
							errore('Responsability non inserite per la Pagina '.$last_function_id);
						}
					}

				} else {
					errore('Page Function '.$last_function_id.' Url '.$_POST['FUNCTION_URL']);
				}
				return TRUE;
			}
}
include ("pageheader.inc");
print('<div class="formContainer">'."\n");

$xlsBar='N';
$backAfterInsert=true;

$midToolBar = new toolBar();


	$leftLink= '<table width="100%" border="0" cellspacing="0" cellpadding="0">' .
			   '<tr><td valign="top" width="10" class="lista2" ><img src="graphics/merge_join.gif" ></td>' .
  			   '<td valign="middle" class="lista2" nowrap >&nbsp;&nbsp;<a href="sysAssignPagresp.php?FUNCTION_ID='.
  			   $FUNCTION_ID.'" >'.get_label('assign_responsability').'</a></td>'."\n";
	$languages_result=dbselect("select LANGUAGE_ID, DESCRIPTION, FLAG_GIF from languages");
	$leftLink.='<td valign="top" class="lista2" nowrap width="25" align="center" >&nbsp;</td>';
	if ($languages_result['NROWS']>0) {
		for($i = 0; $i < $languages_result['NROWS']; $i++){
			$leftLink.='<td valign="middle" class="lista2" nowrap width="50" align="center" >
					<img src="'.$languages_result['ROWS'][$i]['FLAG_GIF'].'" alt="'.get_label('manage_languages').' - '.$languages_result['ROWS'][$i]['DESCRIPTION'].'" STYLE="cursor: pointer"
					onclick="javascript:location.href=\'sysManagePagefunLang.php?mode=modify&FUNCTION_ID='.$FUNCTION_ID.'&LANGUAGE_ID='.$languages_result['ROWS'][$i]['LANGUAGE_ID'].'\';"></td>'."\n";
		} // for

	}

	$leftLink.='</tr></table>'."\n";



	$midToolBar->SetLeftLinks($leftLink,'modify');

//	$midToolBar->SetLeftLinks('<img src="graphics/garbage.png" ' .
//							'STYLE="cursor: pointer; padding-left: 20; padding-right: 5;" ' .
//							'onclick="location.href=\'authWaste.php?TIPO=SMA&RAUTO_ID='.$RAUTO_ID.'\'" title="'.
//							get_label('authWaste').'" >'.'&nbsp;'.get_label('authWaste').'&nbsp;','modify');
//
//	$midToolBar->SetCenterLinks($tableTitle);

include ("manageDbtable.inc");
print('</div>'."\n");

include ("pagefooter.inc");
?>