<?php
/*
 * Created on 15/mag/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
 *
 *
 **/
class FormFields {
	/**
	 * Constructor
	 * @access protected
	 */

	private $_FieldName = '';
	function GetFieldName() {
		return $this->_FieldName;
	}
	function SetFieldName($newValue) {
		$this->_FieldName = $newValue;
	}

	private $_Label = '';
	function GetFieldLabel() {
		return $this->_Label;
	}
	function SetFieldLabel($newValue) {
		$this->_Label = $newValue;
	}

	private $_Type = '';
	function GetDataType() {
		return $this->_Type;
	}
	function SetDataType($newValue) {
		$this->_Type = $newValue;
	}

	private $_Value;
	function GetValue() {
		if ($this->_Value == '') {
			return (stripslashes($this->GetDefaultValue()));
		} else {
			return stripslashes($this->_Value);
		}
	}
	function SetValue($value) {
		$this->_Value = $value;
	}

	private $_File;
	function GetFile() {
		return stripslashes($this->_File);
	}
	function SetFile($value) {
		$this->_File = $value;
	}
	private $_dirUpload=null;
	function setDirUpload($value){
		$this->_dirUpload=$value;
	}
	function getDirUpload(){
		if (is_null($this->_dirUpload)) {
			return $_SESSION['sess_dirUpload'];
		}
		return $this->_dirUpload;
	}

	private $_IsInTable = 'Y';
	function IsInTable() {
		if ($this->_IsInTable == 'Y') {
			return TRUE;
		} else {
			return FALSE;

		}
	}

	function SetInTable($value) {
		$this->_IsInTable = $value;
	}

	private $_IsKey = 'N';
	function IsKey() {
		if ($this->_IsKey == 'Y') {
			return TRUE;
		} else {
			return FALSE;

		}
	}

	function SetKey() {
		$this->_IsKey = 'Y';
	}
	private $_filterField = false;
	public function setFilter() {
		$this->_filterField = true;
	}
	public function isFilter() {
		return $this->_filterField;
	}


	private $_updatable;
	function IsUpdatable() {
		if ($this->_updatable == 'Y') {
			return TRUE;
		}
		return FALSE;
	}
	function SetUpdatable($value) {
		$this->_updatable = $value;
	}
	private $_NullValue;
	function NullValueAllowed() {
		if ($this->_NullValue == 'Y') {
			return TRUE;
		}
		return FALSE;
	}
	function SetNullValue($value) {
		$this->_NullValue = $value;
	}

	private $_Visible = '';
	function GetShowed() {
		if (($this->_Visible == 'Y') or ($this->_Visible == 'R')) {
			return TRUE;
		} else {
			return FALSE;

		}
	}
	function SetShowed($newValue) {
		$this->_Visible = $newValue;
	}
	function hideField(){
		$this->_Visible = 'N';
	}
	function isReadOnly(){
		if ($this->_Visible == 'R') {
			return ' readonly ';
		} else {
			return FALSE;

		}
	}

	private $_TextareaRows = '';
	function GetTextareaRows() {
		return $this->_TextareaRows;
	}
	function SetTextareaRows($value) {
		$this->_TextareaRows = $value;
	}
	private $_fieldFormat;
	function setFieldFormat($value) {
		$this->_fieldFormat = $value;
	}
	function getFieldFormat() {
		return ($this->_fieldFormat);
	}

	private $_Length = -1;
	function GetLength() {
		return $this->_Length;
	}
	function SetLength($newValue) {
		$this->_Length = $newValue;
	}

	private $_MaxLength = -1;
	function GetMaxLength() {
		return $this->_MaxLength;
	}
	function SetMaxLength($newValue) {
		if (intval($newValue) > 0) {
			$this->_MaxLength = $newValue;
		} else {
			$this->_MaxLength = $this->GetLength();
		}

	}


	private $_Id = null;
	function GetId() {
		return $this->_Id;
	}
	function SetId($value) {
		$this->_Id = $value;
	}

	private $_lookup = '';
	function getLookup() {

		$lookArray = explode(';', $this->_lookup);
			$lookQuery = array ();
			for ($index = 0; $index < sizeof($lookArray); $index++) {
				preg_match('[(.*)=(.*)]', $lookArray[$index], $lookResult);
				$lookQuery[$lookResult[1]] = $lookResult[2];
			}
			$keyField = $lookQuery['KEY-P'] > '' ? $lookQuery['KEY-P'] : $lookQuery['KEY'];
			$returnQuery = 'select ' . $keyField . ', ' . $lookQuery['DESCRIPTION'] . ' as DESCRIPTION from ' . $lookQuery['TABLE'] . ' where ' . $keyField . ' = \'' . addslashes($this->GetValue()) .'\'';
		return $returnQuery;
	}
	function setLookup($newValue) {
		$this->_lookup = $newValue;
	}

