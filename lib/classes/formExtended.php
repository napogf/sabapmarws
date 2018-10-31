<?php
/*
 * Created on 5-dic-06
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class formExtended extends DbForm {

     public function getFieldDataType($field){
         if(isSet($this->_FormFields[$field])){
             return $this->_FormFields[$field]->GetDataType();
         }

         return 'NONE';
     }


 	/* Costruisce la form per il filtro */
 	public function showFilterForm(){
 		if(count($this->_FormFieldsFilters) > 0){
 			print ('<div dojoType="dijit.layout.ContentPane" title="Filtra Record" id="Find_' . $this->GetFormName() . '">');
	 			print('<div id="filter_'.$this->GetFormName().'" class="dbFormContainer" >'."\n");
	 			print ('<!-- Search Form open -->');
	 			print ("\n");
				print('<form method="post" action="'.$_SERVER['REQUEST_URI'].'" name="filter_' . $this->GetFormName() .
							'" id="filter_' . $this->GetFormId() . '" >');
				print('<input type="hidden" name="dbTable" value="' . $this->GetFormName() . '">' . "\n");
				print('<input type="hidden" name="filter" value="apply">' . "\n");
	 			print('<fieldset>'."\n");
	 			print('<legend>Ricerca record per .... </legend>'."\n");
	 			print ("<br />\n");
	 			foreach ($this->_FormFieldsFilters as $fieldName => $fieldObj) {
	 				$fieldObj->SetValue($_SESSION['filter_'.$this->GetFormName()][$fieldName]);
	 				$fieldObj->showFilterField();
	 			}
	 			print ("\n");
	 			print ("<br />\n");
	 			print('<div>');
	 			print('<div style="float:left;">');
	 			print('<input type="submit" name="filterButton" value="Cerca">');
	 			print('</div>'."\n");
	 			print('<div style="float:right;">');
	 			print('<input type="submit" name="clearButton" value="Pulisci">');
	 			print('</div>'."\n");

	 			print('</fieldset>'."\n");
	 			print ('</form>'."\n");
	 			print('</div>'."\n");
 			print('</div>');
 		}



 	}

	public function getQueryFilter($post) {
		;
	}

 	/* costruisce la form graficamente a partire dagli oggetti caricati */
 	function ShowForm(){
		/* Form container */
 		print('<div id="'.$this->GetFormName().'" class="dbFormContainer" >'."\n");
		// Titolo
//		print ('<div class="DbFormTitle">');
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
		foreach ($this->_FormFields as $fieldName => $fieldObj) {
			if (array_search($fieldName,$this->_recursiveFields)===FALSE) {
				$fieldObj->showDivField();
			} else {
				if ((strlen(trim($_POST[$fieldName]))>0)) {
					$fieldObj->dispDivField();
				} else {
					$fieldObj->showDivField();
				}
			}
		}
		print ("\n");
		print ("<br />\n");
		print('</fieldset>'."\n");
		print ('<div id="message"></div>' . "\n");
		$this->showButtonBar(FALSE);
		print ('</form>'."\n");
 		print('</div>'."\n");
 	}

	function formMessageShow() {
		print ('<div class="DbFormMessage">');
		print ($this->GetFormMessage());
		print ('</div>');
		print ("\n");
	}


	function formAttachmentsShow() {
		if ($this->GetFormMode() == 'modify' and $this->GetFormAttachments()) {
			print ('<div class="formAttachment">' . "\n");
			print ('<img STYLE="cursor: pointer; padding-left: 20; padding-right: 5;" src="graphics/attach.png" ' .
			' width="28" height="28" border="0" ' .
			' onClick="location.href=\'dbAttach.php?dbTable=ARC_ATTACHMENTS&LINK_ID=' .
			$this->GetFormFieldValue($this->GetFormKey()) .
			'&dbFilter=LINK_ID='.$this->GetFormFieldValue($this->GetFormKey()).
			'&FORM_NAME=' . $this->GetFormName() .
			'\'" Title="Atachments" >' . 'Allegati' . "\n");
			print ("</div>\n");
		}
	}
	function getFormHeader() {
		$_formHeader = '<FORM ACTION="' . $this->getFormDestination()  .$this->getFormAction() . '" ' . $this->getFormEnctype() . $this->getFormMethod() . 'name="' . $this->getFormName() . '" id="' . $this->getFormId() . '" >' . "\n";
		$_formHeader .= '<input type="hidden" name="dbTable" value="' . $this->getFormName() . '">' . "\n";
		return $_formHeader;

	}
 	function displayForm(){
		/* Form container */
 		print('<div id="'.$this->GetFormName().'" class="dbFormContainer" >'."\n");
		// Titolo
		print ('<!-- Form open -->');
		print ("\n");
		print ($this->GetFormHeader());

		$this->formMessageShow();
		$this->formAttachmentsShow();
		print('<fieldset>'."\n");
		print('<legend>'.$this->GetFormTitle().'</legend>'."\n");
		print ("<br />\n");
		foreach ($this->_FormFields as $fieldObj) {
		   $fieldObj->DispDivField();
		}
		print ("\n");
		print ("<br />\n");
		print('</fieldset>'."\n");
		print ('<div id="message"></div>' . "\n");
		print ('</form>'."\n");
 		print('</div>'."\n");
 	}
	// Test mantenimento valori dopo insert
 }

?>