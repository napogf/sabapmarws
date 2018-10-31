<?php


/**
 * Class djDbForm
 * Generatore di Form basato su database
 * La Form Gestisce inserimento, modifica, cancellazione del record mappato
 * Codice rilasciato sotto licenza GNU/GPL
 *
 * Per istanziare la classe:
 *
 * 		$newForm = djDbForm($formId,$language)
 *
 * 		$newForm->setFormMode($mode,$keyValue)
 * 		// $keyValue: 	null in caso di insert
 * 						primary key negli altri casi
 * 		// $mode:		'insert' => inserimento record
 * 						'modify' => modifica record
 * 						'delete' => cancellazione
 * 						'duplicate' => copia
 *
 * 		$newForm->setAfterSubmitLocation($urlDestination);
 * 		// if you want go to another destination after had submitting the form
 * 		$newForm->loadForm();
 *
 * @version $Id: djFormClass.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003
 **/
class djDbForm {
	function __construct($formName,$languageId){
		if (is_null($formName)) {
			errore('Errore Form Name non Passato!');
		} else {
			if (is_null($languageId)) $languageId=$_SESSION['sess_lang'];
			$this->setLanguage($languageId);
			$sql = "SELECT FORMSTB.*, " .
			"LABELS.TITLE " .
			"FROM sys_forms FORMSTB " .
			" LEFT JOIN sys_forms_titles LABELS on (LABELS.FORM_ID = FORMSTB.FORM_ID AND LABELS.LANGUAGE_ID = '$languageId')" .
			" WHERE FORMSTB.FORM_NAME = '$formName'";
			if (!$DbForm = dbselect($sql)){
				errore('You must generate Form '.$formName);
			} else {
				$this->setFormId($DbForm['ROWS'][0]['FORM_ID']);
				$this->setFormName($DbForm['ROWS'][0]['FORM_NAME']);
				$this->setTableName($DbForm['ROWS'][0]['TABLE_NAME']);
				$this->setFormTitle($DbForm['ROWS'][0]['TITLE']);
				$this->setFormButtons();
				$this->loadFields();
			}
		}

	}

	private $_FormId = -1;
	function getFormId() {
		return $this->_FormId;
	}
	function setFormId($newValue) {
		$this->_FormId = $newValue;
	}

	private $_language = -1;
	function getLanguage() {
		return $this->_language;
	}
	function setLanguage($newValue) {
		$this->_language = $newValue;
	}

	private $_formName = '';
	function getFormName() {
		return $this->_formName;
	}
	function setFormName($newValue) {
		$this->_formName = $newValue;
	}

	private $_formTitle = '';
	function getFormTitle() {
		return $this->_formTitle;
	}
	function setFormTitle($newValue) {
		$this->_formTitle = $newValue;
	}

	private $_formButtons;
	function setFormButtons() {
		$this->_formButtons = new djButtonsBar($this->getButtonBar(),$this->getFormDestination());
	}
	private $_buttonBar;
	function setButtonBar($buttonBar) {
		$this->_buttonBar;
	}
	function getButtonBar() {
		return $this->_buttonBar;
	}
	private$_TableName = '';
	function getTableName() {
		return strtolower($this->_TableName);
	}
	function setTableName($newValue) {
		$this->_TableName = $newValue;
	}

	function setDefaultValue($field,$value,$hide=true){
		if (is_object($this->_formFields[$field])){
			$this->_formFields[$field]->SetValue($value);
			if($hide) $this->_formFields[$field]->hideField();
		} else {
			var_dump(	);
			exit;
		}
	}


