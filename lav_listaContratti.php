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
					case 'CONTRATTO_ID':
							$row .= "\t".'<td align="center" ><span onclick="editContratti('.$this->GetColValue($key,$i).')">' .
									'		<img style="cursor: pointer;" src="graphics/application_edit.png" title="Edita il Contratto"></span></td>'."\n";
							$sumArray[$key] = null;
						break;
					case 'DEL_CONTRATTO':
							$row .= "\t".'<td align="center" ><span onclick="delContratto('.$this->GetColValue($key,$i).')"><img style="cursor: pointer;" src="graphics/application_delete.png" title="Cancella il Contratto"></span></td>'."\n";
							$sumArray[$key] = null;
						break;

					case 'Oggetto';
							$row.='<td><span id="lavCo_'.$this->GetColValue('CONTRATTO_ID',$i).'">'.$this->GetColValue($key,$i).'</span>' .
							'<span dojoType="dijit.Tooltip" id="ttlavCo_'.$this->GetColValue('CONTRATTO_ID',$i).'" connectId="lavCo_'.$this->GetColValue('CONTRATTO_ID',$i).'" style="display:none;">' .
							'<div dojoType="dijit.layout.ContentPane" class="djToolTipContainer" href="djGetLavContratti.php?CONTRATTO_ID='.$this->GetColValue('CONTRATTO_ID',$i).'" style="overflow: hidden;" >' .
							'</div>' .
							'</span></td>';
							$sumArray[$key] = null;
						break;
					case 'Liquidazioni';
							$row .= "\t".'<TD id="tp'.$this->GetColValue('CONTRATTO_ID',$i).'" align="center" '.$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >' .
							'<img src="graphics/money_euro.png" style="cursor: pointer" title="Liquidazioni contratto" onclick="liquidaContratto(\''.$this->GetColValue('CONTRATTO_ID',$i).'\')" >'.
							'</TD>'."\n";
							$sumArray[$key] = null;
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
					'id="riga_co_'.$this->GetColValue('CONTRATTO_ID',$i).'" ' .
					' ondblclick="setRowClassCo('.$this->GetColValue('CONTRATTO_ID',$i).')" >'."\n".$row;
					'  >'."\n".$row;
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
$coQuery='SELECT lc.CONTRATTO_ID, ' .
				'lc.oggetto as Oggetto, ' .
//				'li.DESCRIPTION as "Impresa", ' .
//				'tp.value as "Tipologia", ' .
//				'date_format(lc.incarico_del,\'%d-%m-%Y\') as "Del",' .
//				'lc.nr_incarico as "Nr.Incarico", ' .
//				'date_format(lc.data_ultimazione_lavori,\'%d-%m-%Y\') as "Fine Lavori",' .
//
				'lc.importo_netto "Imp.complessivo", ' .
				'lc.oneri_sicurezza "Oneri sic.", ' .
				'lc.importo_netto-lc.oneri_sicurezza "Imp.sogg.ribasso", ' .
				'lc.SCONTO as "Ribasso", ' .
				'(((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100))) "Importo ribassato", ' .
				'((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza) "Importo netto", ' .
				'lc.iva as IVA, ' .
				'((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lc.iva/100)) "Importo lordo", ' .
				'lc.CONTRATTO_ID as "Liquidazioni", '.
				'max(date_format(ll.data_liquidazione,\'%d-%m-%Y\')) as "Data liquidazione",' .
				'sum(ll.IMPORTO_LIQUIDATO) as "Importo liquidato",' .
				'lc.CONTRATTO_ID as DEL_CONTRATTO ' .
			'from lav_contratti lc ' .
			'left join lav_liquidazioni ll on (ll.contratto_id = lc.contratto_id) ' .
//			'left join lav_quadro_economico lq on (lq.QECONOMICO_ID = lc.QECONOMICO_ID) ' .
			'left join lav_imprese as li on (li.impresa_id = lc.impresa_id) ' .
			'left join sys_fields_validations as tc on ((tc.field_name=\'radioTipoContratti\') and (tc.code=lc.tipo_lavori) and (tc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as uc on ((uc.field_name=\'radioUrgContratti\') and (uc.code=lc.urgenza) and (uc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as tp on ((tp.field_name=\'tipoPerizia\') and (tp.code=lc.tipologia) and (tp.language_id='.$_SESSION['sess_lang'].')) ' .
				'where lc.qeconomico_id = '.$_GET['QECONOMICO_ID'] .' group by lc.contratto_id ';

print('<div onclick="addContratti('.$_GET['QECONOMICO_ID'].');" style="cursor: pointer; margin: 5px;" >' .
		'<img src="graphics/add.png" style="width:21px; height:20px; border:none; margin-right:5px;"  vspace="1" align="absbottom"  title="Crea Nuovo Contratto">' .
		'Aggiungi Contratto' .
	'</div>');

print('<div id="dlgLiquidazione" dojoType="dijit.Dialog" ' .
		'onHide="dijit.byId(\'listaContratti\').refresh();dijit.byId(\'listaPerizie\').refresh();" ' .
		'title="Liquida Contratto" ' .
		'href="lav_dlgLiquidazioni.php" ' .
		'>');
print('</div>');

$coTable=new myHtmlETable($coQuery);
	if($coTable->getTableRows()>0){
		$coTable->SetColumnHeader('CONTRATTO_ID','<img src="graphics/page_edit.png" >');
		$coTable->SetColumnHeader('DEL_CONTRATTO','<img src="graphics/page_delete.png" >');
		$coTable->getColumn('Importo liquidato')->SetColumnType('currency',2,true);
		$coTable->getColumn('Importo lordo')->SetColumnType('currency',2,true);
		$coTable->getColumn('Importo ribassato')->SetColumnType('currency',2,true);
		$coTable->getColumn('Importo netto')->SetColumnType('currency',2,true);
		$coTable->getColumn('Imp.complessivo')->SetColumnType('currency',2,true);
		$coTable->getColumn('Importo lordo')->SetColumnType('currency',2,true);
		$coTable->getColumn('Oneri sic.')->SetColumnType('currency',2,true);
		$coTable->getColumn('Imp.sogg.ribasso')->SetColumnType('currency',2,true);

		$coTable->getColumn('Ribasso')->SetColumnType('percent',0,false);
		$coTable->getColumn('IVA')->SetColumnType('percent',0,false);
		$coTable->_decimalsTotal = 2;
		$coTable->printTotal(true,2);
		$coTable->show();

	}
?>