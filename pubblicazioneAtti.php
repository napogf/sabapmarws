<?php
include "login/autentication.php";
$logFile = 'generaXmlTrasparenza'.date('YmdHis').'.log';
$logger = new Logger(LOG_PATH,$logFile);


$db = Db_Pdo::getInstance();
$file = 'trasparenza_' . date('Ymd') . '.zip';
$fileName = TRASPARENZA_PATH . DIRECTORY_SEPARATOR . $file;

$provvedimentoStmt = $db->query ( 'SELECT
			arc_modelli.description,
			pratiche.pratica_id,
			pratiche.numeroregistrazione as protentrata,
			pratiche.dataregistrazione as dataentrata,
			pratiche.uscita,
			pratiche.dataarrivo,
			pratiche.oggetto,
			pratiche.protuscita,
			trim(concat(ifnull(pratiche.nome," "), " " , pratiche.cognome)) as nominativo,
			pratiche.pnome as proprietario,
			concat(uploads.upload_id,"_",uploads.filename) as filename,
			uploads.upload_id,
			uploads.ambito,
			uploads.settore,
			uploads.natura_atto,
			uploads.a_b,
			uploads.tipo_procedimento
			FROM pratiche
			LEFT JOIN arc_modelli ON (arc_modelli.modello = pratiche.modello)
			RIGHT JOIN uploads ON (
				uploads.pratica_id = pratiche.pratica_id
			)
			WHERE uploads.pubblicato_mibact = "N"
			AND uploads.pubblica = "Y"
		' );
try {
	$zip = new ZipArchive();
	$dom = new DOMDocument('1.0', 'UTF-8');
	$xmlRoot = $dom->createElement("provvedimenti_trasparenza");
	$xmlRoot = $dom->appendChild($xmlRoot);

	if($zip->open($fileName,ZipArchive::CREATE)){
		while ($provvedimento = $provvedimentoStmt->fetch()){
			/*
			 * Verifico la compilazione dei campi necessari alla publicazione sul sito del mibact
			 */

			if(empty($provvedimento['ambito']) OR
					empty($provvedimento['settore']) OR
					empty($provvedimento['natura_atto']) OR
					empty($provvedimento['a_b']) OR
					empty($provvedimento['tipo_procedimento'])){
				$logger->alert('Per il protocollo '  . $provvedimento['protentrata'] . ' upload_id: ' . $provvedimento['upload_id'] . ' mancano dati necessari alla pubblicazione!');
				continue;
			}
			/*
			 * Ceerco il provvedimento in uscita se lo trovo ma con data < di quello in entrata vuol dire che devo
			 * cecarlo per nr protocollo
			 */
			if(!empty($provvedimento['protuscita'])){
				$protuscita = $db->query('SELECT numeroregistrazione, dataregistrazione FROM pratiche WHERE pratica_id = :protuscita',[
						':protuscita' => $provvedimento['protuscita']
				])->fetch();
				if(!$protuscita or
						((new Date($provvedimento['dataentrata'])) > (new Date($protuscita['dataregistrazione'])))
				){
					/* cerco per protocollo */
					$protuscita = $db->query('SELECT numeroregistrazione, dataregistrazione FROM pratiche
				WHERE numeroregistrazione REGEXP :protuscita AND dataregistrazione = :uscita',[
							':protuscita' => $provvedimento['protuscita'],
							':uscita' => $provvedimento['uscita']
					])->fetch();
				}
			}
			if(!empty($protuscita['numeroregistrazione'])
					AND !empty($protuscita['dataregistrazione'])){
				if (! file_exists ( FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'] )) {
					$logger->alert('Non trovato il file ' . FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'] );
				} else {
					/*
					 * Aggiungo il pdf al file ZIP
					 */
					$zip->addFile(FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'], $provvedimento['upload_id'] . '_' . $protuscita ['numeroregistrazione'] .'_'. substr($protuscita ['dataregistrazione'],0,4) . '.pdf');
					/*
					 * Aggiungo in nodo provvedimento
					*/
					$provvedimentoEl = $dom->createElement('provvedimento');
					$provvedimentoEl = $xmlRoot->appendChild($provvedimentoEl);

					$pecIdRegExp = '/^PEC[\s|\-|_]ID[\s|\-|_][0-9]{1,}[\s|\-|:]/i';
					$oggetto = preg_replace($pecIdRegExp, '', $provvedimento ['oggetto']);
					if($oggetto > ''){
						$oggetto = substr($oggetto, 0,99); // 99 caratteri
					} else {
						$oggetto = substr($provvedimento ['oggetto'], 0,99); // 99 caratteri
					}
					$post ['istituto'] = 415;
					$post ['ambito'] = $provvedimento['ambito'];
					$post ['settore'] = $provvedimento['settore'];
					$post ['data_richiesta'] = (new Date ( $provvedimento ['dataarrivo'] ))->toReadable ();
					$post ['tipo_procedimento'] = $provvedimento['tipo_procedimento']; // Verificare elenco procedimenti
					$post ['natura_atto'] = $provvedimento['natura_atto'];
					$post ['protocollo_atto'] = $protuscita ['numeroregistrazione'];
					$post ['data_atto'] = (new Date ( $protuscita ['dataregistrazione'] ))->toReadable ();
					$post ['descrizione'] = $oggetto;
					$post ['a_b'] = $provvedimento['a_b']; // Pubblico / Privato
					$post ['nominativo'] = $provvedimento ['nominativo'] . (empty($provvedimento['proprietario']) ? '' : ' - ' . $provvedimento['proprietario']);
					$post ['responsabile_adozione'] = Db_Pdo::getInstance ()->query ( 'select valore FROM sys_config where chiave = "KEY_SOPRINTENDENTE"' )->fetchColumn ();
					$post ['decreto'] = $provvedimento['upload_id'] . '_' .$protuscita ['numeroregistrazione'] .'_'. substr($protuscita ['dataregistrazione'],0,4) . '.pdf';
					foreach ($post as $key => $value) {
						$protEl = $dom->createElement($key,$value);
						$protEl = $provvedimentoEl->appendChild($protEl);
                    }
                    // Loggo l'inserimento
                    $logger->info('Trasferito il file ' . FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'] .
                    ' | ' .$provvedimento['protentrata'] . ' del ' . $provvedimento['dataentrata']);
                    $db->query('UPDATE uploads set pubblicato_mibact = "Y" WHERE upload_id = :upload_id ',[':upload_id' => $provvedimento['upload_id'] ]);
				}
			} else {
				$logger->alert('Non trovato protocollo di uscita per la pratica ' . $provvedimento['protentrata'] . ' del ' . $provvedimento['dataentrata']);
			}
		}

	}
	$zip->addFromString('provvedimentiTrasparenza.xml', $dom->saveXML());
	$zip->close();
} catch (Exception $e) {
	$logger->critical($e->getTraceAsString());
	r($e->getTrace());
}




header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"". $file ."\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($fileName));
while (ob_get_level()) {
	ob_end_clean();
}
@readfile($fileName);
exit;