<?php
/*
 * Created on 05/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");

class myhtmlETable extends htmlETable {


}
$praticheRel = Db_Pdo::getInstance()->query('select * from pratiche_fascicoli pf1
        right join pratiche_fascicoli pf2 ON (pf2.fascicolo_id = pf1.fascicolo_id)
         where pf1.pratica_id = :pratica_id ',array(
    ':pratica_id' => $_GET['praticaId']
))->fetchAll();
$arrayPratiche = array();
if($praticheRel){
    foreach ($praticheRel as $pratiche){
        $arrayPratiche[] = $pratiche['pratica_id'];
    }
} else {
    $arrayPratiche[] = $_GET['praticaId'];
}

$dispDestinazioniQuery = 'select arc_destinazioni.*, ' .
    'concat(\'<img class="cancDestinazioni" src="graphics/webapp/deleterec.gif" STYLE="cursor: pointer;" onClick="cancellaDestinazione(\',arc_destinazioni.dest_id,\')" title="Cancella Destinazione" >\') as "#"' .
    'From arc_destinazioni
         left join pratiche on (pratiche.pratica_id = arc_destinazioni.pratica_id)       ' .
    'where arc_destinazioni.pratica_id in ('. implode(',', $arrayPratiche) . ')';




$destTable = new myHtmlEtable($dispDestinazioniQuery);
$destTable->HideCol('DEST_ID');
$destTable->HideCol('PRATICA_ID');

$destTable->show();