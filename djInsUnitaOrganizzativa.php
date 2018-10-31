<?php
/*
 * Created on 04/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";


$insQuery=' insert into arc_pratiche_uo (PRATICA_ID, UOID) values (' .
		'\''.$_GET['praticaId'].'\', ' .
		'\''.$_GET['uoid'].'\' ' .
		')';
if(dbupdate($insQuery)){
	print('success');
} else {
	print('<div><h2>Impossibile caricare l\'unità organizzativa - Contattare il servizio tecnico!</h2></div>');
}
?>