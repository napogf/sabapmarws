<?php
/*
 * Created on 10/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");


$db = Db_Pdo::getInstance();

$get = [
    ':PRATICA_ID' => $_GET['PRATICA_ID'],
    ':MODELLO' => $_GET['MODELLO'],
    ':ESITO_ID' => $_GET['ESITO_ID'],
    ':NOTE' => $_GET['NOTE'],
    ':USCITA' => (new Date($_GET['USCITA']))->toMysql(),
];


try {

    $fieldsToUpdate = [];
    $result = [
        'status' => 'success',
        'message' => 'Aggiornata!',
        'query' => 'UPDATE pratiche SET ' . implode(',', $fieldsToUpdate) . ' WHERE PRATICA_ID = :PRATICA_ID',
    ];
    $params = [];
    foreach ($get as $key => $param) {
        if(!empty($_GET[substr($key, 1)])){
            $fieldsToUpdate[] = substr($key, 1) . ' = ' . $key;
            $params[$key] = $_GET[substr($key, 1)];
        }
    }
    if(count($params)>1){
        $db->query('UPDATE pratiche SET ' . implode(',', $fieldsToUpdate) . ' WHERE PRATICA_ID = :PRATICA_ID', $params);
    }

    if (!empty($_GET['ZONA'])) {
        if (!$db->query('select * from arc_pratiche_uo where pratica_id = :pratica_id and uoid = :uoid', [
            ':pratica_id' => $_GET['PRATICA_ID'],
            ':uoid' => $_GET['ZONA'],
        ])->fetch()) {
            $db->query('insert into arc_pratiche_uo (pratica_id, uoid) values (:pratica_id, :uoid)', [
                ':pratica_id' => $_GET['PRATICA_ID'],
                ':uoid' => $_GET['ZONA'],
            ]);
        }
        $result['zona'] = $_GET['ZONA'];
    }
    if (!empty($_GET['UFFICIO'])) {
        if (!$db->query('select * from arc_pratiche_uo where pratica_id = :pratica_id and uoid = :uoid', [
            ':pratica_id' => $_GET['PRATICA_ID'],
            ':uoid' => $_GET['UFFICIO'],
        ])->fetch()) {
            $db->query('insert into arc_pratiche_uo (pratica_id, uoid) values (:pratica_id, :uoid)', [
                ':pratica_id' => $_GET['PRATICA_ID'],
                ':uoid' => $_GET['UFFICIO'],
            ]);
        }
        $result['ufficio'] = $_GET['UFFICIO'];

    }

} catch (PDOException $e) {
    $result = [
        'status' => 'error',
        'message' => $_GET,
        'error' => $e->getMessage(),
    ];

} catch (Exception $e) {
    $result = [
        'status' => 'error',
        'message' => $_GET,
        'error' => $e->getMessage(),
    ];

}
print(json_encode($result));

exit;
