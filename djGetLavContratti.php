<?php
/*
 * Created on 04/nov/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");

if($_GET['CONTRATTO_ID']>''){
$coQuery='SELECT ' .
				'lc.oggetto as Oggetto, ' .
				'li.DESCRIPTION as "Impresa", ' .
				'tp.value as "Tipologia", ' .
				'date_format(lc.incarico_del,\'%d-%m-%Y\') as "Del",' .
				'lc.nr_incarico as "Nr.Incarico", ' .
				'date_format(lc.data_ultimazione_lavori,\'%d-%m-%Y\') as "Fine Lavori", ' .
				'((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lq.iva/100)) "Importo lordo" ' .

//				'lq.iva as IVA, ' .
//				'lc.oneri_sicurezza "Oneri sic.", ' .
//				'lc.importo_netto "Importo comp. netto", ' .
//				'lc.SCONTO as "Sconto", ' .
//				'(((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100))) "Importo ribassato", ' .
//				'((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza) "Importo netto", ' .
//				'((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lq.iva/100)) "Importo lordo", ' .
//				'date_format(lc.data_liquidazione,\'%d-%m-%Y\') as "Data liquidazione",' .
//				'lc.IMPORTO_LIQUIDATO as "Importo liquidato" ' .
			'from lav_contratti lc ' .
			'left join lav_quadro_economico lq on (lq.QECONOMICO_ID = lc.QECONOMICO_ID) ' .
			'left join lav_imprese as li on (li.impresa_id = lc.impresa_id) ' .
			'left join sys_fields_validations as tc on ((tc.field_name=\'radioTipoContratti\') and (tc.code=lc.tipo_lavori) and (tc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as uc on ((uc.field_name=\'radioUrgContratti\') and (uc.code=lc.urgenza) and (uc.language_id='.$_SESSION['sess_lang'].')) ' .
			'left join sys_fields_validations as tp on ((tp.field_name=\'tipoPerizia\') and (tp.code=lc.tipologia) and (tp.language_id='.$_SESSION['sess_lang'].')) ' .
				'where lc.contratto_id = '.$_GET['CONTRATTO_ID'];

			$coResult=dbselect($coQuery);



				print('' .
				'<div class="djToolTipContainer" >' .
				'<fieldset ><legend style="border: none; background-color: white; ">'.$label.'</legend>' .
				'');

				foreach ($coResult['ROWS'][0] as $key => $value) {
					if($key=='Importo lordo'){
						print('<LABEL>'.$key.'</LABEL>'.'<span>'.number_format($value,2,',','.').'</span><br />');
					} else {
						print('<LABEL>'.$key.'</LABEL>'.'<span>'.$value.'</span><br />');
					}

				}
				print('<br /><hr />');

				print('' .
				'</fieldset>' .
				'</div>' .
				'');

} else {
	print('<h2>Attenzione contrato non esistente!</h2>');
}

?>
