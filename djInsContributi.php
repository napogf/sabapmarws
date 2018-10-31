<?php
/*
 * Created on 04/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");
class myHtmlETable extends htmlETable {

}

$insContributiQuery=' insert into arc_contributi (PRATICA_ID, RIF_ART,DESCRIPTION,DETRAZIONE,INCIDENZA) values (' .
		'\''.$_GET['praticaId'].'\', ' .
		'\''.$_GET['RIF_ART'].'\', ' .
		'\''.$_GET['DESCRIPTION'].'\', ' .
		'\''.$_GET['DETRAZIONE'].'\', ' .
		'\''.($_GET['INCIDENZA']/100).'\' ' .
		')';
if(dbupdate($insContributiQuery)){

	print('Contributo Inserito!');

} else {
	print('<div><h2>Impossibile caricare la destinazione - Contattare il servizio tecnico!</h2></div>');
}
?>