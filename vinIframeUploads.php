<?php
include "login/autentication.php";
require_once("dbfunctions.php");
define("VIN_UPLOAD_DIR",$vinUploads);

dbupdate('insert into vin_uploads (VM_ID,DESCRIPTION,FILENAME,CREATED_BY,CREATED) VALUES (
			\''.$_POST['up_VM_ID'].'\',
			\''.$_POST['up_DESCRIPTION'].'\',
			\''.$_FILES['up_FILENAME']['name'].'\',
			\''.$_SESSION['sess_uid'].'\',
			now()
			)');
// fake delay of 3 seconds
sleep(3);
$vinUploadId=dbLastId();
// this is what we are returning.  we can do whatever we want to make this
// array.

@copy($_FILES['up_FILENAME']['tmp_name'], VIN_UPLOAD_DIR.$vinUploadId.'_'.$_FILES['up_FILENAME']['name']);
// encode our array, and dump it back to the Deferred that called us to upload this file
// yeah, seems you have to wrap iframeIO stuff in textareas?

$foo = "{'status':'success',details: {name:'".
	$_FILES['up_FILENAME']['name'].
	"',tmp_name:'".$_FILES['up_FILENAME']['tmp_name'].
	"',post_data:'".$_POST['up_DESCRIPTION'].
	"',size:".
	$_FILES['up_FILENAME']['size'].
	"}}";
// yeah, seems you have to wrap iframeIO stuff in textareas?
?><textarea><?php
print $foo;
?></textarea>