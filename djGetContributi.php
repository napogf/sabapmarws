<?php
/*
 * Created on 20/apr/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");
class myHtmlETable extends htmlETable {

		function GetTableHeader(){
			$this->_TableHeader= "<TR>\n" .'<th class="listapiccolo" nowrap>NR</th>';

			foreach ($this->_tableData as $key=>$value){
				if ($value->IsShowed()) {
				    $this->_TableHeader .= "\t".'<TH class="listapiccolo" nowrap>'.$value->GetColHeader().'</TH>'."\n";
				}
			}
			$this->_TableHeader.="</TR>\n";
			return $this->_TableHeader;
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
			$row ='<td>'.($i+1).'</td>';
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
			$sumRow = '<tr class="djSumRow"><td></td>';
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

$delField = $_GET['dispaly']=='Y'
	?''
	:', concat(\'<center><img src="graphics/webapp/deleterec.gif" STYLE="cursor: pointer;" onClick="cancellaContributo(\',id,\')" title="Cancella Contributo" ></center>\') as "#" ' ;


	$dispContributiQuery = 'select ' .
										'RIF_ART as "Rif.Art.",' .
										'DESCRIPTION as "Descrizione Voce",' .
										'DETRAZIONE as "Imp. Detraibile",' .
										'(INCIDENZA*100) as "Incidenza %", ' .
										'(DETRAZIONE*INCIDENZA) as "Detrazione" ' .
										$delField .
										'From arc_contributi ' .
										'where pratica_id = '.$_GET['praticaId'].' order by id ';

	$contrTable = new myHtmlEtable($dispContributiQuery);

	if($contrTable->getTableRows()>0){
		$contrTable->SetTableCaption('Elenco voci non ammesse a contributo');
		$contrTable->getColumn('Incidenza %')->SetColumnType('percent');
		$contrTable->getColumn('Imp. Detraibile')->SetColumnType('number',2,true);
		$contrTable->getColumn('Detrazione')->SetColumnType('number',2,true);
//		$contrTable->getColumn('Amm.')->SetColAlign('center');
		$contrTable->_decimalsTotal = 2;
		$contrTable->printTotal(true,2);
	}



	$contrTable->show();



?>
