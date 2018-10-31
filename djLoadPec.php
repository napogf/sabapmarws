<?php
/*
 * Created on 30/set/2012
 *
 * djLoadPec.php  
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
if(empty($_GET['UPLOAD_ID'])){
	print('<div class="DbFormMessage" style="margin-top: 20px; text-align: center;" >Seleziona una Pec da visualizzare!</div>');
} else {
	print('<div dojoType="dijit.layout.ContentPane" id="dispPecs" style="margin-top: 20px;" href="djDisplayPec.php?UPLOAD_ID=' . $_GET['UPLOAD_ID'] . '" >');
	print ('</div>');		
}

?>