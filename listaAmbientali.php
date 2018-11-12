<?php
/*
 * Created on 30/ago/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");
//require_once('vincoliEtable.inc');
include('pageheader.inc');

$addButton = '<img src="graphics/add.png" width="21" height="20" vspace="1" border="0" align="absbottom" onclick="apriDlg(\'dlgAddVincoloAmbientale\');" style="cursor: pointer; margin-left: 10px;" title="Crea Nuovo Vincolo Ambientale">';

include('barraVincoliAmbientali.inc');

	$sql = 'SELECT ' .
					'amb.va_id as vincolo_amb_id, ' .
					'amb.codice, ' .
					'amb.progressivo, ' .
					'prov.sigla as "Prov.",' .
					'com.comune as Comune, ' .
					'localita as \'Localit&agrave;\', ' .
					'lex.legge as Legge, ' .
					'amb.decreto as Decreto, ' .
					'amb.oggetto as Oggetto, ' .
					'date_format(amb.data_decreto,\'%d-%m-%Y\') as \'Data Decreto\' , ' .
					'fonte_pubblicazione as \'Fonte pub.\', ' .
					'numero_pubblicazione as \'Nr. pub.\', ' .
					'note as Note, ' .
					'mappa ' .
				'from vin_ambientali amb ' .
					'left join arc_comuni as com on (com.id = amb.comune) ' .
					'left join arc_province as prov on (prov.sigla = com.provincia) ' .
					'left join vin_leggi as lex on (lex.legge_id = amb.legge_id) ' .
					$whereClause;



	$vincoliTable=new vincoliHtmlETable($sql);

	if ($vincoliTable->getTableRows()>0) {
		$vincoliTable->setColSubstring('Note',20);
		$vincoliTable->SetColumnHeader('vincolo_amb_id','<img src="graphics/page_edit.png" >');

			//$vincoliTable->SetColumnHref('SER_CODE','<a href="serviceManageRequest.php?STATUS=50&SER_ID=#SER_ID#" title="Attiva Intervento">');
			$vincoliTable->SetPageDivision(true);
//			$vincoliTable->SetColumnHref('Oggetto','<a href="editVincoloMonumentale.php?VM_ID=#vincolo_id#" title="Aggiorna i dati del Vincolo">');
//			$vincoliTable->HideCol('vm_id');
			$wk_page = isSet($wk_page)?$wk_page:1;
			$vincoliTable->show($wk_page);
		}




include('pagefooter.inc')
?>
