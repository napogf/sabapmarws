<?php
/*
 * Created on 15/nov/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
//require_once("Etable_c.inc");




if($_GET['liquidaContratto']=='Y'){

	$updQuery='insert into lav_liquidazioni (contratto_id, ' .
							'data_liquidazione, ' .
							'importo_liquidato, ' .
							'descrizione) values (' .
							'"'.$_GET['CONTRATTO_ID'].'", ' .
							'str_to_date("'.$_GET['DATA_LIQUIDAZIONE'].'","%d/%m/%Y"),' .
							'"'.$_GET['IMPORTO_LIQUIDATO'].'", ' .
							'"'.$_GET['DESCRIZIONE'].'" ' .
							')';
	if(dbupdate($updQuery)){
		exit;
	} else {
		var_dump($updQuery);
		exit;
	}
} else {

		print('<div class="djFormContainer" style="width: 450px; display: block;">');
			print('<fieldset style="border:none">'."\n");
				print('<input dojoType="dijit.form.TextBox"  style="display:none" value="'.$_GET['CONTRATTO_ID'].'" name="CONTRATTO_ID" id="CONTRATTO_IF">');
				print('<label for="DATA_LIQUIDAZIONE" >Data liquidazione</label>' .
						'<input dojoType="dijit.form.DateTextBox" type="text" displayFormat="dd-MM-yyyy" name="DATA_LIQUIDAZIONE" id="DATA_LIQUIDAZIONE" value="'.date('Y-m-d').'" >' .
					  '<br/>');

				print('<label for="IMPORTO_LIQUIDATO">Importo liquidato</label>');
				print('<input dojoType="dijit.form.TextBox"   value="" name="IMPORTO_LIQUIDATO" id="IMPORTO_LIQUIDATO"><br/>');

				print('<label for="DESCRIZIONE">Descrizione operazione</label>');
				print('<input dojoType="dijit.form.TextBox"   value="" name="DESCRIZIONE" id="DESCRIZIONE"><br/>');


			print('</fieldset>'."\n");
		//	print(' <button dojoType="dijit.form.Button" ' .
		//					'onClick="return dijit.byId(\'dialogOne\').isValid();">Chiudi Pratica</button>');
			print(' <button dojoType="dijit.form.Button" ' .
							'onClick="addLiquidazione()">Liquida contratto</button>');
		print('</div>');

	$liqQuery='select ' .
					'descrizione as "Descrizione", ' .
					'importo_liquidato as "Importo liquidato", ' .
					'date_format(data_liquidazione,\'%d-%m-%Y\') as "Liquidato il", ' .
					'concat(\'<img src="graphics/application_delete.png" style="cursor:pointer" onclick="delLiquidazione(\',LIQUIDAZIONE_ID,\')">\') as "DEL_LIQUIDAZIONE" ' .
					'from lav_liquidazioni ' .
					'where contratto_id = '.$_GET['CONTRATTO_ID'].' order by liquidazione_id ';
	$liqTable=new htmlETable($liqQuery);
	if($liqTable->getTableRows()>0){
		$liqTable->SetColumnHeader('DEL_LIQUIDAZIONE','<img src="graphics/page_delete.png" >');
		$liqTable->getColumn('Importo liquidato')->SetColumnType('currency',2,true);
		//$liqTable->_decimalsTotal = 2;
		$liqTable->printTotal(true,2);
		$liqTable->show();
	}


}






?>
