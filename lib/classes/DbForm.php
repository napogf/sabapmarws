<?php

// require_once("classes/table_c.inc");
/**
 * Class DbFrom
 * Generatore di Form basato su database
 * La Form Gestisce inserimento, modifica, cancellazione del record mappato
 * Codice rilasciato sotto licenza GNU/GPL
 *
 * @version $Id: form_c.inc,v 1.3 2011/05/30 10:20:49 cvsuser Exp $
 * @copyright 2003
 **/
class DbForm {

    protected $invalidFields = [];
	/*
	 * Funzioni Pubbliche generiche richiamabili senza istanziare la classe
	 *
	 */
	function passwordVerify($field) {
		global $passLength;
		// var_dump(strlen($field).$passLength.$field);
		if (strlen($field) < $passLength){
//			var_dump($field.$passLength);
			return FALSE;

		}
		return TRUE;
	}

	/**
	 *
	 * @access private
	 * @var string
	 **/

	var $_FormId = -1;

	function GetFormId() {
		return $this->_FormId;
	}

	function SetFormId($newValue) {
		$this->_FormId = $newValue;
	}

	var $_TableName = '';
	function GetInsertTableName() {

		if (preg_match('|(.*)_V$|i', $this->_TableName, $tbName)) {
			return(strtolower($tbName[1]));
		} else {
			return strtolower($this->_TableName);
		}

	}
	function GetTableName() {
		return strtolower($this->_TableName);
	}
	function SetTableName($newValue) {
		$this->_TableName = $newValue;
	}

	var $_FormName = '';
	function GetFormName() {
		return $this->_FormName;
	}
	function SetFormName($newValue) {
		$this->_FormName = $newValue;
	}
	var $_formDestination;
	function getFormDestination() {
		return $this->_formDestination;
	}
	function setFormDestination($destination) {
		$this->_formDestination = $destination;
	}

	var $_FormAction;

	function GetFormAction() {
		$value = is_null($this->_FormAction) ? $this->getFormDestination() . '?mode=insert"' : $this->_FormAction;
		return $value;
	}

	function SetFormAction($value) {
		$this->_FormAction = $value;
	}

	function AddFormActionParameter($value) {
		$this->_FormAction .= $value;
	}

	var $_FormEnctype = 'enctype="multipart/form-data" ';
	function GetFormEnctype() {
		return $this->_FormEnctype;
	}
	function SetFormEnctype($value) {
		$this->_FormEnctype = $value;
	}

	var $_FormMethod = 'METHOD="POST" ';
	function GetFormMethod() {
		return $this->_FormMethod;
	}
	function SetFormMethod($value) {
		$this->_FormMethod = $value;
	}

	function AddActionParameter($_param) {
		$this->SetFormAction($this->_FormAction . $_param);
	}

	var $_language = '';
	function GetLanguage() {
		return $this->_language;
	}
	function SetLanguage($newValue) {
		$this->_language = $newValue;
	}

	/**
	 * Campi Form
	 *
	 **/

	var $_FormFields = array ();
	function SetFormFields($key, $value) {
		$this->_FormFields[$key] = $value;
	}
	function GetFormFields() {
		return $this->_FormFields;
	}
	function AddFormFields($key, $value) {
		$this->_FormFields[$key] = $value;
	}

	protected $_FormFieldsFilters = array ();

	function AddFormFieldsFilter($key, $value) {
		$this->_FormFieldsFilters[$key] = $value;
	}



	function GetFormFieldsByName($key) {
		return $this->_FormFields[$key];
	}

	function GetFormFieldLabel($fieldName) {
		$_fieldObj = $this->GetFormFieldsByName($fieldName);
		return $_fieldObj->GetFieldLabel();

	}

	function SetFormFieldLabel($fieldName, $value) {
		$this->_FormFields[$fieldName]->SetFieldLabel($value);
		return true;

	}

	function SetFormFieldsDownloadLink($key, $value = '') {
		$this->_FormFields[$key]->SetDownloadLink($value);
	}

	function IsFormFieldNull($key) {
		return $this->_FormFields[$key]->IsNull();
	}

	function GetFormFieldType($key) {
		return $this->_FormFields[$key]->GetDataType();
	}

