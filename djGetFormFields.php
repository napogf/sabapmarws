<?php
/*
 * Created on 13/ott/09
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
			$sql = 'select distinct sf.FORM_ID as ID, ' .
							' sf.FORM_NAME as NAME, ' .
							' sft.TITLE ' .
						'from sys_forms sf ' .
						'left join sys_forms_titles sft on ((sft.form_id = sf.form_id) and ' .
															'(sft.language_id = '.$_SESSION['sess_lang'].')) ' .
						'order by 2';

		    $formResult=dbselect($sql);

header('Content-type: application/json');
echo '{ identifier : "ID" , label : "NAME" , type : "TYPE", items : [
{ ID: "0", NAME: "Forms", TYPE: "root", children: [' ;
$commaTag='';
for ($index = 0; $index < $formResult['NROWS']; $index++) {
	print($commaTag.'{ "ID": "'.$formResult['ROWS'][$index]['ID'].'",'."\n" );
	print(' "NAME": "'.$formResult['ROWS'][$index]['NAME'].'",'."\n" );
	print(' "TITLE": "'.$formResult['ROWS'][$index]['TITLE'].'",'."\n" );
	print(' "TYPE": "form",'."\n" );
	print('children: '."\n");
	$fieldResults=dbselect('select FIELD_ID as ID , FIELD_NAME as NAME , \'field\' as TYPE, DATA_TYPE ' .
						'from sys_forms_fields where form_id = '.$formResult['ROWS'][$index]['ID'].' order by vseq, hseq');
	echo json_encode($fieldResults['ROWS'])."\n";
	$commaTag='},';
}
echo '}' .
		']}
	]}';
?>