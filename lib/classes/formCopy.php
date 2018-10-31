<?php
class formCopy {
	private $_formName;
	private $_formId;
	private $_newFormId;
	private $_newFieldId;
	private $_fieldsArray = array ();
	private $_formStruct = array ();
	private $_titleStruct = array ();
	private $_fieldStruct = array ();
	private $_labelStruct = array ();

	function __construct($formName) {
		$this->_formName = $formName;
		if (!$formResult = dbselect('select FORM_ID from sys_forms where form_name = \'' . strtoupper($formName) . '\' ')) {
			print('Importo la form ' . $this->_formName .'</br>');
			return true;
		} else {
			$this->_formId = $formResult['ROWS'][0]['FORM_ID'];
		}
	}

	function exportForm(){
		$creaTmpForm = 'create table tmp_f_' . $this->_formName . ' select * from sys_forms where sys_forms.form_id = ' . $this->_formId;
		dbupdate($creaTmpForm);
		$this->createFormTitles();

	}
	function createFormTitles(){
		if (!$fieldsResult = dbselect('select TITLE_ID from sys_forms_titles where form_id=' . $this->_formId)) {
			print ('I Titolis non esistono! Chiama l\'amministratore!');
			return false;
		} else {
			$creaTmpFormTitles = 'create table tmp_ft_' . $this->_formName . ' select * from sys_forms_titles where sys_forms_titles.form_id = ' . $this->_formId;
			dbupdate($creaTmpFormTitles);
			$this->createFields();
		}
	}
	function createFields() {
		if (!$fieldsResult = dbselect('select FIELD_ID from sys_forms_fields where form_id=' . $this->_formId)) {
			print ('I fields non esistono! Chiama l\'amministratore!');
			return false;
		} else {
			$creaTmpFormFields = 'create table tmp_ff_' . $this->_formName . ' select * from sys_forms_fields where sys_forms_fields.form_id = ' . $this->_formId;
			foreach ($fieldsResult['ROWS'] as $value) {
				$this->_fieldsArray[] = $value['FIELD_ID'];
			}
			dbupdate($creaTmpFormFields);
			$this->createFormsFieldsLabels();
		}
	}
	function createFormsFieldsLabels() {
		$creaTmpFormsFieldsLabels = 'create table tmp_ffl_' . $this->_formName . ' select * from sys_forms_fields_labels where field_id in (' . implode(",", $this->_fieldsArray) . ')';
		dbupdate($creaTmpFormsFieldsLabels);
		print ('Create le tabelle temporanee per la successiva importazione!');
	}
	function importForm() {
		$impForm = 'select * from tmp_f_' . $this->_formName;
		if (!$formResult = dbselect($impForm)) {
			print ('La form ' . $this->_formName . ' non esiste!');
			return false;
		} else {
			$fieldsResult = dbselect('show columns from tmp_f_' . $this->_formName);
			foreach ($fieldsResult['ROWS'] as $value) {
				if ($value['Field'] <> 'FORM_ID')
					$this->_formStruct[] = $value['Field'];
			}
			$fieldsResult = dbselect('show columns from tmp_ft_' . $this->_formName);
			foreach ($fieldsResult['ROWS'] as $value) {
				if ($value['Field'] <> 'TITLE_ID')
					$this->_titleStruct[] = $value['Field'];
			}
			$fieldsResult = dbselect('show columns from tmp_ff_' . $this->_formName);
			foreach ($fieldsResult['ROWS'] as $value) {
				if ($value['Field'] <> 'FIELD_ID')
					$this->_fieldStruct[] = $value['Field'];
			}
			$fieldsResult = dbselect('show columns from tmp_ffl_' . $this->_formName);
			foreach ($fieldsResult['ROWS'] as $value) {
				if ($value['Field'] <> 'LABEL_ID')
					$this->_labelStruct[] = $value['Field'];
			}
			$insFieldsQuery = 'insert into sys_forms (';
			$insValueQuery = ' values (';
			$token = '';
			foreach ($formResult['ROWS'] as $value) {
				foreach ($this->_formStruct as $field) {
					$insFieldsQuery .= $token . $field;
					$insValueQuery .= $token . '\'' .addslashes($value[$field]). '\'';
					$token = ',';
				}
			}
			$insQuery=$insFieldsQuery.') '.$insValueQuery.')';
			print($insQuery."</br>\n");
			dbupdate($insQuery);
			$this->_newFormId=dbLastId();
		}
		$this->importTitles();
		dbupdate('drop table tmp_f_'.$this->_formName);
		dbupdate('drop table tmp_ff_'.$this->_formName);
		dbupdate('drop table tmp_ft_'.$this->_formName);
		dbupdate('drop table tmp_ffl_'.$this->_formName);
		return true;
	}
	function importTitles(){
		$impTitles = 'select * from tmp_ft_' . $this->_formName;
		$titleResult=dbselect($impTitles);
		foreach ($titleResult['ROWS'] as $value) {
			$insFieldsQuery = 'insert into sys_forms_titles (';
			$insValueQuery = ' values (';
			$token = '';
			foreach ($this->_titleStruct as $field) {
				if ($field=='FORM_ID'){
					$insFieldsQuery .= $token . $field;
					$insValueQuery .= $token . $this->_newFormId;
					$token = ',';
				} else {
					$insFieldsQuery .= $token . $field;
					$insValueQuery .= $token . '\'' .addslashes($value[$field]). '\'';
					$token = ',';
				}
			}
			$insQuery=$insFieldsQuery.') '.$insValueQuery.')';
			print($insQuery."</br>\n");
			dbupdate($insQuery);
		}
		$this->importFields();
	}
	function importFields() {
		$impFields = 'select * from tmp_ff_' . $this->_formName;
		$fieldResult=dbselect($impFields);
		foreach ($fieldResult['ROWS'] as $value) {
			$insFieldsQuery = 'insert into sys_forms_fields (';
			$insValueQuery = ' values (';
			$token = '';
			foreach ($this->_fieldStruct as $field) {
				if ($field=='FORM_ID'){
					$insFieldsQuery .= $token . $field;
					$insValueQuery .= $token . $this->_newFormId;
					$token = ',';
				} else {
					$insFieldsQuery .= $token . $field;
					$insValueQuery .= $token . '\'' .addslashes($value[$field]). '\'';
					$token = ',';
				}
			}
			$insQuery=$insFieldsQuery.') '.$insValueQuery.')';
			print($insQuery."</br>\n");
			dbupdate($insQuery);
			$this->_newFieldId=dbLastId();
			$this->importLabels($value['FIELD_ID']);
		}
	}

	function importLabels($fieldId) {
		$impFields = 'select * from tmp_ffl_' . $this->_formName.' where field_id = '.$fieldId;
		$labelResult=dbselect($impFields);
		foreach ($labelResult['ROWS'] as $value) {
			$insFieldsQuery = 'insert into sys_forms_fields_labels (';
			$insValueQuery = ' values (';
			$token = '';
			foreach ($this->_labelStruct as $field) {
				if ($field=='FIELD_ID'){
					$insFieldsQuery .= $token . $field;
					$insValueQuery .= $token . $this->_newFieldId;
					$token = ',';
				} else {
					$insFieldsQuery .= $token . $field;
					$insValueQuery .= $token . '\'' .addslashes($value[$field]). '\'';
					$token = ',';
				}
			}
			$insQuery=$insFieldsQuery.') '.$insValueQuery.')';
			print($insQuery."</br>\n");
			dbupdate($insQuery);
		}
	}
}

?>