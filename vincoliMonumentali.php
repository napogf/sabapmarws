<?php
/*
 * Created on 01/lug/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");
//require_once("fdataentry.php");
//require_once 'MDB2.php';
//require_once('vincoliEtable.inc');

if (!empty($buttapp)) {
	if (!is_null($_GET['VINCOLO_ID']) and !is_null($_GET['PRATICA_ID'])) {
		if (is_array($_GET['VINCOLO_ID'])){
			foreach ($_GET['VINCOLO_ID'] as $vincolo){
				if (!dbselect('select vincolo_id from arc_vincoli_pratiche where tipo = \'M\' and pratica_id = '.$_GET['PRATICA_ID'].' and vincolo_id='.$vincolo)){
					$insSql = 'insert into arc_vincoli_pratiche (pratica_id, vincolo_id, tipo ) values ' .
																'(' .
																'\''.$_GET['PRATICA_ID'].'\', ' .
																'\''.$vincolo.'\', ' .
																		'\'M\' )';
					dbupdate($insSql);
				}
			}
		} else {
			if (!dbselect('select vincolo_id from arc_vincoli_pratiche where tipo ? \'M\' and pratica_id = '.$_GET['PRATICA_ID'].' and vincolo_id='.$_GET['VINCOLO_ID'])){
				$insSql = 'insert into arc_vincoli_pratiche (pratica_id, vincolo_id, tipo ) values ' .
															'(' .
															'\''.$_GET['PRATICA_ID'].'\', ' .
															'\''.$_GET['VINCOLO_ID'].'\', ' .
																	'\'M\' )';
				dbupdate($insSql);
			}

		}
	}
  header("Location: editPratica.php?PRATICA_ID=$PRATICA_ID");
}


include('pageheader.inc');
		$praticaResult=dbselect('select pr.numeroregistrazione, ' .
					'date_format(pr.dataregistrazione,\'%d-%m-%Y\') as "dataregistrazione", ' .
					'pr.oggetto,' .
					'az.description as zonaDesc from ' .
					'pratiche pr ' .
					'left join arc_zone az on (az.zona = pr.zona) ' .
					'where pratica_id = ' . $PRATICA_ID);

		$formTitle='<span id="oggettoEspi" style="cursor: pointer" >Nr Reg.: ' . $praticaResult['ROWS'][0]['numeroregistrazione'] .
					' - Data Reg.: ' . $praticaResult['ROWS'][0]['dataregistrazione'] . ' - Zona: '.
					$praticaResult['ROWS'][0]['zonaDesc'] .'</span>';
		$formTitle .= '<span dojoType="dijit.Tooltip" id ="ttOggettoEspi" connectId="oggettoEspi" style="display:none;"><div class="djToolTipContainer" >'.
					$praticaResult['ROWS'][0]['oggetto'].'</div></span>';


		print ('<div style="background-color: azure; font-size: 1.5em; margin-top:20px; margin-bottom:5px;">' .
		'<span>' .
		$formTitle .
		'</span><span style="float: right; margin-right:10px;"><a href="editPratica.php?PRATICA_ID='.$PRATICA_ID.'" ><< Torna alla pratica</a></div>'.
		'' . "\n");

include('barraVincoliMonumentali.inc');
//$whereClause = '';
//foreach ($_GET as $key => $value){
//	switch ($key) {
//		case 'SIGLA':
//			$whereClause .= $value == '' ? '' : ' and (prv.id = \''.$value.'\') ';
//			break;
//		case 'COMUNE':
//			$whereClause .= $value == '' ? '' : ' and (com.id = \''.$value.'\') ';
//			break;
//		case 'keyword':
//			$whereClause .= $value == '' ? '' : ' and (vin.oggetto similar to \''.$value.'\') ';
//			break;
//		case 'anaFilter':
//			$whereClause .= $value == '' ? '' : ' and (par.numero regexp \''.$value.'\') ';
//			break;
//		case 'foglioFilter':
//			$whereClause .= $value == '' ? '' : ' and (fog.foglio regexp \''.$value.'\') ';
//			break;
////		case 'mappaleFilter':
////			$whereClause .= $value == '' ? '' : ' and (map.mappale regexp \''.$value.'\') ';
////			break;
//	}
//}

if($_GET['mode']=='search' or $whereClause > ''){
	$whereClause = ' where TRUE '.$whereClause;
	$sql = 'select distinct vin.vm_id , ' .
			'vin.vm_id as vincolo_id, ' .
			'lex.legge_id as vincolo_lex, ' .
			'pro.vm_id as vincolo_pro, ' .
			'vin.oggetto as "Oggetto", ' .
			'vin.numeri as "Numeri", ' .
			'vin.collocazione as "Collocazione", ' .
			'vin.note as "Note", ' .
			'com.comune  as "Comune", ' .
			'vin.localita  as "Localita", ' .
			'group_concat(distinct fog.foglio  SEPARATOR \',\') as Fogli, ' .
			'group_concat(distinct if(par.lettera > \'\', concat(par.numero,\'/\',par.lettera), par.numero)  SEPARATOR \',\') as Particelle ' .
		'From vin_monumentali as vin ' .
					'left join vin_leggi as lex on (lex.legge_id = vin.legge_id) ' .
					'left join vin_proprietari as pro on (pro.vm_id = vin.vm_id) ' .
					'left join vin_fogli as fog on (fog.vm_id = vin.vm_id) ' .
					'left join vin_particelle as par on (par.foglio_id = fog.foglio_id) ' .
					'left join arc_comuni as com on (com.id = vin.comune) ' .
					'left join arc_province as prv on (prv.id=vin.prov) or (prv.sigla = com.provincia) ' .
			$whereClause. ' group by vin.vm_id ';


	$vincoliTable=new vincoliHtmlETable($sql);
	if ($vincoliTable->getTableRows()>0) {
		$vincoliTable->setColSubstring('Note',20);
		print('<FORM ACTION="'.$PHP_SELF.'?mode='.$_GET['mode'].'"  METHOD="GET" name="AssociateMenuRespId">'."\n");
		print('<input type="hidden" name="PRATICA_ID" value="'.$PRATICA_ID.'" >');

		MakeButtons('assign');
			//$vincoliTable->SetColumnHref('SER_CODE','<a href="serviceManageRequest.php?STATUS=50&SER_ID=#SER_ID#" title="Attiva Intervento">');
	//		$vincoliTable->SetPageDivision(true);
//			$vincoliTable->SetColumnHref('Oggetto','<a href="get_file.php?f=#mappa#" title="Visualizza Mappa" target="_blank">');
			$vincoliTable->HideCol('vincolo_id');
			$vincoliTable->show();

		MakeButtons('assign');

		print('</FORM>'."\n");
	}



//} else {
//
//	$vincoliQuery='select distinct ' .
//						'concat(\'<span ><img src="graphics/close.gif" style="cursor: pointer" title="Rimuovi vincolo" onclick="rimuoviVincolo(\',pr.PRATICA_ID,\')" ></span>\') Rimuovi,' .
//						'av.denominazione as Vincolo,  ' .
//						'av.comune as Comune,  ' .
//						'av.localita as "Loc.",  ' .
//						'av.provincia as PR,  ' .
//						'av.fogliocatastale as Foglio,  ' .
//						'concat(\'<span id="vinc\',av.vincolo_id,\'">\',substr(av.particelle,1,10),\'</span>' .
//									'<span dojoType="dijit.Tooltip" id="ttVinc\',av.vincolo_id,\'" connectId="vinc\',av.vincolo_id,\'" style="display:none;">' .
//									'<div class="djToolTipContainer" >\',av.particelle,\'</div></span>\') ' .
//						'as "Particelle", ' .
//						'trim(concat(av.ubicazioneinit,\' \',av.ubicazioneprinc)) as Indirizzo,' .
//						'av.vincolodiretto as D,  ' .
//						'av.vincoloindiretto as I,  ' .
//						'av.provvedimentoministeriale as "Provv.Min.",  ' .
//						'av.trascrizioneinconservatoria as "Tras.Cons.",  ' .
//						'av.posizioneMonumentale as "Pos.Mon.",  ' .
//						'av.posizioneVincoli as "Pos.Vinc."' .
//						'from vincoli av ' .
//						'right join pratiche pr on (pr.VINCOLO_ID = av.vincolo_id) ' .
//						'where (pr.pratica_id='.$PRATICA_ID.') and (av.vincolo_id is not null)' ;
////						var_dump($vincoliQuery);
//	$vincoliTable=new HtmlETable($vincoliQuery);
//	if ($vincoliTable->getTableRows()>0) {
//
//		$vincoliTable->show();
//	}



}
print('</div>');
include('pagefooter.inc')
?>