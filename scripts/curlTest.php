<?php
include ('../login/configsess.php');
$username = 'felicegiuseppe.romano@beniculturali.it';
$password = 'Felix001';
$loginUrl = 'http://10.96.0.6/provvedimenti_trasparenza/index.asp';
// $loginUrl = 'http://sbapverona.localnet/testCurl.php';





$db = Db_Pdo::getInstance();

$provvedimento = $db->query ( 'SELECT
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
			uploads.upload_id
			FROM pratiche
			LEFT JOIN arc_modelli ON (arc_modelli.modello = pratiche.modello)
			RIGHT JOIN uploads ON (
				uploads.pratica_id = pratiche.pratica_id
			)
			WHERE uploads.pubblicato_mibact = "N"
			AND uploads.pubblica = "Y"
			AND pratiche.modello = 41
		LIMIT 1
		' )->fetch ();

r($provvedimento,false);
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

if (! file_exists ( FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'] )) {
	r ( $provvedimento ['filename'] );
}


$pecIdRegExp = '/^PEC[\s|\-|_]ID[\s|\-|_][0-9]{1,}[\s|\-|:]/i';
$oggetto = preg_replace($pecIdRegExp, '', $provvedimento ['oggetto']);
if($oggetto > ''){
	$oggetto = substr($oggetto, 0,99); // 99 caratteri
} else {
	$oggetto = substr($provvedimento ['oggetto'], 0,99); // 99 caratteri
}


$provvedimento ['filetoupload'] = new CurlFile ( FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'], 'application/pdf', $provvedimento ['filename'] );
$post ['istituto'] = 415;
$post ['ambito'] = 'Tutela';
$post ['settore'] = 'Paesaggistico';
$post ['data_richiesta'] = (new Date ( $provvedimento ['dataarrivo'] ))->toReadable ();
$post ['tipo_procedimento'] = 165;
$post ['natura_atto'] = 'AUTORIZZAZIONE';
$post ['protocollo_atto'] = $protuscita ['numeroregistrazione'];
$post ['data_atto'] = (new Date ( $protuscita ['dataregistrazione'] ))->toReadable ();
$post ['descrizione'] = $oggetto;
$post ['a_b'] = 'Privato'; // Pubblico / Privato
$post ['nominativo'] = $provvedimento ['nominativo'] . (empty($provvedimento['proprietario']) ? '' : ' - ' . $provvedimento['proprietario']);
$post ['responsabile_adozione'] = Db_Pdo::getInstance ()->query ( 'select valore FROM sys_config where chiave = "KEY_SOPRINTENDENTE"' )->fetchColumn ();
$post ['submit'] = 'INVIA I DATI';

r($post,false);

$postFile ['file1'] = new CurlFile ( FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'], 'application/pdf', $provvedimento ['filename'] );
$postFile ['Enter'] = 'ALLEGA';

r($postFile,false);


$ch = curl_init ();

// Set the URL to work with
curl_setopt ( $ch, CURLOPT_URL, $loginUrl );

// ENABLE HTTP POST
curl_setopt ( $ch, CURLOPT_POST, 1 );


// Set the post parameters
curl_setopt ( $ch, CURLOPT_POSTFIELDS, 'email=' . $username . '&password=' . $password . '&Submit=Accedi' );
// Handle cookies for the login
curl_setopt ( $ch, CURLOPT_COOKIEJAR, 'cookie.txt' );

// Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
// not to print out the results of its query.
// Instead, it will return the results as a string return value
// from curl_exec() instead of the usual true/false.
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
$store = curl_exec ( $ch );

// Follow Location redirects
curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );

// execute the request (the login)
$store = curl_exec ( $ch );
r(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),false);
if (preg_match ( '/modulo_file\.asp/', $store )) {
	$formUrl = 'http://10.96.0.6/provvedimenti_trasparenza/2015/modulo_file.asp';
	curl_setopt ( $ch, CURLOPT_URL, $formUrl );
	$form = curl_exec ( $ch );
	r(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),false);
	curl_close($ch);
exit;
	if (preg_match ( '/ALLEGA FILE/', $form )) {
		$uploadUrl = 'http://10.96.0.6/provvedimenti_trasparenza/2015/modulo_upload.asp';
		curl_setopt ( $ch, CURLOPT_URL, $uploadUrl );
		/*
		 * File upload
		 */
		curl_setopt ( $ch, CURLOPT_POSTFIELDS,$postFile);
		$uploadForm = curl_exec ( $ch );
		/*
		 * reindirizzamento a form ok.asp
		 * Attesa stringa  OK - I TUOI DATI SONO STATI CORRETTAMENTE INSERITI
		 *  e pagina di redirect http://10.96.0.6/provvedimenti_trasparenza/2015/ok.asp
		 */
		if(curl_getinfo($ch,CURLINFO_REDIRECT_URL) == $formUrl){
			curl_setopt ( $ch, CURLOPT_URL, $formUrl );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS,$post);
			$resultForm = curl_exec ( $ch );
			if(curl_getinfo($ch,CURLINFO_REDIRECT_URL) == 'http://10.96.0.6/provvedimenti_trasparenza/2015/ok.asp')	{
				$db->query('UPDATE uploads SET pubblicato_mibact = "Y" WHERE upload_id = :upload_id'.[
						':upload_id' => $provvedimento['upload_id']
				]);
			}
		}
	}

}

curl_close($ch);