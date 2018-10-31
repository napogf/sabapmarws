<?php
/*
 * Created on 01/ott/2012
 *
 * djDisplayPec.php
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("Etable_c.inc");
//require_once('pecList.php');

class myHtmlETable extends htmlETable {

		function GetColValue($column,$i){
			switch ($column) {
				case 'id':
						return '<img src="graphics/report_add.png" style="cursor: pointer" title="Assegna la pratica"
									onclick="assegnaMail(\''.$this->_tableData[$column]->GetValue($i).'\',\''.$this->_tableData['mid']->GetValue($i).'\')" '.
									' id="'.$this->_tableData[$column]->GetValue($i).'">';
					break;
				case 'attach':
						return '<img src="graphics/16_attachment.png" width="16" style="cursor: pointer" title="Allega la mail ad una pratica"
									onclick="allegaMail(\''.$this->_tableData[$column]->GetValue($i).'\',\''.$this->_tableData['mid']->GetValue($i).'\')" '.
									' id="'.$this->_tableData[$column]->GetValue($i).'">';
					break;
				default:
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
				break;
			}
		}

		function SetqueryArray($value,$IsStatement=FALSE){
			if (($IsStatement) and ($value > '')) {
				global $linkDB;
			  	$result = mysql_query($value)
			    	or die("Query non valida: " . mysql_error().'<BR>'.$value.'<br>'.var_dump(debug_backtrace()));
				while($riga = mysql_fetch_array($result, MYSQL_ASSOC)){
					$this->_TableRows++;
					if (is_null($this->_tableData)) {
						$i=0;
						foreach ($riga as $key => $value) {
						   $this->_tableData[$key] = new HtmlECol($key);
						   $this->_tableData[$key]->SetValue($value);
						   $ftype=mysql_field_type($result, $i);
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
				foreach($value as $riga){
					$this->_TableRows++;
					if (is_null($this->_tableData)) {
						$i=0;
						foreach ($riga as $key => $value) {
						   $this->_tableData[$key] = new HtmlECol($key);
						   $this->_tableData[$key]->SetValue($value);
						   if($key == 'id'){
							   $this->_tableData['attach'] = new HtmlECol($key);
							   $this->_tableData['attach']->SetValue($value);
						   }
						   $i++;
						}
					} elseif (is_array($riga)){
						foreach ($riga as $key => $value) {
						   $this->_tableData[$key]->SetValue($value);
						}
					}
				} // End foreach
				if ($this->_TableRows==0) {
					return FALSE;
				}
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
			$row='<TR class="'.$this->GetRowClass().'" ondblclick="dojo.style(dojo.byId(\'hidden_'.$this->GetColValue('mid',$i).'\'),\'display\',\'table-row\')" >'."\n".$row;
			$row='<TR class="'.$this->GetRowClass().' >'."\n".$row;
			$row.='</TR>'."\n";
			print($row);
						// print('<tr  style="background-color: silver; display: none;"
									// id="hidden_' . $this->GetColValue('mid',$i) . '"
									// ondblclick="dojo.style(dojo.byId(\'hidden_'.$this->GetColValue('mid',$i).'\'),\'display\',\'none\')"
								// >
								// <td colspan=2" >' .$this->_tableData['Oggetto']->GetValue($i). '</td>
								// <td colspan=3" >'.$this->_tableData['Messaggio']->GetValue($i).'</td></tr>');
//			print('<tr style="display: block; background-color: silver;" id="hidden_' . $this->GetColValue('mid',$i) . '" ondblclick="dojo.style(dojo.byId(\'hidden_'.$this->GetColValue('mid',$i).'\'),\'display\',\'none\')" >' .
//						'<td colspan="2" >' .  $this->_tableData['Oggetto']->GetValue($i)  . '</td>' .
//						'<td colspan="3" >' . $this->_tableData['Messaggio']->GetValue($i) . '</td>
//						</tr>'."\n");



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

$pecMessages = pecList::getInstance();
$listaMail = $pecMessages->getMail();

foreach ($listaMail as $key => $value) {
	$listaMail[$key]['attach'] = $listaMail[$key]['id'];
//	$elements = imap_mime_header_decode($listaMail[$key]['Oggetto']);
//	$subj = '';
//	for ($i=0; $i<count($elements); $i++) {
//		$subj .= $elements[$i]->text;
//	}
//	$listaMail[$key]['Oggetto'] = $subj;
}


$pecWorkspace = new myHtmlETable($listaMail,FALSE);

// $pecWorkspace->setColSubstring('Mittente', 30);
// $pecWorkspace->setColSubstring('Oggetto', 30);
// $pecWorkspace->setColSubstring('Messaggio', 50);

$pecWorkspace->HideCol('mid');

	$pecWorkspace->SetColumnHeader('id','<img src="graphics/page_edit.png" >');
	$pecWorkspace->SetColumnHeader('attach','<img src="graphics/page_link.png" >');

	print('<div id="dlgProtocollaMail" dojoType="dijit.Dialog" title="Protocolla mail PEC" ' .
			'href="getDialog.php?dialog=protocollaMail" ' .
			'>');
	print('</div>');
	print('<div id="dlgAssegnaMail" dojoType="dijit.Dialog" title="Protocolla mail PEC" ' .
			'href="getDialog.php?dialog=assegnaMail" ' .
			'>');
	print('</div>');
	print('<div id="dlgAllegaMail" dojoType="dijit.Dialog" title="Allega la PEC ad una pratica" ' .
			'href="getDialog.php?dialog=allegaMail" ' .
			'>');
	print('</div>');


print('<div id="dlgLoadingMail" dojoType="dijit.Dialog" title="Caricamento mail" >');

	print('<div style="margin: 20px;"><img src="graphics/ajax-loader.gif" style="margin-right: 10px;" />  Caricamento mail in corso!</div>');

print('</div>');


if($pecWorkspace->getTableRows()>0) {
	$pecWorkspace->show();
} else {
	print('<h3>Non ci sono mail</h3>');
}
