<?php
/*
 * Created on 05/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");

$_SESSION['paneSelected'] = 'destinatari';
$cancDestQuery="delete from arc_destinazioni where dest_id =".$_GET['destId'];
if(dbupdate($cancDestQuery)){
	print('Record Cancellato!');
} else {
	print('Errore nella cancellazione!');
}