<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$filesToUpload = $db->query('SELECT upd.upload_id, concat(upd.upload_id,"_",upd.filename) as file FROM uploads upd
                                    LEFT JOIN pratiche pr ON (pr.pratica_id = upd.pratica_id)
                                    LEFT JOIN arc_esiti es ON (es.esito_id = pr.esito_id)
                             WHERE PUBBLICA ="Y" AND PUBBLICATO="N"');
try {
    $db->beginTransaction();
    while ($file = $filesToUpload->fetch()) {
    	copy(FILES_PATH . DIRECTORY_SEPARATOR . $file['file'],
             ROOT_PATH . DIRECTORY_SEPARATOR . 'procedimentiweb' . DIRECTORY_SEPARATOR . $file['file']);
    	$db->query('update uploads set pubblicato = "Y" where upload_id = :upload_id',array(
    		':upload_id' => $file['upload_id'],
    	));
    }

    $db->commit();
} catch (Exception $e) {
    print('ERROR ' . date('Y-m-d H:m:s') . ' Errore in esportazione procedimenti -> '.$e->getMessage().PHP_EOL);
    $db->rollBack();
}