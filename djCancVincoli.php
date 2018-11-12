<?php
/*
 * Created on 22/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");
if($_GET['praticaId']>'') dbupdate('update pratiche set vincolo_id = null where pratica_id = '.$_GET['praticaId']);
?>
