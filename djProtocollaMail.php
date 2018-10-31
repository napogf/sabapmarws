<?php
/*
 * Created on 15/gen/2013
 *
 * djProtocollaMail.php
*/
include "login/autentication.php";
require_once("dbfunctions.php");
require_once('pecList.php');
require_once 'xmlToArray.inc';

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
$pecMessages->connect();

$rawMessage = $pecMessages->getMessage($_GET['MID']);

$Parser = new  MimeMailParser();
$Parser->setText($rawMessage);

$attachments = $Parser->getAttachments();
foreach($attachments as $attachment) {
	if(strtoupper($attachment->filename) == 'SUAPENTE.XML'){
		$pecStruct = new xml2array($attachment->content);
		$pecData = $pecStruct->getResult();
	}
}

if(is_array($pecData) and count($pecData)>0){
	$pecDataArray = array();
	array_walk_recursive($pecData, 'getPecData', &$pecDataArray);
}

if(!isSet($_GET['PRATICA_ID'])){
	$setQuery = ' insert into pratiche set ';
	$setQuery .= empty($pecDataArray['NOTE']) ? '' :'note = "' .  mysql_real_escape_string($pecDataArray['NOTE']) . '", ';
	$setQuery .= empty($pecDataArray['COMUNEOGG']) ? '' :'comuneogg = "' . mysql_real_escape_string($pecDataArray['COMUNEOGG']) . '", ';
	$setQuery .= empty($pecDataArray['NUMERORIFERIMENTO']) ? '' :'numeroriferimento = "' . $pecDataArray['NUMERORIFERIMENTO'] . '", ';



	$setQuery .= 'zona = '.$_GET['ZONA'].', ';
	$setQuery .= 'ufficio = '.$_GET['UFFICIO'].', ';
	//$setQuery .= 'dataarrivo = "' . $Parser->getHeader('from') . '", ';
	$setQuery .= 'tipologia = "E", ';
	$setQuery .= 'updated = now() , ';
	$setQuery .= 'updated_by = "'.$_SESSION['sess_uid'].'" ';
	if(dbupdate($setQuery)){
		$praticaId = dbLastId();
		} else {
		print('errore');
	}
} else {
	$praticaId = $_GET['PRATICA_ID'];
}
$insPecQuery = 'insert into arc_pratiche_pec (pratica_id, mail_id, mittente, subject, updated, updated_by) values (';
$insPecQuery .= '"' . $praticaId.'", ';
$insPecQuery .= '"'.$_GET['MAIL_ID'].'", ';
$insPecQuery .= '\'' . htmlspecialchars($Parser->getHeader('from')) . '\', ';
$insPecQuery .= '\'' . htmlspecialchars(mb_decode_mimeheader($Parser->getHeader('subject'))) . '\', ';
$insPecQuery .= ' now() , ';
$insPecQuery .= '"'.$_SESSION['sess_uid'].'") ';
if(dbupdate($insPecQuery)){
		dbupdate('insert into uploads (PRATICA_ID,DESCRIPTION,FILENAME,CREATED_BY,CREATED) VALUES (
				\''.$praticaId.'\',
				\'P.E.C protocollata automaticamente\',
				\''.'pec'.$praticaId.'-'.$_GET['MAIL_ID'].'.eml'.'\',
				\''.$_SESSION['sess_uid'].'\',
				now()
				)');
	$uploadId = dbLastId();
	file_put_contents(FILES_PATH.'/'.$uploadId.'_pec'.$praticaId.'-'.$_GET['MAIL_ID'].'.eml', $rawMessage);
	$pecMessages->setMailProtocollata($_GET['MAIL_ID']);
	print('protocollata');
} else {
	print('errore');
}
