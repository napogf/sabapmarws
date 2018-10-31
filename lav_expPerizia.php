<?php
/*
 * Created on 08/nov/10
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
		$numFormat->SetNumFormat('_(#,##0.00_)');
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


//			foreach ($this->_tableData as $key=>$value){
//				if ($value->IsShowed()) {
//					switch ($value->GetColumnType()) {
//						case 'number':
//							$worksheet1->writeNumber($i+1,$col,$value->_Value[$i],$numFormat);
//							break;
//						case 'date':
//							$dateToShow=((strtotime($value->GetValue($i))+$ut_to_ed_diff)/$seconds_in_a_day)+1;
//							$worksheet1->writeNumber($i+1,$col,$dateToShow,$dateFormat);
//							break;
//						case 'currency':
//							$worksheet1->writeNumber($i+1,$col,$value->GetValue($i),$curFormat);
//							break;
//						default:
//							$worksheet1->writeString($i+1,$col,$value->GetValue($i));
//							break;
//					}
//					$col++;
//				}
//			}
		} // for
		$workbook->close();
	}






}

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
			'left join arc_province as ap on (ap.sigla = ac.provincia) ' .
				'where lp.perizia_id='.$_GET['PERIZIA_ID'];
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
		$coTable->saveAsXls('dettaglioPerizia_'.$coTable->GetColValue('Nr.Perizia',0).'.xls');


?>
