<?php
/*
 * Created on 05/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
$cancQuery="delete from arc_contributi where id =".$_GET['Id'];
if(dbupdate($cancQuery)){
	print('Record Cancellato!');
} else {
	print('Errore nella cancellazione!');
}


?>
