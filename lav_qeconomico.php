<?php
/*
 * Created on 19/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
if(!($_GET['PERIZIA_ID']>'')){
	print('<h2>Seleziona una Perizia</h2>');
	exit;
}
$periziaResult=dbselect('select NR_PERIZIA, ' .
								'date_format(DATA_PERIZIA, \'%d-%m-%Y\') as DATA_PERIZIA, ' .
								'DENOMINAZIONE_ISTITUTO ' .
								'from lav_perizie where PERIZIA_ID='.$_GET['PERIZIA_ID']);
if($periziaResult['NROWS']>0) print ('<div style="background-color: azure; font-size: 1.5em;">' .
										$periziaResult['ROWS'][0]['NR_PERIZIA'] . ' '.
										$periziaResult['ROWS'][0]['DATA_PERIZIA'] . ' '.
										$periziaResult['ROWS'][0]['DENOMINAZIONE_ISTITUTO'] . ' '.
									'</div>' . "\n");

print ('<div dojoType="dijit.layout.ContentPane" ' .
		'id="listaQeconomico" ' .
		'style="border: none;" ' .
		'onLoad="setRowClassQe(qeconomicoId);" ' .
		'href="lav_listaQeconomico.php?PERIZIA_ID='.$_GET['PERIZIA_ID'].'"' .
		'>');


print('</div>');
print ('<div dojoType="dijit.layout.ContentPane" ' .
			'id="addQeconomico" ' .
			'style="display: none;" ' .
			'href="lav_djFormQeconomico.php?PERIZIA_ID='.$_GET['PERIZIA_ID'].'" >');

print('</div>');
print ('<div dojoType="dijit.layout.ContentPane" ' .
			'id="editQeconomico" ' .
			'style="display: none;" ' .
			'href="" >');
print('</div>');





?>
