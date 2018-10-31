<?php
include "login/autentication.php";
require_once("dbfunctions.php");


class myhtmlETable extends htmlETable {

		function GetColValue($column,$i){
			if (is_null($this->GetColumnHref($column,$i))) {
				if (($this->getColSubstring($column)>0) and (strlen($this->_tableData[$column]->GetValue($i))>$this->getColSubstring($column))){
					$content ='<span  id="'.$column.'_'.$i.'" >'.substr($this->_tableData[$column]->GetValue($i),0,$this->getColSubstring($column)).'</span>';
					$content.='<span dojoType="dijit.Tooltip" connectId="'.$column.'_'.$i.'" style="display:none;"><div style="max-width:250px; display:block;">'.$this->_tableData[$column]->GetValue($i).'</div></span>';
					return $content;
				} else {
					if($column=='Mittente'){
						return htmlspecialchars(stripslashes($this->_tableData[$column]->GetValue($i)));
					}
			    	return stripslashes($this->_tableData[$column]->GetValue($i));
				}

			} else {
				$value=$this->GetColumnHref($column,$i);
				$pattern='|<([a-zA-Z]{1,3}).*>|';
			    preg_match_all($pattern, $value, $match);
				$closeTag='</'.$match[1][0].'>';

				return $value.$this->_tableData[$column]->GetValue($i).$closeTag;

			}
		}

		function SetRowClass($index){
			$this->_RowClass=$this->GetColValue('rowclass',$index);
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
				switch ($key) {
					case 'PEC_ID':
						$row .= "\t".'<td align="center" ><span onclick="location.href=\'editPraticaPec.php?PEC_ID='.$this->GetColValue('PEC_ID',$i).'\'"><img style="cursor: pointer;" src="graphics/application_edit.png" title="Protocolla la pratica"></span></td>'."\n";
						break;
					default:
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
						break;
				}

			}
			$row='<TR class="'.$this->GetRowClass().'" id="'.$this->GetColValue('PEC_ID',$i).'" >'."\n".$row;
			$row.='</TR>'."\n";
			print($row);
		} // for
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
include('pageheader.inc');

print('<div id="dlgTipoPratica" dojoType="dijit.Dialog" title="Imposta tipo pratica" ' .
		'href="getDialog.php" ' .
		'>');
print('</div>');


include 'barraPec.inc';


$serviceQuery='select distinct
						pec.PEC_ID,
						(case
							when (pec.status = \'U\') then \'praOpen\'
							when (pec.status = \'R\') then \'praClose\'
							when (pec.status = \'P\') then \'praExit\'
						else \'praActive\'
						end) as rowclass ,
						pec.numeroregistrazione as "Protocollo",
						date_format(pec.dataregistrazione,\'%d-%m-%Y\') as "Data Pr.",
						date_format(pec.dataarrivo,\'%d-%m-%Y\') as "Arrivo",
						pec.mittente as Mittente,
						pec.subject as Oggetto,
						pec.DATAARRIVO,
						pec.DATAREGISTRAZIONE
						
					from arc_pratiche_pec pec ' .
				        $whereClause.
					' order by ' . (isSet($_SESSION['barraPec']['order']['field']) ? $_SESSION['barraPec']['order']['field'] : 'pec.dataarrivo ') .
                    ' ' . (isSet($_SESSION['barraPec']['order']['type']) ? $_SESSION['barraPec']['order']['type'] : ' DESC');


if($_GET['DEBUG'] == 'Y'){
    r($serviceQuery);
}

$serviceTable=new myhtmlETable($serviceQuery);
if ($serviceTable->getTableRows()>0) {

	$serviceTable->HideCol('rowclass');
    $serviceTable->HideCol('DATAARRIVO');
    $serviceTable->HideCol('DATAREGISTRAZIONE');


    $serviceTable->SetColumnHeader('PEC_ID','<img src="graphics/page_edit.png" >');
	$serviceTable->SetPageDivision(true);
	$wkPage=isSet($_GET['wk_page'])?$_GET['wk_page']:1;
	$serviceTable->SetPageDivision(true);
	$serviceTable->show($wkPage);

} else {
	print('<h3>Non ci sono Pec</h3>');
}
print('<div class="praLegend" style=" padding-right:20px; padding:5px; margin-right:20px;">' .
	  	'Legenda: ' .
	  		'<span class="praOpen" style="padding-left:5px; padding-right:5px;" >Da Leggere</span>' .
	  		'<span class="praClosed" style="padding-left:5px; padding-right:5px;" >Lette</span>' .
	  		'<span class="praExit" style="padding-left:5px; padding-right:5px;" >Protocollate</span>' .
'</div>');


include('pagefooter.inc');