	function loadFields() {
		$sql = "select " .
				"FFIELDS.*, " .
				"LABELS.DESCRIPTION LABEL " .
				"FROM sys_forms_fields FFIELDS " .
				"	LEFT JOIN sys_forms_fields_labels LABELS ON (LABELS.FIELD_ID = FFIELDS.FIELD_ID AND LABELS.LANGUAGE_ID = '" . $this->GetLanguage() . "') " .
				"where FFIELDS.FORM_ID='" . $this->GetFormId() . "' " .
					"ORDER BY FFIELDS.SHOWED, FFIELDS.VSEQ, FFIELDS.HSEQ";
		$DbColumns = dbselect($sql);
		for ($i = 0; $i < $DbColumns['NROWS']; $i++) {
			$field_object = new FormFields($DbColumns['ROWS'][$i]['FIELD_NAME']);
			$field_object->SetDataType($DbColumns['ROWS'][$i]['DATA_TYPE']);
			$field_object->setFieldFormat($DbColumns['ROWS'][$i]['TYPE']);
			$field_object->SetShowed($DbColumns['ROWS'][$i]['SHOWED']);
			$field_object->SetFieldLabel($DbColumns['ROWS'][$i]['LABEL']);
			$field_object->SetLength($DbColumns['ROWS'][$i]['LENGTH']);
			$field_object->SetMaxLength($DbColumns['ROWS'][$i]['MAXLENGTH']);
			$field_object->SetUpdatable($DbColumns['ROWS'][$i]['UPDATABLE']);
			$field_object->SetNullValue($DbColumns['ROWS'][$i]['NULLVALUE']);
			$field_object->SetTextareaRows($DbColumns['ROWS'][$i]['TEXTAREA_ROWS']);
			$field_object->SetDefaultValue($DbColumns['ROWS'][$i]['DEFAULT_VALUE']);
			$field_object->SetLookUp($DbColumns['ROWS'][$i]['LOOKUP']);
			$field_object->SetId($DbColumns['ROWS'][$i]['FIELD_ID']);
			// $field_object->SetValue($_result['ROWS'][0][$DbColumns['ROWS'][$i]['FIELD_NAME']]);
			$field_object->SetShowed($DbColumns['ROWS'][$i]['SHOWED']);
			$field_object->SetValidation($DbColumns['ROWS'][$i]['VALIDATION']);
			$field_object->SetInTable($DbColumns['ROWS'][$i]['IN_TABLE']);
			$field_object->SetPostValidation($DbColumns['ROWS'][$i]['POSTVAL']);
			if ($DbColumns['ROWS'][$i]['DATA_TYPE'] == 'FILE') {
				$this->setFileFieldToLoad($DbColumns['ROWS'][$i]['FIELD_NAME']);
				if ($DbColumns['ROWS'][$i]['TYPE']>'') $field_object->setDirUpload($DbColumns['ROWS'][$i]['TYPE']);
			}
			if (strtoupper($DbColumns['ROWS'][$i]['IS_KEY']) == 'Y') {
				$field_object->Setkey();
				$this->setFormKey($DbColumns['ROWS'][$i]['FIELD_NAME']);
			}
			$this->addFormFields($DbColumns['ROWS'][$i]['FIELD_NAME'], $field_object);
		} // for

	}

	private $_formDestination;
	function setFormDestination($url){
		$this->_formDestination = $url;
	}
	function getFormDestination(){
		return $this->_formDestination;
	}

	private $_formAction;
	function getFormAction() {
		return $this->_formAction;
	}
	function setFormAction($value) {
		$this->_formAction = $value;
	}
	function addFormActionParameter($value) {
		$this->_formAction .= $value;
	}

	private $_formEnctype = ' enctype="multipart/form-data" ';
	function getFormEnctype() {
		return $this->_formEnctype;
	}
	function setFormEnctype($value) {
		$this->_formEnctype = $value;
	}

	private $_formMethod = 'METHOD="POST" ';
	function getFormMethod() {
		return $this->_formMethod;
	}
	function setFormMethod($value) {
		$this->_formMethod = $value;
	}

