<?php
/*
 * Created on 26/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";

//require_once("inc/dbfunctions.php");

if ($_GET['newFascicolo'] > ''){
	$insFascicoloSql = 'insert into arc_titolazioni (TITOLO, COMUNE, FASCICOLO) values (\''.
							$_GET['titoloId'] . '\',\''.$_GET['comuneId'].'\',\''.addslashes($_GET['newFascicolo']).'\')';
	if(dbupdate($insFascicoloSql)){
		$fascicoloId = dbLastId();
		dbupdate('update pratiche set titolazione = '.$fascicoloId.' where pratica_id = '.$_GET['praticaId']);
	}
} else {
	$fascicoloId = $_GET['fascicoloId'];
	dbupdate('update pratiche set titolazione = '.$fascicoloId.' where pratica_id = '.$_GET['praticaId']);
}

$findFascicoloSql = 'select al1.description as LIV01,' .
						'al2.description as LIV02, ' .
						'al3.description as LIV03, ' .
						'concat(ac.comune,\' ( \',ac.provincia,\')\') as COMUNE, ' .
						'at.fascicolo as FASCICOLO ' .
						'from arc_titolazioni at ' .
						'right join arc_comuni ac on (ac.id = at.comune) ' .
						'right join arc_titolario al3 on (al3.titolo = at.titolo) ' .
						'right join arc_tito02 al2 on ((al2.liv01 = al3.liv01) and (al2.liv02 = al3.liv02)) ' .
						'right join arc_tito01 al1 on (al1.liv01 = al2.liv01) ' .
						'where at.id='.$fascicoloId;

if ($fascicolazioneResult=dbselect($findFascicoloSql)){
	print($fascicolazioneResult['ROWS'][0]['LIV01'].'->'.$fascicolazioneResult['ROWS'][0]['LIV02'].'->'.$fascicolazioneResult['ROWS'][0]['LIV03'].'->');
	print($fascicolazioneResult['ROWS'][0]['COMUNE'].'->');
	print($fascicolazioneResult['ROWS'][0]['FASCICOLO'].'<br/>');
} else {
	print('Fascicolazione non avvenuta!');
}