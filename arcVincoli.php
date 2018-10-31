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
//require_once("Etable_c.inc");
$dbTable='ARC_VINCOLI';

class MyDbForm extends formExtended {

 	function ShowForm(){
		/* Form container */
 		print('<div id="'.$this->GetFormName().'" class="dbFormContainer" >'."\n");
		// Titolo
//		print ('<div class="DbFormTitle">');
//		print ($this->GetFormTitle());
//		print ('</div>'."\n");
		print ('<!-- Form open -->');
		print ("\n");
		print ($this->GetFormHeader());

		$this->formMessageShow();
		$this->formAttachmentsShow();
		$this->showButtonBar(FALSE);
		print('<fieldset>'."\n");
		print('<legend>'.$this->GetFormTitle().'</legend>'."\n");
		print ("<br />\n");
		foreach ($this->_FormFields as $fieldObj) {
		   $fieldObj->showDivField();
		}
		print ("\n");
		print ("<br />\n");
		print('</fieldset>'."\n");

		$this->showProprietari();

		print ('<div id="message"></div>' . "\n");
		$this->showButtonBar(FALSE);
		print ('</form>'."\n");
 		print('</div>'."\n");
 	}

	function showProprietari(){
		if ($this->GetFormMode()=='modify') {
			$propQuery=' select ' .
						'PROPRIETARIO_ID, ' .
						'VINCOLO_ID, ' .
						'NOME as Proprietario, ' .
						'ANAGRAFICO "Anag.", ' .
						'MAPPALE "Mapp.", ' .
						'SUB Sub, ' .
						'PIANI Piani, ' .
						'DATA_PROPR "Data P.", ' .
						'DATA_RICH Richiesta, ' .
						'RISPOSTO_IL Risposta' .
						' from arc_proprietari where vincolo_id = '.$this->_FormFields['VINCOLO_ID']->GetValue();

				print('<fieldset>'."\n");
				print('<legend>Propietari</legend>'."\n");
				print ("<br />\n");
				$propTable = new htmlETable($propQuery);
				if ($propTable->getTableRows()>0) {
					$propTable->SetColumnHref('Proprietario','<a href="arcProprietari.php?mode=modify&PROPRIETARIO_ID=#PROPRIETARIO_ID#&VINCOLO_ID=#VINCOLO_ID#">');
					$propTable->hideCol('PROPRIETARIO_ID');
					$propTable->hideCol('VINCOLO_ID');
					$propTable->show();
				}

				print('<hr><a href="arcProprietari.php?mode=insert&VINCOLO_ID='.$this->_FormFields['VINCOLO_ID']->GetValue().'" style="font-size:12px; color:blue;">Aggiungi Proprietario</<a>');


				print('</fieldset>'."\n");




		}
	}


}
$xlsBar='N';
if (!isSet($xlsExport) or ($xlsExport <> 'Y')) {
	include ("pageheader.inc");


	if (isset($filterField) and isset($$filterField)) {
		$dbTableFilter=" where ($dbTable.$filterField=".$$filterField.") ";
		$recallPage='?dbTable='.$dbTable.'&filterField='.$filterField.'&'.$filterField.'='.$$filterField;
		global $filterField;
	} else {
		$recallPage='?dbTable='.$dbTable;
	}
}
include("manageDbtable.inc");
include ("pagefooter.inc");

?>