	function getFormHeader() {
		$formHeader = '<FORM ACTION="'  .$this->getFormDestination().'?mode=' . $this->getFormMode() .$this->getFormAction(). $this->getFormDbKey(). '" ' . $this->getFormEnctype() . $this->getFormMethod() . 'name="' . $this->GetFormName() . '" id="djForm" >' . "\n";
		$formHeader .= '<input type="hidden" name="formName" value="' . $this->getFormName() . '">' . "\n";
		return $formHeader;
	}


	private $_formFields = array ();

	function getFormFields($key=null) {
		if (is_null($key)){
			return $this->_formFields;
		} else {
			return $this->_formFields[$key];
		}
	}
	function addFormFields($key, $value) {
		$this->_formFields[$key] = $value;
	}

	function showForm(){
		/* Form container */
		$formFields = $this->getFormFields();
 		print('<div id="'.$this->getFormName().'" class="djFormContainer" >'."\n");
		// Titolo
		print ('<!-- Form open -->');
		print ("\n");
		print ($this->getFormHeader());
		// $this->formMessageShow();
		$this->_formButtons->barButtonShow();
		print('<fieldset>'."\n");
		print('<legend>'.$this->getFormTitle().'</legend>'."\n");
		print ("<br />\n");
		// print hidden field mode
		print('<input type="HIDDEN"  name="mode" value="' . $this->getFormMode() . '"  >' . "\n");
		foreach ($formFields as $fieldObj) {
		   $fieldObj->showDivField();
		}
		print ("\n");
		print ("<br />\n");
		print('</fieldset>'."\n");
		print ('<div id="message"></div>' . "\n");
		$this->_formButtons->barButtonShow();
		print ('</form>'."\n");
 		print('</div>'."\n");
 	}



	/**
	 * Inizializzo le Query Automatiche
	 *
	 **/
	private $_lastInsertId = -1;

	function getLastInsertId() {
		return ($this->_lastInsertId);
	}

	function setLastInsertId($newValue) {
		$this->_lastInsertId = $newValue;
	}


	private $_fileFieldsToLoad;
	function setFileFieldToLoad($value) {
		$this->_fileFieldsToLoad[] = $value;
	}
	function getFileFieldsToLoad() {
		return $this->_fileFieldsToLoad;
	}

	function loadFiles() {
		$fieldsToLoad = $this->getFileFieldsToLoad();
		for ($index = 0; $index < sizeof($fieldsToLoad); $index++) {
			$field = $fieldsToLoad[$index];
			$fileName = $_FILES[$field]['name'];
			$fileNameToLoad = $this->getFormName() . '-' .
			$this->getFormFields($field)->GetId() . '-' .
			$fileName;
		}
	}
	private $_selectFormQuery;
	function getSelectFormQuery() {
		return $this->_selectFormQuery;
	}
	function setSelectFormQuery($value) {
		$this->_selectFormQuery = $value;
	}

	private $_deleteFormQuery;
	function getDeleteFormQuery() {
		return $this->_deleteFormQuery;
	}
	function setDeleteFormQuery($value) {
		$this->_deleteFormQuery = $value;
	}

	private $_insertFormQuery;
	function getInsertFormQuery() {
		return $this->_insertFormQuery;
	}
	function setInsertFormQuery($value) {
		$this->_insertFormQuery = $value;
	}

	private $_sequenceFieldQuery = '';

	function getSequenceFieldQuery() {
		return $this->_sequenceFieldQuery;
	}

	function setSequenceFieldQuery($newValue) {
		$this->_sequenceFieldQuery = $newValue;
	}

	private $_KeyFieldQuery = '';
	function getKeyFieldQuery() {
		return $this->_KeyFieldQuery;
	}
	function setKeyFieldQuery($newValue) {
		$this->_KeyFieldQuery = $newValue;
	}

	function formPostValidation() {
		return TRUE;
	}
	function formPreValidation() {
		return TRUE;
	}
	function formValidation() {
		return TRUE;
	}

