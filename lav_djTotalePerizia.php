<?php
/*
 * Created on 29/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
if($_GET['PERIZIA_ID']>''){
	$sql='select pr.IMPORTO_LORDO as "Imp. lordo Perizia", ' .
				'sum(lc.IMPORTO_LIQUIDATO) as "Importo liquidato",' .
				'sum(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lq.iva/100))) "Importo lordo", ' .
				'pr.IMPORTO_LORDO - sum(((((lc.importo_netto-lc.oneri_sicurezza)-((lc.importo_netto-lc.oneri_sicurezza)*lc.sconto/100)))+lc.oneri_sicurezza)*(1+(lq.iva/100))) "Residuo Perizia" ' .
			'from lav_perizie pr ' .
			'left join lav_quadro_economico as lq on (lq.perizia_id = pr.perizia_id) ' .
			'left join lav_contratti lc on (lc.qeconomico_id = lq.qeconomico_id) ' .
			'where pr.perizia_id=' .$_GET['PERIZIA_ID'].' group by pr.perizia_id';
	$result=dbselect($sql);
	print('<div class="djToolTipContainer" >' .
			'<fieldset ><legend style="border: none; background-color: white; ">' .
			'Totale perizia' .
			'</legend>');
	foreach($result['ROWS'] as  $riga){
		foreach ($riga as $key => $value) {
			$value=is_numeric($value)?number_format($value,2,',','.'):$value;
			if($value>'' and $key <> 'va_id') print('<LABEL>'.$key.'</LABEL>'.'<span>'.$value.'</span><br />');
		}
		print('<br /><hr />');
	}
	print('' .
	'</fieldset>' .
	'</div>' .
	'');
} else {
	print('<h2>Seleziona una Perizia</h2>');
}
?>
