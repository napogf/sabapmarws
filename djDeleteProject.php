<?php
/*
 * Created on 22/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
$result = [
	'status' => 'success',
	'message' => 'Pratica eliminata dal Fascicolo',
];
$db =Db_Pdo::getInstance();
try {
    if(isSet($_GET['praticaId'])){
        if($projectId = $db->query('
            SELECT project_id FROM arc_pratiche_prj 
                WHERE pratica_id = :pratica_id',array(
            ':pratica_id' => $_GET['praticaId']
        ))->fetchColumn()){
            /*Il progetto è stato generato a partire dalla pratica quindi setto
             * project_id a null per tutte le pratiche associate e poi cancello
             * il progetto
            */
            $db->query('UPDATE pratiche SET PROJECT_ID = NULL WHERE PROJECT_ID = :project_id',[
                ':project_id' => $projectId,
            ]);
            $db->query('DELETE FROM arc_pratiche_prj WHERE PROJECT_ID = :project_id',[
                ':project_id' => $projectId,
            ]);
            $result = [
                'status' => 'success',
                'message' => 'Pratica eliminata dal Fascicolo, e fascicolo Cancellato!',
            ];

        } else {
            /* Disassocio la pratica dal progetto */
            $db->query('UPDATE pratiche SET PROJECT_ID = NULL WHERE PRATICA_ID = :pratica_id',[
                ':pratica_id' => $_GET['praticaId'],
            ]);
            $result = [
                'status' => 'success',
                'message' => 'Pratica eliminata dal Fascicolo!',
            ];

        }


    } else {
        throw new Exception('Errore nel passaggio parametri!');
    }

} catch (Exception $e) {
    $result = [
	   'status' => 'error',
	   'message' => $e->getMessage(),
    ];
}
echo json_encode($result);