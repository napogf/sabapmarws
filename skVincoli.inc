<?php
/*
 * Created on 02/lug/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
require_once("dbfunctions.php");
//require_once("Etable_c.inc");
//require_once("fdataentry.php");

//require_once('vincoliEtable.inc');

$vinPane=$_GET['tPane']=='vincoli'?' selected="true" ':'';
print ('<div dojoType="dijit.layout.ContentPane" title="Vincoli" '.$vinPane.' >');




// Vincoli Paesaggistici

$vincoliArray=array();
$paeQuery='select vincolo_id from arc_vincoli_pratiche where tipo = \'P\' and pratica_id ='.$this->_FormFields['PRATICA_ID']->GetValue();

if($vincoliResult=dbselect($paeQuery)){
	$token='';
	$whereClause = ' where amb.va_id in ( ';
	foreach ($vincoliResult['ROWS'] as $key => $value){
		$whereClause .= $token . $value['vincolo_id'];
		$token = ',';
	}
	$whereClause .= ') ';

	$paeSql = 'select distinct ' .
//			'amb.va_id, ' .
			'amb.oggetto as "Oggetto", ' .
			'lex.legge as "Legge", ' .
			'amb.decreto as "Decreto", ' .
			'amb.va_id as va_amb_del ' .
			'From vin_ambientali amb ' .
			'left join vin_leggi as lex on (lex.legge_id = amb.legge_id) ' .
			'left join arc_comuni as acom on (acom.id = amb.comune) ' .
			'left join arc_province as prv on (prv.id=amb.prov)  '
			.$whereClause;


	$vincoliPaessaggistici=new paesaggioHtmlETable($paeSql);
	if ($vincoliPaessaggistici->getTableRows()>0) {
			print('<div style="margin:10px;">');
			$vincoliPaessaggistici->SetTableCaption('Vincoli paesaggistici');
//			$vincoliPaessaggistici->hideCol('id');
			$vincoliPaessaggistici->show();
			print('</div>');
	}
} else {
		print('<div style="margin:10px;">');
		print('<h3>Non sono stati selezionati vincoli ambientali per l\'oggetto della pratica!</h3>');
		print('</div>');
}

// Vincoli Monumentali
$vincoliArray=array();
$monQuery='select vincolo_id from arc_vincoli_pratiche ' .
							'where tipo = \'M\' ' .
							'and pratica_id ='.$this->_FormFields['PRATICA_ID']->GetValue();
if($vincoliResult=dbselect($monQuery)){
	$token='';
	$whereClause = ' where vin.vm_id in ( ';
	foreach ($vincoliResult['ROWS'] as $key => $value){
		$whereClause .= $token . $value['vincolo_id'];
		$token = ',';
	}
	$whereClause .= ') ';


//  Vincoli Monumentali
	$sqlMonumentali = 'select distinct vin.vm_id , ' .
			'vin.vm_id as vincolo_id, ' .
			' lex.legge_id as vincolo_lex, ' .
			' pro.vm_id as vincolo_pro, ' .
			'vin.oggetto as "Oggetto", ' .
			'vin.numeri as "Numeri", ' .
			'vin.collocazione as "Collocazione", ' .
			'vin.note as "Note", ' .
			'com.comune  as "Comune", ' .
			'vin.localita  as "Localita", ' .
			'group_concat(distinct fog.foglio  SEPARATOR \',\') as Fogli, ' .
			'group_concat(distinct if(par.lettera > \'\', concat(par.numero,\'/\',par.lettera), par.numero)  SEPARATOR \',\') as Particelle, ' .
			'vin.vm_id as vincolo_id_del ' .
		'From vin_monumentali as vin ' .
					'left join vin_leggi as lex on (lex.legge_id = vin.legge_id) ' .
					'left join vin_proprietari as pro on (pro.vm_id = vin.vm_id) ' .
					'left join vin_fogli as fog on (fog.vm_id = vin.vm_id) ' .
					'left join vin_particelle as par on (par.foglio_id = fog.foglio_id) ' .
					'left join arc_comuni as com on (com.id = vin.comune) ' .
					'left join arc_province as prv on (prv.id=vin.prov) or (prv.sigla = com.provincia) ' .
			$whereClause. ' group by vin.vm_id';





	$vincoliTable=new vincoliHtmlETable($sqlMonumentali);

	if ($vincoliTable->getTableRows()>0) {
			print('<div style="margin:10px;">');
			$vincoliTable->SetTableCaption('Vincoli monumentali');
//			$vincoliTable->SetColumnHref('Oggetto','<a href="http://venezia.gis.beniculturali.it/schede/elencoVincoli/#id#" title="Visualizza dati GIS" target="_blank">');
			$vincoliTable->hideCol('vincolo_id');
			$vincoliTable->hideCol('vm_id');
			$vincoliTable->show();
			print('</div>');
	}

} else {
		print('<div style="margin:10px;">');
		print('<h3>Non sono stati selezionati vincoli monumentali per l\'oggetto della pratica!</h3>');
		print('</div>');
}



print('</div>');
?>
