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
	function SetRowClass($index){
		$this->_RowClass=$this->GetColValue('rowclass',$index);
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
					'id="riga_contratto_'.$this->GetColValue('CONTRATTO_ID',$i).'" ' .
					' >'."\n"
					.$row;
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

	function saveAsXls($fileName=null){
		global $PHP_SELF;
		$fileName=is_null($fileName)?basename($PHP_SELF,'.php').'.xls':$fileName;
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(getcwd());

		// sending HTTP headers
		$workbook->send($fileName);

		$worksheet1 =& $workbook->addWorksheet();
		// Format for the heading
		$formatot =& $workbook->addFormat();
		$formatot->setSize(10);
		$formatot->setAlign('center');
		$formatot->setColor('white');
		$formatot->setFgColor('black');
		$formatot->setPattern();
		$col=0;
		foreach ($this->_tableData as $key=>$value){
			if ($value->IsShowed()) {
				$worksheet1->writeString(0,$col,$key,$formatot);
				$col++;
			}
		}

		$dateFormat=& $workbook->addFormat();
		$numFormat=& $workbook->addFormat();
		$curFormat=& $workbook->addFormat();
		$stringFormat=& $workbook->addFormat();
		$numFormat->SetNumFormat('#,##0.00');
		$dateFormat->SetNumFormat('DD-MM-YYYY');
		$curFormat->SetNumFormat('#,##0.00');

		$stringFormat->setColor(2);
		$stringFormat->setFgColor(3);
		$stringFormat->setPattern(1);
		// number of seconds in a day
		$seconds_in_a_day = 86400;
		// Unix timestamp to Excel date difference in seconds
		$ut_to_ed_diff = $seconds_in_a_day * 25569;
		for($i = 0; $i < $this->_TableRows; $i++){
			$col=0;
			$dateFormat=& $workbook->addFormat();
			$numFormat=& $workbook->addFormat();
			$curFormat=& $workbook->addFormat();
			$stringFormat=& $workbook->addFormat();
			$numFormat->SetNumFormat('#,##0.00');
			$dateFormat->SetNumFormat('DD-MM-YYYY');
			$curFormat->SetNumFormat('#,##0.00');

			switch ($this->GetColValue('rowclass',$i)) {
				case 'conAvvio':
					$numFormat->setColor(2);
    				$numFormat->setFgColor(22);

					$dateFormat->setColor(2);
    				$dateFormat->setFgColor(22);

					$curFormat->setColor(2);
    				$curFormat->setFgColor(22);


					$stringFormat->setColor(2);
    				$stringFormat->setFgColor(22);
					break;
				case 'conCorso':
					$numFormat->setColor(0);
    				$numFormat->setFgColor(19);

					$dateFormat->setColor(0);
    				$dateFormat->setFgColor(19);

					$curFormat->setColor(0);
    				$curFormat->setFgColor(19);


					$stringFormat->setColor(0);
    				$stringFormat->setFgColor(19);
					break;
				case 'conFine':
					$numFormat->setColor(0);
    				$numFormat->setFgColor(2);

					$dateFormat->setColor(0);
    				$dateFormat->setFgColor(2);

					$curFormat->setColor(0);
    				$curFormat->setFgColor(2);


					$stringFormat->setColor(0);
    				$stringFormat->setFgColor(2);
					break;
				case 'conLiquidato':
					$numFormat->setColor(1);
    				$numFormat->setFgColor(23);

					$dateFormat->setColor(1);
    				$dateFormat->setFgColor(23);

					$curFormat->setColor(1);
    				$curFormat->setFgColor(23);


					$stringFormat->setColor(1);
    				$stringFormat->setFgColor(23);
					break;
				case 'conChiuso':
					$numFormat->setColor(0);
    				$numFormat->setFgColor(3);

					$dateFormat->setColor(0);
    				$dateFormat->setFgColor(3);

					$curFormat->setColor(0);
    				$curFormat->setFgColor(3);


					$stringFormat->setColor(0);
    				$stringFormat->setFgColor(3);
					break;

			}

			foreach ($this->_tableData as $key=>$value){
				if ($value->IsShowed()) {
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
								$worksheet1->writeString($i+1,$col,$value->GetValue($i),$stringFormat);
							}
							break;
						case 'currency':
							$worksheet1->writeNumber($i+1,$col,$value->GetValue($i),$curFormat);
							break;
						default:
							$worksheet1->writeString($i+1,$col,$value->GetValue($i),$stringFormat);
							break;
					}
					$col++;
				}
			}
		} // for
		$workbook->close();
	}


}



