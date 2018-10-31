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

//$pecMessages = pecList::getInstance();
//
//$pecMessages->connect();
//$pecMessages->refresh();
//$listaMail = $pecMessages->getMail();
//
//
//
//$Parser = new  MimeMailParser();
//$mailToMove = array();
//
//foreach ($listaMail as $mail) {
//		$controllaProtocollazioneQuery = 'select * from arc_pratiche_pec where mail_hash = sha1("'.$mail['id'].'")';
//		$mailToMove[] = $mail['mid'];
//		if(!dbselect($controllaProtocollazioneQuery)){			
//			$rawMessage = $pecMessages->getMessage($mail['mid']);
//			$Parser->setText($rawMessage);
//			
//			$attachments = $Parser->getAttachments();
//			foreach($attachments as $attachment) {	
//				if(strtoupper($attachment->filename) == 'SUAPENTE.XML'){
//					$pecStruct = new xml2array($attachment->content);
//					$pecData = $pecStruct->getResult();
//				}
//			}
//			
//			if(is_array($pecData) and count($pecData)>0){
//				$pecDataArray = array();
//				array_walk_recursive($pecData, 'getPecData', &$pecDataArray);	
//			}
//			$mailHeader = $Parser->getHeaders();
//			
//			$mailDate = date('Y-m-d',strtotime($mailHeader['date']));
//			
//			if(!preg_match('/ACCETTAZIONE|CONSEGNA/',$mail['Oggetto'])){
//				$tipoArchiviazione = ' Importata ';
//				$status = 'U';
//			} else {
//				$tipoArchiviazione = ' Archiviata ';
//				$status = 'A';
//			}
//			
//			$insPecQuery = 'insert into arc_pratiche_pec (mail_hash, mail_id, mittente, subject, dataarrivo, status, updated, updated_by) values (';
//			$insPecQuery .= 'sha1("'.$mail['id'].'"), ';
//			$insPecQuery .= '"'.$mail['id'].'", ';
//			$insPecQuery .= '\'' . mysql_real_escape_string($mail['Mittente']) . '\', ';
//			$insPecQuery .= '\'' . mysql_real_escape_string($mail['Oggetto']) . '\', ';
//			$insPecQuery .= '\'' . mysql_real_escape_string($mailDate) . '\', ';
//			$insPecQuery .= '\'' . mysql_real_escape_string($status) . '\', ';
//			$insPecQuery .= ' now() , ';
//			$insPecQuery .= ' "1") ';
//			if(dbupdate($insPecQuery)){
//				$pecId = dbLastId();			
//				$pecFile = PEC_PATH.'/'.$pecId.'_pec_'.sha1($mail['id']).'.eml';	
//				file_put_contents($pecFile, $rawMessage);				
//			} else {
//				r('Errore' . $insPecQuery);
//			}				
//			print('INFO ' . date('Y-m-d H:m:s') . $tipoArchiviazione .' la mail -> '.$pecFile."\n");
//		} else {
//			print('INFO ' . date('Y-m-d H:m:s') . ' Mail -> '.$pecFile.' precedentemente archiviata!'."\n");
//		}
//}
//
//$pecMessages->archiviaMail($mailToMove);

// check pratiche protocollate il giorno prima dell'aggiornamento numero di protocollo

$mailDaAllegareQuery = 'SELECT * FROM arc_pratiche_pec WHERE pratica_id is null and numeroregistrazione > \' \' and dataregistrazione > \'0000-00-00\' ';

if($mailDaAllegare = dbselect($mailDaAllegareQuery)){
	foreach($mailDaAllegare['ROWS'] as $mail){
		$findPratica = 'SELECT PRATICA_ID from pratiche WHERE numeroregistrazione = "' . $mail['NUMEROREGISTRAZIONE'] . '" 
							AND dataregistrazione = "' . $mail['DATAREGISTRAZIONE'] . '" LIMIT 1';
		if( $pratica = dbselect($findPratica)){
			dbupdate('UPDATE arc_pratiche_pec set pratica_id = ' . $pratica['ROWS'][0]['PRATICA_ID'] . ' WHERE pec_id = ' . $mail['PEC_ID']);
			print('INFO ' . date('Y-m-d H:m:s') . ' allegata la mail -> al protocollo ' . $mail['NUMEROREGISTRAZIONE'] . ' del ' . $mail['DATAREGISTRAZIONE'] . "\n");
		} else {
			print('ERR ' . date('Y-m-d H:m:s') . ' non trovato il protocollo ' . $mail['NUMEROREGISTRAZIONE'] . ' del ' . $mail['DATAREGISTRAZIONE'] . "\n");
		}	
	}
}


	