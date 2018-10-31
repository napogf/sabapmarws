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
					case 'STAFF_ID':
							$row .= "\t".'<td align="center" ><span onclick="setRowClassSt('.$this->GetColValue($key,$i).');' .
										'editStaff('.$this->GetColValue($key,$i).')">' .
									'<img style="cursor: pointer;" src="graphics/application_edit.png" title="Edita Staff"></span></td>'."\n";
						break;
					case 'DEL_STAFF':
							$row .= "\t".'<td align="center" ><span onclick="delStaff('.$this->GetColValue($key,$i).')"><img style="cursor: pointer;" src="graphics/application_delete.png" title="Cancella il componente lo Staff"></span></td>'."\n";
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
			$row='<TR class="'.$this->GetRowClass().'" ' .
					'id="riga_st_'.$this->GetColValue('STAFF_ID',$i).'" ' .
					'>'."\n".$row;
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

$stQuery='SELECT st.STAFF_ID, ' .
				'concat(li.UFFICIO,\' \',li.INCARICO) as Incarico, ' .
				'concat(us.LAST_NAME,\' \',us.FIRST_NAME) as "Componente", ' .
				'li.INCENTIVO as Incentivo, ' .
				'sum(lq.importo_netto*lq.incentivo/100)*li.incentivo/100 as "Incentivi maturati", ' .
				'st.STAFF_ID as DEL_STAFF ' .
			'from lav_staff st ' .
			'left join lav_perizie pr on (pr.PERIZIA_ID = st.PERIZIA_ID) ' .
			'left join lav_quadro_economico lq on (lq.perizia_id = st.perizia_id) ' .
			'left join sys_users us on (us.USER_ID = st.USER_ID) ' .
			'left join lav_incarichi li on (li.INCARICO_ID = st.INCARICO_ID) ' .
				'where st.perizia_id = '.$_GET['PERIZIA_ID'].' group by st.incarico_id, st.staff_id';


print('<div onclick="addStaff('.$_GET['PERIZIA_ID'].');" style="cursor: pointer; margin: 5px;" >' .
		'<img src="graphics/add.png" style="width:21px; height:20px; border:none; margin-right:5px;"  vspace="1" align="absbottom"  title="Crea Nuova Perizia">' .
		'Aggiungi un componente dello staff' .
	'</div>');
$stTable=new myHtmlETable($stQuery);
	if($stTable->getTableRows()>0){
		$stTable->SetColumnHeader('STAFF_ID','<img src="graphics/page_edit.png" >');
		$stTable->SetColumnHeader('DEL_STAFF','<img src="graphics/page_delete.png" >');
		$stTable->getColumn('Incentivo')->SetColumnType('percent',2,false);
		$stTable->getColumn('Incentivi maturati')->SetColumnType('number',2,true);

		$stTable->show();
	}
?>
