<?php
/*
 * Created on 11/mar/09
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");

$sospensioniQuery = 'select SOSPENSIONE_ID,' .
					'PRATICA_ID, ' .
					'description as "Tipo Pratica", ' .
					'date_format(inizio,\'%d-%m-%Y\') as "Inizio Sospensione", ' .
					'PROTOENTRATA as "Prot.entrata", ' .
					'date_format(fine,\'%d-%m-%Y\') as "Fine Sospensione", ' .
					'PROTOUSCITA as "Prot.uscita", ' .
					'substr(asp.motivazione,1,20) as Motivo,' .
					'riavvia as "Riavvia" ' .
					' from arc_sospensioni asp ' .
					' left join arc_modelli am on (am.modello = asp.modello) ' .
					' where pratica_id ='.$PRATICA_ID;

$sospensioniTable = new htmlEtable($sospensioniQuery);

if ($sospensioniTable->getTableRows()>0) {
	include('pageheader.inc');
	$sospensioniTable->SetColumnHref('Tipo Pratica','<a href="arcSospensioni.php?mode=modify&SOSPENSIONE_ID=#SOSPENSIONE_ID#&PRATICA_ID=#PRATICA_ID#">');
	$sospensioniTable->hideCol('SOSPENSIONE_ID');
	$sospensioniTable->hideCol('PRATICA_ID');
	$sospensioniTable->show();
	print('<div style="float: left;margin:0px; vertical-align: text-middle;"><img src="graphics/webapp/prevpager.gif" style="margin-right: 10px;" ><a href="editPratica.php?PRATICA_ID='.$PRATICA_ID.'" style="color: blue; cursor:pointer; position:relative; top: -10px;">Torna alla pratica</a></div>');
	print('<div style="float: right;margin:0px; vertical-align: text-middle;"><a href="arcSospensioni.php?mode=insert&PRATICA_ID='.$PRATICA_ID.'" style="cursor:pointer; color: blue;position:relative; top: -10px;" >Aggiungi Sospensione</a><img src="graphics/webapp/addnewr.gif" style="margin-left: 10px;" ></div>');
	print('<div style="clear: both"></div>');
	include('pagefooter.inc');
} else {
	header('Location: arcSospensioni.php?mode=insert&PRATICA_ID='.$PRATICA_ID);
}
?>
