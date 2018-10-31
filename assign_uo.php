<?php
/*
 * Created on 22/lug/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";

if (!empty($_POST)) {
	if (!is_null($_POST['UOID']) and !is_null($_POST['USER_ID'])) {
		$_POST['UOID']=is_array($_POST['UOID'])?$_POST['UOID']:array($_POST['UOID']);
		for($i = 0; $i < sizeof($_POST['UOID']); $i++){
			$selected_uoid.=$i==0?"'".$_POST['UOID'][$i]."'":", '".$_POST['UOID'][$i]."'";
		} // for
		// cancello quelli che non sono stati selezionati (quindi le relazioni non desiderate)
		dbupdate('delete from user_uo_ref where user_id = \''.$_POST['USER_ID'].'\'');
	    for($i = 0; $i < sizeof($_POST['UOID']); $i++){
	    	$sql='insert into user_uo_ref (uoid, user_id)
						                     values ( \''.$_POST['UOID'][$i].' \', \''.$_POST['USER_ID'].'\')';
			dbupdate($sql);
	    } // for
	} else {
		dbupdate('delete from user_uo_ref where user_id = \''.$_POST['USER_ID'].'\'');
	}
	 header("Location: sys_manage_users.php?mode=modify&USER_ID=".$_POST['USER_ID']."&dbKey=where user_id='".$_POST['USER_ID']."'");
}

include('pageheader.inc');
?>

<TABLE width="100%">
<?php
	print('<TR>');
	print('<TD class="DbFormTitle">');
	print('Assegna Zone di Competenza');
	print('</TD>');
	print('</TR>'."\n");

	print('<FORM ACTION="'.$_SERVER['PHP_SELF'].'?mode='.$_GET['mode'].'"  METHOD="POST" name="AssociateUserUO">'."\n");
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

	$uo_query="SELECT arc_organizzazione.description as UO ,
								concat('<input type=\"checkbox\" name=\"UOID[]\" value=\"',arc_organizzazione.uoid,'\" ',
								   (case when urr.user_id is null then ''
								   		 when urr.user_id is not null then 'checked'
									end),'>') as assign
								  FROM arc_organizzazione
								 left join user_uo_ref urr on ((urr.uoid=arc_organizzazione.uoid)
								        							AND (urr.user_id = '".$_GET['USER_ID']."' ))";

	$zoneTable = new htmlTable($uo_query);
	$zoneTable->SetWidth('80%');
	$zoneTable->SetColumnHeader(0,'Unità Organizzativa');
	$zoneTable->SetColumnHeader(1,'Assegna');
	$zoneTable->show();

	print('</TD>');
	print('</TR>'."\n");


	print('<TR>');
	print('<TD>');
	MakeButtons('assign');
	print('</TD>');
	print('</TR>'."\n");
print('</TABLE>');
include('pagefooter.inc');
?>
