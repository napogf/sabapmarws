<?php
/*
 * Created on 31/ago/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");
require_once ("fdataentry.php");
require_once ("formExt.inc");
require_once("Etable_c.inc");
class MyDbForm extends formExtended {
	/* costruisce la form graficamente a partire dagli oggetti caricati */
	function ShowForm() {
			$this->editForm();
	}

	function showButtonBar() {

		print ('<div style="background-color: #FFFFCC; height:22px; padding: 2px 30px 2px 30px;">');
		print ('<input type="submit" value="Aggiorna" name="buttapp" class="buttons" style="float:left;" />');
		print ('<input type="reset" value="Annulla" name="buttdel" class="buttons" style="float:right;" />');
		print ('</div>');
	}

	function editForm() {
		/* Form container */

		print ('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");

		print ('<!-- Form open -->');
		print ("\n");
		print ($this->GetFormHeader());

		$this->formMessageShow();
		$this->formAttachmentsShow();
		$this->showButtonBar(FALSE);

		print ('<div style="background-color: azure; font-size: 1.5em; margin-bottom:10px;">' . $this->GetFormTitle() . '</div>' . "\n");
		// Pane Container
		print ('<div dojoType="dijit.layout.TabContainer"
							style="width:98%; height:450px; margin:0px;">');
			print ('<div dojoType="dijit.layout.ContentPane" title="Vincolo Ambientale" style=" margin:10px;">');

				$this->_FormFields['VA_ID']->showDivField();
				$this->_FormFields['CODICE']->showDivField();
				$this->_FormFields['PROGRESSIVO']->showDivField();
				$this->_FormFields['OGGETTO']->showDivField();
				// FilteringSelect per Provincia Comune
					print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
					'url="xml/jsonSql.php?sql=select ID, SIGLA, PROVINCIA from arc_province " ' .
					'jsId="jProvince" ' .
					'></div>');

					print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
					'url="xml/jsonSql.php?sql=select DISTINCT ID, COMUNE, PROVINCIA from arc_comuni" ' .
					'jsId="jComuni" ' .
					'></div>');

					print('<script language="JavaScript" type="text/javascript">
						dojo.addOnLoad(function(){
						    new dijit.form.FilteringSelect({
						                store: jComuni,
						                labelAttr: \'COMUNE\',
						                searchAttr: \'COMUNE\',
						                value: "'. $this->_FormFields['COMUNE']->GetValue() .'" ,
						                name: "COMUNE",
						                autoComplete: true,
						                style: "width: 250px;",
						                query : { PROVINCIA : "*"},
						                id: "COMUNE"
						            },
						            "COMUNE");
						    new dijit.form.FilteringSelect({
						                store: jProvince,
						                labelAttr: \'PROVINCIA\',
						                searchAttr: \'PROVINCIA\',
						                value: "'.$this->_FormFields['PROV']->GetValue().'"  ,
						                name: "PROV",
						                autoComplete: true,
						                style: "width: 250px;",
						                id: "PROV",
						                onChange: function(PROV) {
						                	dijit.byId(\'COMUNE\').query.PROVINCIA = dijit.byId(\'PROV\').item.SIGLA[0] ;
											return true;
						                }
						            },
						            "PROV");

						});
					</script>');

					print ('<label for="PROV">Provincia</label>');
					print ('<input id="PROV" >');
					print ('<br>');
					print ('<label for="COMUNE">Comune</label>');
					print ('<input id="COMUNE" >');
					print ('<br>');

				$this->_FormFields['LOCALITA']->showDivField();

				$this->_FormFields['LEGGE_ID']->showDivField();
				$this->_FormFields['DECRETO']->showDivField();
				$this->_FormFields['DATA_DECRETO']->showDivField();
				$this->_FormFields['FONTE_PUBBLICAZIONE']->showDivField();
				$this->_FormFields['NUMERO_PUBBLICAZIONE']->showDivField();
				$this->_FormFields['NOTE']->showDivField();
//				$this->_FormFields['PUBBLICA']->showDivField();
				$this->_FormFields['MAPPA']->showDivField();
			print ('</div>');
		print ('</form>' . "\n");
			// Visualizzo la scheda della mappa se caricata
			if ($this->_FormFields['MAPPA']->GetValue()>''){
				print ('<div dojoType="dijit.layout.ContentPane" title="Mappa Catastale" style=" margin:10px;" href="djGetImage.php?f=' . $this->_FormFields['MAPPA']->GetFile().'" >');
				print ('</div>');
			}
		print ('</div>');
		print ("\n");
		print ("<br />\n");
		print ('</div>' . "\n");
	}

}

$VM_ID = isSet($_GET['VA_ID'])?$_GET['VA_ID']:$_POST['VA_ID'];
$dbKey = ' where VA_ID=' . $VA_ID;

$ManagedTable = new MyDbForm('VIN_AMBIENTALI', $sess_lang);
$ManagedTable->setAfterUpdateLocation($PHP_SELF.'?mode=modify&VA_ID=' . $VA_ID);
$ManagedTable->SetFormMode("modify", stripslashes($dbKey));
include('pageheader.inc');
if (isSet ($display) and ($display == 'Y')) {
	$ManagedTable->displayForm();
} else {
	$ManagedTable->ShowForm();
}
include('pagefooter.inc')
?>
