<?php
/*
 * Created on 15/ott/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
print('<style>		table.integrazioni {
			font-family:Lucida Grande, Verdana;
			width:100%;
			border:1px solid #ccc;
			cursor:default;
		}

		* html div.tableContainer {	/* IE only hack */
			width:95%;
			border:1px solid #ccc;
			height: 285px;
			overflow-x:hidden;
			overflow-y: auto;
		}

		table.integrazioni td,
		table.integrazioni th{
			border-right:1px solid #999;
			padding:2px;
			font-weight:normal;
		}
		table.integrazioni thead td, table.integrazioni thead th {
			background:#94BEFF;
		}

		* html div.tableContainer table.integrazioni thead tr td,
		* html div.tableContainer table.integrazioni thead tr th{
			/* IE Only hacks */
			position:relative;
			top:expression(dojo.html.getFirstAncestorByTag(this,\'table\').parentNode.scrollTop-2);
		}

		html>body tbody.scrollContent {
			height: 262px;
			overflow-x:hidden;
			overflow-y: auto;
		}

		tbody.scrollContent td, tbody.scrollContent tr td {
			background: #FFF;
			padding: 2px;
		}

		tbody.scrollContent tr.alternateRow td {
			background: #e3edfa;
			padding: 2px;
		}

		tbody.scrollContent tr.selected td {
			background: yellow;
			padding: 2px;
		}
		tbody.scrollContent tr:hover td {
			background: #a6c2e7;
			padding: 2px;
		}
		tbody.scrollContent tr.selected:hover td {
			background: #ff3;
			padding: 2px;
		}
</style>');

if($_GET['TIPO']>'') {
	$integrazioniResult=dbselect('select ID, TIPO, DESCRIZIONE from arc_integrazioni where tipo = \''.$_GET['TIPO'].'\' ');
	print('<div class="tableContainer"><table class="integrazioni"><caption>'.$integrazioniResult['ROWS'][0]['TIPO']."</caption>\n");
	for ($index = 0; $index < $integrazioniResult['NROWS']; $index++) {
		print("\t".'<tr><td id="INT_'.$integrazioniResult['ROWS'][$index]['ID'].'">'.$integrazioniResult['ROWS'][$index]['DESCRIZIONE'].'</td>' .
				'<td><a onClick="addIntegrazioni('.$integrazioniResult['ROWS'][$index]['ID'].');" >Aggiungi</a></td>' .
				'</tr>'."\n");
	}
	print('<table></div>');

} else {
	print('<h3>Seleziona la tipologia di integrazioni da aggiungere!</h3>');
}



?>