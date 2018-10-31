<?php
/*
 * Created on 19-ott-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("fdataentry.php");
//require_once("formExt.inc");
//require_once("table_c.inc");
//require_once("toolBar.inc");
class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
     */

}



$dbKey=' where PRATICA_ID='.$PRATICA_ID;

$ManagedTable = new MyDbForm('PRATICHE',$sess_lang);

$del_message=get_label('del_message');

$ManagedTable->setAfterUpdateLocation('praticheStatus.php');
$ManagedTable->SetFormMode("insert",null);

$ManagedTable->_FormFields['DATAREGISTRAZIONE']->SetShowed('Y');
$ManagedTable->_FormFields['NUMEROREGISTRAZIONE']->SetShowed('Y');






include ("pageheader.inc");


$ManagedTable->ShowForm();



include ("pagefooter.inc");

?>
