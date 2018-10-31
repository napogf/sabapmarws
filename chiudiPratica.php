<?php
/*
 * Created on 23/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
$updQuery='update pratiche set uscita = str_to_date("'.$_GET['dataUscita'].'","%d/%m/%Y"), ' .
							'esito_id="'.$_GET['esitoId'].'" ,' .
							'modello = "'.$_GET['tipoPratica'].'" ' .
							'where pratica_id='.$_GET['praticaId'];
if(dbupdate($updQuery)){
	print('ok');
} else {
	print('Error ');
}

?>