	function IsFormFieldUpdatable($key) {
		return $this->_FormFields[$key]->IsUpdatable();
	}

	function GetFormFieldValue($key) {
		return $this->_FormFields[strtoupper($key)]->GetValue();
	}

	function SetFormFieldValue($key, $value) {
		return $this->_FormFields[$key]->SetValue($value);
	}

	function SetFormFieldQuery($key, $query) {
		return $this->_FormFields[$key]->SetSelectTableQuery($query);
	}

	function GetFormFieldId($key) {
		return $this->_FormFields[$key]->GetId();
	}

	function HideFormField($key) {
		$this->_FormFields[$key]->SetShowed('N');
	}
	function ShowFormField($key) {
		$this->_FormFields[$key]->SetShowed('Y');
	}
	/**
	* Bottoniera
	*
	**/
	protected $_buttonBar;
	function setButtonBar($buttonBar) {
		$this->_buttonBar;
	}
	function getButtonBar() {
		return $this->_buttonBar;
	}

	var $_FormButtons;
	function GetFormButtons() {
		return $this->_FormButtons;
	}

	function SetFormButtons() {
		$ButtonBarObject = new ButtonsBar($this->getButtonBar());
		$this->_FormButtons = $ButtonBarObject;
	}

	function GetButtonByName($key) {
		return $this->_FormButtons->GetBarButtonByName($key);
	}

	function ShowButtonBar($mode = null) {
		return $this->_FormButtons->BarButtonShow();
	}

	var $_FormTitle = '';
	function GetFormTitle() {
		return $this->_FormTitle;
	}
	function SetFormTitle($newValue) {
		$this->_FormTitle = $newValue;
	}

	function GetFormHeader() {
		$_formHeader = '<FORM ACTION="' . $this->getFormDestination() . $this->GetFormAction() . '" ' . $this->GetFormEnctype() . $this->GetFormMethod() .
								'name="' . $this->GetFormName() . '" id="' . $this->GetFormId() . '" >' . "\n";
		$_formHeader .= '<input type="hidden" name="dbTable" value="' . $this->GetFormName() . '">' . "\n";
		return $_formHeader;

	}

	/**
	 * Inizializzo le Query Automatiche
	 *
	 **/
	var $_LastInsertId = -1;

	function GetLastInsertId() {
		return ($this->_LastInsertId);
	}

	function SetLastInsertId($newValue) {
		$this->_LastInsertId = $newValue;
		return true;
	}

	var $_FormQuery;
    protected $_params;
	function GetFormQuery() {
		// var_dump($this->_FormQuery);
		return $this->_FormQuery;
	}
	function SetFormQuery($value,$params=null) {
		$this->_FormQuery = $value;
        $this->_params = $params;

        return $this;
	}


	function ExecFormQuery() {
		$dir_upload = ($this->GetTableName() == 'arc_documenti' ? DOC_PATH : FILES_PATH) . DIRECTORY_SEPARATOR;
        $db = Db_Pdo::getInstance();
		if ($this->FormValidation()) {
		    $result = $db->query($this->getFormQuery(),$this->_params);
            foreach ($_FILES as $key => $value) {
                switch ($this->GetFormMode()) {
                    case 'insert' :
                        if (!empty ($value['name'])) {
                            copy($value['tmp_name'], $dir_upload . dbLastId() . '-' .$key.'-'. strtoupper($this->GetTableName()) . '-' . $value['name']);
                        }
                        break;
                    case 'modify' :
                        if (!empty ($value['name'])) {
                            $recImage=glob($dir_upload . $this->GetFormFieldValue($this->GetFormKey()) . '-' .$key.'-'. strtoupper($this->GetTableName()) . '-*');
                            if (is_array($recImage)){
                                foreach (glob($dir_upload . $this->GetFormFieldValue($this->GetFormKey()) . '-' .$key.'-'. strtoupper($this->GetTableName()) . '-*') as $filename) {
                                    if (file_exists($filename)){
                                        unlink($filename);
                                    }
                                }
                            } else {
                                if (file_exists($recImage)){
                                    unlink($recImage);
                                }
                            }
                            copy($value['tmp_name'], $dir_upload . $this->GetFormFieldValue($this->GetFormKey()) . '-' .$key.'-'. strtoupper($this->GetTableName()) . '-' . $value['name']);
                        }
                        break;
                }

            }

			return true;
		}
		return false;
	}

