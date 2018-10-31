<?php
require_once('Spreadsheet/Excel/Writer.php');
// Classe PEAR Spreadsheet_Excel_Writer

/**
 *
 *
 * @version $Id: Etable_c.inc,v 1.1 2012/09/13 15:42:22 cvsuser Exp $
 * @copyright 2003
 **/
/**
 *
 *
 **/

class htmlETable{
	/**
     * Constructor
     * @access protected
     */
		var $_tableData=null;

		function getColumn($column){
			if (is_object($this->_tableData[$column])){
				return $this->_tableData[$column];
			} else {
				return null;
			}
		}

		var $_TableRows=0;
		function getTableRows(){
			return $this->_TableRows;
		}
		// Column Properties Access
		var $_totalColumn=null;
		function totalColumn($column){
			$this->_totalColumn=$column;
		}

		function HideCol($column){
			if(isset($this->_tableData[$column])){
				$this->_tableData[$column]->Disable();
			}
			return $this;
		}

		function SetColumnHref($column,$href){
			$this->_tableData[$column]->SetColHref($href);
		}

		function SetColumnOrder($column,$type=null){
			if (is_object($this->_tableData[$column])) {
				$this->_tableData[$column]->SetOrder($type);
			}
		}



		function GetColumnHref($column,$key){
			if (($value=$this->_tableData[$column]->GetColHref())<>null) {
				$pattern='|#(.*)#|U';
			    if (preg_match_all($pattern, $value, $match)>0) {
					for($i = 0; $i < count($match[1]); $i++){
						if (is_object($this->_tableData[$match[1][$i]])) {
							$replaceValue=$this->_tableData[$match[1][$i]]->GetValue($key);
							$replacePattern=$match[0][$i];
							$value=str_replace($replacePattern, $replaceValue, $value);
						} else {
							r($match[1][$i]);
						}
					} // for
			    }
				return $value;
			}
			return null;
		}
		private $_colSubstring=array();
		function setColSubstring($column,$strLen){
			$this->_colSubstring[$column]= $strLen ;
		}
		function getColSubstring($column){
			return $this->_colSubstring[$column];
		}

		function GetColValue($column,$i){
			if (is_null($this->GetColumnHref($column,$i))) {
				if (($this->getColSubstring($column)>0) and (strlen($this->_tableData[$column]->GetValue($i))>$this->getColSubstring($column))){
					$content ='<span  id="'.$column.'_'.$i.'" >'.substr($this->_tableData[$column]->GetValue($i),0,$this->getColSubstring($column)).'</span>';
					$content.='<span dojoType="dijit.Tooltip" connectId="'.$column.'_'.$i.'" style="display:none;"><div style="max-width:250px; display:block;">'.$this->_tableData[$column]->GetValue($i).'</div></span>';
					return $content;
				} else {
			    	return $this->_tableData[$column]->GetValue($i);
				}
			} else {
				$value=$this->GetColumnHref($column,$i);
				$pattern='|<([a-zA-Z]{1,3}).*>|';
			    preg_match_all($pattern, $value, $match);
				$closeTag='</'.$match[1][0].'>';
				return $value.$this->_tableData[$column]->GetValue($i).$closeTag;

			}
		}


		function SetColumnAttribute($column,$value){
			$this->_tableData[$column]->SetColAttribute($value);
		}

		function SetColAligment($column,$value){
			$this->_tableData[$column]->SetColAlign($value);
		}

		function SetColumnClass($column,$value){
			$this->_tableData[$column]->SetColClass($value);
		}

		function GetColumnClass($column,$index){
			$this->_tableData[$column]->GetColClass();
		}



		var $_queryArray;
		function GetqueryArray(){
			return $this->_queryArray;
		}
		function SetqueryArray($value,$IsStatement=TRUE){
			if (($IsStatement) and ($value > '')) {
				$db = Db_Pdo::getInstance();
			  	$result = $db->query($value);
				while($riga = $result->fetch()){
					$this->_TableRows++;
					if (is_null($this->_tableData)) {
						$i=0;
						foreach ($riga as $key => $value) {
						   $this->_tableData[$key] = new htmlECol($key);
						   $this->_tableData[$key]->SetValue($value);
						   $ftype = $result->getColumnMeta($i);

//						   $ftype=mysql_field_type($result, $i);
						   $i++;
						}
					} elseif (is_array($riga)){
						foreach ($riga as $key => $value) {
						   $this->_tableData[$key]->SetValue($value);
						}
					}
				} // while
				if ($this->_TableRows==0) {
					return FALSE;
				}
			} else {
				r($value);
			}
		}

			var $_RowClass;

