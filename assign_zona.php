<?php
/*
 * Created on 22/lug/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("fdataentry.php");
//require_once("table_c.inc");

if (!empty($buttapp)) {
	if (!is_null($ZONA) and !is_null($USER_ID)) {
		$ZONA=is_array($ZONA)?$ZONA:array($ZONA);
		for($i = 0; $i < sizeof($ZONA); $i++){
			$selected_zona.=$i==0?"'".$ZONA[$i]."'":", '".$ZONA[$i]."'";
		} // for
		// cancello quelli che non sono stati selezionati (quindi le relazioni non desiderate)
		dbupdate('delete from user_zone_ref where user_id = \''.$USER_ID.'\'');
	    for($i = 0; $i < sizeof($ZONA); $i++){
	    	$sql='insert into user_zone_ref (zona, user_id)
						                     values ( \''.$ZONA[$i].' \', \''.$USER_ID.'\')';
			dbupdate($sql);
	    } // for
	} else {
		dbupdate('delete from user_zone_ref where user_id = \''.$USER_ID.'\'');
	}
	if (!is_null($UFFICIO) and !is_null($USER_ID)) {
		$UFFICIO=is_array($UFFICIO)?$UFFICIO:array($UFFICIO);
		for($i = 0; $i < sizeof($UFFICIO); $i++){
			$selected_ufficio.=$i==0?"'".$UFFICIO[$i]."'":", '".$UFFICIO[$i]."'";
		} // for
		// cancello quelli che non sono stati selezionati (quindi le relazioni non desiderate)
		dbupdate('delete from user_uffici_ref where user_id = \''.$USER_ID.'\'');
	    for($i = 0; $i < sizeof($UFFICIO); $i++){
	    	$sql='insert into user_uffici_ref (ufficio, user_id)
						                     values ( \''.$UFFICIO[$i].' \', \''.$USER_ID.'\')';
			dbupdate($sql);
	    } // for
	} else {
		dbupdate('delete from user_uffici_ref where user_id = \''.$USER_ID.'\'');
	}
	 header("Location: sys_manage_users.php?mode=modify&USER_ID=$USER_ID&dbKey=where user_id='$USER_ID'");
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

	print('<FORM ACTION="'.$PHP_SELF.'?mode='.$mode.'"  METHOD="POST" name="AssociateUserZone">'."\n");
	// Campo chiave hidden USER_ID
	print('<input type="hidden" name="USER_ID" value="'.$USER_ID.'" >');
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

	$zone_query="SELECT arc_zone.description as zona,
								concat('<input type=\"checkbox\" name=\"ZONA[]\" value=\"',arc_zone.zona,'\" ',
								   (case when urr.user_id is null then ''
								   		 when urr.user_id is not null then 'checked'
									end),'>') as assign
								  FROM arc_zone
								 left join user_zone_ref urr on ((urr.zona=arc_zone.zona)
								        							AND (urr.user_id = '$USER_ID' ))";

	$zoneTable = new htmlTable($zone_query);
	$zoneTable->SetWidth('80%');
	$zoneTable->SetColumnHeader(0,'Zona');
	$zoneTable->SetColumnHeader(1,'Assegna');
	$zoneTable->show();

	$uffici_query="SELECT arc_uffici.description as ufficio,
								concat('<input type=\"checkbox\" name=\"UFFICIO[]\" value=\"',arc_uffici.ufficio,'\" ',
								   (case when urr.user_id is null then ''
								   		 when urr.user_id is not null then 'checked'
									end),'>') as assign
								  FROM arc_uffici
								 left join user_uffici_ref urr on ((urr.ufficio=arc_uffici.ufficio)
								        							AND (urr.user_id = '$USER_ID' ))";

	$ufficiTable = new htmlTable($uffici_query);
	$ufficiTable->SetWidth('80%');
	$ufficiTable->SetColumnHeader(0,'Ufficio');
	$ufficiTable->SetColumnHeader(1,'Assegna');
	$ufficiTable->show();

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
