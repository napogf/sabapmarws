<?php

/**
 *
 *
 * @version $Id: table_c.inc,v 1.3 2010/08/05 14:03:02 cvsuser Exp $
 * @copyright 2003
 **/
/**
 *
 *
 **/
class htmlTable {
	/**
	 * Constructor
	 * @access protected
	 */
	var $_tableRows;
	function AddtableRows($rowObj) {
		$this->_tableRows[$rowObj];
	}

	var $_tableWidth = '100%';

	function GetWidth() {
		return $this->_tableWidth;
	}

	function SetWidth($newValue) {
		$this->_tableWidth = $newValue;
	}

	var $_tableBorder = '0';

	function GetBorder() {
		return $this->_tableBorder;
	}

	function SetBorder($newValue) {
		$this->_tableBorder = $newValue;
	}

	var $_rowClass = 'rowClass1';

	function GetrowClass() {
		return $this->_rowClass;
	}

	function SetrowClass() {
		$this->_rowClass = $this->GetrowClass() == 'rowClass1' ? 'rowClass2' : 'rowClass1';
	}

	// Caratteristiche colonne
	var $_TableColClass = array ();
	function GetTableColClass($i) {
		return $this->_TableColClass[$i];
	}
	function SetTableColClass($value, $i) {
		$this->_TableColClass[$i] = $value;
	}

	var $_TableColWrap = array ();
	function GetTableColWrap($i) {
		return $this->_TableColWrap[$i];
	}
	function SetTableColWrap($i, $value) {
		$this->_TableColWrap[$i] = $value;
	}

	function tableEnd() {
		print ('</table>' . "\n");
	}

	var $_tablePadding = array (
		'LEFT' => '"0"',
		'TOP' => '"0"',
		'RIGHT' => '"0"',
		'BOTTOM' => '"0"'
	);
	function SetTableMargins($paddings) {
		if (is_array($paddings)) {
			switch (strval(sizeof($paddings))) {
				case '1' :
					$_tablePadding['LEFT'] = $paddings (1);
					$_tablePadding['TOP'] = $paddings (1);
					$_tablePadding['RIGTH'] = $paddings (1);
					$_tablePadding['BOTTOM'] = $paddings (1);
					break;
				case '2' :
					$_tablePadding['LEFT'] = $paddings (1);
					$_tablePadding['TOP'] = $paddings (2);
					$_tablePadding['RIGTH'] = $paddings (1);
					$_tablePadding['BOTTOM'] = $paddings (2);
					break;
				case '3' :
					$_tablePadding['LEFT'] = $paddings (1);
					$_tablePadding['TOP'] = $paddings (2);
					$_tablePadding['RIGTH'] = $paddings (3);
					$_tablePadding['BOTTOM'] = $paddings (2);
					break;
				case '4' :
					$_tablePadding['LEFT'] = $paddings (1);
					$_tablePadding['TOP'] = $paddings (2);
					$_tablePadding['RIGTH'] = $paddings (3);
					$_tablePadding['BOTTOM'] = $paddings (4);
					break;
			}
		} else {
			$_tablePadding['LEFT'] = $paddings;
			$_tablePadding['TOP'] = $paddings;
			$_tablePadding['RIGTH'] = $paddings;
			$_tablePadding['BOTTOM'] = $paddings;
		}

	}

	function tableInit() {
		print ('<table border="' . $this->getBorder() . '" cellPadding="1" cellSpacing="1" width="' . $this->getWidth() . '">');
	}

	var $_tableColumns = -1;
	function GettableColumns() {
		return $this->_tableColumns;
	}
	function SettableColumns($newValue) {
		$this->_tableColumns = $newValue;
	}

	var $_htmlQuery = '';

	function GethtmlQuery() {
		return $this->_htmlQuery;
	}

	function SethtmlQuery($newValue) {
		$this->_htmlQuery = $newValue;
	}

	var $_queryArray;
	function GetqueryArray() {
		return $this->_queryArray;
	}
	function SetqueryArray($value = '', $IsStatement = TRUE) {
		if (($IsStatement) and ($value > '')) {
			$this->_queryArray = dbselect($value, false);
			if ($this->_queryArray['NROWS'] > 0) {
				return TRUE;
			} else {
				$this->_queryArray = NULL;
				return FALSE;
			}
		} else {
			$this->_queryArray = $value;
			if ($this->_queryArray['NROWS'] > 0) {
				return TRUE;
			} else {
				$this->_queryArray = NULL;
				return FALSE;
			}
		}
	}

	var $_HeaderRow;
	function GetHeaderRow() {
		return $this->_HeaderRow;
	}
	function SetHeaderRow($value) {
		$this->_HeaderRow = $value;
	}

	function SetColumnHeader($numCol, $value) {
		if (is_object($this->_HeaderRow)) {
			$this->_HeaderRow->SetColContent($numCol, $value);
		}

	}

	var $_PageDivision = FALSE;
	function GetPageDivision() {
		return $this->_PageDivision;
	}
	function SetPageDivision($value) {
		$this->_PageDivision = $value;
	}

	var $_TableFilter = array ();

