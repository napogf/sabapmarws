<?php 	
	print ('<div>' . "\n");
	print ('<div style="float: left;">' . "\n");

	print ('<input id="selDocuments">' .
	'<button id="creaButton" type="button"  disabled="disabled" ' .
	'	onclick="return creaDaModello(\'' . $ManagedTable->_FormFields['PRATICA_ID']->GetValue() . '\')" >Crea da Modello</button>');


	print ('</div>' . "\n");
	print ('<div style="float: right;">' . "\n");
	// Project create when project don't exist
	if (!$prjResult=dbselect(' select * from arc_pratiche_prj where pratica_id='.$ManagedTable->_FormFields['PRATICA_ID']->GetValue())
		and strlen($ManagedTable->_FormFields['PROJECT_ID']->GetValue())==0) {
		print ('<a href="#" onClick="creaProgetto('.$ManagedTable->_FormFields['PRATICA_ID']->GetValue().')">Crea un Progetto</a><img src="graphics/report_add.png" style="margin-left:10px; margin-right:10px;"  title="Crea un progetto a partire da questa pratica" >');
	}

	if ($ManagedTable->GetFormFieldValue('USCITA') > ' ') {
		if(!dbselect('select * from arc_sospensioni where protoentrata = '.$ManagedTable->_FormFields['PRATICA_ID']->GetValue())){
			print ('<a href="praticaRiattivazione.php?PRATICA_ID=' . $ManagedTable->_FormFields['PRATICA_ID']->GetValue() . '">Riprendi Pratica</a><img src="graphics/control_play_blue.png" style="margin-left:10px; margin-right:10px;"  title="Riattiva Pratica" >');
			
		} else {
			print('<span id="sos_'.$ManagedTable->_FormFields['PRATICA_ID']->GetValue().'"><img src="graphics/control_pause_blue.png" style="cursor: pointer;margin-left:10px; margin-right:10px;" ><a href="" >Sospensioni</a></span>' .
					'<span dojoType="dijit.Tooltip" id="ttsos_'.$ManagedTable->_FormFields['PRATICA_ID']->GetValue().'" connectId="sos_'.$ManagedTable->_FormFields['PRATICA_ID']->GetValue().'" style="display:none;">' .
					'<div dojoType="dijit.layout.ContentPane" class="djToolTipContainer" href="djGetSospensione.php?praticaId='.$ManagedTable->_FormFields['PRATICA_ID']->GetValue().'" style="overflow: hidden;" >' .
					'</div>' .
					'</span>');
		}
	} else {
		if(!dbselect('select sospensione_id from arc_sospensioni where pratica_id = '.$ManagedTable->_FormFields['PRATICA_ID']->GetValue())){
			print ('<a href="praticaSospensione.php?PRATICA_ID=' . $PRATICA_ID . '">Sospendi Pratica</a><img src="graphics/control_pause_blue.png" style="margin-left:10px; margin-right:10px;"  title="Sospendi Pratica" >');
		} else {
			print ('<a href="praticaRiattivazione.php?PRATICA_ID=' . $PRATICA_ID . '">Riprendi Pratica</a><img src="graphics/control_play_blue.png" style="margin-left:10px; margin-right:10px;"  title="Riprendi Pratica" >');
		}
		print ('<a href="praticaVincoli.php?PRATICA_ID=' . $PRATICA_ID . '">Visualizza Vincoli</a><img src="graphics/caution.gif" style="margin-left:10px; margin-right:10px;"  title="Visualizza Vincoli" >');	
			// print ('<a href="vincoliPaesaggistici.php?PRATICA_ID=' . $PRATICA_ID . '">Vincoli Paesaggistici</a><img src="graphics/photo_1.png" style="margin-left:10px; margin-right:10px;"  title="Visualizza Vincoli Paesaggistici" >');

			//print ('<a href="vincoliMonumentali.php?PRATICA_ID=' . $PRATICA_ID . '' .
			//		'&keyword=' .$ManagedTable->GetFormFieldValue('OGGETTO').
			//		'&foglioFilter=' .$ManagedTable->GetFormFieldValue('FOGLIO').
			//		'&mappaleFilter=' .$ManagedTable->GetFormFieldValue('MAPPALE').
			//		'&anaFilter=' .$ManagedTable->GetFormFieldValue('MAPPALE').
			//		'">Vincoli Monumentali</a><img src="graphics/home.png" style="margin-left:10px; margin-right:10px;"  title="Visualizza Vincoli Monumentali" >');
	}

	print ('</div>' . "\n");
	print ('<div style="clear: both;"></div>' . "\n");
?>