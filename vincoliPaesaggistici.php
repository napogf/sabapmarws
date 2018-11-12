<?php
/*
 * Created on 01/lug/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("Etable_c.inc");
//require_once("fdataentry.php");



if (!empty($_GET['buttapp'])) {
	if (!is_null($_GET['VINCOLO_ID']) and !is_null($_GET['PRATICA_ID'])) {
		if (is_array($_GET['VINCOLO_ID'])){
			foreach ($_GET['VINCOLO_ID'] as $vincolo){
				$vinAssResult=dbselect('select vincolo_id from arc_vincoli_pratiche where tipo = \'P\' and pratica_id = '.$_GET['PRATICA_ID'].' and vincolo_id='.$vincolo);
				if (!$vinAssResult){
					$insSql = 'insert into arc_vincoli_pratiche (pratica_id, vincolo_id, tipo ) values ' .
																'(' .
																'\''.$_GET['PRATICA_ID'].'\', ' .
																'\''.$vincolo.'\', ' .
																		'\'P\' )';
					dbupdate($insSql);
				}
			}
		} else {
			$vinAssResult=dbselect('select vincolo_id from arc_vincoli_pratiche where tipo ? \'P\' and pratica_id = '.$_GET['PRATICA_ID'].' and vincolo_id='.$_GET['VINCOLO_ID']);
			if (!$vinAssResult){
				$insSql = 'insert into arc_vincoli_pratiche (pratica_id, vincolo_id, tipo ) values ' .
															'(' .
															'\''.$_GET['PRATICA_ID'].'\', ' .
															'\''.$_GET['VINCOLO_ID'].'\', ' .
																	'\'P\' )';
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
					'where pratica_id = ' . $_GET['PRATICA_ID']);

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

		// Comuni
include('barraVincoliAmbientali.inc');


if($_GET['mode']=='search' or $whereClause > ''){

	$whereClause = ' where TRUE '.$whereClause;

	$sql = 'SELECT ' .
					'amb.va_id, ' .
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




//var_dump($sql);

	$vincoliTable=new paesaggioHtmlETable($sql);


	if ($vincoliTable->getTableRows()>0) {
		print('<div style="margin:10px; clear: both;">');
		$vincoliTable->setColSubstring('Note',20);
		print('<FORM ACTION="'.$PHP_SELF.'?mode='.$_GET['mode'].'"  METHOD="GET" name="AssociateMenuRespId">'."\n");
		print('<input type="hidden" name="PRATICA_ID" value="'.$_GET['PRATICA_ID'].'" >');

		MakeButtons('assign');

			$vincoliTable->show();

		MakeButtons('assign');

		print('</FORM>'."\n");
		print('</div>');
	} else {
		print('<div style="margin:10px; clear: both;">');
		print('<h2>Nessun Vincolo trovato!</h2>');
		print('</div>');
	}





}



include('pagefooter.inc')



?>
