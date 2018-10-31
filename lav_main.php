<?php
/*
 * Created on 04/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
include('pageheader.inc');
print('<script type="text/javascript" src="javascript/lavoriPubblici.js"></script>');
print ('<div dojoType="dijit.layout.TabContainer" id="lavContainer"
				style="min-height:600px; margin:5px;">');
	// Perizie
	print ('<div dojoType="dijit.layout.ContentPane" ' .
					'title="Perizie" ' .
					'style="border: none;min-height:200px;" ' .
					'id="PERIZIE" >');
			print ('<div dojoType="dijit.layout.ContentPane" ' .
					'id="listaPerizie" ' .
					'style="border: none;" ' .
					'onLoad="setRowClass(periziaId);" ' .
					'href="lav_perizie.php">');
			print('</div>');
			print ('<div dojoType="dijit.layout.ContentPane" ' .
						'id="addPerizia" ' .
						'style="display: none;" ' .
						'href="" >');

			print('</div>');
			print ('<div dojoType="dijit.layout.ContentPane" ' .
						'id="editPerizia" ' .
						'style="display: none;" ' .
						'href="" >');
			print('</div>');

	print('</div>');
	// Quadro Economico
	print ('<div dojoType="dijit.layout.ContentPane" ' .
					'title="Quadro Economico" ' .
					'id="QECONOMICO" >');
		print('<h2>Seleziona una Perizia</h2>');
	print('</div>');
	// Contratti
	print ('<div dojoType="dijit.layout.ContentPane" title="Contratti" id="CONTRATTI">');
		print('<h2>Seleziona una voce del quadro economico</h2>');
	print('</div>');
	//Staff
	print ('<div dojoType="dijit.layout.ContentPane" title="Staff" id="STAFF" >');
		print('<h2>Seleziona una Perizia</h2>');
	print('</div>');

print('</div>');


include('pagefooter.inc')
?>
