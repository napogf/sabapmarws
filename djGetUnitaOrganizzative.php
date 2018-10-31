<?php
/*
 * Created on 05/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";

class myhtmlETable extends htmlETable {

}
$uoQuery='select ao.DESCRIPTION as "Unit&agrave Organizzativa", 
				concat(\'<center><img src="graphics/webapp/deleterec.gif" 
					STYLE="cursor: pointer;" 
					onClick="cancellaUnitaOrganizzativa(\',apo.prauoid,\')" 
					title="Cancella Unità Organizzativa" ></center>\') as "#" 
			from arc_pratiche_uo apo 
			left join arc_organizzazione ao on (ao.uoid = apo.uoid) 
				where apo.pratica_id = '.$_GET['praticaId'];

$uoTable = new myHtmlEtable($uoQuery);
print('<html><body><div dojoType="dijit.layout.ContentPane">');
$uoTable->show();
print('</div></body></html>');
?>
