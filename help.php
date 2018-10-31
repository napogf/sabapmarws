<?php
include "login/autentication.php";
include "Browser.php";
global $sess_uid;



if (!file_exists("$file")) {
	print("File $file not exist - call system administrator!");
	exit;
}



$fileStruct=pathinfo($file);

$fname=$fileStruct['basename'];






$browser_cap=$HTTP_SERVER_VARS['HTTP_ACCEPT'];
$plugins=explode(",", $browser_cap);

$f="$file";




$browser = new Browser();
$file_type='application/pdf';




	header('Content-Type: '.$file_type);

if ($browser->hasQuirk('break_disposition_header')) {
	if ($wk_inline=='Y') {
	    header('Content-Disposition: inline; filename=' . $fname);
	} else {
      header("Content-Disposition: attachment; filename=$fname" );
	}
 } else {
	if ($wk_inline=='Y') {
	    header('Content-Disposition: inline; filename=' . $fname);
	} else {
       	header('Content-Disposition: attachment; filename="' . $fname .'"');
	}
 }


if ($browser->hasQuirk('cache_ssl_downloads')) {
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
}
Header('Content-Length: '.filesize($f));

readfile($f);
exit;
?>