	var $_fileFieldsToLoad;
	function SetFileFieldToLoad($value) {
		$this->_fileFieldsToLoad[] = $value;
	}
	function GetFileFieldsToLoad() {
		return $this->_fileFieldsToLoad;
	}
	function LoadFiles() {
		$fieldsToLoad = $this->GetFileFieldsToLoad();
		for ($index = 0; $index < sizeof($fieldsToLoad); $index++) {
			$field = $fieldsToLoad[$index];
			$fileName = $_FILES[$field]['name'];
			$fileNameToLoad = $this->GetTableName() . '-' .
			$this->_FormFields[$field]->GetId() . '-' .
			$fileName;
//			var_dump($fileName);
		}
	}
	var $_SelectFormQuery;
	function GetSelectFormQuery() {
		return $this->_SelectFormQuery;
	}
	function SetSelectFormQuery($value) {
		$this->_SelectFormQuery = $value;
	}

	var $_DeleteFormQuery;
	function GetDeleteFormQuery() {
		return $this->_DeleteFormQuery;
	}
	function SetDeleteFormQuery($value) {
		$this->_DeleteFormQuery = $value;
	}

	var $_InsertFormQuery;
	function GetInsertFormQuery() {
		return $this->_InsertFormQuery;
	}
	function SetInsertFormQuery($value) {
		$this->_InsertFormQuery = $value;
	}

	var $_SequenceFieldQuery = '';

	function GetSequenceFieldQuery() {
		return $this->_SequenceFieldQuery;
	}

	function SetSequenceFieldQuery($newValue) {
		$this->_SequenceFieldQuery = $newValue;
	}

	var $_KeyFieldQuery = '';

	function GetKeyFieldQuery() {
		return $this->_KeyFieldQuery;
	}

	function SetKeyFieldQuery($newValue) {
		$this->_KeyFieldQuery = $newValue;
	}

	var $_afterUpdateLocation = null;

	function getAfterUpdateLocation() {
		return $this->_afterUpdateLocation;
	}

	function setAfterUpdateLocation($value) {
		$this->_afterUpdateLocation = $value;
	}

	var $_afterInsertLocation = null;

	function getAfterInsertLocation() {
		return $this->_afterInsertLocation;
	}

	function setAfterInsertLocation($value) {
		$this->_afterInsertLocation = $value;
	}

	function FormPostValidation() {
		return TRUE;
	}

	function FormPreValidation() {
		return TRUE;
	}

	function FormValidation() {
		return TRUE;
	}

	var $_FormMode = 'insert';
	function GetFormMode() {
		return $this->_FormMode;
	}

