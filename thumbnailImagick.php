<?php
include "login/autentication.php";
/**
 *
 *
 * @version $Id: thumbnailImagick.php,v 1.1 2010/10/06 12:30:09 cvsuser Exp $
 * @copyright 2003
 *
 **/


function thumbnail ($src, $maxw=190, $maxh=220) {

	$img = new Imagick();
	if(file_exists($src)){
		$img->readImage($src);
	} else {
		$noimage=getcwd().'/immagini/noImage.jpg';
		$img->readImage($noimage);
	}

	$width=$img->getImageWidth();
	  if ($width > $maxw) { $img->thumbnailImage(200,null,0); }

	$height=$img->getImageHeight();
	  if ($height > $maxh) { $img->thumbnailImage(null,82,0); }

	if(strtoupper($img->getImageFormat())=='PDF'){
		$img->setImageColorspace(255);
		$img->setCompression(Imagick::COMPRESSION_JPEG);
		$img->setCompressionQuality(60);
		$img->setImageFormat('jpeg');
	}


	// Output the image
	$output = $img->getimageblob();
	$outputtype = $img->getFormat();
	header("Content-type: $outputtype");
	echo $output;

}
$maxh = $_GET["maxh"] == NULL ? $_GET["maxw"] : $_GET["maxh"];
thumbnail ( $dir_upload.$_GET["src"], $_GET["maxw"], $maxh );

?>

