<?php
/*
 * Created on 01/ott/2012
 *
 * djDisplayPec.php
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once('MimeMailParser.class.php');
//require_once('Mail/mimeDecode.php');

    function quotedPrintableDecode($input)
    {
        // Remove soft line breaks
        $input = preg_replace("/=\r?\n/", '', $input);

        // Replace encoded characters
		$input = preg_replace('/=([a-f0-9]{2})/ie', "chr(hexdec('\\1'))", $input);

        return $input;
    }


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
			$html = nl2br(htmlentities(utf8_decode($html),ENT_COMPAT,'UTF-8'));
		}

		$attachments = $Parser->getAttachments();


		$attachments = $Parser->getAttachments();

		print('<div onclick="printPec();" style="float:right; margin-right: 10px; cursor: pointer;" ><span style="font-weight: bold; color: #D56F2B; margin-right:5px;">Stampa PEC</span><img src="graphics/printer.png"></div>');
		print('<div id="pecToPrint">');

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
			$fileName = $attachment->filename;
			if(preg_match('|iso-8859|i',$fileName)){
				mb_internal_encoding('ISO-8859-1');
				$fileName = str_replace("_"," ", mb_decode_mimeheader($fileName));
			}

			print('<li class="'. strtolower($attachment->extension) .'" onclick="getAttachment('.$_GET['UPLOAD_ID'].','.$attachIndex.')" style="cursor: pointer">' .  $fileName . '</li>');
			$attachIndex++;
		}
		print('</ul></div>');
		print('</div>');
}


	$dispEmlFilesQuery = 'select * from uploads ' .
										'where upload_id = '.$_GET['UPLOAD_ID'] ;


if (empty($_GET['UPLOAD_ID'])){
	print('<div class="DbFormMessage" style="margin-top: 20px; text-align: center;" >Seleziona una Pec da visualizzare!</div>');
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