			function GetRowClass(){
				return $this->_RowClass;
			}
			function SetRowClass($num){
				if (($num % 2)==0) {
				    $this->_RowClass= 'rowClass2';
				} else {
					$this->_RowClass='rowClass1';
				}
			}

		    var $_tableWidth = '100%';

		    function GetWidth(){
				return $this->_tableWidth;
			}

			function SetWidth($newValue){
				$this->_tableWidth = $newValue;
			}

		    var $_tableBorder = '0';

		    function GetBorder(){
				return $this->_tableBorder;
			}

			function SetBorder($newValue){
				$this->_tableBorder = $newValue;
			}
			var $_tableCaption=FALSE;

			function SetTableCaption($value){
				$this->_tableCaption=$value;
			}
			function GetTableCaption(){
				return $this->_tableCaption;
			}

		function tableEnd(){
			print('</table>'."\n");
		}

		function tableInit(){
			print('<table border="'.$this->getBorder().'" width="'.$this->getWidth().'">'."\n");
			if ($this->GetTableCaption()>''){
				print('<caption class="etableCaption">'.$this->GetTableCaption().'</caption>'."\n");
			}
		}

		var $_TableFilter=FALSE;

		function GetTableFilter(){
			$tableFilter='';
			if ($this->_TableFilter) {
				$tableFilter="<TR>\n";
				foreach ($this->_tableData as $key=>$value){
					if ($value->IsShowed()) {
					    $tableFilter .= "\t".'<TD class="TableFilter" >'.$value->GetFilterContent().'</TD>'."\n";
					}
				}
				$tableFilter.="</TR>\n";
			}
			return $tableFilter;
		}

		function SetTableFilter(){
//			$this->_TableRows++;
			$this->_TableFilter = TRUE;
		}

		// Momentanea da escludere con PHP5
		function SetColumnFilter($key,$value,$type,$size=null){
			if (is_object($this->_tableData[$key])) {
				$size=is_null($size)?'':' size="'.$size.'" ';
				$this->_tableData[$key]->SetFilterContent($type,$value,$size);
			} else {
				var_dump($key);
			}
		}

		var $_TableHeader;

		function SetTableHeader($value){
			$this->_TableHeader = $value;
		}
		function GetTableHeader(){
			$this->_TableHeader="<TR>\n";
			foreach ($this->_tableData as $key=>$value){
				if ($value->IsShowed()) {
				    $this->_TableHeader .= "\t".'<TH class="listapiccolo" nowrap>'.$value->GetColHeader().'</TH>'."\n";
				}
			}
			$this->_TableHeader.="</TR>\n";
			return $this->_TableHeader;
		}

		private $_headerClass='listapiccolo';

		function getHeaderClass(){
			return $this->_headerClass;
		}
		function setHeaderClass($value){
			$this->_headerClass=$value;
		}



		// Momentanea da escludere con PHP5
		function SetColumnHeader($key,$value){
			if (is_object($this->_tableData[$key])) {
				$this->_tableData[$key]->SetColHeader($value);
			}
		}



		var $_PageDivision=FALSE;
		function GetPageDivision(){
			return $this->_PageDivision;
		}
		function SetPageDivision($value){
			$this->_PageDivision = $value;
		}

		var $_pageRows=20;
		function SetPageRows($value){
			$this->_pageRows=$value;
		}
		function GetPageRows(){
			return $this->_pageRows;
		}

	function __construct($selectTableQuery, $IsStatement=TRUE){
		if (!$this->SetqueryArray($selectTableQuery, $IsStatement)) {
			return FALSE;
		}
		return TRUE;
	}

	protected $_printTotal = false;
	public $_decimalsTotal = 0;

	function printTotal($value=null,$decimals=0){
		if(is_null($value)){
			return $this->_printTotal;
		} else {
			$this->_printTotal=$value;
			$this->_decimalsTotal=$decimals;
		}
	}