	function GetTableFilter() {
		return $this->_TableFilter;
	}

	function SetTableFilter() {
		$colArray = $this->GetqueryArray();
		if (!is_null($colArray)) {
			reset($colArray['ROWS'][0]);
			while (!is_null($key = key($colArray['ROWS'][0]))) {
				if (strtoupper(substr($key, 0, 5)) <> 'ORDER') {
					$this->_TableFilter[] = new htmlCol();
				}
				next($colArray['ROWS'][0]);
			} // for
		}

	}

	function SetColumnFilter($key, $value, $type, $size = null) {
		if (is_object($this->_TableFilter[$key])) {
			$size = is_null($size) ? '' : ' size="' . $size . '" ';
			$this->_TableFilter[$key]->SetFilterContent($type, $value, $size);
			$this->_TableFilter[$key]->SetcolClass('TableFilter');
		}
	}
	var $_FilterShow = FALSE;
	function GetFilterShow() {
		return $this->_FilterShow;
	}
	function SetFilterShowed() {
		$this->SetTableFilter();
		$this->_FilterShow = TRUE;

	}

	function htmlTable($selectTableQuery, $IsStatement = TRUE) {

		if (!$this->SetqueryArray($selectTableQuery, $IsStatement)) {
			return FALSE;
		}

		$colArray = $this->GetqueryArray();
		$Trow = new htmlRow();
		$HeaderColumns[] = new htmlCol(); // Testata
		for ($i = 0; $i <= sizeof($colArray['ROWS'][0]); $i++) {
			$HeaderColumns[] = new htmlCol();
		} // for
		if ($colArray['NROWS'] > 0) {
			$x = 0;
			reset($colArray['ROWS'][0]);
			while (!is_null($key = key($colArray['ROWS'][0]))) {
				if (strtoupper(substr($key, 0, 5)) <> 'ORDER') {
					$HeaderColumns[$x]->SetcolContent(key($colArray['ROWS'][0]));
					$Trow->AddrowColumns($HeaderColumns[$x]);
				}
				next($colArray['ROWS'][0]);
				$x++;
			} // while
			$this->SetHeaderRow($Trow);
		}
	}



	function show($wk_page = 1) {

		// Valorizzo l'array coi dati e le caratteristiche di default della colonna
		$colArray = $this->GetqueryArray();
		// Genero gli oggetti colonna
		if (is_null($colArray)) {
			return FALSE;
		}

		$HeaderColumns = array ();
		$Columns = array ();
		$this->tableInit();

		$HeaderColumns[] = new htmlCol();
		for ($i = 0; $i <= sizeof($colArray['ROWS'][0]); $i++) {
			$HeaderColumns[] = new htmlCol();
		} // for

		$header_row = $this->GetHeaderRow();
		$header_row->rowShowHeader();

		// Mostro la riga dei filtri se inizializzata
		if ($this->GetFilterShow()) {
			$Trow = new htmlRow($this->GetTableFilter());
			$Trow->rowShow();

		}

		if ($this->GetPageDivision()) {
			$pages_counter = new pages($colArray['NROWS']);
			$pages_counter->SetactualPage($wk_page);
			$limit = $pages_counter->GetmaxLines() * $wk_page >= $colArray['NROWS'] ? $colArray['NROWS'] : $pages_counter->GetmaxLines() * $_GET['wk_page'];
			$start = $pages_counter->GetmaxLines() * ($wk_page -1);
		} else {
			$start = 0;
			$limit = $colArray['NROWS'];
		}
		// for($i = 0; $i < count($colArray['ROWS']); $i++){
		for ($i = $start; $i < $limit; $i++) {
			$this->SetrowClass();
			$Trow = new htmlRow();
			$x = 0;
			reset($colArray['ROWS'][0]);
			while (!is_null($key = key($colArray['ROWS'][0]))) {
				if (strtoupper(substr($key, 0, 5)) <> 'ORDER') {
					$HeaderColumns[$x]->SetcolContent($colArray['ROWS'][$i][$key]);
					$HeaderColumns[$x]->SetcolClass($this->GetrowClass());
					$HeaderColumns[$x]->SetcolWrap($this->GetTableColWrap($x));
					$Trow->AddrowColumns($HeaderColumns[$x]);
				}
				next($colArray['ROWS'][0]);
				$x++;
			} // while
			$Trow->rowShow($x);

		} // for
		$this->tableEnd();
		if ($this->GetPageDivision()) {
			print ('<tr height="100%" >');
			print ('    <td width="100%" valign="bottom" align="center">');

			$pages_counter->ShowPages();

			print ('    </td>');
			print ('</tr>');

		}

	}

}

/**
 *
 *
 **/

class htmlRow {
	/**
	 * Constructor
	 * @access protected
	 */
	var $_rowColumns; // Colonne della riga

	function AddrowColumns($colObj, $index = null) {
		$this->_rowColumns[] = $colObj;
	}

