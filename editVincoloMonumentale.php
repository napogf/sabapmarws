<?php
/*
 * Created on 26/lug/10
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
		print ('<input type="submit" value="Aggiorna" name="buttapp" class="buttons" onClick="document.getElementById(\'VIN_MONUMENTALI_MAIN\').submit();" style="float:left;" />');
		print ('<input type="reset" value="Annulla" name="buttdel" class="buttons" style="float:right;" />');
		print ('</div>');
	}
	function dispOggettoVincolo(){
			print ('<div dojoType="dijit.layout.ContentPane" id="vincoloMainPane" title="Vincolo Monumentale" style=" margin:10px;">');
				$this->_FormFields['VM_ID']->showDivField();
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
						});
					</script>');

					print ('<label for="PROV">Provincia</label>');
					print ('<input id="PROV" >');
					print ('<br>');
					print ('<label for="COMUNE">Comune</label>');
					print ('<input id="COMUNE" >');
					print ('<br>');
				$this->_FormFields['LOCALITA']->showDivField();
				$this->_FormFields['NUMERI']->showDivField();
				$this->_FormFields['COLLOCAZIONE']->showDivField();
				$this->_FormFields['LEGGE_ID']->showDivField();
				$this->_FormFields['DATA']->showDivField();
				$this->_FormFields['TRASCRIZIONE']->showDivField();
				$this->_FormFields['NOTIFICA']->showDivField();
				$this->_FormFields['NOTE']->showDivField();
//				$this->_FormFields['PUBBLICA']->showDivField();				
				$this->_FormFields['MAPPA']->showDivField();
			print ('</div>');

	}
	function dispProprietari(){
		print ('<div dojoType="dijit.layout.ContentPane" id="propPane" title="Proprietari" style=" margin:10px;"  >');

				// Dialog Box Creazione nuovo Proprietario

				print('<div id="dlgAddProprietario" dojoType="dijit.Dialog" title="Aggiungi Proprietario" ></div>');
				print('<span style="margin:10px;"><a style="cursor: pointer;" onClick="dialogProprietario('.$this->_FormFields['VM_ID']->GetValue().');dijit.byId(\'cPaneProprietari\').refresh();"><img src="graphics/group_add.png"> Aggiungi Proprietario</a></span>');
				// Aggiunta Proprietari

				print ('<div dojoType="dijit.layout.ContentPane" id="cPaneProprietari" href="djGetProprietari.php?VM_ID='.$this->_FormFields['VM_ID']->GetValue().'"  >');
				print('</div>');

		print ('</div>');

	}
	function dispDatiCatastali(){
		print ('<div dojoType="dijit.layout.ContentPane" title="Dati Catastali" id="cPaneCatasto" style=" margin:10px;"  >');
				print('<div id="dlgAddParticella" dojoType="dijit.Dialog" title="Aggiungi Particella" ></div>');
					include('skFoglioParticella.inc');
		print ('</div>');

	}
	function dispDecreti(){
		print ('<div dojoType="dijit.layout.ContentPane" title="Decreti" id="cPaneDecreti" style=" margin:10px;"  >');
					include('skDecreti.inc');
		print ('</div>');
	}
	function dispMappe() {
		print ('<div dojoType="dijit.layout.ContentPane" title="Uploads" id="cPaneUploads" style=" margin:10px;"  >');
					include('skUploads.inc');
		print ('</div>');
	}
	function editForm() {
		/* Form container */

		print ('<div id="' . $this->GetFormName() . '" class="dbFormContainer" >' . "\n");

		print ('<!-- Form open -->');
		print ("\n");
		print('<form id="VIN_MONUMENTALI_MAIN" name="VIN_MONUMENTALI_MAIN" method="POST" enctype="multipart/form-data" class="VIN_MONUMENTALI" action="?mode=modify&dbKey= where VM_ID=1">');

		$this->formMessageShow();
		$this->formAttachmentsShow();
				$this->showButtonBar(FALSE);

		print ('<div style="background-color: azure; font-size: 1.5em; margin-bottom:10px;">' . $this->GetFormTitle() . '</div>' . "\n");
		// Pane Container
		print ('<div dojoType="dijit.layout.TabContainer" id="editVincoloContainer"
							style="width:98%; height:450px; margin:0px;">');
			// Scheda Oggetto di Vincolo
			$this->dispOggettoVincolo();
			// visualizzo la scheda dei Proprietari
			$this->dispProprietari();

			// Visualizzo Dati Catastali - Scheda Fogli / Particelle
			$this->dispDatiCatastali();
			// Visualizzo Decreti - Scheda Dati sul Vincolo
			$this->dispDecreti();
		print ('</form>' . "\n");			
			// Visualizzo la scheda della mappa se caricata
			$this->dispMappe();
			




		print ('</div>');
		print ("\n");
		print ("<br />\n");
//		print ('<div id="message"></div>' . "\n");
//		$this->showButtonBar(FALSE);
//		print ('</div>' . "\n");

		
	}
}

$VM_ID = isSet($_GET['VM_ID'])?$_GET['VM_ID']:$_POST['VM_ID'];
$dbKey = ' where VM_ID=' . $VM_ID;

$ManagedTable = new MyDbForm('VIN_MONUMENTALI', $sess_lang);
$ManagedTable->setAfterUpdateLocation($PHP_SELF.'?mode=modify&VM_ID=' . $VM_ID);
$ManagedTable->SetFormMode("modify", stripslashes($dbKey));
include('pageheader.inc');
print('<script type="text/javascript" src="javascript/djVincoli.js"></script>');
if (isSet ($display) and ($display == 'Y')) {
	$ManagedTable->displayForm();
} else {
	$ManagedTable->ShowForm();
}

include('pagefooter.inc')
?>