	private $_formKey;
	/* richiamata da loadFields il field definito come KEY*/
	function setFormKey($value) {
		$this->_formKey = $value;
	}
	function getFormKey() {
		return $this->_formKey;
	}
	private $_notUniqueKey=array();
	function addKeyField($field){
		$this->_notUniqueKey[]=$field;
	}
	function getDbKey() {
		if ($this->getFormMode()=='insert'){
			$whereClause = 'where ' . $this->getFormKey() . '=' . $this->getLastInsertId();
		} else {
			$whereClause = ($this->getFormFields($this->getFormKey())->GetValue()>'')?
						 'where ' . $this->getFormKey() . '=' . $this->getFormFields($this->getFormKey())->GetValue():
						 'where ';
			$andClause='';
			foreach($this->_notUniqueKey as $field){
				$whereClause .= $andClause . $field .' = ' . $this->_formFields[$field]->GetValue();
				$andClause = ' and ';
			}
		}
		return $whereClause;
	}
	function getFormDbKey() {
		if ($this->getFormMode()=='insert'){
			$paramClause = '&key=' . $this->getLastInsertId();
		} else {
			$paramClause = ($this->getFormFields($this->getFormKey())->GetValue()>'')?
						 '&key='.$this->getFormFields($this->getFormKey())->GetValue():
						 '';
			foreach($this->_notUniqueKey as $field){
				$paramClause .= '&key_'.$field.'=' . $this->_formFields[$field]->GetValue();
			}
		}
		return $paramClause;
	}


	private $_formMode = 'insert';
	function getFormMode() {
		return $this->_formMode;
	}

	function setFormMode($mode,$keyValue=null) {
		$this->_formMode=$mode;
		if ($keyValue>'') {
			$this->getFormFields($this->getFormKey())->SetValue($keyValue);
		}
		switch ($mode) {
			case 'modify':
				$this->_formButtons->barButtonEnable('buttapp');
				$this->_formButtons->barButtonDisable('buttadd');
				break;
			case 'insert':
				$this->_formButtons->barButtonEnable('buttadd');
				$this->_formButtons->barButtonDisable('buttapp');
				break;
		}
	}

	function loadForm(){
		$mode=$this->getFormMode();
		switch ($mode) {
			case 'duplicate';
				$this->copyRecord($this->getDbKey());
				break;
			case 'delete' :
				$this->SetFormQuery('delete from ' . strtolower($this->GetTableName()) . ' ' . $this->getDbKey());
				$this->execFormQuery();
				print(GetMessage('delrec'));
				break;
			case 'modify' :
				if ($this->_formButtons->barButtonIsPressed('buttapp') and ($this->formPreValidation())) {
					// ricavo dalla definizione della form tutti i campi da aggiornare e imposto la relativa query
					$repost_form = $this->setUpdateQuery();
					if ($repost_form) {
						$this->setFormMessage(GetMessage('form_incomplete'));
						$this->repostFormValues();
					} else {
						if ($this->execFormQuery()) {
							// la modifica del record va a buon fine eseguo FormPostValidation e torno in insert
							$this->formPostValidation();
							print(GetMessage('modrec'));
						} else {
							// la query fallisce e ritorno errore .....
							errore(var_dump(debug_backtrace()));
						}
					}
				} else {
					// recupero dalla tabella i dati per valorizzare la form
					$fields_to_valorize = $this->getFieldsValues($this->getDbKey());
					// li carico nella form
					$this->valorizeFields($fields_to_valorize);
					/* Testo se il bottone Aggiorna è stato premuto se si devo aggiornare il db */
					// $this->setFormAction($this->getFormDestination().'?mode=' . $this->getFormMode() . $this->getFormAction(). $this->getFormDbKey());
					$this->showForm();
				}
				break;
			default :
				if ($this->_formButtons->barButtonIsPressed('buttadd') and ($this->formPreValidation())) {
					if ($this->setInsertQuery()) {
						$this->setFormMessage(GetMessage('form_incomplete'));
						$this->_formButtons->barButtonDisable('buttapp');
						$this->_formButtons->barButtonEnable('buttadd');
						$this->repostFormValues();
						$this->showForm();
					} else {
						if ($this->execFormQuery()) {
							$this->setLastInsertId(dbLastId());
							$this->formPostValidation();
							// $this->setFormMessage(GetMessage('insrec'));
							print(GetMessage('insrec'));
							exit;
						} else {
							// la query fallisce e ritorno errore .....
							errore(var_dump(debug_backtrace()));
						}
					}
				} else {
					$this->showForm();
				}
		} // switch
	}

