<?php
/*
 * Created on 19-ott-2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include "login/autentication.php";
$hideForm='Y';
class MyDbForm extends formExtended {
	/**
	 * Constructor
	 * @access protected
	 */
	public function GetFormTitle(){
		if ($this->getFormName()=='PRATICHE' and $this->GetFormMode()=='modify') {
			return ('Nr Reg.: '.$this->GetFormFieldValue('numeroregistrazione').' - Data Reg.: '.$this->GetFormFieldValue('dataregistrazione'));
		} else {
			return $this->_FormTitle;
		}
	}

	public function getFieldDataType($field){
		if(isSet($this->_FormFields[$field])){
			return $this->_FormFields[$field]->GetDataType();
		}

		return 'NONE';
	}
}

class dbTable extends htmlETable {
	protected $_formKey;

	public function setFormKey($value){
		$this->_formKey = $value;
	}
	public function GetColValue($column,$i){
		if (is_null($this->GetColumnHref($column,$i))) {
			if (($this->getColSubstring($column)>0) and (strlen($this->_tableData[$column]->GetValue($i))>$this->getColSubstring($column))){
				$content ='<span  id="'.$column.'_'.$i.'" >'.substr($this->_tableData[$column]->GetValue($i),0,$this->getColSubstring($column)).'</span>';
				$content.='<span dojoType="dijit.Tooltip" connectId="'.$column.'_'.$i.'" style="display:none;"><div style="max-width:250px; display:block;">'.$this->_tableData[$column]->GetValue($i).'</div></span>';
				return $content;
			} elseif($column == $this->_formKey){
				$content = '<img title="Modifica il record" onclick="location.href=\'' .
				$_SERVER['PHP_SELF']  . '?' . $column . '=' .  $this->_tableData[$column]->GetValue($i)
				. '\'" style="cursor: pointer" src="graphics/application_edit.png">';
				return $content;
			} else {
				return $this->_tableData[$column]->GetValue($i);
			}
		}  else {
			$value=$this->GetColumnHref($column,$i);
			$pattern='|<([a-zA-Z]{1,3}).*>|';
			preg_match_all($pattern, $value, $match);
			$closeTag='</'.$match[1][0].'>';
			return $value.$this->_tableData[$column]->GetValue($i).$closeTag;
		}
	}
}


if(isSet($_GET['dbTable'])){
	$_SESSION['FORM'] = $_GET['dbTable'];
}
$dbTable=$_SESSION['FORM'];

$xlsBar='Y';
// Se la $xlsExport  definito non esporto il tutto in formato Excel
if (!isSet($_GET['xlsExport']) or $_GET['xlsExport'] <> 'Y') {
	include ("pageheader.inc");
	if (isset($filterField) and isset($$filterField)) {
		$dbTableFilter=" where ($dbTable.$filterField=".$$filterField.") ";
		$recallPage='?dbTable='.$dbTable.'&filterField='.$filterField.'&'.$filterField.'='.$$filterField;
	} else {
		$recallPage='?dbTable='.$dbTable;
	}
}


include("lib/classes/Form/manageDbtable.inc");

include ("pagefooter.inc");