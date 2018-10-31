<?php
include "login/autentication.php";
require_once("dbfunctions.php");

	$insQuery = 'insert into uploads
			(PRATICA_ID,
			DESCRIPTION,
			PUBBLICA,
			FILENAME,
			AMBITO,
			SETTORE,
			TIPO_PROCEDIMENTO,
			A_B,
			NATURA_ATTO,
			CREATED_BY,
			CREATED) VALUES (
				:PRATICA_ID,
				:DESCRIPTION,
				:PUBBLICA,
				:FILENAME,
				:AMBITO,
				:SETTORE,
				:TIPO_PROCEDIMENTO,
				:A_B,
				:NATURA_ATTO,
				:CREATED_BY,
				now()
				)';
	Db_Pdo::getInstance()->query($insQuery,[
			':PRATICA_ID' => $_POST['up_PRATICA_ID'],
			':DESCRIPTION' => $_POST['up_DESCRIPTION'],
			':PUBBLICA' => (isset($_POST['up_PUBBLICA']) ? $_POST['up_PUBBLICA'] : 'N'),
			':FILENAME' => $_FILES['up_FILENAME']['name'],
			':AMBITO' => (isset($_POST['up_PUBBLICA']) ? $_POST['AMBITO'] : null),
			':SETTORE' => (isset($_POST['up_PUBBLICA']) ? $_POST['SETTORE'] : null),
			':TIPO_PROCEDIMENTO' => (isset($_POST['up_PUBBLICA']) ? $_POST['TIPO_PROCEDIMENTO'] : null),
			':A_B' => (isset($_POST['up_PUBBLICA']) ? $_POST['A_B'] : null),
			':NATURA_ATTO' => (isset($_POST['up_PUBBLICA']) ? $_POST['NATURA_ATTO'] : null),
			':CREATED_BY' => $_SESSION['sess_uid'],
	]);
	$UploadId=Db_Pdo::getInstance()->lastInsertId();
	$result = array(
	    'status' => '',
	    'details' => array(
	        'name' => $_FILES['up_FILENAME']['name'],
	        'tmp_name' => $_FILES['up_FILENAME']['tmp_name'],
	        'size' => $_FILES['up_FILENAME']['size']
	    )
	);
if (copy($_FILES['up_FILENAME']['tmp_name'], FILES_PATH . DIRECTORY_SEPARATOR . $UploadId.'_'.$_FILES['up_FILENAME']['name'])){
    $result['status'] = 'success';
} else {
	Db_Pdo::getInstance()->query('delete from uploads where upload_id = :upload_id',[
			':upload_id' => $UploadId
	]);
	$result['status'] = 'failure';
}
// encode our array, and dump it back to the Deferred that called us to upload this file
// yeah, seems you have to wrap iframeIO stuff in textareas?


// yeah, seems you have to wrap iframeIO stuff in textareas?
?><textarea><?php
print json_encode($result);
?></textarea>