<?php
/*
 * Created on 23/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
$result = [
    'status' => 'success',
    'message' => 'ok',
];
$db = Db_Pdo::getInstance();


try {
    $db->beginTransaction();
    if(!$progressivoResult=$db->query('select count(*) as PROGRESSIVO from pratiche where numeroregistrazione regexp :praticheDiOggi',[
        ':praticheDiOggi' => 'NP-' . (new Date($_GET['dataRegistrazione']))->format('Ymd'),
    ])->fetchColumn()){
    	$progressivoPratica=1;
    } else {
    	$progressivoPratica = (integer) $progressivoResult+1;
    }



    $updQuery='insert into pratiche (tipologia,modello, dataregistrazione, dataarrivo, numeroregistrazione ) values
    							("E", :modello, :dataregistrazione, :dataarrivo, :numeroregistrazione )';

    $db->query($updQuery,[
    		':modello' => $_GET['tipoPratica'],
    		':dataregistrazione' => (new Date($_GET['dataRegistrazione']))->toMysql() ,
    		':dataarrivo' => (new Date($_GET['dataRegistrazione']))->toMysql() ,
    		':numeroregistrazione' => 'NP-' . (new Date($_GET['dataRegistrazione']))->format('Ymd') . '-' .$progressivoPratica
    ]);

    $praticaId = $db->lastInsertId();
    $db->query('insert into arc_pratiche_uo (pratica_id, uoid) values (:pratica_id, :zona), (:pratica_id, :ufficio)',[
    		':pratica_id' => $praticaId,
    		':zona' => $_GET['zona'],
    		':ufficio' => $_GET['ufficio']
    ]);
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
	$result['status'] = 'error';
	$result['message'] = $e->getMessage();
	$result['params'] = $_GET;
}
print(json_encode($result));
