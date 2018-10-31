<?php
/*
 * Created on 15/gen/2013
 *
 * djProtocollaMail.php  
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once('pecList.php');
//require_once 'xmlToArray.inc';

function revertDate($data){
	if(strpos($data,'-')>0){
		$data = implode('/',array_reverse(explode('-', $data)));
	} else {
		$data = implode('-',array_reverse(explode('/', $data)));
	}
	return $data;	
}

function getPecData($element, $key, $pecDataArray){
	switch ($key) {
		case 'oggetto-comunicazione':
			$pecDataArray['COMUNEOGG'] = $element;
			break;
		case 'testo-comunicazione':
			$pecDataArray['NOTE'] = $element;
			break;
		case 'codice-pratica':
			$pecDataArray['NUMERORIFERIMENTO'] = $element;
			break;
	}	
	return $pecDataArray;
}

$pecMessages = pecList::getInstance();

$pecMessages->connectArchiviati();
$pecMessages->refresh();
$listaMail = $pecMessages->getMail();

$mailToMove = array();

foreach ($listaMail as $mail) {
		$mailToMove[] = $mail['mid'];
		r($mail,false);
}


$pecMessages->ripristinaMail($mailToMove);




	