<?php
include "login/autentication.php";
include "Browser.php";
//require_once("dbfunctions.php");
if(isSet($_GET['f'])){
    if (file_exists(FILES_PATH . DIRECTORY_SEPARATOR . $_GET['f'])) {
        $f = FILES_PATH . DIRECTORY_SEPARATOR . $_GET['f'];
    } elseif(file_exists(DOC_PATH . DIRECTORY_SEPARATOR . $_GET['f'])){
        $f = DOC_PATH . DIRECTORY_SEPARATOR . $_GET['f'];
    } elseif(file_exists(PEC_PATH . DIRECTORY_SEPARATOR . $_GET['f'])){
        $f = PEC_PATH . DIRECTORY_SEPARATOR . $_GET['f'];
    } else {
        print('File ' . $_GET['f'] . ' not exist - call system administrator!');
        exit;
    }
} elseif (isset($_GET['fid'])){
    if($f = Db_Pdo::getInstance()->query('SELECT concat(upload_id,"_",filename) FROM uploads WHERE upload_id = :upload_id',[
        ':upload_id' => $_GET['fid'],
    ])->fetchColumn()){
        if (file_exists(FILES_PATH . DIRECTORY_SEPARATOR . $f)) {
            $f = FILES_PATH . DIRECTORY_SEPARATOR . $f;
        } else {
            print('File ' . $f . ' not exist - call system administrator!');
            exit;
        }
    }

}


$file_ext = pathinfo($f, PATHINFO_EXTENSION);
$fname = pathinfo($f, PATHINFO_BASENAME);
$browser_cap=$HTTP_SERVER_VARS['HTTP_ACCEPT'];
$plugins=explode(",", $browser_cap);








$browser = new Browser();
$plugin_not_found=false;
switch ($file_ext){
	case 'conf':
         $file_type='text/plain';
         break;
	case 'txt':
         $file_type='text/plain';
         break;
	case 'htm':
         $file_type='text/html';
         break;
	case 'html':
         $file_type='text/html';
         break;
    case 'pdf':
         $file_type='application/pdf';
         break;
    case 'out':
         $file_type='application/pdf';
		 $fname=substr($fname,0,strlen($fname)-4).'.pdf';
         break;
    case 'xls':
         $file_type='application/vnd.ms-excel';
         break;
    case 'doc':
         $file_type='application/msword';
         break;
    case 'ppt':
         $file_type='application/vnd.ms-powerpoint';
         break;
    case 'pps':
         $file_type='application/vnd.ms-powerpoint';
         break;
    case 'jpg':
         $file_type='image/jpeg';
         break;
    case 'bmp':
         $file_type='image/x-xbitmap';
         break;
    case 'gif':
         $file_type='image/gif';
         break;
     case 'png':
         $file_type='image/png';
         break;
    case 'png':
        $file_type='message/rfc822';
        break;
    default:
		 $plugin_not_found=true;
}



/* Some browsers don't like spaces in the filename. */
if ($browser->hasQuirk('no_filename_spaces')) {
    $fname = strtr($fname, ' ', '_');
}
/* MSIE doesn't like multiple periods in the file name. Convert
   all periods (except the last one) to underscores. */
if ($browser->isBrowser('msie')) {
    if (($pos = strrpos($fname, '.'))) {
        $fname = strtr(substr($fname, 0, $pos), '.', '_') . substr($fname, $pos);
    }
}

if (($plugin_not_found) or ($wk_inline != 'Y')) {
	if ($browser->isBrowser('msie')) {
	    header('Content-Type: application/x-msdownload');
	} else {
	    header('Content-Type: application/octet-stream');
	}
} else {
	header('Content-Type: '.$file_type);
}

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
//dbupdate("insert into link_audit (user_id, link_id, filename, audit_date) values ('$sess_uid', '$wk_link_id', '$fname', NOW())");
//    header("Cache-control: private");
//unlink($f);
exit;


/*
//Handle special IE request if needed
if($HTTP_ENV_VARS['USER_AGENT']=='contype')
{
    Header('Content-Type: application/pdf');
    exit;
}
*/
//Output PDF
?>
