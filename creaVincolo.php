<?php
/*
 * Created on 22/lug/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");

if ($_GET['TIPO']=='amb'){
	if(dbupdate('insert into vin_ambientali (oggetto, created, created_by) values (\''.$_GET['OGGETTO'].'\',now(),\''.$_SESSION['sess_uid'].'\')')){
		print(dbLastId());
	}
} else {
	if(dbupdate('insert into vin_monumentali (oggetto, created, created_by) values (\''.$_GET['OGGETTO'].'\',now(),\''.$_SESSION['sess_uid'].'\')')){
		print(dbLastId());
	}
}


?>
