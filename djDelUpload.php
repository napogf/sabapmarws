<?php
/*
 * Created on 05/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");


try {
    $sql = 'select concat(UPLOAD_ID,\'_\',FILENAME) as File
			from uploads where upload_id='.$_GET['uploadId'];
    $fileResult=dbselect($sql);
    unlink(FILES_PATH . DIRECTORY_SEPARATOR .$fileResult['ROWS'][0]['File']);
    $cancQuery="delete from uploads where upload_id =".$_GET['uploadId'];
    dbupdate($cancQuery);
    $_SESSION['paneSelected'] = 'paneUploads';
    $result['status'] = 'success';
} catch (Exception $e) {
}
if(dbupdate($cancQuery)){

} else {
    $result['status'] = 'error';
	$result['message'] = $e->getMessage();
}
print json_encode($result);