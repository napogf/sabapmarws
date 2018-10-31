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
		// Format for the heading
		$formatot =& $workbook->addFormat();
		$formatot->setSize(10);
		$formatot->setAlign('center');
		$formatot->setColor('white');
		$formatot->setPattern();
		$formatot->setFgColor('black');
		$col=0;
		foreach ($this->_tableData as $key=>$value){
			if ($value->IsShowed() and $key <> 'PERIZIA_ID' and $key <> 'QECONOMICO_ID' and $key <> 'CONTRATTO_ID') {
				$worksheet1->writeString(0,$col,$key,$formatot);
				$col++;
			}
		}
		$dateFormat=& $workbook->addFormat();
		$numFormat=& $workbook->addFormat();
		$numFormat->SetNumFormat('#,##0.00');
		$dateFormat=& $workbook->addFormat();
		$dateFormat->SetNumFormat('DD-MM-YYYY');
		$curFormat=& $workbook->addFormat();
		$curFormat->SetNumFormat('#,##0.00');
		// number of seconds in a day
		$seconds_in_a_day = 86400;
		// Unix timestamp to Excel date difference in seconds
		$ut_to_ed_diff = $seconds_in_a_day * 25569;
		$flagPerizia=0;
		$flagQeconomico=0;
		$flagContratto=0;

		for($i = 0; $i < $this->_TableRows; $i++){
			$col=0;
			if($flagPerizia!=$this->_tableData['PERIZIA_ID']->_Value[$i]){
				$flagPerizia=$this->_tableData['PERIZIA_ID']->_Value[$i];
				$worksheet1->writeString($i+1,0,$this->_tableData['Nr.Perizia']->GetValue($i));
				$dateToShow=((strtotime($this->_tableData['Data Perizia']->GetValue($i))+$ut_to_ed_diff)/$seconds_in_a_day)+1;
				$worksheet1->writeNumber($i+1,1,$dateToShow,$dateFormat);
				$worksheet1->writeNumber($i+1,2,$this->_tableData['Importo Perizia']->_Value[$i],$numFormat);
			}
			if($flagQeconomico<>$this->_tableData['QECONOMICO_ID']->_Value[$i]){
				$flagQeconomico=$this->_tableData['QECONOMICO_ID']->_Value[$i];
				$worksheet1->writeString($i+1,3,$this->_tableData['Desc.Lavori']->GetValue($i));
				$worksheet1->writeNumber($i+1,4,$this->_tableData['Importo netto Q.E.']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,5,$this->_tableData['Importo lordo Q.E.']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,6,$this->_tableData['IVA']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,7,$this->_tableData['Incentivo']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,8,$this->_tableData['Val.Incentivo']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,9,$this->_tableData['Oneri sicurezza']->_Value[$i],$numFormat);
			}
			if($flagContratto<>$this->_tableData['CONTRATTO_ID']->_Value[$i]){
				$flagContratto=$this->_tableData['CONTRATTO_ID']->_Value[$i];
				$worksheet1->writeString($i+1,10,$this->_tableData['Tipologia Intervento']->GetValue($i));
				$worksheet1->writeString($i+1,11,$this->_tableData['Dettaglio Intervento']->GetValue($i));
				$worksheet1->writeString($i+1,12,$this->_tableData['Cod.CIG']->GetValue($i));
				$worksheet1->writeNumber($i+1,13,$this->_tableData['Imp.complessivo']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,14,$this->_tableData['Oneri sic.']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,15,$this->_tableData['Sconto']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,16,$this->_tableData['Importo ribassato']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,17,$this->_tableData['Importo netto']->_Value[$i],$numFormat);
				$worksheet1->writeNumber($i+1,18,$this->_tableData['Importo lordo']->_Value[$i],$numFormat);

			}
			$dateToShow=((strtotime($this->_tableData['Data liquidazione']->GetValue($i))+$ut_to_ed_diff)/$seconds_in_a_day)+1;
			$worksheet1->writeNumber($i+1,19,$dateToShow,$dateFormat);
			$worksheet1->writeNumber($i+1,20,$this->_tableData['Importo liquidato']->_Value[$i],$numFormat);

		} // for
		$workbook->close();
	}





}


$coQuery='SELECT distinct concat(\'<input type="checkbox" value="\',lp.perizia_id,\'" name="EXP_PERIZIE[]">\') as "#", ' .
				'lp.nr_perizia as "Nr.Perizia", ' .
				'date_format(lp.data_perizia,\'%d-%m-%Y\') as "Data Perizia", ' .
				'lp.denominazione_istituto as Istituto, ' .
				'lp.oggetto as Oggetto ' .
			'from lav_perizie lp ' .
			'left join lav_quadro_economico lq on (lq.PERIZIA_ID = lp.PERIZIA_ID) ' .
			'left join lav_contratti lc on (lc.QECONOMICO_ID = lq.QECONOMICO_ID) ' .
			'left join lav_liquidazioni ll on (ll.CONTRATTO_ID = lc.CONTRATTO_ID) ' .
			'left join lav_imprese as li on (li.impresa_id = lc.impresa_id) ' .
			'left join sys_fields_validations as tc on ((tc.field_name=\'radioTipoContratti\') and (tc.code=lc.tipo_lavori) and (tc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as uc on ((uc.field_name=\'radioUrgContratti\') and (uc.code=lc.urgenza) and (uc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as tu on ((tu.field_name=\'radioUtilPerizie\') and (tu.code=lp.tipo_utilizzo) and (tu.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as pr on ((pr.field_name=\'radioPropPerizie\') and (pr.code=lp.proprieta) and (pr.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as tp on ((tp.field_name=\'tipoPerizia\') and (tp.code=lc.tipologia) and (tp.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join arc_comuni as ac on (ac.id = lp.comune) ' .
			'left join arc_province as ap on (ap.sigla = ac.provincia) ';

