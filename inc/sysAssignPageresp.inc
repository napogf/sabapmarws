<?php
/*
 * Created on 29-set-06
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


if (!empty($buttapp)) {
	if (!is_null($RESPONSABILITY_ID) and !is_null($FUNCTION_ID)) {
		$RESPONSABILITY_ID=is_array($RESPONSABILITY_ID)?$RESPONSABILITY_ID:array($RESPONSABILITY_ID);
		for($i = 0; $i < sizeof($RESPONSABILITY_ID); $i++){
			$selected_resp_id.=$i==0?"'".$RESPONSABILITY_ID[$i]."'":", '".$RESPONSABILITY_ID[$i]."'";
		} // for
		// cancello quelli che non sono stati selezionati (quindi le relazioni non desiderate)
		dbupdate('delete from sys_pagfun_resp_ref where function_id = \''.$FUNCTION_ID.'\'');
	    for($i = 0; $i < sizeof($RESPONSABILITY_ID); $i++){
	    	$sql='insert into sys_pagfun_resp_ref (resp_id, function_id)
						                     values ( \''.$RESPONSABILITY_ID[$i].' \', \''.$FUNCTION_ID.'\')';
			dbupdate($sql);
	    } // for
	} else {
		dbupdate('delete from sys_pagfun_resp_ref where function_id = \''.$FUNCTION_ID.'\'');
	}
	 header("Location: sysManagePageFunctions.php?mode=modify&FUNCTION_ID=$FUNCTION_ID&dbKey=where function_id='$FUNCTION_ID'");
}

include('pageheader.inc');
print('<div class="formContainer">'."\n");

print('<TABLE width="100%">');
	print('<TR>');
	print('<TD class="DbFormTitle">');
	print(get_label('title',$_SERVER['PHP_SELF']));
	print('</TD>');
	print('</TR>'."\n");

	print('<FORM ACTION="'.$_SERVER['PHP_SELF'].'?mode='.$mode.'"  METHOD="POST" name="AssociatePagfunRespId">'."\n");
	// Campo chiave hidden USER_ID
	print('<input type="hidden" name="FUNCTION_ID" value="'.$FUNCTION_ID.'" >');
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
								   (case when smr.function_id is null then ''
								   		 when smr.function_id is not null then 'checked'
									end),'>') as assign
								  FROM (responsabilities, resp_lang_descriptions)
								 left join sys_pagfun_resp_ref smr on ((smr.resp_id=responsabilities.resp_id)
								        							AND (smr.function_id = '$FUNCTION_ID' ))
								 WHERE (    (responsabilities.resp_id = resp_lang_descriptions.resp_id)
								        AND (resp_lang_descriptions.language_id = '" .$_SESSION['sess_lang']. "')
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
