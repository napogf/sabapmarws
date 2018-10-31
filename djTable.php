<?php
/*
 * Created on 22/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once ("fdataentry.php");
//require_once ("formExt.inc");
//require_once ("Etable_c.inc");
//require_once ("butt_c.inc");
//require_once('dbTableList.inc');
class myDbList extends dbList {

}

class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
     */
}
include('pageheader.inc');
		print ('<div dojoType="dijit.layout.TabContainer"
					style="width:95%; margin:0px;">');

			print ('<div dojoType="dijit.layout.ContentPane" title="Cerca">');

				$listRecord = new myDbList($dbTable);
				$listRecord->showTable();

			print ('</div>');

			print ('<div dojoType="dijit.layout.ContentPane" title="Edit">');
				$ManagedTable = new MyDbForm($dbTable, $_SESSION['sess_lang']);
				if (isSet($recursiveFields))
					$ManagedTable->setRecursiveFields($recursiveFields);
				if (isSet ($backAfterInsert))
					$ManagedTable->setAfterInsertLocation($backAfterInsert);
				if (isSet ($backAfterUpdate))
					$ManagedTable->setAfterUpdateLocation($backAfterUpdate);
				if (isSet ($formTitleAdd))
					$ManagedTable->SetFormTitle($ManagedTable->GetFormTitle() . ' - ' . $formTitleAdd);
				$ManagedTable->SetFormMode($mode, stripslashes($dbKey));
				if (isSet ($wk_page))
					$ManagedTable->AddFormActionParameter('&wk_page=' . $wk_page);
				if (isSet ($dbFilter)) {
					$ManagedTable->AddFormActionParameter('&dbFilter=' . $dbFilter);
				}
				if (isSet ($recallPage)) {
					$_action = '&' . substr($recallPage, 1);
					$ManagedTable->AddFormActionParameter($_action);
					$recallPage = $recallPage . '&';
				} else {
					$recallPage = '';
				}
				if ($filterField > '') {
					$ManagedTable->HideFormField($filterField);
					$ManagedTable->SetFormFieldValue($filterField, $$filterField);
				}
				$ManagedTable->ShowForm();
			print ('</div>');


		print ('</div>');






include('pagefooter.inc')
?>
