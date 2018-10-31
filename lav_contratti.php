<?php
/*
 * Created on 19/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
if(!($_GET['QECONOMICO_ID']>'')){
	print('<h2>Seleziona una voce del quadro economico</h2>');
	exit;
}
$periziaResult=dbselect('select distinct lp.NR_PERIZIA, ' .
								'date_format(lp.DATA_PERIZIA, \'%d-%m-%Y\') as DATA_PERIZIA, ' .
								'lq.DESCRIZIONE_LAVORI  ' .
								'from lav_perizie as lp ' .
								'left join lav_quadro_economico as lq on (lq.perizia_id = lp.perizia_id) ' .
								'where lq.QECONOMICO_ID='.$_GET['QECONOMICO_ID']);


if($periziaResult['NROWS']>0) print ('<div style="background-color: azure; font-size: 1.5em;">' .
										$periziaResult['ROWS'][0]['NR_PERIZIA'] . ' '.
										$periziaResult['ROWS'][0]['DATA_PERIZIA'] . ' - '.
										$periziaResult['ROWS'][0]['DESCRIZIONE_LAVORI'] . ' '.
									'</div>' . "\n");

print ('<div dojoType="dijit.layout.ContentPane" ' .
		'id="listaContratti" ' .
		'style="border: none;" ' .
		'onLoad="setRowClassCo(contrattoId);" ' .
		'href="lav_listaContratti.php?QECONOMICO_ID='.$_GET['QECONOMICO_ID'].'"' .
		'>');

print('</div>');
print ('<div dojoType="dijit.layout.ContentPane" ' .
			'id="addContratti" ' .
			'style="display: none;" ' .
			'href="lav_djFormContratti.php?QECONOMICO_ID='.$_GET['QECONOMICO_ID'].'" >');

print('</div>');
print ('<div dojoType="dijit.layout.ContentPane" ' .
			'id="editContratti" ' .
			'style="display: none;" ' .
			'href="" >');
print('</div>');





?>
