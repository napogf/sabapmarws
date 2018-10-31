<?php
/*
 * Created on 23/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
switch ($_GET['dlg']) {
	case 'dlgApriPratica':
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_organizzazione where TIPO = \'Z\' " ' .
		'jsId="getZone" ' .
		'/>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_organizzazione where TIPO = \'U\'" ' .
		'jsId="getUffici" ' .
		'/>');

		print('<div class="djFormContainer" style="width: 450px; display: block;">');
			print('<fieldset style="border:none">'."\n");
				print('<label for="DATAREGISTRAZIONE" >Data Registrazione</label>' .
						'<input dojoType="dijit.form.DateTextBox" type="text" displayFormat="dd-MM-yyyy" name="DATAREGISTRAZIONE" id="DATAREGISTRAZIONE" value="'.date('Y-m-d').'" >' .
					  '<br/>');

				print('<label for="SEL_MODELLO">Tipo Pratica</label>');
				print('<input dojoType="dijit.form.FilteringSelect" ID="DLG_MODELLO"
							store="getModelli"
							labelAttr="DESCRIPTION"
							searchAttr="DESCRIPTION"
							name="DLG_MODELLO" ' .
							'>'.
					  '<br/>');
				print('<label for="SEL_ZONA">Zona</label>');
				print('<input dojoType="dijit.form.FilteringSelect" ID="DLG_ZONA"
							store="getZone"
							required="false"
							labelAttr="DESCRIPTION"
							searchAttr="DESCRIPTION"
							name="DLG_ZONA" ' .
							'>'.
					  '<br/>');
				print('<label for="SEL_ZONA">Ufficio</label>');
				print('<input dojoType="dijit.form.FilteringSelect" ID="DLG_UFFICIO"
							store="getUffici"
							required="false"
							labelAttr="DESCRIPTION"
							searchAttr="DESCRIPTION"
							name="DLG_UFFICIO" ' .
							'>'.
					  '<br/>');

			print('</fieldset>'."\n");
		//	print(' <button dojoType="dijit.form.Button" ' .
		//					'onClick="return dijit.byId(\'dialogOne\').isValid();">Chiudi Pratica</button>');
			print(' <button dojoType="dijit.form.Button" ' .
							'onClick="apriPratica(\''.$_GET['dlg'].'\')">Genera Procedimento</button>');
		print('</div>');

		break;

	default:

		break;
}
?>
