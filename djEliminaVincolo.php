<?php
/*
 * Created on 12/ago/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");
$deleteVincoloSql='delete from arc_vincoli_pratiche where ' .
					'pratica_id = '.$_GET['praticaId']. ' and ' .
					'vincolo_id = '.$_GET['vincoloId']. ' and ' .
					'tipo = \''.$_GET['tipo']. '\' ';
dbupdate($deleteVincoloSql);
?>