if (!is_array($_POST['EXP_PERIZIE']) or ((!isSet($_POST['xlsSave']) or ($_POST['xlsSave'] <> 'Y') or !($_POST['daData']>'' and $_POST['aData']>'')))) {
	include('pageheader.inc');
	print('<div style="margin-left:20px;" >' ."\n".
				'<form name=searchForm ' .
//						'onSubmit="javascript: return isNotNull(this.keyword.value)" ' .
						'action='.$PHP_SELF.' method=post style="margin-bottom: 5px">'."\n".
				'<input type="hidden" name="xlsSave" value="N" id="xlsSave" >' .
				'<div style="float:left;" >' .
				'<table>' .
				'<tr>' .
					'<td>' .
					'Da data perizia<input dojoType="dijit.form.DateTextBox" type="text" name="daData"  value="'.$_POST['daData'].'" style="margin: 5px; width: 10em;" >' .
					'</td>' .
					'<td>' .
					'A data perizia<input dojoType="dijit.form.DateTextBox" type="text" name="aData"  value="'.$_POST['aData'].'" style="margin: 5px; width: 10em;"><br/>' .
				'</td>' .
				'<td>Oggetto <input type="text" length="20" name="oggetto" value="'.$_POST['oggetto'].'"></td> ' .
				'</tr>' .
				'</table>' .
				'</div><div style="padding-top:5px;">' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'N\';document.searchForm.submit()">'."\n".
				'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'Y\';document.searchForm.submit()">'."\n".
				'<img src="graphics/mime/msexcel.gif" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
				'</div>' .
		'</div>'."\n");
	print('<div style=" clear: both;" ></div>');
	$wherClause = 'where 1 ';
	if(($_POST['daData']>'' and $_POST['aData']>'') or ($_POST['oggetto']>'') ){
		if ($_POST['daData']>'' and $_POST['aData']>'') $wherClause .= ' and (lp.data_perizia between ' .
			' str_to_date(\''.$daData.'\',\'%Y-%m-%d\') and str_to_date(\''.$aData.'\',\'%Y-%m-%d\') ) ';
		if ($_POST['oggetto']) $wherClause .= ' and lp.oggetto like \'%'.$_POST['oggetto'].'%\' ';

		// var_dump($coQuery.$wherClause);

		$perizieTable=new myHtmlETable($coQuery.$wherClause);
		$perizieTable->show();
	}

	print('</form>');
	include('pagefooter.inc');
} else {
	if($_POST['EXP_PERIZIE']>''){
		$coQuery='SELECT ' .
				'lp.PERIZIA_ID,' .
				'lq.QECONOMICO_ID, ' .
				'lc.CONTRATTO_ID,' .
				'lp.nr_perizia as "Nr.Perizia", ' .
				'date_format(lp.data_perizia,\'%d-%m-%Y\') as "Data Perizia", ' .
				'lp.importo_lordo as "Importo Perizia", ' .
				'lq.DESCRIZIONE_LAVORI "Desc.Lavori", ' .
				'lq.IMPORTO_NETTO as "Importo netto Q.E.", ' .
				'round((lq.importo_netto*(1+(lq.iva/100))),2) as "Importo lordo Q.E.", ' .
				'lq.IVA, ' .
				'lq.INCENTIVO as "Incentivo", ' .
				'round((lq.importo_netto*lq.INCENTIVO/100),2) as "Val.Incentivo", ' .
				'lq.ONERI_SICUREZZA as "Oneri sicurezza",' .
				'tp.value as "Tipologia Intervento", ' .
				'lc.oggetto as "Dettaglio Intervento", ' .
				'lc.codice_cig as "Cod.CIG", ' .
				'lc.importo_netto "Imp.complessivo", ' .
				'lc.oneri_sicurezza "Oneri sic.", ' .
				'lc.SCONTO as "Sconto", ' .
				'round((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100))),2) "Importo ribassato", ' .
				'round(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza),2) "Importo netto", ' .
				'lq.iva as IVA, ' .
				'round(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lq.iva/100)),2) "Importo lordo", ' .
				'date_format(ll.data_liquidazione,\'%d-%m-%Y\') as "Data liquidazione",' .
				'll.IMPORTO_LIQUIDATO as "Importo liquidato" ' .
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
			'where lp.perizia_id in ('.implode(',',$_POST['EXP_PERIZIE']).') ' .
			'order by lp.nr_perizia, lq.descrizione_lavori ';
		$coTable=new myHtmlETable($coQuery);
			$coTable->getColumn("Data Perizia")->SetColumnType('date');
			$coTable->getColumn("Data liquidazione")->SetColumnType('date');
			$coTable->getColumn("Importo netto Q.E.")->SetColumnType('number');
			$coTable->getColumn("Importo lordo Q.E.")->SetColumnType('number');
			$coTable->getColumn("IVA")->SetColumnType('number');
			$coTable->getColumn("Incentivo")->SetColumnType('number');
			$coTable->getColumn("Val.Incentivo")->SetColumnType('number');
			$coTable->getColumn("Oneri sicurezza")->SetColumnType('number');
			$coTable->getColumn("Oneri sic.")->SetColumnType('number');
			$coTable->getColumn("Sconto")->SetColumnType('number');
			$coTable->getColumn("Imp.complessivo")->SetColumnType('number');
			$coTable->getColumn("Importo ribassato")->SetColumnType('number');
			$coTable->getColumn("Importo netto")->SetColumnType('number');
			$coTable->getColumn("Importo lordo")->SetColumnType('number');
			$coTable->getColumn("Importo liquidato")->SetColumnType('number');

		$coTable->saveAsXls('listaPerizie.xls');
	}
}
?>