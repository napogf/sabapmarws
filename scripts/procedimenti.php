<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$filesToUpload = $db->query('SELECT id, concat(id,"-pdf-",pdf) as file FROM arc_procedimenti
                             WHERE pubblicato="N" ');
while ($file = $filesToUpload->fetch()) {
	try {
	    $db->beginTransaction();
	    $fileSource = FILES_PATH . DIRECTORY_SEPARATOR . $file['file'];
	    $fileDestination = ROOT_PATH . DIRECTORY_SEPARATOR . 'procedimentiweb' . DIRECTORY_SEPARATOR . $file['file'];
    		copy($fileSource, $fileDestination);
    		$db->query('update arc_procedimenti set pubblicato = "Y" where id = :id',array(
    				':id' => $file['id'],
    		));
    	print('INFO arc_procedimenti ' . date('Y-m-d H:m:s') . ' Trasferito il file -> '.$file['file'].PHP_EOL);
	    $db->commit();
	} catch (Exception $e) {
	    print('ERROR arc_procedimenti ' . date('Y-m-d H:m:s') . ' Errore in esportazione procedimenti -> '.$e->getMessage().PHP_EOL);
	    $db->rollBack();
	}
}

/*
 * Procedimenti caricati in pratiche normali
 */
$filesToUpload = $db->query('SELECT upd.upload_id, concat(upd.upload_id,"_",upd.filename) as file FROM uploads upd
                                    LEFT JOIN pratiche pr ON (pr.pratica_id = upd.pratica_id)
                                    LEFT JOIN arc_esiti es ON (es.esito_id = pr.esito_id)
                             WHERE PUBBLICA ="Y" AND PUBBLICATO="N" and upd.FILENAME regexp ".pdf" ');
while ($file = $filesToUpload->fetch()) {
	try {
	    $db->beginTransaction();
	    $fileSource = FILES_PATH . DIRECTORY_SEPARATOR . $file['file'];
	    $fileDestination = ROOT_PATH . DIRECTORY_SEPARATOR . 'procedimentiweb' . DIRECTORY_SEPARATOR . utf8_decode($file['file']);
	    	copy($fileSource, $fileDestination);
			$db->query('update uploads set pubblicato = "Y" where upload_id = :upload_id',[':upload_id' => $file['upload_id']]);
			print('INFO uploads ' . date('Y-m-d H:m:s') . ' Trasferito il file -> '.$file['file'].PHP_EOL);
		$db->commit();
	} catch (Exception $e) {
		print('ERROR uploads ' . date('Y-m-d H:m:s') . ' Errore in esportazione procedimenti -> '.$e->getMessage().PHP_EOL);
		$db->rollBack();
	}
}