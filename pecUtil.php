<?php
require_once "login/configsess.php";
require_once "dbfunctions.php";

$db = Db_Pdo::getInstance();

$pecRegExp = '/^PEC_ID[\s|\-|_]([0-9]{1,})[\s|\-|:](.*)/i';

$praticheDaAllegare = $db->query('SELECT arc_pratiche_pec.pec_id, pratiche.pratica_id, pratiche.numeroregistrazione, pratiche.dataregistrazione, pratiche.oggetto
		FROM pratiche
        left join arc_pratiche_pec on (arc_pratiche_pec.pratica_id = pratiche.pratica_id)
		WHERE oggetto like "PEC_ID%" and arc_pratiche_pec.pec_id is null order by 2 desc');
while($pratica = $praticheDaAllegare->fetch()){
	preg_match_all($pecRegExp,$pratica['oggetto'],$pecMail);
	if(isSet($pecMail[1][0]) and (integer) $pecMail[1][0] > 0){
		$db->query('update arc_pratiche_pec set
							pratica_id = :pratica_id,
							numeroregistrazione = :numeroregistrazione,
							dataregistrazione = :dataregistrazione
					where pec_id = :pec_id',[
							':pec_id' => (integer) $pecMail[1][0],
							':pratica_id' => $pratica['pratica_id'],
							':numeroregistrazione' => $pratica['numeroregistrazione'],
							':dataregistrazione' => $pratica['dataregistrazione']
					]);
		print('INFO ' . date('Y-m-d H:i:s') . ' allegata la mail ' . $pecMail[1][0] . ' -> al protocollo ' . $pratica['pratica_id'] . ' - ' . $pratica['numeroregistrazione'] . ' del ' . $pratica['dataregistrazione'] . PHP_EOL);
	} else {
	    print('ERR ' . date('Y-m-d H:i:s') . ' non allegata la mail ' . $pratica['oggetto'] . ' -> al protocollo ' . $pratica['pratica_id'] . ' - ' . $pratica['numeroregistrazione'] . ' del ' . $pratica['dataregistrazione'] . PHP_EOL);
	}

}
