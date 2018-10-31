<?php
/*
 * Created on 26/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");

class myHtmlETable extends htmlETable {

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
					case 'QECONOMICO_ID':
							if ($this->GetColValue('CODICE',$i)=='E') {
								$row .= "\t".'<td align="center" ></td>'."\n";
								$sumArray[$key] = null;
							} else {
								$row .= "\t".'<td align="center" ><span onclick="' .
												'editQeconomico('.$this->GetColValue($key,$i).')"><img style="cursor: pointer;" src="graphics/application_edit.png" title="Edita la Voce di Quadro Economico"></span></td>'."\n";
								$sumArray[$key] = null;
							}
						break;
					case 'DEL_QECONOMICO':
							if ($this->GetColValue('CODICE',$i)=='E') {
								$row .= "\t".'<td align="center" ></td>'."\n";
								$sumArray[$key] = null;
							} else {
								$row .= "\t".'<td align="center" ><span onclick="delQeconomico('.$this->GetColValue($key,$i).')"><img style="cursor: pointer;" src="graphics/application_delete.png" title="Cancella la Voce di Quadro Economico"></span></td>'."\n";
								$sumArray[$key] = null;
							}
						break;
					default:
						if ($value->IsShowed()) {
							if ($value->GetColRowClass()>'') {
							    $this->_RowClass=$value->GetColRowClass();
							}
							if(($value->GetColumnType()=='number' or $value->GetColumnType()=='currency') and $value->getColTotal()){
								if ($this->GetColValue('CODICE',$i)=='E') {
									$sumArray[$key] += 0;
								} else {
									$sumArray[$key] += $value->_Value[$i];
								}
							} else {
								$sumArray[$key] = null;
							}
							$row .= "\t".'<TD '.$value->GetColAlign().$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >'.$this->GetColValue($key,$i).'</TD>'."\n";
						}
						break;
				}

			}
			$rowStyle=($this->GetColValue('CODICE',$i)=='E')?' style="background-color: #90EE90;" ':'';
			$rigaE=($this->GetColValue('CODICE',$i)=='E')?'true':'false';
			$row='<TR class="'.$this->GetRowClass().'" ' .
					'id="riga_qe_'.$this->GetColValue('QECONOMICO_ID',$i).'" ' .$rowStyle.
					' ondblclick="loadContratti('.$this->GetColValue('QECONOMICO_ID',$i).','.$rigaE.')" >'."\n".$row;
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



$qeQuery='select * from (SELECT lq.QECONOMICO_ID, ' .
				'lq.CODICE, ' .
				'lq.DESCRIZIONE_LAVORI, ' .
				'sum(lic.IMPORTO_COMPLESSIVO) as "Importo netto", ' .
				'round(sum(lic.IMPORTO_COMPLESSIVO*lic.iva/100),2) as "IVA",' .
//				'lq.importo_netto as "QE Importo netto", ' .
				'lq.IMPORTO_LORDO as "Importo lordo", ' .
				'sum(lic.ONERI_SICUREZZA) as "Oneri sicurezza", ' .
//				'lq.ONERI_SICUREZZA as "QE Oneri sicurezza",' .
				'lq.INCENTIVO as "Incentivo", ' .
				'round((sum(lic.IMPORTO_COMPLESSIVO)*lq.INCENTIVO/100),2) as "Val.Incentivo", ' .
//				'round((lq.importo_netto*lq.INCENTIVO/100),2) as "QE Val.Incentivo", ' .
//				'round(((lq.importo_netto*(1+(lq.iva/100)))+(lq.importo_netto*lq.INCENTIVO/100)),2) as "QE Imp.Totale", ' .
//				'round((sum(lic.IMPORTO_COMPLESSIVO)*lq.INCENTIVO/100),2)+' .

				'lq.IMPORTO_LORDO+if(lic.importo_complessivo>0,round((sum(lic.IMPORTO_COMPLESSIVO)*lq.INCENTIVO/100),2),0) as "Imp.Totale", ' .
				'lic.importo_lordo "Val.Contratti" , ' .
				//'(lq.importo_lordo+round((sum(lic.IMPORTO_COMPLESSIVO)*lq.INCENTIVO/100),2)) as "Imp.Totale", ' .
				'lq.QECONOMICO_ID as DEL_QECONOMICO ' .
			'from lav_quadro_economico lq ' .
			'left join lav_importi_contratti lic on (lic.QECONOMICO_ID = lq.QECONOMICO_ID) ' .
				'where lq.perizia_id = '.$_GET['PERIZIA_ID'].' and lq.CODICE <> \'E\' ' .
			'group by lq.QECONOMICO_ID order by 3 ) qe ' .
			'UNION ' .
			'SELECT le.QECONOMICO_ID,' .
				'le.CODICE, ' .
				'le.DESCRIZIONE_LAVORI, ' .
				'le.IMPORTO_LORDO as "Importo lordo", ' .
				'le.IVA as "IVA", ' .
				'le.IMPORTO_NETTO as "Importo netto", ' .
				'le.ONERI_SICUREZZA as "Oneri sicurezza", ' .
				'0 as "Incentivo", ' .
				'0 as "Val.Incentivo", ' .
				'0 as "Imp.Totale", ' .
				'le.IMPORTO_NETTO+le.IVA as "Val.Contratti", ' .
				'le.QECONOMICO_ID as DEL_QECONOMICO ' .
			'from lav_qe_economie le ' .
			'where le.perizia_id = ' .$_GET['PERIZIA_ID'];




print('<div onclick="addQeconomico('.$_GET['PERIZIA_ID'].');" style="cursor: pointer; margin: 5px;" >' .
		'<img src="graphics/add.png" style="width:21px; height:20px; border:none; margin-right:5px;"  ' .
			'vspace="1" align="absbottom"  title="Crea Nuova Perizia">' .
		'Crea una nuova Voce di Quadro Economico' .
	'</div>');

$qeTable=new myHtmlETable($qeQuery);
	if($qeTable->getTableRows()>0){
		$qeTable->SetColumnHeader('QECONOMICO_ID','<img src="graphics/page_edit.png" >');
		$qeTable->SetColumnHeader('DEL_QECONOMICO','<img src="graphics/page_delete.png" >');
		$qeTable->getColumn('Importo netto')->SetColumnType('number',2,true);
		$qeTable->getColumn('Importo lordo')->SetColumnType('number',2,true);
		$qeTable->getColumn('Oneri sicurezza')->SetColumnType('currency',2,true);
		$qeTable->getColumn('Incentivo')->SetColumnType('percent',2,false);
		$qeTable->getColumn('Val.Incentivo')->SetColumnType('currency',2,true);
		$qeTable->getColumn('Imp.Totale')->SetColumnType('currency',2,true);
		$qeTable->getColumn('Val.Contratti')->SetColumnType('currency',2,true);
		$qeTable->getColumn('IVA')->SetColumnType('currency',2,true);
		$qeTable->_decimalsTotal = 2;
		$qeTable->printTotal(true,2);

		$qeTable->show();
	}
?>
