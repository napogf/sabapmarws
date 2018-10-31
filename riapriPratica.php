<?php
/*
 * Created on 10/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
if (dbupdate('update pratiche set uscita = null where pratica_id = '.$_GET['praticaId'])){
	header('location: editPratica.php?PRATICA_ID='.$_GET['praticaId']);
} else {
	debug_print_backtrace();
}


?>
