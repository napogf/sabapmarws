<?php
/*
 * Created on 01/ott/2012
 *
 * djDisplayPec.php
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once('MimeMailParser.class.php');


function viewMail($Parser){

		$to = $Parser->getHeader('to');
		$delivered_to = $Parser->getHeader('delivered-to');
		$from = $Parser->getHeader('from');
		$subject = mb_decode_mimeheader($Parser->getHeader('subject'));
		$text = $Parser->getMessageBody('text');
		$html = $Parser->getMessageBody('html');


		$txtBodyHeader = $Parser->getMessageBodyHeaders();
		if(preg_match('|UTF-8|i',$txtBodyHeader['content-type'])){
			$text = nl2br(htmlentities($text,ENT_COMPAT,'UTF-8'));
		}

		$attachments = $Parser->getAttachments();


		$attachments = $Parser->getAttachments();

		print('<div class="mailCert"><span>Mittente:</span>' . $from . '</div>');
		print('<div class="mailCert"><span>Oggetto:</span>' . $subject . '</div>');
		if(!empty($html)){
			print('<div class="mailCert"><p>' . $html . '</p></div>');
		} else {
			print('<div class="mailCert"><p>' . $text . '</p></div>');
		}


		print('<div class="mailCert"><ul>Allegati:');
		$attachIndex = 0;
		foreach($attachments as $attachment) {
			print('<li class="'. strtolower($attachment->extension) .'" onclick="getAttachment('.$_GET['UPLOAD_ID'].','.$attachIndex.')" style="cursor: pointer">' .  $attachment->filename . '</li>');
			$attachIndex++;
		}
		print('</ul></div>');

}
print('<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN"> -->
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="MSSmartTagsPreventParsing" content="true">
	<link rel="stylesheet" type="text/css" href="css/main.css>">
	<link rel="stylesheet" type="text/css" href="css/tests.css">
	<link rel="stylesheet" type="text/css" href="css/menuh.css">
    <title><?php print($PHP_SELF); ?></title>
    <base target="_self">
</head>
<body>');

	$dispEmlFilesQuery = 'select * from uploads ' .
										'where upload_id = '.$_GET['UPLOAD_ID'] ;
if (empty($_GET['UPLOAD_ID'])){
	print('<div class="DbFormMessage" style=" margin-top: 20px; text-align: center;" >Seleziona una Pec da visualizzare!</div>');
} else {
	if(! $emlResult = dbselect($dispEmlFilesQuery)){
		print('<div class="DbFormMessage">Attenzione! File non trovato contattare l\'assistenza</div>');
	} else {

		$pecFile = PEC_PATH . '/' . $emlResult['ROWS'][0]['UPLOAD_ID']."_".$emlResult['ROWS'][0]['FILENAME'];

		$Parser = new MimeMailParser();
		$Parser->setText(file_get_contents($pecFile));

		viewMail($Parser);

	}
}

print('</body></html>');