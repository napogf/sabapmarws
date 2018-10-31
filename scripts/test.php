<?php
include ('../login/configsess.php');
$username = 'felicegiuseppe.romano@beniculturali.it';
$password = 'Felix001';
$loginUrl = 'http://10.96.0.6/provvedimenti_trasparenza/index.asp';
// $loginUrl = 'http://sbapverona.localnet/testCurl.php';





$db = Db_Pdo::getInstance();

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
			uploads.upload_id
			FROM pratiche
			LEFT JOIN arc_modelli ON (arc_modelli.modello = pratiche.modello)
			RIGHT JOIN uploads ON (
				uploads.pratica_id = pratiche.pratica_id
			)
			WHERE uploads.pubblicato_mibact = "N"
			AND uploads.pubblica = "Y"
			AND pratiche.modello = 41
		' );



/*
 * Ceerco il provvedimento in uscita se lo trovo ma con data < di quello in entrata vuol dire che devo
 * cecarlo per nr protocollo
 */
while ($provvedimento = $provvedimentoStmt->fetch()){
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
			r ( $provvedimento ['filename'] );
		} else {
			break;
		}
	}

}





$pecIdRegExp = '/^PEC[\s|\-|_]ID[\s|\-|_][0-9]{1,}[\s|\-|:]/i';
$oggetto = preg_replace($pecIdRegExp, '', $provvedimento ['oggetto']);
if($oggetto > ''){
	$oggetto = substr($oggetto, 0,90); // 99 caratteri
} else {
	$oggetto = substr($provvedimento ['oggetto'], 0,90); // 99 caratteri
}


$provvedimento ['filetoupload'] = new CurlFile ( FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'], 'application/pdf', $provvedimento ['filename'] );



$post = array(
    'MM_insert' => 'form1',
    'a_b' => 'Privato', // Pubblico / Privato
    'ambito' => 'Tutela',
    'data' => date('d/m/Y G.i.s'),
    'data_atto' => substr((new Date ( $protuscita ['dataregistrazione'] ))->toReadable (),0,10),
    'data_richiesta' => substr((new Date ( $provvedimento ['dataarrivo'] ))->toReadable (),0,10),
    'decreto' => '',
    'descrizione' => $oggetto,
    'istituto' => 415,
    'natura_atto' => 'AUTORIZZAZIONE',
    'nominativo' => $provvedimento ['nominativo'] . (empty($provvedimento['proprietario']) ? '' : ' - ' . $provvedimento['proprietario']),
    'protocollo_atto' => $protuscita ['numeroregistrazione'],
    'responsabile_adozione' => Db_Pdo::getInstance ()->query ( 'select valore FROM sys_config where chiave = "KEY_SOPRINTENDENTE"' )->fetchColumn (),
    'settore' => 'Paesaggistico',
    'submit' => 'INVIA I DATI',
    'tipo_procedimento' => 165,
    'utente' => 'felicegiuseppe.romano@beniculturali.it'
);



$postFile ['file1'] = new CurlFile ( FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'], 'application/pdf', $provvedimento ['filename'] );
$postFile ['Enter'] = 'ALLEGA';

r($postFile,false);


$ch = curl_init ();
$options = [
		CURLOPT_COOKIEJAR => __DIR__ . DIRECTORY_SEPARATOR . 'cookie.txt',
		CURLOPT_COOKIEFILE => __DIR__ . DIRECTORY_SEPARATOR . 'cookie.txt',
		CURLOPT_VERBOSE => 1,
		CURLINFO_HEADER_OUT => 1,
		CURLOPT_HEADER => 1,
		CURLOPT_POST => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FOLLOWLOCATION => 1
	];

curl_setopt_array($ch, $options);

// Set the URL to work with
curl_setopt ( $ch, CURLOPT_URL, $loginUrl );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, 'email=' . $username . '&password=' . $password . '&Submit=Accedi' );
// Handle cookies for the login



$store = curl_exec ( $ch );


r(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),false);
if (preg_match ( '/modulo_file\.asp/', $store )) {
	$formUrl = 'http://10.96.0.6/provvedimenti_trasparenza/2015/modulo_file.asp';
	curl_setopt ( $ch, CURLOPT_URL, $formUrl );
	curl_setopt_array($ch, $options);
	$form = curl_exec ( $ch );
// Read the session saved in the cookie file

	r(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),false);
	if (preg_match ( '/ALLEGA FILE/', $form )) {
		curl_setopt_array($ch, $options);
		$moduloUploadUrl = 'http://10.96.0.6/provvedimenti_trasparenza/2015/modulo_upload.asp';
		curl_setopt ( $ch, CURLOPT_URL, $moduloUploadUrl );
        $paginaUpload = curl_exec($ch);
		/*
		 * File upload
		 */
		/*
		 * reindirizzamento a form ok.asp
		 * Attesa stringa  OK - I TUOI DATI SONO STATI CORRETTAMENTE INSERITI
		 *  e pagina di redirect http://10.96.0.6/provvedimenti_trasparenza/2015/ok.asp
		 */
        r($postFile,false);
        r(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),false);
        $uploadUrl = 'http://10.96.0.6/provvedimenti_trasparenza/2015/upload.asp';
        curl_setopt_array($ch, $options);
		curl_setopt ( $ch, CURLOPT_URL, $uploadUrl );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS,$postFile);
		$fileUploaded = curl_exec($ch);
        r(curl_getinfo( $ch ),false);
		r(curl_getinfo($ch,CURLINFO_REDIRECT_URL),false);
		if(preg_match('/RIMUOVI ALLEGATO/',$fileUploaded)){
			r('Torno nella Form',false);
            r($formUrl,false);
            $formArray = explode("\n", $fileUploaded);
            foreach ($formArray as $line) {
            	if(preg_match('/FILE ALLEGATO :(.*)/',$line,$match)){
            		$post['decreto'] = 'files/' . trim($match[1]);
            		break;
            	}
            }

            r($post,false);
            curl_setopt_array($ch, $options);
            curl_setopt ( $ch, CURLOPT_URL, 'http://10.96.0.6/provvedimenti_trasparenza/2015/modulo_file.asp' );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS,$post);
			$resultForm = curl_exec ( $ch );
            r(curl_errno( $ch ),false);
            r(curl_error( $ch ),false);
            r(curl_getinfo( $ch ),false);
            r($resultForm,false);
            r(curl_getinfo($ch,CURLINFO_REDIRECT_URL),false);
			if(curl_getinfo($ch,CURLINFO_REDIRECT_URL) == 'http://10.96.0.6/provvedimenti_trasparenza/2015/ok.asp')	{
				$db->query('UPDATE uploads SET pubblicato_mibact = "Y" WHERE upload_id = :upload_id'.[
						':upload_id' => $provvedimento['upload_id']
				]);
            }
            r(curl_error($ch),false);
		}
	}
}
curl_close($ch);