	private $_formQuery;

	function getFormQuery() {
		return $this->_formQuery;
	}
	function setFormQuery($value) {
		$this->_formQuery = $value;
	}

	function execFormQuery() {
		global $dir_upload;
		if ($this->formValidation()) {
			if (dbupdate($this->getFormQuery(),false)) {
				foreach ($_FILES as $key => $value) {
					// $dir_upload=$this->_formFields[$key]->getDirUpload();
					switch ($this->getFormMode()) {
						case 'insert' :
							if (!empty ($value['name'])) {
								copy($value['tmp_name'], $dir_upload . dbLastId() . '-' . $this->getFormName() . '-' . $value['name']);
							}
							break;
						case 'modify' :
							if (!empty ($value['name'])) {
								foreach (glob($dir_upload . $this->_formFields[$this->getFormKey()]->GetValue() . '-' . $this->getFormName() . '-*') as $filename) {
									unlink($filename);
								}
								copy($value['tmp_name'], $dir_upload . $this->_formFields[$this->getFormKey()]->GetValue() . '-' . $this->getFormName() . '-' . $value['name']);
							}
							break;
					}

				}
			}
			return true;
		} else {
			$this->setFormMessage('Invalid Form!');
		}
		return false;
	}

	// Genera la query d'inserimento'
	function setInsertQuery() {
		$repost_form = false;
		$query = 'insert into ' . strtolower($this->getTableName()) . ' (';
		foreach ($this->_formFields as $key => $formField){
			if ($formField->IsUpdatable()) {
				if ((is_null($_POST[$key]) or strlen(trim($_POST[$key])) == 0) //												and !$this->_FormFields[$key]->IsKey()
				and !$formField->NullValueAllowed() and !($formField->GetDataType() == 'FILE')) {
					$formField->SetIsNull(TRUE);
					$repost_form = TRUE;
				}
				$field_query .= empty ($field_query) ? $key : ', ' . $key;
				if ($formField->IsKey() and !$formField->GetShowed() == 'N') {
					$value_query .= empty ($value_query) ? $formField->GetValidation() : ', ' . $formField->GetValidation();
					$this->setSequenceFieldQuery(substr($formField->GetValidation(), 0, strpos($formField->GetValidation(), '.')));
					$this->setKeyFieldQuery($key);
				} else {
					switch ($key) {
						case 'CREATION' :
							$valueToUpdate = " now() ";
							break;
						case 'UPDATED' :
							$valueToUpdate = " now() ";
							break;
						case 'CREATED_BY' :
							$valueToUpdate = $_SESSION['sess_uid'];
							break;
						case 'UPDATED_BY' :
							$valueToUpdate = $_SESSION['sess_uid'];
							break;
						default :

							switch ($this->getFormFields($key)->GetDataType()) {
								case 'FILE' :
									$valueToUpdate = strlen($_FILES[$key]['name']) > 0 ? "'" . $this->getFormName() . '-' . $_FILES[$key]['name'] . "'" : 'NULL';
									break;
								case 'DATE' :
									$dateField = @strtotime($_POST[$key]);
									if (($dateField === -1)) {
										$valueToUpdate = " now() ";
									} else {
										$valueToUpdate = " str_to_date('" . $_POST[$key] . "','%Y-%m-%d') ";
									}
									break;
								case 'TEXTAREA' :
									$valueToUpdate = "'" . substr($_POST[$key], 0, $formField->GetMaxLength()) . "'";
									break;
								case 'CHECK' :
									$valueToUpdate = $_POST[$key] == 'Y' ? "'Y'" : "'N'";
									break;
								default :
									$valueToUpdate = strlen($_POST[$key]) > 0 ? "'" . $_POST[$key] . "'" : 'NULL';
							} // switch
					} // switch
					$value_query .= empty ($value_query) ? $valueToUpdate : ', ' . $valueToUpdate;
				}
			}
			next($this->_formFields);
		} // while
		$query .= $field_query . ') values (' . $value_query . ')';
		$this->setFormQuery($query);
		return ($repost_form);
	}
	// Genera la query di modifica
	function setUpdateQuery() {
		$repost_form = FALSE;
		$query = 'update ' . strtolower($this->GetTableName()) . ' set ';
		foreach ($this->_formFields as $key => $updateField){
			if ($updateField->IsUpdatable()) {
				if ((is_null($_POST[$key]) or strlen(trim($_POST[$key])) == 0) and !$updateField->NullValueAllowed() and !$updateField->IsKey() and !($updateField->GetDataType() == 'FILE')) {
					$updateField->SetIsNull(TRUE);
					$repost_form = TRUE;
				}
				if ($key == 'UPDATED'){
					$valueToUpdate = $key . " = " . " now() ";
				} elseif ($key == 'UPDATED_BY'){
					$valueToUpdate = $key . " = " . $_SESSION['sess_uid'];
				} else {
					switch ($updateField->GetDataType()) {
						case 'FILE' :
							$valueToUpdate = strlen($_FILES[$key]['name']) > 0 ? $key . " = " . "'" . $this->GetFormName() . '-' . $_FILES[$key]['name'] . "'" : '';
							break;
						case 'NUMBER' :
							$valueToUpdate = strlen($_POST[$key]) > 0 ? $key . " = " . str_replace(',', '.', $_POST[$key]) : $key . " = " . 'NULL';
							break;
						case 'CHECK' :
							$valueToUpdate = $_POST[$key] == 'Y' ? $key . " = " . "'Y'" : $key . " = " . "'N'";
							break;
						default :
							$valueToUpdate = strlen($_POST[$key]) > 0 ? $key . " = " . "'" . $_POST[$key] . "'" : $key . " = " . 'NULL';
					} // switch
				}
				if (!empty ($valueToUpdate))
					$body_query .= empty ($body_query) ? $valueToUpdate : ', ' . $valueToUpdate;
			}
		} // while
		$query .= $body_query . ' ' . $this->getDbKey();
		$this->setFormQuery($query);
		return $repost_form;
	}



