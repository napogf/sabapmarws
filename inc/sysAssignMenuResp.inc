<?php
/*
 * Created on 29-set-06
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


if (!empty($_POST)) {
	if (!is_null($_POST['RESPONSABILITY_ID']) and !is_null($_POST['MENU_ID'])) {
		$_POST['RESPONSABILITY_ID']=is_array($_POST['RESPONSABILITY_ID'])?$_POST['RESPONSABILITY_ID']:array($_POST['RESPONSABILITY_ID']);
		for($i = 0; $i < sizeof($_POST['RESPONSABILITY_ID']); $i++){
			$selected_resp_id.=$i==0?"'".$_POST['RESPONSABILITY_ID'][$i]."'":", '".$_POST['RESPONSABILITY_ID'][$i]."'";
		} // for
		// cancello quelli che non sono stati selezionati (quindi le relazioni non desiderate)
		dbupdate('delete from sys_menu_resp_ref where menu_id = \''.$_POST['MENU_ID'].'\'');
	    for($i = 0; $i < sizeof($_POST['RESPONSABILITY_ID']); $i++){
	    	$sql='insert into sys_menu_resp_ref (resp_id, menu_id)
						                     values ( \''.$_POST['RESPONSABILITY_ID'][$i].' \', \''.$_POST['MENU_ID'].'\')';
			dbupdate($sql);
	    } // for
	} else {
		dbupdate('delete from sys_menu_resp_ref where menu_id = \''.$_POST['MENU_ID'].'\'');
	}
	 header("Location: sys_manage_menu.php?mode=modify&MENU_ID=".$_POST['MENU_ID']."&dbKey=where menu_id=".$_POST['MENU_ID']);
}

include('pageheader.inc');
print('<div class="formContainer">'."\n");
print('<TABLE width="100%">');
	print('<TR>');
	print('<TD class="DbFormTitle">');
	print('Assign Responsability');
	print('</TD>');
	print('</TR>'."\n");

	print('<FORM ACTION="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'"  METHOD="POST" name="AssociateMenuRespId">'."\n");
	// Campo chiave hidden USER_ID
	print('<input type="hidden" name="MENU_ID" value="'.$_GET['MENU_ID'].'" >');
	print('<TR>');
	print('<TD class="DbFormMessage">');
	print($menu_name);
	print('&nbsp;</TD>');
	print('</TR>'."\n");

	print('<TR>');
	print('<TD>');
	MakeButtons('assign');
	print('</TD>');
	print('</TR>'."\n");

	print('<TR>');
	print('<TD align="center" >');

	$responsability_query="SELECT sys_resp_lang_descriptions.description as resp,
								concat('<input type=\"checkbox\" name=\"RESPONSABILITY_ID[]\" value=\"',sys_responsabilities.resp_id,'\" ',
								   (case when smr.menu_id is null then ''
								   		 when smr.menu_id is not null then 'checked'
									end),'>') as assign
								  FROM (sys_responsabilities, sys_resp_lang_descriptions)
								 left join sys_menu_resp_ref smr on ((smr.resp_id=sys_responsabilities.resp_id)
								        							AND (smr.menu_id = '".$_GET['MENU_ID']."' ))
								 WHERE (    (sys_responsabilities.resp_id = sys_resp_lang_descriptions.resp_id)
								        AND (sys_resp_lang_descriptions.language_id = '".$_SESSION['sess_lang']."')
								       )";


	$selectTable = new htmlTable($responsability_query);
	$selectTable->SetWidth('80%');
	$selectTable->SetColumnHeader(0,'responsability');
	$selectTable->SetColumnHeader(1,'assign');

	$selectTable->show();

	print('</TD>');
	print('</TR>'."\n");


	print('<TR>');
	print('<TD>');
	MakeButtons('assign');
	print('</TD>');
	print('</TR>'."\n");
print('</TABLE>');
print('</div>'."\n");
include('pagefooter.inc');
?>