	private $_DefaultValue = null;
	function GetDefaultValue() {
		$default_value = null;
		if ($this->_DefaultValue>'') {
			if (preg_match_all('[<(.+)/>]U', $this->_DefaultValue, $sess_var)) {
				for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
					$this->_DefaultValue = preg_replace('[' . $sess_var[0][$z] . ']', $_SESSION[$sess_var[1][$z]], $this->_DefaultValue);
				} // for
			}
			if (!empty($this->_DefaultValue)) {
				eval ("\$default_value=" . $this->_DefaultValue . ";");
			}
		}
		return $default_value;
	}
	function SetDefaultValue($value) {
		$this->_DefaultValue = $value;
	}

	private $_Validation;
	function GetValidation() {
		return $this->_Validation;
	}
	function SetValidation($value) {
		$_SESSION['sess_lang'] = $_SESSION['sess_lang'];
		$_SESSION['sess_uid'] = $_SESSION['sess_uid'];
		$sess_person_id = $_SESSION['sess_person_id'];

		// testo se  una validazione da fare in AJAX
		if (preg_match('[^<AJAX>(.+)</AJAX>]', $value, $ajaxValidation)) {
			$this->AjaxValidation($ajaxValidation[1]);
			return true;
		}
		if (preg_match('[^<SQL>(.+)</SQL>]', $value, $sqlValidation)) {
			if (preg_match_all('|<([a-z,A-Z].+)/>|U', $sqlValidation[1], $sess_var)) {
				for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
					$sqlValidation[1] = preg_replace('[' . $sess_var[0][$z] . ']', $_SESSION[$sess_var[1][$z]], $sqlValidation[1]);
				} // for
			}
			$this->setSqlQuery($sqlValidation[1]);
			return true;
		}
		if (preg_match('[SQL=(.+)]', $value, $sqlValidation)) {
			if (preg_match_all('|<([a-z,A-Z].+)/>|U', $sqlValidation[1], $sess_var)) {
				for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
					$sqlValidation[1] = preg_replace('[' . $sess_var[0][$z] . ']', $_SESSION[$sess_var[1][$z]], $sqlValidation[1]);
				} // for
			}
			$this->setSqlQuery($sqlValidation[1]);
			return true;
		}
		$this->_Validation = $value;
		$delimiter = ';';
		$splarray = explode($delimiter, $this->_Validation);
		for ($i = 0; $i < sizeof($splarray); $i++) {
			if (preg_match('[^TABLE=(.+)]', $splarray[$i], $searched_value)) {
				$this->SetSelectTable($searched_value[1]);
			}
			if (preg_match('[KEY=(.*)]', $splarray[$i], $searched_value)) {
				$this->SetSelectKey($searched_value[1]);
			}
			if (preg_match('[DESTFIELD=(.*)]', $splarray[$i], $searched_value)) {
				$this->SetSelectDestField($searched_value[1]);
			}
			if (preg_match('[DESCRIPTION=(.*)]', $splarray[$i], $searched_value)) {

				$this->SetSelectField($searched_value[1]);

			}
			if (preg_match('[FILTER=(.*)]', $splarray[$i], $searched_value)) {
				if (preg_match_all('[<([a-zA-Z0-9].+)/>]U', $searched_value[1], $sess_var)) {
					for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
						$searched_value[1] = preg_replace('[' . $sess_var[0][$z] . ']', $_SESSION[$sess_var[1][$z]], $searched_value[1]);
					} // for
				}
				$this->SetSelectFilter($searched_value[1]);
			}
			if (preg_match('[NULL=(.*)]', $splarray[$i], $searched_value)) {
				if (preg_match('[false]i', $searched_value[1])) {
					$this->SetSelectNullValue(FALSE);
				} else {
					$this->SetSelectNullValue(TRUE);
				}

			}
			if (preg_match('[ORDER_BY=(.*)]', $splarray[$i], $searched_value)) {
				$this->SetSelectOrderBy($searched_value[1]);
			}
			if (preg_match('[MULTIPLE=(.*)]', $splarray[$i], $searched_value)) {
				$this->SetIsMultiple($searched_value[1]);
			}
			if (preg_match('[SIZE=(.*)]', $splarray[$i], $searched_value)) {
				$this->SetSelectSize($searched_value[1]);
			}
			if (preg_match('[QUERY=(.+)]', $splarray[$i], $searched_value)) {
				$this->SetSelectTableQuery($searched_value[1]);
			}
			if (preg_match('[GRPDESC=(.+)]', $splarray[$i], $searched_value)) {
				$this->SetGroupDescriptionField($searched_value[1]);
			}
			if (preg_match('[QUERY=(.+)]', $splarray[$i], $searched_value)) {

				if (preg_match_all('[<(.+)/>]U', $searched_value[1], $sess_var)) {
					for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
						$searched_value[1] = preg_replace('[' . $sess_var[0][$z] . ']', $_SESSION[$sess_var[1][$z]], $searched_value[1]);
					} // for
				}
				$this->SetSelectTableQuery($searched_value[1]);
			}

		} // for

	}

	private $_sqlQuery;
	function setSqlQuery($value) {

		$this->_sqlQuery = $value;
	}
	function getSqlQuery(){
		return $this->_sqlQuery;
	}


	// Ajax Post validation
	private $_postValidation;
	function setPostValidation($value){
		$this->_postValidation=$value;
	}
	function getPostValidation(){
		if (!is_null($this->_postValidation)) return ' onChange="'.$this->_postValidation.'" ';
	}
	// Ajax Select Control

	private $_jxSql;

	function setJxSql($value) {
		$this->_jxSql = addslashes($value);
	}

	function getJxSql() {
		$this->_jxSql = preg_replace_callback('[<J_(.*)/>]U', "ajaxCallBack", $this->_jxSql);
		$this->_jxSql = preg_replace('[<JAUTO/>]U', "'+$('filter_" . $this->getId() . "').value+'", $this->_jxSql);
		return $this->_jxSql;
	}
	function setJxTextSearch() {
		$value = preg_match('[<JAUTO/>]U', $this->_jxSql) ? $fieldToReturn = '<input type="TEXT" id="filter_' . $this->getId() . '" name="FILTER" value="" size="10" maxlength="10" >' . "\n" : '';

		return ($value);
	}

	function AjaxValidation($value) {
		$this->setJxSql($value);
	}

	function AjaxPopulate() {
		// rimuovo la where dalla chiamata ajax che costruisce la select e ci metto where field_name = field_value
		preg_match('|.* (.*),|U',$this->getJxSql(),$key);
		$keyQuery = preg_replace('|distinct|','',$key[1]);
		$decodQuery=preg_replace('[(where.*)]','where '.$keyQuery.' = '.$this->GetValue(),$this->getJxSql());
		$decodResult=rowselect(stripslashes($decodQuery));
		return ('<option value="'.$decodResult[0].'">'.$decodResult[1].'</option>'."\n");
	}

	function GetAjaxField() {
		$fieldToReturn = $this->setJxTextSearch();
		$jxLength=$this->GetMaxLength()>20?$this->GetMaxLength():20;
		$nullValue=$this->NullValueAllowed()?1:0;
		$fieldToReturn .= "\n" . '<select style = "width:'.$jxLength.'em" name="' .
		$this->getFieldName() . '" id="' . $this->getId() . '" onfocus="' .
		'new getAjaxSelect(this ,\'' . $this->getJxSql() . '\','.$nullValue.');" '.$this->getPostValidation().'>' . "\n";

		$nullOption = $this->NullValueAllowed() ? "\n" . '<option value="">----------------</option>' . "\n" : '';

		$selectedValue = $this->GetValue() > '' ? $this->AjaxPopulate() : '';

		$fieldToReturn .= $nullOption;
		$fieldToReturn .= $selectedValue;
		$fieldToReturn .= '</select>' . "\n";
		$fieldToReturn .= '<div id="loading-' . $this->getId() . '" style="margin-left: 10px; display: none;" >
															loading data ....
															<img src="graphics/loading.gif" />
						 								</div> ';
		return $fieldToReturn;
	}



	// Ajax Dojo based Select
	function getDjSelect($prefix = ''){
		$jxLength=$this->GetMaxLength()>200?$this->GetMaxLength():200;

		$nullValue = $this->NullValueAllowed()?'Y':'N';
 		$fieldToReturn = '<div dojoType="dojo.data.ItemFileReadStore" ' .
							'url="xml/jsonSql.php?nullValue='.$nullValue.'&sql='.$this->GetSqlQuery().'" ' .
							'jsId="'.$prefix.$this->getFieldName().$this->GetId().'" ' .
							'></div>';
		$fieldToReturn .= '<div dojoType="dijit.form.FilteringSelect"  ' .
							'store="'.$prefix.$this->getFieldName().$this->GetId().'"
							searchAttr="DESCRIPTION" ' .
									'name="'.$this->getFieldName().'" ' .
									'id="'.$prefix.$this->GetId().'" ' .
									' value="'.$this->GetValue().'" ' .
									' style="width:'.$jxLength.'px;" '.
									' queryExpr="${0}*" ' .
									' searchDelay="1000"  ' .
									' pageSize="100" ' .
//									' searchDelay="2000" ' .
									'></div>';

		return $fieldToReturn;
	}


	// Ajax Dojo based Select
	function getDjQuerySelect(){

		$jxLength=$this->GetMaxLength()>200?$this->GetMaxLength():200;
		$sql=preg_replace('|FIELDVALUE|',$this->GetValue(),$this->GetSqlQuery());
		$nullValue = $this->NullValueAllowed()?'Y':'N';

 		$fieldToReturn = '<div dojoType="dojox.data.QueryReadStore" ' .
							'url="xml/jsonSql.php?nullValue='.$nullValue.'&sql='.$sql.'" ' .
							'jsId="'.$this->getFieldName().$this->GetId().'" ' .
							'></div>';
		$fieldToReturn .= '<div dojoType="dijit.form.FilteringSelect"  ' .
							'store="'.$this->getFieldName().$this->GetId().'"
							searchAttr="DESCRIPTION" ' .
									'name="'.$this->getFieldName().'" ' .
									'id="'.$this->GetId().'" ' .
									' value="'.$this->GetValue().'" ' .
									' style="width:'.$jxLength.'px;" '.
									' searchDelay="2000" ' .
									' pageSize="100" ' .
									'></div>';

		return $fieldToReturn;
	}



	private $_GroupDescriptionField;
	function GetGroupDescriptionField() {
		return $this->_GroupDescriptionField;
	}

	function SetGroupDescriptionField($value) {
		$this->_GroupDescriptionField = $value;
	}

	private $_SelectTableQuery;
	function GetSelectTableQuery() {
		return $this->_SelectTableQuery;
	}
	function SetSelectTableQuery($value) {
		$this->_SelectTableQuery = $value;
	}

	private $_SelectTable;
	function GetSelectTable() {
		return $this->_SelectTable;
	}
	function SetSelectTable($value) {
		$this->_SelectTable = $value;
	}
	private $_SelectKey;
	function GetSelectKey() {
		return $this->_SelectKey;
	}
	function SetSelectKey($value) {
		$this->_SelectKey = $value;
	}
	private $_SelectField;
	function GetSelectField() {
		return $this->_SelectField;
	}
	function SetSelectField($value) {
		$this->_SelectField = $value;
	}

	private $_DownloadLink = '';

	function GetDownloadLink() {
		return $this->_DownloadLink;
	}
	function SetDownloadLink($value) {
		$this->_DownloadLink = $value;
	}

	private $_SelectValue;
	function GetSelectValue() {
		return $this->_SelectValue;
	}
	function SetSelectValue($value) {
		$this->_SelectValue = $value;
	}
	private $_SelectDestField;
	function GetSelectDestField() {
		return $this->_SelectDestField;
	}
	function SetSelectDestField($value) {
		$this->_SelectDestField = $value;
	}
	private $_SelectFilter;
	function GetSelectFilter() {
		return $this->_SelectFilter;
	}
	function SetSelectFilter($value) {
		$this->_SelectFilter = $value;
	}
	private $_IsNullValue;
	function GetSelectNullValue() {
		return $this->_IsNullValue;
	}
	function SetSelectNullValue($value) {
		$this->_IsNullValue = $value;
	}
	private $_IsNull;
	function IsNull() {
		return $this->_IsNull;
	}
	function SetIsNull($value) {
		$this->_IsNull = $value;
	}

	private $_OrderBy;
	function GetSelectOrderBy() {
		return $this->_OrderBy;
	}
	function SetSelectOrderBy($value) {
		$this->_OrderBy = $value;
	}

	private $_IsMultiple;
	function GetIsMultiple() {
		return $this->_IsMultiple;
	}
	function SetIsMultiple($value) {
		$this->_IsMultiple = $value;
	}
	private $_SelectSize;
	function GetSelectSize() {
		return $this->_SelectSize;
	}
	function SetSelectSize($value) {
		$this->_SelectSize = $value;
	}

	function getSelectLength(){
		return ($this->GetMaxLength()>=10?$this->GetMaxLength():20);
	}

	function getSqlSelect($prefixName = '') {

		// retrive validation query
		$query = $this->getSqlQuery(); // SELECT=

		$null_value = $this->GetSelectNullValue(); // NULL=
		$nulldesc = '-------';
		//				$null_value=FALSE;

		$destination_field = $prefixName . $this->GetFieldName(); // DESTFIELD=
		$key_value = $prefixName == '' ? $this->GetValue() : $_POST[$destination_field];


		$key_selected = false;
		// get results in array where first column is field value and second is field description
		$rows_array = rowselect($query,true);

		$return_value .= '<select id="'.$this->GetId().'" name="'.$destination_field.'" '.$this->getPostValidation().' >';

		if ($null_value) {
			if ($key_selected) {
				$retrun_value .= "<option value=\"\" >$nulldesc</option>\n";
			} else {
				$return_value .= "<option value=\"\" selected>$nulldesc</option>\n";
				$key_selected = true;
			}
		}

		for ($i = 0; $i < sizeof($rows_array); $i++) {

			if ($rows_array[$i][0] == $key_value) {
				$return_value .= '<option value="' . $rows_array[$i][0] . '" selected>' . $rows_array[$i][1] . '</option>' . "\n";
				$key_selected = true;
			} else {
				if ((!$null_value) and ($key_value == null) and (!$key_selected)) {
					$return_value .= '<option value="' . $rows_array[$i][0] . '" selected>' . $rows_array[$i][1] . '</option>' . "\n";
					$key_selected = true;
				} else {
					$return_value .= '<option value="' . $rows_array[$i][0] . '" >' . $rows_array[$i][1] . '</option>' . "\n";
				}

			}
		}
		$return_value .= "</select>\n";
		return $return_value;
	}



	function GetGroupSelect($prefixName = '') {
		global $dbconn;

		$_SESSION['sess_lang'] = $_SESSION['sess_lang'];


		$retun_value = '';
		$query = $this->GetSelectTableQuery(); // SELECT=
		$key = $this->GetSelectKey(); // KEY=
		$field = $this->GetSelectField(); // DESCRIPTION=
		$destination_field = $prefixName . $this->GetSelectDestField(); // DESTFIELD=
		$group_description = $this->GetGroupDescriptionField(); // GRPDESC=
		$null_value = $this->GetSelectNullValue(); // NULL=
		$nulldesc = '-------';
		//				$null_value=FALSE;
		$key_value = $prefixName == '' ? $this->GetValue() : $_POST[$destination_field];

		if (strlen($destination_field) == 0)
			$destination_field = "$prefixName$key";

		$key_selected = false;
		$query = preg_replace('<SESS_LANG/>', $_SESSION['sess_lang'], $query);
		$rows_array = dbselect($query);
		$return_value .= '<select id="'.$this->GetId().'" name="'.$destination_field.'" '.$this->getPostValidation().' >';
		if ($null_value) {
			if ($key_selected) {
				$retrun_value .= "<option value=\"\" >$nulldesc</option>\n";
			} else {
				$return_value .= "<option value=\"\" selected>$nulldesc</option>\n";
				$key_selected = true;
			}
		}

		$groupOption = '';
		for ($i = 0; $i < $rows_array[NROWS]; $i++) {
			if (($group_description > '') and $groupOption <> $rows_array[ROWS][$i][$group_description]) {
				$return_value .= $groupOption == '' ? '<OPTGROUP label="' . $rows_array[ROWS][$i][$group_description] . '">' : '</OPTGROUP><OPTGROUP label="' . $rows_array[ROWS][$i][$group_description] . '">';
				$groupOption = $rows_array[ROWS][$i][$group_description];
				$printGroup = true;
			}
			if ($rows_array[ROWS][$i][$key] == $key_value) {
				$return_value .= '<option value="' . $rows_array[ROWS][$i][$key] . '" selected>' . $rows_array[ROWS][$i][$field] . '</option>' . "\n";
				$key_selected = true;
			} else {
				if ((!$null_value) and ($key_value == null) and (!$key_selected)) {
					$return_value .= '<option value="' . $rows_array[ROWS][$i][$key] . '" selected>' . $rows_array[ROWS][$i][$field] . '</option>' . "\n";
					$key_selected = true;
				} else {
					$return_value .= '<option value="' . $rows_array[ROWS][$i][$key] . '">' . $rows_array[ROWS][$i][$field] . '</option>' . "\n";
				}

			}
		}
		$retun_value .= $groupOption > '' ? '</OPTGROUP>' : '';
		$return_value .= "</select>\n";
		return $return_value;
	}

	function SelectedValue($value) {
		if ($this->GetISMultiple()) {
			if (!is_array($this->GetValue())) {
				$fieldArray = explode(',', $this->GetValue());
			} else {
				$fieldArray = $this->GetValue();
			}
			foreach ($fieldArray as $fieldValue) {
				if ($fieldValue == $value) {
					return true;
				}
			}
		} else {
			if ($value == $this->GetValue()) {
				return true;
			}
		}
		return false;
	}
	function GetSelect($prefixName = '') {
		global $dbconn;

		$_SESSION['sess_lang'] = $_SESSION['sess_lang'];

		$retun_value = '';
		$table = strtolower($this->GetSelectTable());
		$key = $this->GetSelectKey();
		$field = 'substr('.$this->GetSelectField(). ',1,60)' . ' AS DESCRIPTION';

		$destination_field = $this->GetSelectDestField();
		$query_filter = $this->GetSelectFilter();
		$null_value = $this->GetSelectNullValue();
		$order_by = $this->GetSelectOrderBy();
		$multiple = $this->GetISMultiple();
		$destination_field = $multiple == 'multiple' ? $prefixName . $this->GetSelectDestField() . '[]' : $prefixName . $this->GetSelectDestField();

		$key_value = $prefixName == '' ? $this->GetValue() : $_POST[$destination_field];

		$size = strlen($this->GetSelectSize()) >= 1 ? 'size="' . $this->GetSelectSize() . '" ' : '';
		$nulldesc = '-------';
		if (strlen($destination_field) == 0)
			$destination_field = "$prefixName$key";

		$key_selected = $key_value == null ? false : true;

		$order_by = ($order_by == null) ? '' : ' order by ' . $order_by;
		$query_filter = ($query_filter == null) ? '' : ' where ' . $query_filter;

		$query = "SELECT distinct $key, $field from $table $query_filter $order_by";
		$rows_array = dbselect($query);


		if ($null_value) {
			if ($key_selected) {
				$retun_value .= "<option value=\"\" >$nulldesc</option>\n";
			} else {
				$retun_value .= "<option value=\"\" selected>$nulldesc</option>\n";
				$key_selected = true;
			}
		}
		$keyIndex=0;
		for ($i = 0; $i < $rows_array[NROWS]; $i++) {

			if (($this->SelectedValue($rows_array[ROWS][$i][$key]) and $prefixName == '') or ($prefixName > '' and $rows_array[ROWS][$i][$key] == $key_value)) {
				$retun_value .= '<option value="' . $rows_array[ROWS][$i][$key] . '" selected>' . $rows_array[ROWS][$i][DESCRIPTION] . '</option>' . "\n";
				$key_selected = true;
			} else {
				if ((!$null_value) and ($key_value == null) and (!$key_selected)) {
					$retun_value .= '<option value="' . $rows_array[ROWS][$i][$key] . '" selected>' . $rows_array[ROWS][$i][DESCRIPTION] . '</option>' . "\n";
					$keyIndex=$i;
					$key_selected = true;
				} else {
					$retun_value .= '<option value="' . $rows_array[ROWS][$i][$key] . '">' . $rows_array[ROWS][$i][DESCRIPTION] . '</option>' . "\n";
				}

			}
		}
		if ($this->isReadOnly()==' readonly ') $javascriptValue=' onChange="this.selectedIndex='.($keyIndex+1).'" ';
		$retun_value ='<select dojoType="dijit.form.FilteringSelect" '.$javascriptValue.' id="'.$this->GetId().'" name="'.$destination_field.'" '.$multiple.' '.$size.' '.$this->getPostValidation().'  >'.$retun_value;
//		$retun_value ='<select disabled id="'.$this->GetId().'" name="'.$destination_field.'" '.$multiple.' '.$size.' '.$this->getPostValidation().'  >'.$retun_value;
		$retun_value .= "</select>\n";
		return $retun_value;
	}
	function getCountrySelect(){
		return $selectValue;
	}


	function getChkSelect(){
		$result = dbselect($this->getSqlQuery());
		// $valuesSelected = explode('-',$this->GetValue());
		$inputField = '<div class="chkval" > ';
		for ($index = 0; $index < $result['NROWS']; $index++) {
			$field_checked = preg_match('|'.$result['ROWS'][$index]['CHKVAL'].'|',$this->GetValue()) ? 'checked' : '';
			$inputField .= '<p><input '.$this->isReadOnly().' type="checkbox" id="' . $this->GetId() . '" name="' . $this->GetFieldName() . '['.$index.']" value="'.$result['ROWS'][$index]['CHKVAL'].'" ' . $field_checked . ' class="form_field">'. 	$result['ROWS'][$index]['CHKVAL'] . "</p>\n";
		}
		return $inputField.'</div>';
	}

	function getRadio(){
		$query = $this->getSqlQuery();
		if(!empty($query)){
			$result = dbselect($query);
			$inputField = '<div class="radioval" > ';
			for ($index = 0; $index < $result['NROWS']; $index++) {
				$field_checked = $result['ROWS'][$index]['CODE'] == $this->GetValue() ? 'checked' : '';
				$inputField .= '<p> <input '.$this->isReadOnly().' type="RADIO" id="' . $this->GetId() . '" name="' . $this->GetFieldName() . '" value="'.$result['ROWS'][$index]['CODE'].'" class="form_field" ' . $field_checked . '>'.$result['ROWS'][$index]['VALUE']. "</p>\n";
			}
			return $inputField.'</div>';
		} else {
			$validation = $this->GetValidation();
			$fieldValue = $this->GetValue();
			$checkedValue = (empty($fieldValue) ? $this->GetDefaultValue() : $fieldValue);
			if(preg_match('|<RADIO>(.*)</RADIO>|i',$validation,$radioValues)){
				$values = explode(',', $radioValues[1]);
				$inputField = '<div class="radioval" > ';
				foreach ($values as $value) {
					$field_checked = $value == $checkedValue ? 'checked' : '';
					$inputField .= '<p> <input '.$this->isReadOnly().' type="RADIO" id="' . $this->GetId() . '" name="' . $this->GetFieldName() . '" value="'.$value.'" class="form_field" ' . $field_checked . '>'.$value. "</p>\n";
				}
				return $inputField.'</div>';
			}
		}
	}


	function GetInputField($prefixName = '') {
		global $_POST;
		$fieldValue = $prefixName == '' ? $this->GetValue() : $_POST[$prefixName . $this->GetFieldName()];
		if ($this->GetShowed()) {
		    /*
		     * Larghezza field di testo
		     */
		    $length = (integer) $this->GetLength();
            if($length < 20){
                $djClass = '';
            } elseif ($length < 30){
                $djClass = 'w200';
            } elseif ($length < 40){
                $djClass = 'w300';
            } elseif ($length < 50){
                $djClass = 'w400';
            } elseif ($length < 60){
                $djClass = 'w500';
            } else {
                $djClass = 'w600';
            }

			switch (trim($this->GetDataType())) {
				case 'TIME' :
					$inputField = '<div dojoType="dijit.form.TimeTextBox" lang="it-it" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" ></div>' . "\n";
					break;
				case 'DATE' :
					// da usare con dojo 1.x
					$inputField = '<input dojoType="dijit.form.DateTextBox"
							type="text" name="' . $prefixName . $this->GetFieldName() . '"
							id="' . $this->GetId() . '"
							value="' . $fieldValue . '" >';
					break;
				case 'TEXT' :
					$inputField = '<input dojoType="dijit.form.TextBox" '. $this->isReadOnly() .
                        ' type="TEXT" id="' . $this->GetId() .
                        '" name="' . $prefixName . $this->GetFieldName() .
                        '" value="' . $fieldValue .
//                        '" autocomplete="on' .
                        '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() .
                        '" '.$this->getPostValidation().
                        ' class="' . $djClass . '" ' .
                        '>' . "\n";
					break;
				case 'TEXTAREA' :


					$inputField = '<textarea dojoType="dijit.form.Textarea" '.$this->isReadOnly().' id="' . $this->GetId() .
									'" name="' . $prefixName . $this->GetFieldName() .
									'" wrap="PHYSICAL" rows="' . $this->GetTextareaRows() . '" cols="' . $this->GetLength() . '">' .
									$fieldValue .
									'</textarea>' . "\n";

					if ($this->getSqlQuery()>''){
						$inputField .= '<div id="dlgAddNote_'.$this->GetId().'" dojoType="dijit.Dialog" title="Aggiungi Note"
										parseOnload="true" ></div>
										<span style="margin:10px;vertical-align: top;">' .
										'<a style="cursor: pointer;" onClick="dialogNote('.$this->GetId().');">' .
										'<img src="graphics/table_add.png">'.'Aggiungi note'.'</a></span>';
					}
					break;
				case 'HIDDEN' :
					$inputField = '<input type="HIDDEN" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . "\n";
					break;
				case 'FILE' :

					if ($this->GetValue() > '' and file_exists('"'.$this->getDirUpload() . $this->GetFile()).'"') {
						$fType=pathinfo($this->GetFile());
						if (strtoupper($fType['extension'])=='JPG'){
//							$blobLink = '&nbsp;&nbsp;<img src="graphics/data.gif" ';
							$blobLink = '&nbsp;&nbsp;<img src="thumbnail.php?srcFile='.$this->GetFile().'&maxh=80" ';
							$blobLink .= 'onclick="javascript:window.open(\'get_file.php?dir='.$this->getDirUpload().'&wk_inline=Y&f=' . $this->GetFile() . '\');" ';
							$blobLink .= ' STYLE="cursor: pointer; margin: 2px 0px 0 30px ;"';
							$blobLink .= ' title="Download Attachment - ' . $this->GetFile() . '" />';
						} else {
							$blobLink = '&nbsp;&nbsp;<img src="graphics/data.gif" ';
							$blobLink .= 'onclick="javascript:location.href=\'get_file.php?f=' . $this->GetFile() . '\';" ';
							$blobLink .= ' STYLE="cursor: pointer; margin: 2px 0px 0 30px ;"';
							$blobLink .= ' title="Download Attachment - ' . $this->GetFile() . '" />';
						}

						$this->SetDownloadLink($blobLink);


					}

					$inputField = '<input '.$this->isReadOnly().' type="FILE" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . $this->GetDownloadLink() . "\n";
					break;
				case 'SQLSELECT' :
					$inputField = $this->getSqlSelect($prefixName) . "\n";
					break;
				case 'SELECT' :
					$inputField = $this->GetSelect($prefixName) . "\n";
					break;
				case 'GRPSELECT' :
					$inputField = $this->GetGroupSelect($prefixName) . "\n";
					break;
				case 'COUNTRY' :
					$inputField = $this->GetCountrySelect($prefixName) . "\n";
					break;
				case 'AJAX' :
					$inputField = $this->GetAjaxField();
					break;
				case 'DJSELECT' :
					$inputField = $this->getDjSelect();
					break;
				case 'DJQSELECT' :
					$inputField = $this->getDjQuerySelect();
					break;
				case 'CHKSELECT' :
					$inputField = $this->getChkSelect();
					break;
				case 'RADIO' :
					$inputField = $this->getRadio();
					break;
				case 'PASSWORD' :
					$inputField = '<input '.$this->isReadOnly().' type="PASSWORD" id="' . $this->GetId() . '" name="' . $this->GetFieldName() . '" value="' . $this->GetValue() . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . "\n";
					break;
				case 'NUMBER' :
					$inputField = '<input '.$this->isReadOnly().' type="TEXT" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . "\n";
					break;
				case 'CHECK' :
					$field_checked = $fieldValue == 'Y' ? 'checked' : '';
					$inputField = '<input '.$this->isReadOnly().' type="checkbox" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="Y" ' . $field_checked . ' class="form_field">' . "\n";
					break;
				default :
					} // switch
		} else {
			return '<input type="hidden" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" >' . "\n";
		}
		return $inputField;
	}


	function dispInputField($prefixName = '') {
		if ($this->GetShowed()) {
			switch (trim($this->GetDataType())) {
				case 'TIME' :
					break;
				case 'DATE' :

					break;
				case 'TEXT' :
					break;
				case 'TEXTAREA' :
					break;
				case 'HIDDEN' :
					break;
				case 'FILE' :
					break;
				case 'SQLSELECT' :
					$inputField = $this->getSqlSelect($prefixName) . "\n";
					break;
				case 'SELECT' :
					$table = strtolower($this->GetSelectTable());
					$key = $this->GetSelectKey();
					$field = 'substr('.$this->GetSelectField(). ',1,60)' . ' AS DESCRIPTION';

					$destination_field = $this->GetSelectDestField();
					$query_filter = $this->GetSelectFilter();
					$null_value = $this->GetSelectNullValue();
					$order_by = $this->GetSelectOrderBy();
					$multiple = $this->GetISMultiple();
					$destination_field = $multiple == 'multiple' ? $prefixName . $this->GetSelectDestField() . '[]' : $prefixName . $this->GetSelectDestField();

					$key_value = $prefixName == '' ? $this->GetValue() : $_POST[$destination_field];

					$size = strlen($this->GetSelectSize()) >= 1 ? 'size="' . $this->GetSelectSize() . '" ' : '';
					$nulldesc = '-------';
					if (strlen($destination_field) == 0)
						$destination_field = "$prefixName$key";

					$key_selected = $key_value == null ? false : true;

					$order_by = ($order_by == null) ? '' : ' order by ' . $order_by;
					$query_filter = ($query_filter == null) ? '' : ' where ' . $query_filter;

					$query = "SELECT distinct $key, $field from $table $key=".$this->GetValue();
					$rows_array = dbselect($query);
					var_dump($rows_array);
					break;
				case 'GRPSELECT' :
					$inputField = $this->GetGroupSelect($prefixName) . "\n";
					break;
				case 'COUNTRY' :
					$inputField = $this->GetCountrySelect($prefixName) . "\n";
					break;
				case 'AJAX' :
					$inputField = $this->GetAjaxField();
					break;
				case 'DJSELECT' :
                    r(__LINE__,false);
					$inputField = $this->getDjSelect();
					break;
				case 'DJQSELECT' :
					$inputField = $this->getDjQuerySelect();
					break;
				case 'CHKSELECT' :
					$inputField = $this->getChkSelect();
					break;
				case 'RADIO' :
					$inputField = $this->getRadio();
					break;
				case 'PASSWORD' :
					$inputField = '<input '.$this->isReadOnly().' type="PASSWORD" id="' . $this->GetId() . '" name="' . $this->GetFieldName() . '" value="' . $this->GetValue() . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . "\n";
					break;
				case 'NUMBER' :
					$inputField = '<input '.$this->isReadOnly().' type="TEXT" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . "\n";
					break;
				case 'CHECK' :
					$field_checked = $fieldValue == 'Y' ? 'checked' : '';
					$inputField = '<input '.$this->isReadOnly().' type="checkbox" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="Y" ' . $field_checked . ' class="form_field">' . "\n";
					break;
				default :
					} // switch
		}
	}


	function ShowField() {
		if ($this->GetShowed()) {
			print ('<tr>');
			print ('<td class="DbFormLabel" >');
			print ($this->GetFieldLabel());
			if ($this->IsNull()) {
				print ('<font face="Arial, Helvetica, sans-serif" color="#FF0000">*</font>');
			}
			elseif (!$this->NullValueAllowed()) {
				print ('<font face="Arial, Helvetica, sans-serif" >*</font>');
			}
			print ('</td>');
			print ('<td class="DbFormField" >');
			print ($this->GetInputField());
			print ('</td>');
			print ('</tr>');
			print ("\n");
		} else {
			print ($this->GetInputField());
		}
	}
	public function showFilterField(){

	    $prefixName = 'filter_';
        $fieldValue = $_POST[$prefixName.$this->GetFieldName()];
		switch (trim($this->GetDataType())) {
			case 'NUMBER' :
				$inputField = '<input '.$this->isReadOnly().' type="TEXT" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . "\n";
				break;
			case 'DATE' :
				// da usare con dojo 1.x
				if($_SESSION['dojoVersion']=='1.x'){
					$inputField = '<input dojoType="dijit.form.DateTextBox"
								type="text" name="' . $prefixName . $this->GetFieldName() . '"
								id="' . $this->GetId() . '"
								value="' . $fieldValue . '" >';
				} else {
					$inputField = '<div dojoType="dijit.form.TimeTextBox"  lang="it-it" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" ></div>' . "\n";
				}
				break;
			case 'TEXTAREA' :
			case 'TEXT' :
				$inputField = '<input '.$this->isReadOnly().' type="TEXT" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $fieldValue . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" '.$this->getPostValidation().'>' . "\n";
				break;
			case 'SQLSELECT' :
				$inputField = $this->getSqlSelect() . "\n";
				break;
			case 'SELECT' :
				$inputField = $this->GetSelect() . "\n";
				break;
			case 'GRPSELECT' :
				$inputField = $this->GetGroupSelect() . "\n";
				break;
			case 'COUNTRY' :
				$inputField = $this->GetCountrySelect() . "\n";
				break;
			case 'AJAX' :
				$inputField = $this->GetAjaxField();
				break;
			case 'DJSELECT' :
				$inputField = $this->getDjSelect($prefixName);
				break;
			case 'DJQSELECT' :
				$inputField = $this->getDjQuerySelect();
				break;
			case 'CHKSELECT' :
				$inputField = $this->getChkSelect();
				break;
			case 'RADIO' :
				$inputField = $this->getRadio();
				break;
			case 'CHECK' :
				$field_checked = $fieldValue == 'Y' ? 'checked' : '';
				$inputField = '<input '.$this->isReadOnly().' type="checkbox" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="Y" ' . $field_checked . ' class="form_field">' . "\n";
				break;
			case 'FILE' :
			case 'PASSWORD' :
			case 'TIME' :
			case 'HIDDEN' :


		} // switch



			print ('<label for="' . $this->GetFieldName() . '" ' . $nullClass . '>');
			print ($this->GetFieldLabel());
			print ($requiredToken);
			print ('</label>');
			print ($inputField);
			print ('<br/>');
			print ("\n");
	}



	function showDivField() {
		if ($this->GetShowed()) {
			$requiredToken = '';
			$nullClass = '';
			if ($this->IsNull()) {
				$nullClass = ' id="valueRequired" ';
				$requiredToken = '<font face="Arial, Helvetica, sans-serif" >*</font>';
			}
			elseif (!$this->NullValueAllowed()) {
				$requiredToken = '<font face="Arial, Helvetica, sans-serif" >*</font>';
			}
			print ('<label for="' . $this->GetFieldName() . '" ' . $nullClass . '>');
			print ($this->GetFieldLabel());
			print ($requiredToken);
			print ('</label>');
			print ($this->GetInputField());
			print ('<br/>');
			print ("\n");
		} else {
			print ($this->GetInputField());
		}
	}


	function dispDivField() {
		if ($this->GetShowed()) {
			switch (trim($this->GetDataType())) {
				case 'HIDDEN' :
					break;
				case 'FILE' :
					break;
				case 'SQLSELECT' :
					break;
				case 'DJSELECT' :
			    case 'DJQSELECT' :
					if ($this->getLookup()>''){
						if ($this->GetValue()>''){
							$rows_array = dbselect($this->getLookup());
							$selDescription=$rows_array['ROWS'][0]['DESCRIPTION'];
						} else {
							$selDescription='..........';
						}
					} else {
						$selDescription='..........';
					}

					$requiredToken = '';
					$nullClass = '';
					print ('<label>');
					print ($this->GetFieldLabel());
					print ('</label>');
					print ('<span >');
						print($selDescription);
					print ('</span>');
					print ('<br/>');
					print ("\n");
					break;
				case 'SELECT' :
					$table = strtolower($this->GetSelectTable());
					$key = $this->GetSelectKey();
					$field = 'substr('.$this->GetSelectField(). ',1,60)' . ' AS DESCRIPTION';

					$destination_field = $this->GetSelectDestField();
					$query_filter = $this->GetSelectFilter();
					$null_value = $this->GetSelectNullValue();
					$order_by = $this->GetSelectOrderBy();
					$multiple = $this->GetISMultiple();
					$destination_field = $multiple == 'multiple' ? $prefixName . $this->GetSelectDestField() . '[]' : $prefixName . $this->GetSelectDestField();

					$key_value = $prefixName == '' ? $this->GetValue() : $_POST[$destination_field];

					$size = strlen($this->GetSelectSize()) >= 1 ? 'size="' . $this->GetSelectSize() . '" ' : '';
					$nulldesc = '-------';
					if (strlen($destination_field) == 0)
						$destination_field = "$prefixName$key";

					$key_selected = $key_value == null ? false : true;

					$order_by = ($order_by == null) ? '' : ' order by ' . $order_by;
					$query_filter = ($query_filter == null) ? '' : ' where ' . $query_filter;
					if ($this->GetValue()>''){
						$query = "SELECT distinct $key, $field from $table where $key=".$this->GetValue();
						$rows_array = dbselect($query);
						$selDescription=$rows_array['ROWS'][0]['DESCRIPTION'];
					} else {
						$selDescription='..........';
					}
					$requiredToken = '';
					$nullClass = '';
					print ('<label>');
					print ($this->GetFieldLabel());
					print ('</label>');
					print ('<span >');
						print($selDescription);
					print ('</span>');
					print ('<br/>');
					print ("\n");
					break;
				case 'DATE' :
					$requiredToken = '';
					$nullClass = '';
					print ('<label>');
					print ($this->GetFieldLabel());
					print ('</label>');
					print ('<span >');
					if($this->GetValue()>''){
						print (date('d/m/Y',strtotime($this->GetValue())));
					}
					print ('</span>');
					print ('<br/>');
					print ("\n");
					break;
				case 'TEXTAREA' :
					$requiredToken = '';
					$nullClass = '';
					print ('<label>');
					print ($this->GetFieldLabel());
					print ('</label>');
					print ('<span >');
					print ($this->GetValue());
					print ('</span>');
					print ('<br/>');
					print ("\n");
					break;
				default :
					$requiredToken = '';
					$nullClass = '';
					print ('<label>');
					print ($this->GetFieldLabel());
					print ('</label>');
					print ('<span >');
					print ($this->GetValue());
					print ('</span>');
					print ('<br/>');
					print ("\n");
				} // switch
		}
		print  '<input type="HIDDEN" id="' . $this->GetId() . '" name="' . $prefixName . $this->GetFieldName() . '" value="' . $this->GetValue() . '" size="' . $this->GetLength() . '" maxlength="' . $this->GetMaxLength() . '" >' . "\n";

	}



	function FormFields($FieldName) {
		$this->SetFieldName($FieldName);
	}
}
?>