	function SetFormMode($mode, $dbKey) {
		$this->_FormMode = empty ($mode) ? 'insert' : $mode;
		if (!empty ($dbKey) and !is_null($dbKey) and strlen(trim($dbKey)) > 0) {
			$this->SetFormAction('?mode=' . $this->_FormMode . '&dbKey=' . $dbKey);
		} else {
			$this->SetFormAction('?mode=insert');
		}
		switch ($this->_FormMode) {
			case 'duplicate';
				$this->copyRecord($dbKey);
				break;
			case 'delete' :
				$this->SetFormQuery('delete from ' . strtolower($this->GetInsertTableName()) . ' ' . $dbKey);
				$this->SetFormAction('?mode=insert');
				$this->SetFormMessage(GetMessage('delrec'));
				$this->_FormButtons->BarButtonDisable('buttapp');
				$this->_FormButtons->BarButtonEnable('buttadd');
				$this->ExecFormQuery();
				$this->FormPostValidation();
				$this->_FormMode = 'insert';
				break;
			case 'modify' :
				$this->_FormButtons->BarButtonEnable('buttapp');
				$this->_FormButtons->BarButtonDisable('buttadd');
				// recupero dalla tabella i dati per valorizzare la form
				$fields_to_valorize = $this->getFiedlsValues($dbKey);
				// li carico nella form
				$this->valorizeFields($fields_to_valorize);

				/* Testo se il bottone Aggiorna è stato premuto se si devo aggiornare il db */
				if ($this->_FormButtons->BarButtonIsPressed('buttapp') and ($this->FormPreValidation())) {
					// ricavo dalla definizione della form tutti i campi da aggiornare e imposto la relativa query
					$repost_form = $this->setUpdateQuery($dbKey);
					if ($repost_form) {
//						r($this->invalidFields);
						$this->_FormButtons->BarButtonEnable('buttapp');
						$this->_FormButtons->BarButtonDisable('buttadd');
						$this->SetFormMessage('Inserire un valore nei seguenti campi: ' . implode(', ', $this->invalidFields));
						$this->repostFormValues();
					} else {
						if ($this->ExecFormQuery()) {
							// la modifica del record va a buon fine eseguo FormPostValidation e torno in insert
							$this->FormPostValidation();
							$this->SetFormMessage(GetMessage('modrec'));
							$this->SetFormAction('?mode=insert');
							$this->_FormButtons->BarButtonDisable('buttapp');
							$this->_FormButtons->BarButtonEnable('buttadd');
							$this->_FormMode = 'insert';
						} else {
							// la query fallisce e ritorno errore .....
							errore(var_dump(debug_backtrace()));
						}
						$this->resetForm();
						if (!is_null($this->getAfterUpdateLocation())) {
							header("Location: " . $this->getAfterUpdateLocation());
						}
					}
				}
				break;
			default :
				if ($this->GetFormMode() == 'insert') {
					if ($this->_FormButtons->BarButtonIsPressed('buttadd') and ($this->FormPreValidation())) {
						if ($this->setInsertQuery()) {
							$this->SetFormMessage(GetMessage('form_incomplete'));
							$this->_FormButtons->BarButtonDisable('buttapp');
							$this->_FormButtons->BarButtonEnable('buttadd');
							$this->repostFormValues();
							$this->FormPostValidation();
						} else {
							if ($this->ExecFormQuery()) {
								$this->SetLastInsertId(dbLastId());
								$this->FormPostValidation();
								$this->SetFormMessage(GetMessage('insrec'));
								// Ritorno al record Inserito se definita la funzione backTo()
								$backTo = $this->getAfterInsertLocation();

									if (!is_null($backTo)) {
										// Caso in cui voglio tornare in modifica
										if (is_bool($backTo)) {
											$this->SetFormAction('?mode=modify&dbKey=' . $this->getFormDbKey());
											$this->_FormButtons->BarButtonDisable('buttadd');
											$this->_FormButtons->BarButtonEnable('buttapp');
											$this->_FormMode = 'modify';
											$this->repostFormValues();
											$this->SetFormFieldValue($this->GetFormKey(), $this->GetLastInsertId());
										} elseif (is_string($backTo)) {
											header("Location: " . $backTo);
										}
									} else {
										// Caso in cui mi riposiziono In insert mode resetto la Form
										$this->resetForm();
									}
								} else {
									// la query fallisce e ritorno errore .....
									$this->_FormButtons->BarButtonDisable('buttapp');
									$this->_FormButtons->BarButtonEnable('buttadd');
									$this->repostFormValues();
//									errore(var_dump(debug_backtrace()));
								}
						}
					} else {
						$this->_FormButtons->BarButtonEnable('buttadd');
						$this->_FormButtons->BarButtonDisable('buttapp');
					}

				}
		} // switch
	}

	var $_recursiveFields = array();
	function setRecursiveFields($fieldArray){
		if (is_array($fieldArray)) $this->_recursiveFields=$fieldArray;
	}
	function initRecursiveFields(){
		for ($index = 0; $index < sizeof($this->_recursiveFields); $index++) {
			if (strlen(trim($_POST[$this->_recursiveFields[$index]]))>0) {
				$this->SetFormFieldValue($this->_recursiveFields[$index],$_POST[$this->_recursiveFields[$index]]);
			}
		}
	}

