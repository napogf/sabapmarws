<?php
/*
 * Created on 02/nov/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");

class myHtmlETable extends htmlETable {

	function saveAsXls($fileName=null){
		global $PHP_SELF;
		$fileName=is_null($fileName)?basename($PHP_SELF,'.php').'.xls':$fileName;
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(getcwd());

		// sending HTTP headers
		$workbook->send($fileName);

		$worksheet1 =& $workbook->addWorksheet();
		// Formato per il titolo
		$formatot =& $workbook->addFormat();
		for($index=0; $index<=4; $index++){
			$col=0;
			foreach ($this->_tableData as $key=>$value){
				if ($value->IsShowed()) {
					$worksheet1->writeString($index,$col,'',$formatot);
					$col++;
				}
			}
		}



		// Format for the heading
		$formatot =& $workbook->addFormat();
		$formatot->setSize(10);
		$formatot->setAlign('center');
		$formatot->setColor('white');
		$formatot->setPattern();
		$formatot->setFgColor('black');

		$col=0;
		foreach ($this->_tableData as $key=>$value){
			if ($value->IsShowed()) {
				$worksheet1->writeString(5,$col,$key,$formatot);
				$col++;
			}
		}
		$dateFormat=& $workbook->addFormat();
		$numFormat=& $workbook->addFormat();
		$numFormat->SetNumFormat('_(#,##0.00_)');
		$dateFormat=& $workbook->addFormat();
		$dateFormat->SetNumFormat('DD-MM-YYYY');
		$curFormat=& $workbook->addFormat();
		$curFormat->SetNumFormat('#,##0.00');
		// number of seconds in a day
		$seconds_in_a_day = 86400;
		// Unix timestamp to Excel date difference in seconds
		$ut_to_ed_diff = $seconds_in_a_day * 25569;
		for($riga = 0; $riga < $this->_TableRows; $riga++){
			$col=0;
			$i=$riga+5;
			foreach ($this->_tableData as $key=>$value){
				if ($value->IsShowed()) {
					switch ($value->GetColumnType()) {
						case 'number':
							$worksheet1->writeNumber($i+1,$col,$value->_Value[$riga],$numFormat);
							break;
						case 'date':
							if($value->GetValue($riga)>''){
								$dateToShow=((strtotime($value->GetValue($riga))+$ut_to_ed_diff)/$seconds_in_a_day)+1;
								$worksheet1->writeNumber($i+1,$col,$dateToShow,$dateFormat);
							} else {
								$worksheet1->writeString($i+1,$col,$value->GetValue($riga));
							}
							break;
						case 'currency':
							$worksheet1->writeNumber($i+1,$col,$value->GetValue($riga),$curFormat);
							break;
						default:
							$worksheet1->writeString($i+1,$col,$value->GetValue($riga));
							break;
					}
					$col++;
				}
			}
		} // for
		$workbook->close();
	}

}

$coQuery='SELECT ' .
				'ap.regione as "Regione",' .
				'ap.provincia as "Provincia", ' .
				'ac.comune as "Comune", ' .
				'lp.indirizzo as "Indirizzo", ' .
				'pr.value as "Proprietà", ' .
				'tu.value as "Tipo Utilizzo", ' .
				'"MIBAC" as "Amministrazione Centrale", ' .
				'"SBAP-VR" as "Amministrazione Utilizzatrice", ' .
				'tp.value as "Tipologia Intervento", ' .
				'lc.oggetto as "Dettaglio Intervento", ' .
				'lc.codice_cig as "Cod. CIG", ' .
				'll.IMPORTO_LIQUIDATO as "Importo liquidato", ' .
				'concat(\'Rif. Perizia \',lp.nr_perizia) as "Note", ' .
				'"Pagato" as "#", '.
				'date_format(ll.data_liquidazione,\'%d-%m-%Y\') as "Data liquidazione" ' .
			'from lav_contratti lc ' .
			'left join lav_liquidazioni ll on (ll.CONTRATTO_ID = lc.CONTRATTO_ID) ' .
			'left join lav_quadro_economico lq on (lq.QECONOMICO_ID = lc.QECONOMICO_ID) ' .
			'left join lav_imprese as li on (li.impresa_id = lc.impresa_id) ' .
			'left join sys_fields_validations as tc on ((tc.field_name=\'radioTipoContratti\') and (tc.code=lc.tipo_lavori) and (tc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as uc on ((uc.field_name=\'radioUrgContratti\') and (uc.code=lc.urgenza) and (uc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join lav_perizie as lp on (lp.perizia_id = lq.perizia_id) ' .
			'left join sys_fields_validations as tu on ((tu.field_name=\'radioUtilPerizie\') and (tu.code=lp.tipo_utilizzo) and (tu.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as pr on ((pr.field_name=\'radioPropPerizie\') and (pr.code=lp.proprieta) and (pr.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as tp on ((tp.field_name=\'tipoPerizia\') and (tp.code=lc.tipologia) and (tp.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join arc_comuni as ac on (ac.id = lp.comune) ' .
			'left join arc_province as ap on (ap.sigla = ac.provincia) ' .
				'where '.
			'(ll.data_liquidazione between ' .
			' str_to_date(\''.$daData.'\',\'%Y-%m-%d\') and str_to_date(\''.$aData.'\',\'%Y-%m-%d\') ) ';

//var_dump($coQuery);

if (!isSet($_GET['xlsSave']) or ($_GET['xlsSave'] <> 'Y') or !($_GET['daData']>'' and $_GET['daData']>'')) {
	include('pageheader.inc');
	print('<div style="margin-left:20px;" >' ."\n".
				'<form name=searchForm ' .
//						'onSubmit="javascript: return isNotNull(this.keyword.value)" ' .
						'action='.$PHP_SELF.' method=get style="margin-bottom: 5px">'."\n".
				'<input type="hidden" name="xlsSave" value="N" id="xlsSave" >' .
				'<div style="float:left;" >' .
				'<table>' .
				'<tr>' .
					'<td>' .
					'Da data liquidazione<input dojoType="dijit.form.DateTextBox" type="text" name="daData"  value="'.$_GET['daData'].'" style="margin: 5px; width: 10em;" >' .
					'</td>' .
					'<td>' .
					'A data liquidazione<input dojoType="dijit.form.DateTextBox" type="text" name="aData"  value="'.$_GET['aData'].'" style="margin: 5px; width: 10em;"><br/>' .
				'</td>' .
				'</tr>' .
				'</table>' .
				'</div><div style="padding-top:5px;">' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'N\';document.searchForm.submit()">'."\n".
				'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'Y\';document.searchForm.submit()">'."\n".
				'<img src="graphics/mime/msexcel.gif" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
				'</div>' .
			'</form>'."\n".
		'</div>'."\n");
	print('<div style=" clear: both;" ></div>');
	if($_GET['daData']>'' and $_GET['daData']>''){
		$perizieTable=new myHtmlETable($coQuery);
		if ($perizieTable->getTableRows()>0) {
			$perizieTable->getColumn("Importo liquidato")->SetColumnType('number');
			$perizieTable->show();
		} else {
			print('<h2>Non ci sono perizie liquidate nell\'arco di tempo definito!</h2>');
		}

	}
	include('pagefooter.inc');
} else {
	if($_GET['daData']>'' and $_GET['daData']>''){
		$perizieTable=new myHtmlETable($coQuery);
		if ($perizieTable->getTableRows()>0) {
			$perizieTable->getColumn("Importo liquidato")->SetColumnType('number');
			$perizieTable->saveAsXls('semestralePerizie.xls');
		} else {
			print('<h2>Non ci sono perizie liquidate nell\'arco di tempo definito!</h2>');
		}
	}
}

?>
