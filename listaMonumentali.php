<?php
/*
 * Created on 20/lug/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");
//require_once('vincoliEtable.inc');
include('pageheader.inc');

$addButton = '<img src="graphics/add.png" width="21" height="20" vspace="1" border="0" align="absbottom" onclick="apriDlg(\'dlgAddVincoloMonumentale\');" style="cursor: pointer; margin-left: 10px;" title="Crea Nuovo Vincolo Monumentale">';

include('barraVincoliMonumentali.inc');

	$whereClause = ' where TRUE '.$whereClause;
	$sql = 'select distinct vin.vm_id, ' .
						'vin.vm_id as vincolo_id, ' .
						'vin.legge_id as vincolo_lex, ' .
						'pro.vm_id as vincolo_pro, ' .
						'vin.ordine as "Nr.Archivio", ' .
						'vin.oggetto as Oggetto,' .
						'com.provincia as PR, ' .
						'com.comune as Comune,' .
						'vin.localita as "Localit&agrave;", ' .
						'vin.numeri as Numeri, ' .
						'vin.collocazione as Collocazione, ' .
						'group_concat( distinct fog.foglio  SEPARATOR \',\') as Fogli, ' .
						'group_concat( distinct par.numero  SEPARATOR \',\') as Particelle ' .
//							'array_to_string(array(select distinct map3.foglio from vin__mappali as map3 where (map3.vm_id = vin.vm_id)), \',\') as Fogli, ' .
//							'array_to_string(array(select distinct map2.mappale from vin_mappali as map2 where (map2.vm_id = vin.vm_id)), \',\') as Mappali, ' .
//							'array_to_string(array(select distinct ana2.anagrafico from vin_anagrafici as ana2 where (ana2.vm_id = vin.vm_id)), \',\') as Anagrafici ' .
					'from vin_monumentali as vin ' .
					'left join vin_leggi as lex on (lex.legge_id = vin.legge_id) ' .
					'left join vin_proprietari as pro on (pro.vm_id = vin.vm_id) ' .
					'left join vin_fogli as fog on (fog.vm_id = vin.vm_id) ' .
					'left join vin_particelle as par on (par.foglio_id = fog.foglio_id) ' .
					'left join arc_comuni as com on (com.id = vin.comune) ' .
					'left join arc_province as prv on (prv.id=vin.prov) or (prv.sigla = com.provincia) ' .
					$whereClause .
					' group by vin.vm_id ';


	$vincoliTable=new vincoliHtmlETable($sql);

	if ($vincoliTable->getTableRows()>0) {
		$vincoliTable->setColSubstring('Note',20);
		$vincoliTable->SetColumnHeader('vincolo_id','<img src="graphics/page_edit.png" >');

			//$vincoliTable->SetColumnHref('SER_CODE','<a href="serviceManageRequest.php?STATUS=50&SER_ID=#SER_ID#" title="Attiva Intervento">');
			$vincoliTable->SetPageDivision(true);
//			$vincoliTable->SetColumnHref('Oggetto','<a href="editVincoloMonumentale.php?VM_ID=#vincolo_id#" title="Aggiorna i dati del Vincolo">');
			$vincoliTable->HideCol('vm_id');
			$wk_page = isSet($wk_page)?$wk_page:1;
			$vincoliTable->show($wk_page);
		}


include('pagefooter.inc');
?>