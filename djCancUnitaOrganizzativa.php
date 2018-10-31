<?php
/*
 * Created on 05/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";

$cancQuery="delete from arc_pratiche_uo where prauoid =".$_GET['prauoid'];
if(dbupdate($cancQuery)){
	print('Record Cancellato!');
} else {
	print('Errore nella cancellazione!');
}


?>
