<?php
/*
 * Created on 29-set-06
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

if (!empty($_POST)) {
	if (!is_null($_POST['RESPONSABILITY_ID']) and !is_null($_POST['USER_ID'])) {
		$_POST['RESPONSABILITY_ID']=is_array($_POST['RESPONSABILITY_ID'])?$_POST['RESPONSABILITY_ID']:array($_POST['RESPONSABILITY_ID']);
		for($i = 0; $i < sizeof($_POST['RESPONSABILITY_ID']); $i++){
			$selected_resp_id.=$i==0?"'".$_POST['RESPONSABILITY_ID'][$i]."'":", '".$_POST['RESPONSABILITY_ID'][$i]."'";
		} // for
		// cancello quelli che non sono stati selezionati (quindi le relazioni non desiderate)
		dbupdate('delete from user_resp_reference where user_id = \''.$_POST['USER_ID'].'\'');
	    for($i = 0; $i < sizeof($_POST['RESPONSABILITY_ID']); $i++){
	    	$sql='insert into user_resp_reference (resp_id, user_id)
						                     values ( \''.$_POST['RESPONSABILITY_ID'][$i].' \', \''.$_POST['USER_ID'].'\')';
			dbupdate($sql);
	    } // for
	} else {
		dbupdate('delete from user_resp_reference where user_id = \''.$_POST['USER_ID'].'\'');
	}
	 header("Location: sys_manage_users.php?mode=modify&USER_ID=".$_POST['USER_ID']."&dbKey=where user_id=".$_POST['USER_ID']);
    exit;
}

include('pageheader.inc');
print('<div class="formContainer">'."\n");

?>

<TABLE width="100%">
<?php
	print('<TR>');
	print('<TD class="DbFormTitle">');
	print('Responsability Assignments');
	print('</TD>');
	print('</TR>'."\n");

	print('<FORM ACTION="'.$_SERVER['PHP_SELF'].'?mode='.$_GET['mode'].'"  METHOD="POST" name="AssociateUserRespId">'."\n");
	// Campo chiave hidden USER_ID
	print('<input type="hidden" name="USER_ID" value="'.$_GET['USER_ID'].'" >');
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

	$responsability_query="SELECT resp_lang_descriptions.description as resp,
								concat('<input type=\"checkbox\" name=\"RESPONSABILITY_ID[]\" value=\"',responsabilities.resp_id,'\" ',
								   (case when urr.user_id is null then ''
								   		 when urr.user_id is not null then 'checked'
									end),'>') as assign
								  FROM (responsabilities, resp_lang_descriptions)
								 left join user_resp_reference urr on ((urr.resp_id=responsabilities.resp_id)
								        							AND (urr.user_id = '".$_GET['USER_ID']."' ))
								 WHERE (    (responsabilities.resp_id = resp_lang_descriptions.resp_id)
								        AND (resp_lang_descriptions.language_id = '" . $_SESSION['sess_lang'] . "')
								       )";
// r($responsability_query);
	$selectTable = new htmlTable($responsability_query);
	$selectTable->SetWidth('80%');
	$selectTable->SetColumnHeader(0,get_label('responsability',$_SERVER['PHP_SELF']));
	$selectTable->SetColumnHeader(1,get_label('assign',$_SERVER['PHP_SELF']));

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