	// funzione che salva in formato EXCEL devo passare il nome del file altrimenti lo ricavo dal programma
	function saveAsXls($fileName=null){

		$fileName=is_null($fileName) ? basename($_SERVER['PHP_SELF'],'.php').'.xls': basename($fileName) . '.xls';

		$workbook = new Spreadsheet_Excel_Writer();

		//$workbook->setTempDir(TMP_PATH);
		// sending HTTP headers
		$workbook->send($fileName);

		$worksheet1 =& $workbook->addWorksheet();
		// Format for the heading
		$formatot =& $workbook->addFormat();
		$formatot->setSize(10);
		$formatot->setAlign('center');
		$formatot->setColor('white');
		$formatot->setPattern();
		$formatot->setFgColor('black');
		$col=0;
		foreach ($this->_tableData as $key=>$value){
//			if ($value->IsShowed()) {
				$worksheet1->writeString(0,$col,$key,$formatot);
				$col++;
//			}
		}
		$dateFormat=& $workbook->addFormat();
		$numFormat=& $workbook->addFormat();
		$numFormat->SetNumFormat('#,##0.00');
		$dateFormat=& $workbook->addFormat();
		$dateFormat->SetNumFormat('DD-MM-YYYY');
		$curFormat=& $workbook->addFormat();
		$curFormat->SetNumFormat('#,##0.00');
		// number of seconds in a day
		$seconds_in_a_day = 86400;
		// Unix timestamp to Excel date difference in seconds
		$ut_to_ed_diff = $seconds_in_a_day * 25569;
		for($i = 0; $i < $this->_TableRows; $i++){
			$col=0;
			foreach ($this->_tableData as $key=>$value){
//		Esporto tutti i campi
//				if ($value->IsShowed()) {
					switch ($value->GetColumnType()) {
						case 'number':
							$worksheet1->writeNumber($i+1,$col,$value->_Value[$i],$numFormat);
							break;
						case 'currency':
							$worksheet1->writeNumber($i+1,$col,$value->_Value[$i],$numFormat);
							break;
						case 'date':
							if($value->GetValue($i)>''){
								$dateToShow=((strtotime($value->GetValue($i))+$ut_to_ed_diff)/$seconds_in_a_day)+1;
								$worksheet1->writeNumber($i+1,$col,$dateToShow,$dateFormat);
							} else {
								$worksheet1->writeString($i+1,$col,$value->GetValue($i));
							}
							break;
//						case 'date':
//							$dateToShow=((strtotime($value->GetValue($i))+$ut_to_ed_diff)/$seconds_in_a_day)+1;
//							$worksheet1->writeNumber($i+1,$col,$dateToShow,$dateFormat);
//							break;
						case 'currency':
							$worksheet1->writeNumber($i+1,$col,$value->GetValue($i),$curFormat);
							break;
						default:
							$worksheet1->writeString($i+1,$col,$value->GetValue($i));
							break;
					}
					$col++;
//				}
			}
		} // for
		$workbook->close();
	}


	function show($wk_page=1){
		if ($this->_TableRows==0) {
			return FALSE;
		}
		$this->tableInit();
		$this->SetPageRows(20);
		print($this->GetTableHeader());
		print($this->GetTableFilter());
		// Mostro la riga dei filtri se inizializzata
		if ($this->GetPageDivision()) {
			$pages_counter = new pages($this->_TableRows);
			$pages_counter->SetactualPage($wk_page);
			$pages_counter->SetmaxLines($this->GetPageRows());
			$start = ($pages_counter->GetmaxLines()*($wk_page-1));
			$limit = $start+$pages_counter->GetmaxLines()>$this->_TableRows?$this->_TableRows:$start+$pages_counter->GetmaxLines();
		} else {
			$start=0;
			$limit=$this->_TableRows;
		}
		$sumArray=array();
		for($i = $start; $i < $limit; $i++){
			$this->SetRowClass($i);
			$row ='';
			foreach ($this->_tableData as $key=>$value){
				if ($value->IsShowed()) {
					if ($value->GetColRowClass()>'') {
					    $this->_RowClass=$value->GetColRowClass();
					}
					if(($value->GetColumnType()=='number' or $value->GetColumnType()=='currency') and $value->getColTotal() ){
						$sumArray[$key] += $value->_Value[$i];
					} else {
						$sumArray[$key] = null;
					}
					$row .= "\t".'<TD '.$value->GetColAlign().$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >'.$this->GetColValue($key,$i).'</TD>'."\n";
				}
			}
			$row='<TR class="'.$this->GetRowClass().'">'."\n".$row;
			$row.='</TR>'."\n";
			print($row);
		} // for
		if ($this->printTotal()){
			$sumRow = '<tr class="djSumRow">';
			foreach ($sumArray as $value) {
				$value=$value>0?number_format($value,$this->_decimalsTotal,',','.'):null;
				$sumRow .= '<td align="right" > '.$value.'</td>';
			}
			$sumRow .= '</tr>';
			print($sumRow);
		}
		$this->tableEnd();
		if ($this->GetPageDivision()) {
	        print('<table align="center" width="'.$this->GetWidth().'" border="0" cellspacing="0" cellpadding="0" >');
	        print('<tr height="100%" >');
	        print('    <td width="100%" valign="bottom" align="center">');

			$pages_counter->ShowPages();

	        print('    </td>');
	        print('</tr>');
			print('</table>');
		}

	}


}





?>