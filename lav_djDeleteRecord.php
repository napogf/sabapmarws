<?php
/*
 * Created on 28/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
if($_GET['sqlQuery']>''){
	dbupdate($_GET['sqlQuery']);
	print('ok');
} else {
	print('Errore PERIZIA_ID mancante!');
}
?>