	function SetColContent($numCol, $value) {
		if($this->_rowColumns[$numCol]->getColSubstring()>0){
			$content ='<span  id="'.$numCol.'_'.$this->_rowNum.'" >'.substr($value,1,$this->_rowColumns[$numCol]->getColSubstring()).'</span>';
			$content.='<span dojoType="dijit.Tooltip" connectId="'.$numCol.'_'.$this->_rowNum.'" style="display:none;">'.$value.'</span>';
		} else {
			$this->_rowColumns[$numCol]->SetColContent($value);
		}
	}

	var $_rowInit = '';

	function GetrowInit() {
		return $this->_rowInit;
	}
	function SetrowInit($newValue) {
		$this->_rowInit = $newValue;
	}

	function htmlRow($ColumnArray = null) {
		if (!is_null($ColumnArray)) {
			$this->_rowColumns = $ColumnArray;
		}
		$this->SetrowInit('<tr >');
	}

	function rowEnd() {
		print ('</tr>' . "\n");
	}

	function rowStart() {
		print ($this->GetrowInit());
	}
	var $_rowNum=0;

	function rowShow($index) {
		$this->_rowNum = $index;
		$this->rowStart();
		for ($i = 0; $i < count($this->_rowColumns); $i++) {
			$this->_rowColumns[$i]->colShow();
		} // for
		$this->rowEnd();
	}

	function rowShowHeader() {
		$this->rowStart();
		for ($i = 0; $i < count($this->_rowColumns); $i++) {
			$this->_rowColumns[$i]->colShowHeader();
		} // for
		$this->rowEnd();
	}

}

/**
 * Classe htmlCol classe usata in htmlTable per inizializzare un array di oggetti di queta classe
 *
 *
 **/

class htmlCol {
	/**
	 * Constructor
	 * @access protected
	 */
	var $_colWrap = '';
	function GetcolWrap() {
		return $this->_colWrap;
	}
	function SetcolWrap($value) {
		$this->_colWrap = $value;
	}

	var $_colClass = '';

	function GetcolClass() {
		return $this->_colClass;
	}

	function SetcolClass($newValue) {
		$this->_colClass = ' class="' . $newValue . '" ';
		$this->SetcolInit();
	}

	var $_colWhidth = '';

	function GetcolWhidth() {
		return $this->_colWhidth;
	}

	function SetcolWhidth($newValue) {
		$this->_colWhidth = $newValue;
		$this->SetcolInit();
	}

	var $_colAlignment = '';

	function GetcolAlignment() {
		return $this->_colAlignment;
	}

	function SetcolAlignment($newValue) {
		$this->_colAlignment = $newValue;
		$this->SetcolInit();
	}

	var $_colInit = '';

	function GetcolInit() {
		return $this->_colInit;
	}

	function SetcolInit() {
		$this->_colInit = '<td' . $this->_colAlignment . $this->_colWhidth . $this->_colClass . $this->_colWrap . '>';
	}
	var $_colContent = '';
	function GetcolContent() {
		return $this->_colContent;
	}
	function SetcolContent($value) {
		$this->_colContent = $value;
	}
	var $_colSubstring=0;
	function setColSubstring($value){
		$this->_colSubstring=$value;
	}
	function getColSubstring(){
		return $this->_colSubstring;
	}

	var $_FilterContent = '';

	function GetFilterContent() {
		return $this->_FilterContent;
	}

	function SetFilterContent($type, $value, $size) {
		switch ($type) {
			case 'TEXT' :
				$this->SetcolContent('<input type="TEXT" name="' . $value . '" value="' . $_GET[$value] . '"  class="TableFilter" ' . $size .  '>' . "\n");
				break;
			case 'TEXTAREA' :
				$this->SetcolContent('<input type="TEXT" name="' . $value . '" value="' . $_GET[$value] . '"  class="TableFilter" size="40">' . "\n");
				break;
			case 'DATE' :
				//					$inputField.='<input type="TEXT" name="'.$value.'" value="'.$_GET[$value].'"  class="TableFilter" '.$size.'>'."\n";
				//					$inputField.='<input type="button" name="data" value="..." onMouseDown="cal('.$value.', event, 1)">'."\n";
				//					$inputField = '<div dojoType="dropdowndatepicker" displayFormat="dd-MM-yyyy" lang="it-it" name="' .  $value . '" value="' . $_GET[$value] . '" ></div>' . "\n";
				$inputField = '<script type="text/javascript">
													  dojo.require("dijit.form.DateTextBox");
													  dojo.require("dojo.parser");
													</script>';

				$inputField .= '<input dojoType="dijit.form.DateTextBox" style="width:10em;" type="text" id="q1" name="' . $value . '" value="' . $_GET[$value] . '">';
				$this->SetcolContent($inputField);
				break;
			default :
				$this->SetcolContent(var_dump($type.$value.$size));
		} // switch
	}

	function htmlCol() {
		$this->SetcolInit();
	}

	function colEnd() {
		print ('</td>');
	}

	function colShow() {
		print ($this->GetcolInit());
		print ($this->GetcolContent());
		$this->colEnd();
	}

	function colShowHeader() {
		print ('<th class="listapiccolo" nowrap >');
		print ($this->GetcolContent());
		print ('</th>');
	}

}
?>