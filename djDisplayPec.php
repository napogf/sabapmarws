<?php
/*
 * Created on 01/ott/2012
 *
 * djDisplayPec.php  
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once('displayMail.php');



	$dispEmlFilesQuery = 'select * from arc_pratiche_pec ' .
										'where pec_id = '.$_GET['PEC_ID'] ;
	
	
if (empty($_GET['PEC_ID'])){
	print('<div class="DbFormMessage" style="margin-top: 20px; text-align: center;" >Selezionare una mail da visualizzare!</div>');
} else {
	if(! $emlResult = dbselect($dispEmlFilesQuery)){
		print('<div class="DbFormMessage">Attenzione! File non trovato contattare l\'assistenza</div>');
	} else {
		
		$pecFile = PEC_PATH . '/' . $emlResult['ROWS'][0]['PEC_ID']."_pec_".$emlResult['ROWS'][0]['MAIL_HASH'].'.eml';
		
		$Parser = new displayMail();
		$Parser->setText(file_get_contents($pecFile));
		$Parser->viewMail($_GET['PEC_ID']);		
		
	}
}
