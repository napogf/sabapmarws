<?php
/*
 * Created on 11/apr/2011
 *
 * djCreaProgettoa.php
*/

include "login/autentication.php";
$db = Db_Pdo::getInstance();
$result = [
    'status' => 'success',
    'message' => '',
];
try {
    $db->beginTransaction();
    if (isset($_GET['project']) and $_GET['project']>'') {
        $sql='update pratiche set PROJECT_ID = :project_id where pratica_id=:pratica_id';
        $db->query($sql,[
            ':project_id' => $_GET['project'],
            ':pratica_id' => $_GET['praticaId']
            ]);
        $result['message'] = 'Protocollo associato al Fascicolo!';
    } else {
        $db->query('insert into arc_pratiche_prj
					(pratica_id, description) values (:pratica_id, :description)',array(
    					    ':pratica_id' => $_GET['praticaId'],
    					    ':description' => $_GET['description'],
    					));
        $projectId = $db->lastInsertId();
        $db->query('update pratiche set project_id = :project_id where pratica_id = :pratica_id',array(
            ':project_id' => $projectId,
            ':pratica_id' => $_GET['praticaId'],
        ));
        $result['message'] = 'Fascicolo creato e Protocollo associato!';
    }
    // se non esiste un fascicolo per la pratica lo creo
    if(!$db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[':pratica_id' => $_GET['praticaId']])->fetch()){
        $praticheProgetto = $db->query('SELECT pratica_id, tipologia, OGGETTO FROM pratiche WHERE pratica_id = :pratica_id',[
                ':pratica_id' => $_GET['praticaId'],
        ])->fetch();
        $fascicoloId = (integer) $db->query(
                'select max(fascicolo_id) from pratiche_fascicoli')->fetchColumn() +1;
        $db->query(
                'insert into pratiche_fascicoli (fascicolo_id, pratica_id,tipologia) values (:fascicolo_id, :pratica_id, :tipologia)',
                [
                ':fascicolo_id' => $fascicoloId,
                ':pratica_id' => $praticheProgetto['pratica_id'],
                ':tipologia' => $praticheProgetto['tipologia']
                ]);
    }

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    $result = [
        'status' => 'success',
        'message' => $e->getMessage(),
    ];
}


echo json_encode($result);