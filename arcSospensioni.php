<?php
/**
 *
 *
 * @version $Id: arcSospensioni.php,v 1.1.1.1 2010/02/02 12:13:50 root Exp $
 * @copyright 2003
 **/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("fdataentry.php");
//require_once("formExt.inc");
//require_once("table_c.inc");
if ($mode=='modify') {
	$mode='modify';
	$dbKey='where sospensione_id='.$SOSPENSIONE_ID;
} else {
	$mode='insert';
	$dbKey = null;
}

class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
     */
//			function ExecFormQuery(){
//				global $LINK_ID, $DIR_ID;
//				if ($this->FormValidation()) {
//					if ($this->GetFormMode()=='insert') {
//
//
//						} else {
//							dbupdate($this->GetFormQuery());
//							header("Location: manage_link.php?mode=modify&wk_link_id=$LINK_ID&wk_dir_id=$DIR_ID");
//						}
//					return true;
//				} else {
//					return false;
//				}
//			}
}



$propForm = new MyDbForm('ARC_SOSPENSIONI',$sess_lang);
$afterUpdate='manageSospensioni.php?PRATICA_ID='.$PRATICA_ID;
$propForm->SetAfterUpdateLocation($afterUpdate);
$propForm->SetAfterInsertLocation($afterUpdate);

if ($mode=='insert') {
	$propForm->SetFormMode($mode,null);
	$propForm->SetFormFieldValue('PRATICA_ID',$PRATICA_ID);
} else {
	$propForm->SetFormMode($mode,stripslashes($dbKey));
}
include("pageheader.inc");



$propForm->ShowForm();

include("pagefooter.inc");
?>