$whereClause = ' where 1';

if($_GET['annoPerizie']>'') $whereClause.=' and date_format(pr.data_perizia,"%Y")=\''.$_GET['annoPerizie'].'\' ';

if($_GET['daDataFine']>'' and $_GET['aDataFine']) $whereClause.='and (lc.data_ultimazione_lavori between str_to_date(\''.$_GET['daDataFine'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_GET['aDataFine'].'\',\'%Y-%m-%d\') ) ';

if($_GET['daDataLiquidazione']>'' and $_GET['aDataLiquidazione']) $whereClause.=' and (ll.data_liquidazione between str_to_date(\''.$_GET['daDataLiquidazione'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_GET['aDataLiquidazione'].'\',\'%Y-%m-%d\') ) ';

if($_GET['IMPRESA_ID'] > '') $whereClause .= ' and (lc.impresa_id = '.$_GET['IMPRESA_ID'].' ) ';
if($_GET['NR_PERIZIA'] > '') $whereClause .= ' and (pr.nr_perizia = \''.$_GET['NR_PERIZIA'].'\') ';


if($_GET['ordField']=='data'){
	$orderBy= ' order by pr.data_perizia '.$_GET['ordType'].' ';
} elseif ($_GET['ordField']=='nr'){
	$orderBy= ' order by pr.nr_perizia '.$_GET['ordType'].' ';
} else {
	$orderBy=' order by pr.anno_finanziamento desc, pr.nr_perizia desc';
}




$contrattiQuery='SELECT ' .
				'pr.anno_finanziamento as "Anno",' .
				'pr.nr_perizia as "Nr.P.", ' .
				'lc.CONTRATTO_ID, lq.codice as "QE", ' .
//				'round(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lc.iva/100)),-1) r1,' .
//				'round(sum(ll.IMPORTO_LIQUIDATO),-1) r2, ' .
				'case ' .
					'when ((ll.contratto_id is not null) and (round(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lc.iva/100)),-1) = round(sum(ll.IMPORTO_LIQUIDATO),-1) )) then "conChiuso" ' .
					'when ((ll.contratto_id is not null) and (round(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lc.iva/100)),-1) <> round(sum(ll.IMPORTO_LIQUIDATO),-1) )) then "conLiquidato" ' .
					'when ((ll.contratto_id is null) and (lc.data_ultimazione_lavori is not null)) then "conFine" ' .
					'when ((ll.contratto_id is null) and (lc.data_ultimazione_lavori is null) and (lc.incarico_del is not null)) then "conCorso" ' .
					'else "conAvvio" ' .
				'end as "rowclass", ' .
				'lc.oggetto as Oggetto, ' .
				'li.DESCRIPTION as "Impresa", ' .
//				'tp.value as "Tipologia", ' .
				'lc.nr_incarico as "Nr.Incarico", ' .
				'date_format(lc.incarico_del,\'%d-%m-%Y\') as "Del",' .
				'date_format(lc.data_ultimazione_lavori,\'%d-%m-%Y\') as "Fine lavori",' .
				'round(lc.importo_netto,2) "Imp.complessivo", ' .
				'round(lc.oneri_sicurezza,2) "Oneri sic.", ' .
				'round(lc.importo_netto-lc.oneri_sicurezza,2) "Imp.sogg.ribasso", ' .
				'round(lc.SCONTO,2) as "Ribasso", ' .
				'round((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100))),2) "Imp.ribassato", ' .
				'round(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza),2) "Imp.netto", ' .
				'round(lc.iva,2) as IVA, ' .
				'round(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lc.iva/100)),2) "Imp.lordo", ' .
				'round(sum(ll.IMPORTO_LIQUIDATO),2) as "Imp.liquidato", ' .
				'max(date_format(ll.data_liquidazione,\'%d-%m-%Y\')) as "Data liq." ' .
			'from lav_contratti lc ' .
			'left join lav_liquidazioni ll on (ll.contratto_id = lc.contratto_id) ' .
			'left join lav_quadro_economico lq on (lq.QECONOMICO_ID = lc.QECONOMICO_ID) ' .
			'left join lav_perizie as pr on (pr.perizia_id = lq.perizia_id) ' .
			'left join lav_imprese as li on (li.impresa_id = lc.impresa_id) ' .
			'left join sys_fields_validations as tc on ((tc.field_name=\'radioTipoContratti\') and (tc.code=lc.tipo_lavori) and (tc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as uc on ((uc.field_name=\'radioUrgContratti\') and (uc.code=lc.urgenza) and (uc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as tp on ((tp.field_name=\'tipoPerizia\') and (tp.code=lc.tipologia) and (tp.language_id='.$_SESSION['sess_lang'].')) ' .
				' '.$whereClause.' group by lc.contratto_id '.$orderBy;

//var_dump($contrattiQuery);
$contrattiTable=new myHtmlETable($contrattiQuery);
$wk_page=$_GET['wk_page']>''?$_GET['wk_page']:1;





	if($contrattiTable->getTableRows()>0){
		$contrattiTable->_decimalsTotal = 2;
		$contrattiTable->printTotal(true,2);
		$contrattiTable->SetPageDivision(false);

			$contrattiTable->getColumn("Del")->SetColumnType('date');
			$contrattiTable->getColumn("Fine lavori")->SetColumnType('date');
			$contrattiTable->getColumn("Data liq.")->SetColumnType('date');

		$contrattiTable->getColumn('Imp.complessivo')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('Oneri sic.')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('Imp.sogg.ribasso')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('Ribasso')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('Imp.ribassato')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('Imp.netto')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('IVA')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('Imp.lordo')->SetColumnType('currency',2,true);
		$contrattiTable->getColumn('Imp.liquidato')->SetColumnType('currency',2,true);
		$contrattiTable->HideCol('CONTRATTO_ID');
		$contrattiTable->HideCol('rowclass');
		$contrattiTable->setColSubstring('Impresa',20);
		$contrattiTable->setColSubstring('Oggetto',20);
		if (isSet($_GET['xlsSave']) and ($_GET['xlsSave'] == 'Y')){
			$contrattiTable->saveAsXls('statusContratti.xls');
			exit;
		}
	}
		include('pageheader.inc');
				print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
				'url="xml/jsonSql.php?nullValue=Y&sql=select distinct date_format(data_perizia,\'%Y\') as VALUE from lav_perizie" ' .
				'jsId="selAnno" ' .
				'></div>');

				print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
				'url="xml/jsonSql.php?nullValue=Y&sql=select distinct IMPRESA_ID, DESCRIPTION from lav_imprese where impresa_id in (select distinct impresa_id from lav_contratti) order by 2" ' .
				'jsId="selImpresa" ' .
				'></div>');
			print('<div style="margin-left:20px;" >' ."\n".
						'<form name=searchForm ' .
								'action='.$PHP_SELF.' method=get style="margin-bottom: 5px">'."\n".
						'<input type="hidden" name="xlsSave" value="N" id="xlsSave" >' .
						'<div style="float:left;" >' .
						'<table style="text-align: right;">' .
						'<tr>' .
							'<td>' .
								'<div style="margin: 5px;">&nbsp;Anno Perizie<div style="width:100px;" ' .
										'dojoType="dijit.form.FilteringSelect" ID="SELANNO"
														store="selAnno"
														labelAttr="VALUE"
														searchAttr="VALUE"
														name="annoPerizie" ' .
														'value="'.$_GET['annoPerizie'].'" ' .
										'></div>' .
							'</td>' .
							'<td>' .
							'Nr.Perizia<input dojoType="dijit.form.TextBox" type="text" name="NR_PERIZIA"  value="'.$_GET['NR_PERIZIA'].'" style="margin: 5px; width: 10em;" >' .
							'</td>' .
							'<td>' .
							'Da data liquidazione<input dojoType="dijit.form.DateTextBox" type="text" name="daDataLiquidazione"  value="'.$_GET['daDataLiquidazione'].'" style="margin: 5px; width: 10em;" >' .
							'</td>' .
							'<td>' .
							'A data liquidazione<input dojoType="dijit.form.DateTextBox" type="text" name="aDataLiquidazione"  value="'.$_GET['aDataLiquidazione'].'" style="margin: 5px; width: 10em;"><br/>' .
						'</td>' .
							'<td>' .
								'</div><div style="padding-top:5px;">' .
									'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'N\';document.searchForm.submit()">'."\n".
								'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
									'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'Y\';document.searchForm.submit()">'."\n".
								'<img src="graphics/mime/msexcel.gif" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
								'</div>' .
							'</td>' .
						'</tr>' .
						'<tr>' .
							'<td></td>' .
							'<td>' .
								'<div style=" margin: 5px;">&nbsp;Impresa<div style="width:100px;" ' .
										'dojoType="dijit.form.FilteringSelect" ID="IMPRESA_ID"
														store="selImpresa"
														labelAttr="DESCRIPTION"
														searchAttr="DESCRIPTION"
														name="IMPRESA_ID" ' .
														'value="'.$_GET['IMPRESA_ID'].'" ' .
										'></div>' .
							'</td>' .
							'<td>' .
							'Da data fine lavori<input dojoType="dijit.form.DateTextBox" type="text" name="daDataFine"  value="'.$_GET['daDataFine'].'" style="margin: 5px; width: 10em;" >' .
							'</td>' .
							'<td>' .
							'A data fine lavori<input dojoType="dijit.form.DateTextBox" type="text" name="aDataFine"  value="'.$_GET['aDataFine'].'" style="margin: 5px; width: 10em;"><br/>' .
						'</td>' .
							'<td></td>' .
						'</tr>' .
						'</table>' .
					'</form>'."\n".
				'</div>'."\n");
			print('<div style=" clear: both;" ></div>');
		print('<div dojoType="dijit.layout.ContentPane"' .
				'	design="headline" style="border:none;">');
			print('<div dojoType="dijit.layout.ContentPane" ' .
					'style="min-height:450px;border:none;margin: 10px 20px;" ' .
					'region="top">');
	if($contrattiTable->getTableRows()>0){
		$contrattiTable->show($wk_page);
	} else {
		print('<h2>Non ci sono contratti nella selezione!</h2>');
	}
	print('<div class="praLegend" style=" padding-right:20px; padding:5px; margin-right:20px;">' .
		  	'Legenda: ' .
		  		'<span class="conAvvio" style="padding-left:5px; padding-right:5px;" >Da avviare</span>' .
		  		'<span class="conCorso" style="padding-left:5px; padding-right:5px;" >In Corso</span>' .
		  		'<span class="conFine" style="padding-left:5px; padding-right:5px;" >Conclusi</span>' .
		  		'<span class="conLiquidato" style="padding-left:5px; padding-right:5px;" >Par.liquidati</span>' .
		  		'<span class="conChiuso" style="padding-left:5px; padding-right:5px;" >Liquidati</span>' .
		  '</div>');

	print('</div>');
print('</div>');
include('pagefooter.inc');
?>