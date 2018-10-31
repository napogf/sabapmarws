<?php
/*
 * Created on 07/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
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

	function GetTableHeader(){
		$this->_TableHeader="<TR>\n";
		foreach ($this->_tableData as $key=>$value){
			if ($value->IsShowed()) {
				$orderType=$_GET['ordType']=='DESC'?'ASC':'DESC';
				$imgOrder=$_GET['ordType']=='ASC'?'up.gif':'down.gif';
				switch ($key) {
					case 'Nr.':
					    $this->_TableHeader .= "\t".'<TH class="listapiccolo" nowrap>';
					    $this->_TableHeader .= $_GET['ordField']=='nr'?$value->GetColHeader().'&nbsp;<img src="graphics/'.$imgOrder.'" onClick="setPerizieOrder(\'nr\',\''.$orderType.'\');" >':
							'<a onClick="setPerizieOrder(\'nr\',\''.$orderType.'\');" style="color:yellow;" >'.$value->GetColHeader().'</a>';
						$this->_TableHeader .= '</TH>'."\n";
						break;
					case 'Data':
					    $this->_TableHeader .= "\t".'<TH class="listapiccolo" nowrap>';
					    $this->_TableHeader .= $_GET['ordField']=='data'?$value->GetColHeader().'&nbsp;<img src="graphics/'.$imgOrder.'" onClick="setPerizieOrder(\'data\',\''.$orderType.'\');" >':
							'<a onClick="setPerizieOrder(\'data\',\''.$orderType.'\');" style="color:yellow;" >'.$value->GetColHeader().'</a>';
						$this->_TableHeader .= '</TH>'."\n";
						break;

					default:
					    $this->_TableHeader .= "\t".'<TH class="listapiccolo" nowrap>'.$value->GetColHeader().'</TH>'."\n";
						break;
				}
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
			$row ='';
			foreach ($this->_tableData as $key=>$value){
				switch ($key) {
					case 'PERIZIA_ID':
							$row .= "\t".'<td align="center" ><span onclick="editPerizia('.$this->GetColValue($key,$i).')"><img style="cursor: pointer;" src="graphics/application_edit.png" title="Edita la Perizia"></span></td>'."\n";
							$sumArray[$key] = null;
						break;
					case 'EXP_PERIZIA':
							 $row .= "\t".'<td align="center" ><span onclick="location.href=\'lav_expPerizia.php?PERIZIA_ID='.$this->GetColValue($key,$i).'\'"><img src="graphics/mime/msexcel.gif" width="16" height="16" vspace="1" border="0" align="absbottom" style="cursor:pointer;" title="Esporta la Perizia" /></span></td>'."\n";
							$sumArray[$key] = null;
						break;
					case 'DEL_PERIZIA':
							$row .= "\t".'<td align="center" ><span onclick="delPerizia('.$this->GetColValue($key,$i).')"><img style="cursor: pointer;" src="graphics/application_delete.png" title="Cancella la Perizia"></span></td>'."\n";
							$sumArray[$key] = null;
						break;
//					case 'Istituto';
//							$row.='<span id="nrp_'.$this->GetColValue('PERIZIA_ID',$i).'">'.$this->GetColValue($key,$i).'</span>' .
//							'<span dojoType="dijit.Tooltip" id="ttnrp_'.$this->GetColValue('PERIZIA_ID',$i).'" connectId="nrp_'.$this->GetColValue('PERIZIA_ID',$i).'" style="display:none;">' .
//							'<div dojoType="dijit.layout.ContentPane" class="djToolTipContainer" href="lav_djTotalePerizia.php?PERIZIA_ID='.$this->GetColValue('PERIZIA_ID',$i).'" style="overflow: hidden;" >' .
//							'</div>' .
//							'</span>';
//							$sumArray[$key] = null;
//						break;


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
					'id="riga_perizia_'.$this->GetColValue('PERIZIA_ID',$i).'" ' .
					' ondblclick="loadQeconomico('.$this->GetColValue('PERIZIA_ID',$i).');loadStaff('.$this->GetColValue('PERIZIA_ID',$i).');" >'."\n".$row;
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

if($_GET['annoPerizie']>'') $whereClause=' where date_format(pr.data_perizia,"%Y")=\''.$_GET['annoPerizie'].'\' ';

if($_GET['ordField']=='data'){
	$orderBy= ' order by pr.data_perizia '.$_GET['ordType'].' ';
} elseif ($_GET['ordField']=='nr'){
	$orderBy= ' order by pr.nr_perizia '.$_GET['ordType'].' ';
} else {
	$orderBy=' order by pr.anno_finanziamento desc, pr.nr_perizia desc';
}

$perizieQuery='select pr.PERIZIA_ID, ' .
					'pr.PERIZIA_ID as EXP_PERIZIA,' .
//					'qe.QECONOMICO_ID, ' .
//					'lc.CONTRATTO_ID, ' .
					'pr.nr_perizia as "Nr.", ' .
					'date_format(pr.data_perizia,\'%d-%m-%Y\') as "Data", ' .
					'pr.anno_finanziamento as "A.F.",' .
					'pr.capitolo_spesa as "CAP", ' .
//					'tp.value as "Tipologia", ' .
					'pr.denominazione_istituto as "Istituto", ' .
					'pr.oggetto as "Oggetto", ' .
					'pr.importo_lordo as "Importo",' .
					'sum(lic.importo_lordo) "Impegno spesa", ' .
					'sum(lic.importo_liquidato) as "Importo liquidato",' .
					'pr.importo_lordo - sum(lic.importo_lordo) "Residuo Perizia", ' .
					'lqe.importo_lordo-lqe.importo_netto-lqe.iva as "Econ.disponibili", ' .
					'pr.PERIZIA_ID as DEL_PERIZIA ' .
				'from lav_perizie pr ' .
				'left join lav_qe_economie as lqe on (lqe.perizia_id = pr.perizia_id) ' .
				'left join lav_importi_contratti as lic on ((lic.perizia_id = pr.perizia_id) and (lic.codice <> \'E\')) ' .
					$whereClause.' group by pr.perizia_id'.$orderBy;

$perizieTable=new myHtmlETable($perizieQuery);
//var_dump($perizieQuery);


//$perizieTable->SetColumnHeader('QECONOMICO_ID','<img src="graphics/calendar_view_month.png" >');
//$perizieTable->SetColumnHeader('CONTRATTO_ID','<img src="graphics/calendar_view_week.png" >');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?nullValue=Y&sql=select distinct date_format(data_perizia,\'%Y\') as VALUE from lav_perizie" ' .
		'jsId="selAnno" ' .
		'></div>');

	print('<div style="float:left; margin: 5px;">&nbsp;Anno Perizie<div style="width:100px;" ' .
					'dojoType="dijit.form.FilteringSelect" ID="SELANNO"
									store="selAnno"
									labelAttr="VALUE"
									searchAttr="VALUE"
									name="annoPerizia" ' .
									'value="'.$_GET['annoPerizie'].'" ' .
									'onChange="selAnnoPerizie();"' .
					'></div></div>' .
			'<div onclick="addPerizia();" style="cursor: pointer; margin: 5px;float:left;" >' .
			'<img src="graphics/add.png" style="width:21px; height:20px; border:none; margin-right:5px;"  vspace="1" align="absbottom"  title="Crea Nuova Perizia">' .
			'Crea una nuova Perizia' .
		'</div><div style="clear:both"></div>');


	$wk_page=$_GET['wk_page']>''?$_GET['wk_page']:1;



print('<div dojoType="dijit.layout.ContentPane"' .
		'	design="headline" style="border:none; overflow:auto;">');
	print('<div dojoType="dijit.layout.ContentPane" ' .
			'style="min-height:450px;border:none; overflow:auto;" ' .
			'region="top">');
	if($perizieTable->getTableRows()>0){
		$perizieTable->_decimalsTotal = 2;
		$perizieTable->printTotal(true,2);
		$perizieTable->SetColumnHeader('PERIZIA_ID','<img src="graphics/page_edit.png" >');
		$perizieTable->SetColumnHeader('EXP_PERIZIA','<img src="graphics/disk.png" >');
		$perizieTable->SetColumnHeader('DEL_PERIZIA','<img src="graphics/page_delete.png" >');
		$perizieTable->getColumn('Importo')->SetColumnType('currency',2,true);
		$perizieTable->getColumn('Importo liquidato')->SetColumnType('currency',2,true);
		$perizieTable->getColumn('Impegno spesa')->SetColumnType('currency',2,true);
		$perizieTable->getColumn('Residuo Perizia')->SetColumnType('currency',2,true);
		$perizieTable->getColumn('Econ.disponibili')->SetColumnType('currency',2,true);
		$perizieTable->setColSubstring('Istituto',30);
		$perizieTable->setColSubstring('Oggetto',30);
		$perizieTable->show($wk_page);

	}
	print('</div>');
print('</div>');
?>