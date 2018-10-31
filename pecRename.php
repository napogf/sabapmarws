<?php
/*
 * Created on 04/mag/2013
 *
 * pecRename.php  
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
$pecDir = '/home/soprive/pratiche/sbapvel/pecmail';
var_dump(is_dir($pecDir));

$d = dir($pecDir);

echo "Handle: " . $d->handle . "\n";
echo "Path: " . $d->path . "\n";
while (false !== ($entry = $d->read())) {
	// if(preg_match_all('|(.*)(<.*>)(.*)|',$entry,$matches)){
		
		 // if($pec = dbselect('select PEC_ID, MAIL_HASH from arc_pratiche_pec where mail_id = \''.$matches[2][0].'\'')){
			
			// rename($pecDir.'/'.$entry, $pecDir.'/'.$pec['ROWS'][0]['PEC_ID'].'_pec_'.$pec['ROWS'][0]['MAIL_HASH'].'.eml');
		 // } else {
			// r($pecDir.'/'.$matches[1][0].$matches[3][0]);
		 // }
		
		
	// }




	if(preg_match_all('|(.*-)(.*)(\.eml)|',$entry, $matches)){
		
		
		 if($pec = dbselect('select PEC_ID from arc_pratiche_pec where mail_hash = \''.$matches[2][0].'\'')){
			rename($pecDir.'/'.$entry, $pecDir.'/'.$pec['ROWS'][0]['PEC_ID'].'_pec_'.$matches[2][0].'.eml');
		 } else {
			r($pecDir.'/'.$matches[1][0].$matches[3][0]);
		 }
		 // r($pec['ROWS'][0]['PEC_ID'].'_pec_'.$matches[2][0].'.eml');
		// r($pecDir.'/'.$matches[1][0].$matches[3][0]);
		
	}
   echo $entry."<br />\n";
}
$d->close();
