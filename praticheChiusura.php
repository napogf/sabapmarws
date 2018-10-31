<?php
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("Etable_c.inc");


class myHtmlETable extends htmlETable {
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
				if ($key == 'Avanzamento'){
					if ($this->GetColValue($key,$i)> '' ) {
						switch ($this->GetColValue('rowclass',$i)) {
							case 'praAlert':
								$bgColor='yellow';
								$maxVal = $this->GetColValue($key,$i);
								$advVal = $this->GetColValue('ggadv',$i);

								break;
							case 'praAllarm':
								$bgColor='red';
								$advVal = $this->GetColValue($key,$i);
								$maxVal = $this->GetColValue('ggadv',$i);
								break;
							case 'praClosed':
								$maxVal = $this->GetColValue($key,$i);
								$advVal = $this->GetColValue('ggadv',$i);
								$bgColor='white';
								break;
							default:
								$bgColor='lime';
								$maxVal = $this->GetColValue($key,$i);
								$advVal = $this->GetColValue('ggadv',$i);
								break;
						}
						$row .= "\t".'<TD '.$value->GetColAlign().$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >'.
							'<div style="width:100px; background: '.$bgColor.' none repeat;" annotate="true"
					  		maximum="'.$maxVal.'" id="Bar'.$this->GetColValue('PRATICA_ID',$i).'" ' .
					  		'progress="'.$advVal.'" ' .
					  		'label="Prova giorni" ' .
					  		'dojoType="dijit.ProgressBar">' .
					  		'<script type="dojo/method" event="report">
							    return dojo.string.substitute("gg '.$this->GetColValue('ggadv',$i).' di '.$this->GetColValue($key,$i).'", [this.progress, this.maximum]);
							  </script>' .
					  		'</div>'.
						'</TD>'."\n";
					} else {
						$row .= "\t".'<TD '.$value->GetColAlign().$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >'.
						'</TD>'."\n";
					}
				} else {
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
			}
			$row='<TR class="'.$this->GetRowClass().'" id="'.$this->GetColValue('PRATICA_ID',$i).'" >'."\n".$row;
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
include('pageheader.inc');

// Dialog Box per chiudere la pratica

print('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_esiti" ' .
		'jsId="getEsiti" ' .
						'/>');
print('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_modelli order by description" ' .
		'jsId="getModelli" ' .
						'/>');




print('<div id="dialogOne" dojoType="dijit.Dialog" title="Chiudi Pratica" ' .
		'href="getDialog.php" ' .
		'>');
print('</div>');




//switch ($_GET['filter']) {
//	case 'open':
//		$whereClause=' and (pr.modello is null or dataarrivo is null) ' .
//					 ' and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ' ;
//		break;
//	case 'suspended':
//		$whereClause=' and ((pr.scadenza = \'00-00-0000\' or pr.scadenza is null) and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) and pr.modello is not null) ' ;
//		break;
//	case 'allarm':
//		$whereClause=' and  date_add(pr.dataarrivo, INTERVAL (am.scadenza) DAY) <= now() ' .
//					 ' and (pr.scadenza > \'00-00-0000\' ) ' .
//					 ' and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ' ;
//		break;
//	case 'alert':
//		$whereClause=' and  date_add(pr.dataarrivo, INTERVAL (am.scadenza-am.allarme) DAY) <= now() ' .
//					 ' and (pr.scadenza > \'00-00-0000\' ) ' .
//					 'and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ' ;
//		break;
//	case 'closed':
//		$whereClause=' and (pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ';
//		break;
//	default:
//		$whereClause= ((isSet($_GET['keyword']) and ($_GET['keyword'] > '')) or (isSet($_GET['nregFilter']) and ($_GET['nregFilter'] > '')) or (isSet($_GET['anagFilter']) and ($_GET['anagFilter'] > '')))?'':' and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ';
//		break;
//}

if ($_POS['filter']=='closed') {
	$tableFields=		'	concat(\'<span onclick="riapriPratica(\',pr.PRATICA_ID,\')" id="dlg\',pr.PRATICA_ID,\'" ><img src="graphics/webapp/save.gif" style="cursor: pointer" ></span>\') as Attiva, ' .
						'(case ' .
						'	when (pr.modello is null) then \'praOpen\' ' .
						'	when pr.dataarrivo is null then \'praOpen\' ' .
//						'	when (vs.sospesa = \'00-00-0000\')  then \'praSuspended\' ' .
						'	when (pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\')) ) then \'praClosed\' ' .
						'	when ( (pr.dataarrivo is not null and pr.dataarrivo > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\') and pr.scadenza = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\')) ) then \'praSuspended\' ' .
						'	when (date_add(now(), INTERVAL am.allarme DAY) >= pr.scadenza and now() < pr.scadenza) then \'praAlert\' ' .
						'	when (now() > pr.scadenza) then \'praAllarm\' ' .
						'else \'praActive\' ' .
						'end) as rowclass , ' .
						'datediff(pr.uscita , pr.dataarrivo) as Durata,' .
						'if((pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))),' .
						'									datediff(now() , pr.uscita), ' .
						'									datediff(now() , pr.dataarrivo)) as ggadv , ' .
						'pr.numeroregistrazione, ' .
						'date_format(pr.dataregistrazione,\'%d-%m-%Y\') as "Data Reg.", ' .
						'pr.condizione as Parere, ' .
						'am.description as \'Tipo Pratica\', ' .
						'au.description as "Ufficio", ' .
						'pr.comuneogg as "Oggetto", ' .
						'pr.pnome as "Proprietario", ' .
						'pr.cognome cognome, ' .
						'date_format(pr.dataarrivo,\'%d-%m-%Y\') as "Arrivo", ' .
//						'date_format(pr.funzionario,\'%d-%m-%Y\') as "Al funz.", ' .
//						'date_format(pr.firma,\'%d-%m-%Y\') as "Alla firma", ' .
						'date_format(pr.uscita,\'%d-%m-%Y\') as "Uscita", ' .
//						' date_format(pr.scadenza,\'%d-%m-%Y\') as Scadenza, ' .
//						'(case ' .
//						'	when (vs.ggsospensione is null) then date_format(pr.scadenza,\'%d-%m-%Y\') ' .
//						'else date_format(date_add(pr.scadenza, INTERVAL vs.ggsospensione DAY ),\'%d-%m-%Y\')' .
//						'end)  as "Scadenza", ' .
						'am.allarme, ' .
						'az.code as zonaCod, ' .
//						'pr.oggetto, ' .
						'substring(az.description,1,20) zonaDes ' ;
} else {
	$tableFields=
						'concat(\'<span ><img src="graphics/close.gif" style="cursor: pointer" title="Chiudi Pratica" onclick="openDialog(\',pr.PRATICA_ID,\')" ></span>\') Chiudi,' .
						'(case ' .
						'	when (pr.modello is null) then \'praOpen\' ' .
						'	when pr.dataarrivo is null then \'praOpen\' ' .
//						'	when (vs.sospesa = \'00-00-0000\')  then \'praSuspended\' ' .
						'	when (pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\')) ) then \'praClosed\' ' .
						'	when ( (pr.dataarrivo is not null and pr.dataarrivo > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\') and pr.scadenza = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\')) ) then \'praSuspended\' ' .
						'	when (date_add(now(), INTERVAL am.allarme DAY) >= pr.scadenza and now() < pr.scadenza) then \'praAlert\' ' .
						'	when (now() > pr.scadenza) then \'praAllarm\' ' .
						'else \'praActive\' ' .
						'end) as rowclass , ' .
						'datediff(pr.scadenza , pr.dataarrivo) as Avanzamento,' .
						'if((pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))),' .
						'									datediff(now() , pr.uscita), ' .
						'									datediff(now() , pr.dataarrivo)) as ggadv , ' .
						'pr.numeroregistrazione, ' .
						'date_format(pr.dataregistrazione,\'%d-%m-%Y\') as "Data Reg.", ' .
//						'am.description as \'Modello\', ' .
						'au.description as "Ufficio", ' .
						'pr.oggetto as "Oggetto", ' .
						'pr.pnome as "Proprietario", ' .
						'pr.cognome cognome, ' .
						'date_format(pr.dataarrivo,\'%d-%m-%Y\') as "Arrivo", ' .
//						'date_format(pr.funzionario,\'%d-%m-%Y\') as "Al funz.", ' .
//						'date_format(pr.firma,\'%d-%m-%Y\') as "Alla firma", ' .
//						'date_format(pr.uscita,\'%d-%m-%Y\') as "Uscita", ' .
						' date_format(pr.scadenza,\'%d-%m-%Y\') as Scadenza, ' .
//						'(case ' .
//						'	when (vs.ggsospensione is null) then date_format(pr.scadenza,\'%d-%m-%Y\') ' .
//						'else date_format(date_add(pr.scadenza, INTERVAL vs.ggsospensione DAY ),\'%d-%m-%Y\')' .
//						'end)  as "Scadenza", ' .
						'am.allarme, ' .
						'az.code as zonaCod, ' .
//						'pr.oggetto, ' .
						'substring(az.description,1,20) zonaDes ' ;
}

include('barraRicerca.inc');
$serviceQuery='select distinct pr.PRATICA_ID, ' .
						'(case ' .
							'when (pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) then ' .
										'concat(\'<img src="graphics/scheda.gif" style="cursor: pointer" onclick="editPratica(\',pr.PRATICA_ID,\')" title="Visualizza pratica" >\') ' .
							'when pr.modello is null then ' .
										'concat(\'<img src="graphics/CreateDoc.gif" style="cursor: pointer" onclick="editPratica(\',pr.PRATICA_ID,\')" title="Imposta tipo pratica" >\') ' .
							'when pr.dataarrivo is null then ' .
										'concat(\'<img src="graphics/CreateDoc.gif" style="cursor: pointer" onclick="editPratica(\',pr.PRATICA_ID,\')" title="Imposta data di arrivo" >\') ' .
							'when pr.funzionario is null then ' .
										'concat(\'<img src="graphics/CreateDoc.gif" style="cursor: pointer" onclick="editPratica(\',pr.PRATICA_ID,\')" title="Imposta data al funzionario" >\') ' .
							'when pr.firma is null then ' .
										'concat(\'<img src="graphics/CreateDoc.gif" style="cursor: pointer" onclick="editPratica(\',pr.PRATICA_ID,\')" title="Imposta data alla firma" >\') ' .
							'when (pr.uscita is null or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) then ' .
										'concat(\'<img src="graphics/CreateDoc.gif" style="cursor: pointer" onclick="editPratica(\',pr.PRATICA_ID,\')" title="Imposta data alla firma" >\') ' .
						'end) as "#", ' .
						$tableFields.
				'from pratiche pr ' .
				'left join arc_zone az on (az.zona = pr.zona) ' .
				'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
				'left join arc_modelli am on (am.modello = pr.modello) ' .
//				'left join arc_vincoli_pratiche avp on (avp.pratica_id = pr.pratica_id)' .
				'left join arc_vincoli av on (av.vincolo_id = pr.vincolo_id) ' .
//				'left join v_sospensioni vs on (vs.pratica_id = pr.pratica_id) ' .
				'where 1 ' .
				$whereClause.
				' order by pr.dataregistrazione desc, pr.numeroregistrazione ';


//var_dump($serviceQuery);

$serviceTable=new myHtmlETable($serviceQuery);
if ($serviceTable->getTableRows()>0) {

	$serviceTable->HideCol('PRATICA_ID');
	$serviceTable->HideCol('ggadv');
	$serviceTable->HideCol('rowclass');
	$serviceTable->HideCol('zonaCod');
	$serviceTable->HideCol('zonaDes');
	$serviceTable->HideCol('Ufficio');
	$serviceTable->HideCol('allarme');
//	$serviceTable->HideCol('Alla firma');
//	$serviceTable->HideCol('Al funz.');

//	$serviceTable->HideCol('Modello');
	$serviceTable->SetPageDivision(TRUE);
	$serviceTable->SetColumnHeader('numeroregistrazione','Prot.Nr.');
	$serviceTable->SetColumnHeader('dataReg','Registrata');
	$serviceTable->SetColumnHeader('dataArr','Arrivata');
	$serviceTable->SetColumnHeader('dataSca','Scadenza');
	$serviceTable->SetColumnHeader('cognome','Mittente');
	$serviceTable->SetColumnAttribute('numeroregistrazione',' style="text-align: center;" ');

//	$serviceTable->SetColumnAttribute('Vincoli',' style="text-align: center;" ');

	$serviceTable->setColSubstring('Modello',15);
	$serviceTable->setColSubstring('Oggetto',20);
	$serviceTable->setColSubstring('Proprietario',15);
	$serviceTable->setColSubstring('cognome',15);

	//$serviceTable->SetColumnHref('SER_CODE','<a href="serviceManageRequest.php?STATUS=50&SER_ID=#SER_ID#" title="Attiva Intervento">');
	$serviceTable->show($_GET['wk_page']);
} else {
	print('<h3>Non ci sono Pratiche</h3>');
}
	print('<div class="praLegend" style=" padding-right:20px; padding:5px; margin-right:20px;">' .
		  	'Legenda: ' .
		  		'<span class="praOpen" style="padding-left:5px; padding-right:5px;" >Da classificare</span>' .
		  		'<span class="praAlert" style="background-color: yellow; padding-left:5px; padding-right:5px;" >In scadenza</span>' .
		  		'<span class="praAllarm" style="background-color: red; padding-left:5px; padding-right:5px;" >Scadute</span>' .
		  		'<span class="praSuspended" style="padding-left:5px; padding-right:5px;" >Sospese</span>' .
		  		'<span class="praClosed" style="padding-left:5px; padding-right:5px;" >Chiuse</span>' .
		  '</div>');



include('pagefooter.inc');
?>