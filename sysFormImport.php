<?php
/*
 * Created on 25/gen/11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");
//require_once('sysFormCopy.inc');
class myHtmlETable extends htmlETable {
}
if($_GET['sysForm']>''){

	$obj= new formCopy($_GET['sysForm']);
	$obj->importForm();

}



include('pageheader.inc');

$queryTable='select distinct substring(table_name,7) as FormName, \'\' as Import from information_schema.TABLES where table_name like \'tmp\_f\_%\' ';
$listaTabelle = new myHtmlETable($queryTable);
$listaTabelle->SetColumnHref('Import','<img src="graphics/table_go.png" style="cursor:pointer" onclick="location.href=\'sysFormImport.php?sysForm=#FormName#\'" >');
$listaTabelle->show();

include('pagefooter.inc')
?>