	function copyRecord($dbKey) {
		$selectedRecord = dbselect('select * from ' . strtolower($this->GetTableName()) . ' ' . $dbKey);
		if (!$selectedRecord) {
			$this->SetFormMessage('Rcord non duplicato');
			$this->_FormMode = 'insert';
		} else {
			$duplicateQuery = 'insert into ' . $this->GetInsertTableName() . ' (';
			$duplicateValues = ' values ( ';
			$comma = '';
			foreach ($this->_FormFields as $fieldName => $fieldObj) {
				if (!$fieldObj->IsKey() and $fieldObj->IsInTable() ) {
					$duplicateQuery .= $comma . $fieldName;
					switch ($fieldObj->GetDataType()) {
						case 'NUMBER' :
							$duplicateValues .= $selectedRecord['ROWS'][0][$fieldName] > '' ? $comma . " " . $selectedRecord['ROWS'][0][$fieldName] . " " : $comma . ' NULL ';
							break;
						default :
							$duplicateValues .= $comma . "'" . $selectedRecord['ROWS'][0][$fieldName] . "'";
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
		$this->SetFormQuery($duplicateQuery);
		$this->SetFormAction('?mode=insert');
		$this->SetFormMessage(GetMessage('copyrec'));
		$this->_FormButtons->BarButtonDisable('buttapp');
		$this->_FormButtons->BarButtonEnable('buttadd');
		$this->ExecFormQuery();
		$this->_FormMode = 'insert';
	}

	function getFiedlsValues($dbKey) {

		/* Inizio modifica preselezione dei forms fields */
		$select_query = 'select ';
		$comma = false;
		while (!is_null($key = key($this->_FormFields))) {
			if ($this->_FormFields[$key]->IsInTable()) {
				switch ($this->GetFormFieldType($key)) {
					//						case 'DATE' :
					//							$select_query .= $comma ? ', date_format(' . $key . ',"%d-%m-%Y") as ' . $key : ' date_format(' . $key . ',"%d-%m-%Y") as ' . $key;
					//							break;
					default :
						$select_query .= $comma ? ', ' . $key : $key;
				} // switch
				$comma = true;
			}
			next($this->_FormFields);
		} // while
		$select_query .= ' from ' . strtolower($this->GetTableName()) . ' ' . $dbKey;
		$result = dbselect($select_query);
		return ($result['ROWS'][0]);
	}

	function valorizeFields($fieldsValues) {
		reset($this->_FormFields);
		while (!is_null($key = key($this->_FormFields))) {
			if ($key == 'UPDATED_BY') {
				$this->SetFormFieldValue($key, $this->_FormFields[$key]->GetValue());
			} else {
				if ($fieldsValues[$key] <> null) {
					switch ($this->GetFormFieldType($key)) {
						case 'FILE' :
							$this->_FormFields[$key]->SetFile($fieldsValues[$this->GetFormKey()] . '-' .
							$key .'-'.
							$fieldsValues[$key]);
							$this->SetFormFieldValue($key, $fieldsValues[$this->GetFormKey()] . '-' .
							$key .'-'.
							$fieldsValues[$key]);
							break;
						case 'DATE' :
							if ($fieldsValues[$key]=='0000-00-00') {
								$this->SetFormFieldValue($key, '');
							} else {
								$this->SetFormFieldValue($key, $fieldsValues[$key]);
							}
							break;
						case 'NUMBER' :
							$this->SetFormFieldValue($key, str_replace('.', ',', $fieldsValues[$key]));
							break;
						default :
							$this->SetFormFieldValue($key, htmlspecialchars($fieldsValues[$key]));
							break;
					}
				}
			}
			next($this->_FormFields);
		} // while
	}

	function resetForm() {
		//								Modifica Per riazzerare i fields dopo l'update'
		$this->_FormButtons->BarButtonEnable('buttadd');
		$this->_FormButtons->BarButtonDisable('buttapp');
		$this->SetFormAction('?mode=insert');
		reset($this->_FormFields);
		while (!is_null($key = key($this->_FormFields))) {
			$this->SetFormFieldValue($key, null);
			next($this->_FormFields);
		} // while
		$this->initRecursiveFields();

	}

	function repostFormValues() {
		reset($this->_FormFields);
		while (!is_null($key = key($this->_FormFields))) {
			$this->SetFormFieldValue($key, $_POST[$key]);
			next($this->_FormFields);
		} // while
	}

	function setUpdateQuery($dbKey) {
		$repost_form = FALSE;
		$query = 'update ' . strtolower($this->GetTableName()) . ' set ';
		reset($this->_FormFields);
		while (!is_null($key = key($this->_FormFields))) {
			if ($this->IsFormFieldUpdatable($key)) {
				if ((is_null($_POST[$key]) or strlen(trim($_POST[$key])) == 0)
						and !$this->_FormFields[$key]->NullValueAllowed()
						and !$this->_FormFields[$key]->IsKey()
						and !($this->_FormFields[$key]->GetDataType() == 'FILE')) {
					$this->_FormFields[$key]->SetIsNull(TRUE);
					$this->invalidFields[] = $key;
					$repost_form = TRUE;
				}
				if (!$this->FormPreValidation()){
					$repost_form = TRUE;
				}
				switch ($key) {
					case 'UPDATED' :
						$valueToUpdate = $key . " = " . " now() ";
						break;
					case 'UPDATED_BY' :
						$valueToUpdate = $key . " = :" . $key;
                        $params[':'.$key] = $_SESSION['sess_uid'];
						break;
					default :
						switch ($this->GetFormFieldType($key)) {
                            case 'DATE':
                                $valueToUpdate = $key . " = :" . $key;
                                $params[':'.$key] = empty($_POST[$key]) ? null : (new Date($_POST[$key]))->toMysql();
                                break;
                            case 'FILE' :
                                $valueToUpdate = $key . " = :" . $key;
                                $params[':'.$key] = strlen($_FILES[$key]['name']) > 0 ?  strtoupper($this->GetTableName()) . '-' . $_FILES[$key]['name'] : '';
                                break;
                            case 'NUMBER' :

                                if(strlen($_POST[$key]) > 0){
                                    $valueToUpdate = $key . " = :" . $key;
                                    if(strpos($_POST[$key],',') > 0){
                                        $_POST[$key] = str_replace(',','.',$_POST[$key]);
                                    }
                                    $params[':'.$key] = strpos($_POST[$key],'.') > 0 ? (float) $_POST[$key] : (integer) $_POST[$key];
                                }

                                break;
                            case 'CHECK' :
                                $valueToUpdate = $key . " = :" . $key;
                                $params[':'.$key] = $_POST[$key] == 'Y' ? $_POST[$key] : 'N';
                                break;
                            default :
                                $valueToUpdate = $key . " = :" . $key;
                                $params[':'.$key] = (empty($_POST[$key]) ? null : $_POST[$key]);
                        } // switch
						break;
				}
				if (!empty ($valueToUpdate))
					$body_query .= empty ($body_query) ? $valueToUpdate : ', ' . $valueToUpdate;
    			}
			next($this->_FormFields);
		} // while
		$query .= $body_query . ' ' . $dbKey;
		$this->SetFormQuery($query,$params);
		return $repost_form;
	}

	function setInsertQuery() {
		$repost_form = false;
		$query = 'insert into ' . $this->GetInsertTableName() . ' set ';

		reset($this->_FormFields);
		while (!is_null($key = key($this->_FormFields))) {
            $valueToUpdate = null;
			if ($this->IsFormFieldUpdatable($key)) {
				if ((is_null($_POST[$key]) or strlen(trim($_POST[$key])) == 0) //												and !$this->_FormFields[$key]->IsKey()
				and !$this->_FormFields[$key]->NullValueAllowed() and !($this->_FormFields[$key]->GetDataType() == 'FILE')) {
					$this->_FormFields[$key]->SetIsNull(TRUE);
					$repost_form = TRUE;
				}

				if ($this->_FormFields[$key]->IsKey() and !$this->_FormFields[$key]->GetShowed() == 'N') {
					$value_query .= empty ($value_query) ? $this->_FormFields[$key]->GetValidation() : ', ' . $this->_FormFields[$key]->GetValidation();
					$this->SetSequenceFieldQuery(substr($this->_FormFields[$key]->GetValidation(), 0, strpos($this->_FormFields[$key]->GetValidation(), '.')));
					$this->SetKeyFieldQuery($key);
				} else {
					switch ($key) {
                        case 'CREATED' :
                            $valueToUpdate = $key . " = " . " now() ";
                            break;
                        case 'CREATED_BY' :
                            $valueToUpdate = $key . " = :" . $key;
                            $params[':'.$key] = $_SESSION['sess_uid'];
                            break;
						default :
							switch ($this->GetFormFieldType($key)) {
                                case 'DATE':
                                    $valueToUpdate = $key . " = :" . $key;
                                    $params[':'.$key] = empty($_POST[$key]) ? null : (new Date($_POST[$key]))->toMysql();
                                    break;
                                case 'FILE' :
                                    $valueToUpdate = $key . " = :" . $key;
                                    $params[':'.$key] = strlen($_FILES[$key]['name']) > 0 ?  strtoupper($this->GetTableName()) . '-' . $_FILES[$key]['name'] : '';
                                    break;
                                case 'NUMBER' :
                                    if(strlen($_POST[$key] == 0)){
                                        $valueToUpdate = $key . " = :" . $key;
                                        if(strpos($_POST[$key],',') > 0){
                                            str_replace(',','.',$_POST[$key]);
                                        }
                                        $params[':'.$key] = strpos($_POST[$key],'.') > 0 ? (float) $_POST[$key] : (integer) $_POST[$key];
                                    }

                                    break;
                                case 'CHECK' :
                                    $valueToUpdate = $key . " = :" . $key;
                                    $params[':'.$key] = $_POST[$key] == 'Y' ? $_POST[$key] : 'N';
                                    break;
                                default :
                                    $valueToUpdate = $key . " = :" . $key;
                                    $params[':'.$key] = (empty($_POST[$key]) ? null : $_POST[$key]);
							} // switch
					} // switch
                    if(!is_null($valueToUpdate)){
                        $body_query .= empty ($body_query) ? $valueToUpdate : ', ' . $valueToUpdate;
                    }
				}
			}
			next($this->_FormFields);
		} // while

		$this->SetFormQuery($query . $body_query,$params);
		return ($repost_form);
	}

	var $_FormAttachments;
	function GetFormAttachments() {
		return $this->_FormAttachments;
	}
	function SetFormAttachments($value) {
		$this->_FormAttachments = $value == 'Y' ? TRUE : FALSE;
	}

	function FormAttachmentsShow() {
		if ($this->GetFormMode() == 'modify' and $this->GetFormAttachments()) {
			print ('<tr>' . "\n");
			print ('<hr>
																						<table width="100%" border="0" cellspacing="0" cellpadding="0">
																			  			<tr>' . "\n");
			print ('<td valign="middle" align="left" width="100%" class="lista2" nowrap >' .
			'<img STYLE="cursor: pointer; padding-left: 20; padding-right: 5;" src="graphics/attach.png" ' .
			' width="28" height="28" border="0" ' .
			' onClick="location.href=\'dbAttach.php?dbTable=ARC_ATTACHMENTS&LINK_ID=' .
			$this->GetFormFieldValue($this->GetFormKey()) .
			'&FORM_NAME=' . $this->GetFormName() .
			'&dbFilter=LINK_ID='.$this->GetFormFieldValue($this->GetFormKey()).
			'\'" Title="Atachments" >' . 'Allegati</td>' . "\n");
			print ('</tr></table><hr>' . "\n");
			print ("</tr>\n");
		}
	}

	var $_formKey;
	function SetFormKey($value) {
		$this->_formKey = $value;
	}
	function GetFormKey() {
		return $this->_formKey;
	}
	function getFormDbKey() {
		return ('where ' . $this->_formKey . '=' . $this->GetLastInsertId());
	}

	var $_FormMessage;
	function GetFormMessage() {
		return $this->_FormMessage;
	}
	function SetFormMessage($value) {
		$this->_FormMessage = $value;
	}

	function FormMessageShow() {
		print ('<tr><td class="DbFormMessage">');
		print ('&nbsp;' . $this->GetFormMessage());
		print ('</td></tr>');
		print ("\n");
	}

	/**
	 * Constructor
	 * @access protected
	 */
	function __construct($Form, $Language = 1) {
		if ($Form == '') {
			print ('Errore Form Name non Passato!<br>');
		} else {
			$this->SetLanguage($Language);
			$sql = "SELECT FORMSTB.*, " .
			"LABELS.TITLE " .
			"FROM sys_forms FORMSTB " .
			" LEFT JOIN sys_forms_titles LABELS on (LABELS.FORM_ID = FORMSTB.FORM_ID AND LABELS.LANGUAGE_ID = '$Language')" .
			" WHERE FORMSTB.FORM_NAME = '$Form'";
			$DbForm = dbselect($sql);
			$this->SetFormMessage('');
			$this->SetFormName($DbForm['ROWS'][0]['FORM_NAME']);
			$this->SetTableName($DbForm['ROWS'][0]['TABLE_NAME']);
			$this->SetFormId($DbForm['ROWS'][0]['FORM_ID']);
			$this->SetFormTitle($DbForm['ROWS'][0]['TITLE']);
			$this->SetFormAttachments($DbForm['ROWS'][0]['ATTACH']);
			// $ButtonBarObject = new ButtonsBar();
			$this->SetFormButtons();
			$this->LoadFields();

		}
	}

	function LoadFields() {
		$sql = "select " .
		"FFIELDS.*, " .
		"LABELS.DESCRIPTION LABEL " .
		"FROM sys_forms_fields FFIELDS " .
		"	LEFT JOIN sys_forms_fields_labels LABELS ON (LABELS.FIELD_ID = FFIELDS.FIELD_ID AND LABELS.LANGUAGE_ID = '" . $this->GetLanguage() . "') " .
		"where FFIELDS.FORM_ID='" . $this->GetFormId() . "' " .
		"ORDER BY FFIELDS.VSEQ, FFIELDS.HSEQ";
		$DbColumns = dbselect($sql);
		for ($i = 0; $i < $DbColumns['NROWS']; $i++) {
			$field_object = new FormFields($DbColumns['ROWS'][$i]['FIELD_NAME']);
			$field_object->SetDataType($DbColumns['ROWS'][$i]['DATA_TYPE']);
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
				$this->SetFileFieldToLoad($DbColumns['ROWS'][$i]['FIELD_NAME']);
			}
			if (strtoupper($DbColumns['ROWS'][$i]['IS_KEY']) == 'Y') {
				$field_object->Setkey();
				$this->SetFormKey($DbColumns['ROWS'][$i]['FIELD_NAME']);
			}
			$this->AddFormFields($DbColumns['ROWS'][$i]['FIELD_NAME'], $field_object);
			if($DbColumns['ROWS'][$i]['LISTED']=='F'){
				$this->AddFormFieldsFilter($DbColumns['ROWS'][$i]['FIELD_NAME'], $field_object);
			}

		} // for

	}

	function ShowForm() {

		print ('<table border="0" cellPadding="1" cellSpacing="1" width="100%">');
		print ("\n");
		print ('<!-- Form open -->');
		print ("\n");
		print ($this->GetFormHeader());
		print ("\n");
		// Titolo
		print ('<tr><td align="center" width="100%" cellpadding="10" ><div class="DbFormTitle">');
		print ($this->GetFormTitle());
		print ('</div></td></tr>');
		print ("\n");
		// Messaggio
		$this->FormMessageShow();
		// Sopra
		print ('<tr><td>');

		$this->FormAttachmentsShow();

		$this->ShowButtonBar('top');

		print ('</td></tr>');
		print ("\n");
		$Formfields = $this->GetFormFields();
		reset($Formfields);
		print ('<tr><td><table>');
		while (!is_null($key = key($Formfields))) {
			$Formfields[$key]->ShowField();
			next($Formfields);
		} // while
		print ('</table></td></tr>');
		print ("\n");
		// Sotto
		print ('<tr><td>');
		$this->ShowButtonBar('bottom');
		print ('</td></tr>');
		print ("\n");
		print ('<div id="message"></div>' . "\n");
		print ('</Form>');
		print ("\n");
		print ('</table>' . "\n");

	}

}
?>
