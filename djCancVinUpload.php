<?php
/*
 * Created on 05/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");
define("VIN_UPLOAD_DIR",$vinUploads);
$sql = 'select concat(VIN_UPLOAD_ID,\'_\',FILENAME) as File 
			from vin_uploads where vin_upload_id='.$_GET['vinUploadId'];
var_dump($_GET);
$fileResult=dbselect($sql);
unlink(VIN_UPLOAD_DIR.$fileResult['ROWS'][0]['File']);
$cancQuery="delete from vin_uploads where vin_upload_id =".$_GET['vinUploadId'];
if(dbupdate($cancQuery)){
	print('Record Cancellato!');
} else {
	print('Errore nella cancellazione!');
}
?>