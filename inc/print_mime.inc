<?php
/**
 *
 *
 * @version $Id: print_mime.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003
 **/




function ErrorImage ($text) {
global $maxw;
$len = strlen ($text);
if ($maxw < 154) $errw = 300;
	$errh = 30;
	$chrlen = intval (5.9 * $len);
	$offset = intval (($errw - $chrlen) / 2);
	$im = imagecreate ($errw, $errh); /* Create a blank image */
	$bgc = imagecolorallocate ($im, 153, 63, 63);
	$tc = imagecolorallocate ($im, 255, 255, 255);
	imagefilledrectangle ($im, 0, 0, $errw, $errh, $bgc);
	imagestring ($im, 2, $offset, 7, $text, $tc);
	header ("Content-type: image/jpeg");
	imagejpeg ($im);
	imagedestroy ($im);
	exit;
}

function thumbnail ($gdver, $src, $maxw=190) {

$gdarr = array (1,2);
for ($i=0; $i<count($gdarr); $i++) {
	if ($gdver != $gdarr[$i]) $test.="|";
}
$exp = explode ("|", $test);
if (count ($exp) == 3) {
ErrorImage ("Incorrect GD version!");
}

if (!file_exists($src)) {
    ErrorImage ("File not Found! - $src");
}

if (!function_exists ("imagecreate") || !function_exists ("imagecreatetruecolor")) {
ErrorImage ("No image create functions!");
}

$size = @getimagesize ($src);
if (!$size) {
ErrorImage ("Image size problem! - $src");
} else {

if ($size[0] > $maxw) {
$newx = intval ($maxw);
$newy = intval ($size[1] * ($maxw / $size[0]));
} else {
$newx = $size[0];
$newy = $size[1];
}

if ($gdver == 1) {
$destimg = imagecreate ($newx, $newy );
} else {
$destimg = @imagecreatetruecolor ($newx, $newy ) or die (ErrorImage ("Cannot use GD2 here!"));
}
// ErrorImage($src.$size[0].'-'.$size[1].'-'.$size[2] );
if ($size[2] == 1) {
if (!function_exists ("imagecreatefromgif")) {
ErrorImage ("Cannot Handle GIF Format!");
} else {
$sourceimg = imagecreatefromgif ($src);

if ($gdver == 1)
imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
else
@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));

header ("content-type: image/gif");
imagegif ($destimg);
}
}
elseif ($size[2]==2) {
$sourceimg = imagecreatefromjpeg ($src);

if ($gdver == 1)
imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
else
@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));

header ("content-type: image/jpeg");
imagejpeg ($destimg);
}
elseif ($size[2] == 3) {
$sourceimg = imagecreatefrompng ($src);

if ($gdver == 1)
imagecopyresized ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]);
else
@imagecopyresampled ($destimg, $sourceimg, 0,0,0,0, $newx, $newy, $size[0], $size[1]) or die (ErrorImage ("Cannot use GD2 here!"));

header ("content-type: image/png");
imagepng ($destimg);
}
else {
ErrorImage ("Image Type Not Handled!");
}
}

imagedestroy ($destimg);
imagedestroy ($sourceimg);
}


// include "login/configsess.php";

	$ext = '';
	$ext_array=array('null', 'pdf', 'xls', 'doc', 'ppt', 'zip', 'txt', 'htm', 'html', 'exe','out','sxc','sxw','gif','jpg','jpeg');
	$mime_array=array('pdf'=>'pdf.png', 'xls'=>'msexcel.png','doc'=>'msword.png','ppt'=>'mspowerpoint',
									'zip'=>'compressed.png','txt'=>'text.png','htm'=>'html.png',
									'html'=>'html.png','exe'=>'executable.png','bin'=>'binary.png',
									'out' => 'pdf.png','sxc'=>'oologo.png','sxw'=>'oologo.png',
									'gif' => 'image.png','jpg'=>'image.png','jpeg'=>'image.png');
	$i = strlen($fname)-1;
	while($i > 0){
		if (substr($fname,$i,1)=='.') {
		    break;
		} else {
		  	$ext = substr($fname,$i,1).$ext;
			$i--;
		}
	} // while
	$ext = strtolower($ext);
	$index = (array_search($ext,$ext_array) <> FALSE)?$ext:'bin';
	// modificare per archeologico
	// $dir_mime="/Programmi/Apache Group/Apache2/htdocs/archeologico/mime/";
	$dir_mime=getcwd()."/mime/";
	$image = $dir_mime.$mime_array[$index];
	// ErrorImage($image.'-'.$mime_array[$index].'-'.$index);

//	switch($ext){
//		case '.gif':
//			$sourceimg = imagecreatefromgif ($image);
//			header ("content-type: image/gif");
//			imagegif ($sourceimg);
//			break;
//		case '.png':
//			$sourceimg = imagecreatefrompng ($image);
//			header ("content-type: image/png");
//			imagepng ($sourceimg);
//			break;
//		default:
//			;
//	} // switch
	thumbnail (2, $image, 20);


?>