	function copyRecord($dbKey) {
		$selectedRecord = dbselect('select * from ' . strtolower($this->GetTableName()) . ' ' . $dbKey);
		if (!$selectedRecord) {
			$this->setFormMessage('Rcord non duplicato');
			$this->_FormMode = 'insert';
		} else {
			$duplicateQuery = 'insert into ' . $this->GetTableName() . ' (';
			$duplicateValues = ' values ( ';
			$comma = '';
			foreach ($this->_FormFields as $fieldName => $fieldObj) {
				if (!$fieldObj->IsKey() and $fieldObj->IsInTable()) {
					$duplicateQuery .= $comma . $fieldName;
					switch ($fieldObj->GetDataType()) {
						case 'NUMBER' :
							$duplicateValues .= $selectedRecord['ROWS'][0][$fieldName] > '' ? $comma . " " . addslashes($selectedRecord['ROWS'][0][$fieldName]) . " " : $comma . ' NULL ';
							break;
						default :
							$duplicateValues .= $comma . "'" . addslashes($selectedRecord['ROWS'][0][$fieldName]) . "'";
							break;
					}

					$comma = ',';
				}
				elseif ($fieldObj->IsKey()) {
					$dbKey = ' where ' . $fieldName . '=';
				}
			}
			$duplicateQuery .= ') ' . $duplicateValues . ') ';
		}
	}
	function getFieldsValues($dbKey) {
		/* Inizio modifica preselezione dei forms fields */
		$select_query = 'select ';
		$comma = false;
		$formFields=$this->getFormFields();
		while (!is_null($key = key($formFields))) {
			if ($formFields[$key]->IsInTable()) {
				switch ($formFields[$key]->GetDataType()) {
					//						case 'DATE' :
					//							$select_query .= $comma ? ', date_format(' . $key . ',"%d-%m-%Y") as ' . $key : ' date_format(' . $key . ',"%d-%m-%Y") as ' . $key;
					//							break;
					default :
						$select_query .= $comma ? ', ' . $key : $key;
				} // switch
				$comma = true;
			}
			next($formFields);
		} // while
		$select_query .= ' from ' . strtolower($this->getTableName()) . ' ' . $this->getDbKey();
		$result = dbselect($select_query);
		return ($result['ROWS'][0]);
	}
	function valorizeFields($fieldsValues) {
		reset($this->_formFields);
		while (!is_null($key = key($this->_formFields))) {
			if ($fieldsValues[$key] <> null) {
				switch ($this->getFormFields($key)->GetDataType()) {
					case 'FILE' :
						$this->_formFields[$key]->SetFile($fieldsValues[$this->GetFormKey()] . '-' .
						$fieldsValues[$key]);
						$this->_formFields[$key]->SetValue($fieldsValues[$this->GetFormKey()] . '-' .
						$fieldsValues[$key]);
						break;
					case 'DATE' :
						$this->_formFields[$key]->SetValue($fieldsValues[$key]);
						break;
					case 'NUMBER' :
						$this->_formFields[$key]->SetValue( str_replace('.', ',', $fieldsValues[$key]));
						break;
					default :
						$this->_formFields[$key]->SetValue($fieldsValues[$key]);
						break;
				}
			}
			next($this->_formFields);
		} // while
	}
	function resetForm() {
		//								Modifica Per riazzerare i fields dopo l'update'
		$this->_formButtons->barButtonEnable('buttadd');
		$this->_formButtons->barButtonDisable('buttapp');
//		$this->setFormAction('?mode=insert');
		reset($this->_formFields);
		while (!is_null($key = key($this->_formFields))) {
			$this->_formFields[$key]->SetValue(null);
			next($this->_formFields);
		} // while
	}
	function repostFormValues() {
		reset($this->_formFields);
		while (!is_null($key = key($this->_formFields))) {
			$this->_formFields[$key]->SetValue( $_POST[$key]);
			next($this->_formFields);
		} // while
	}


	private $_afterSubmitLocation = null;
	function setAfterSubmitLocation($url){
		$this->_afterSubmitLocation = $url;
	}
	function getAfterSubmitLocation(){
		if (!is_null($this->_afterSubmitLocation)) {
			header('Location: '.$this->_afterSubmitLocation);
		}
		return true;
	}


	private $_afterUpdateLocation = null;
	function getAfterUpdateLocation() {
		header('Location: '.$this->_afterUpdateLocation);
	}
	function setAfterUpdateLocation($value) {
		$this->_afterUpdateLocation = $value;
	}

	private $_afterInsertLocation = false;
	function getAfterInsertLocation() {
		header('Location: '.$this->_afterInsertLocation);
	}
	function setAfterInsertLocation($value) {
		$this->_afterInsertLocation = $value;
	}

	private$_FormMessage;
	function getFormMessage() {
		return $this->_FormMessage;
	}
	function setFormMessage($value) {
		$this->_FormMessage = $value;
	}

	function formMessageShow() {
		print ('<div class="DbFormMessage">');
		print ('&nbsp;' . $this->getFormMessage());
		print ('</div>');
		print ("\n");
	}
}
?>
