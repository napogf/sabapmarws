<?php
class XmlTrasparenza {


	protected $file;
	protected $fileName;

	protected $provvedimentoStmt;

	protected $logger;

	protected $db;

	protected $debug = false;

	protected $webAddress = 'web@beniculturali.it';
	protected $sbapAddress = 'sabap-vr@beniculturali.it';
	protected $logAddress = 'felicegiuseppe.romano@beniculturali.it';

	protected $logFile;


	public function __construct(){

		$this->logFile = 'generaXmlTrasparenza'.date('YmdHis').'.log';
		$this->logger = new Logger(LOG_PATH,$this->logFile);


		$this->db = Db_Pdo::getInstance();
		$this->file = 'trasparenza_' . date('Ymd') . '.zip';
		$this->fileName = TRASPARENZA_PATH . DIRECTORY_SEPARATOR . $this->file;

		$this->provvedimentoStmt = $this->db->query ( 'SELECT
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
			AND pratiche.dataregistrazione >= "2016-01-01"
		' );


		return $this;
	}

	public function setDebug($value = true){
		$this->debug = $value;

		return $this;
	}

	public function generateZipFile(){
		try {
			$this->db->beginTransaction();
			$zip = new ZipArchive();
			$dom = new DOMDocument();
			$dom->encoding = 'utf-8';
			$xmlRoot = $dom->createElement("provvedimenti_trasparenza");
			$xmlRoot = $dom->appendChild($xmlRoot);

			if($zip->open($this->fileName,ZipArchive::CREATE)){
				$limiteProcedimenti = 0;
				while ($provvedimento = $this->provvedimentoStmt->fetch()){
					/*
					 * Verifico la compilazione dei campi necessari alla publicazione sul sito del mibact
					 */

					if(empty($provvedimento['ambito']) OR
							empty($provvedimento['settore']) OR
							empty($provvedimento['natura_atto']) OR
							empty($provvedimento['a_b']) OR
							empty($provvedimento['tipo_procedimento'])){
						$this->logger->alert('Per il protocollo '  . $provvedimento['protentrata'] . ' data ' . $provvedimento['dataentrata'] . ' upload_id: ' . $provvedimento['upload_id'] . ' mancano dati necessari alla pubblicazione!');
						continue;
					}
					/*
					 * Ceerco il provvedimento in uscita se lo trovo ma con data < di quello in entrata vuol dire che devo
					 * cecarlo per nr protocollo
					 */
					if(!empty($provvedimento['protuscita'])){
						$protuscita = $this->db->query('SELECT numeroregistrazione, dataregistrazione FROM pratiche WHERE pratica_id = :protuscita',[
								':protuscita' => $provvedimento['protuscita']
						])->fetch();
						if(!$protuscita or
								((new Date($provvedimento['dataentrata'])) > (new Date($protuscita['dataregistrazione'])))
						){
							/* cerco per protocollo */
							$protuscita = $this->db->query('SELECT numeroregistrazione, dataregistrazione FROM pratiche
					WHERE numeroregistrazione REGEXP :protuscita AND dataregistrazione = :uscita',[
									':protuscita' => $provvedimento['protuscita'],
									':uscita' => $provvedimento['uscita']
							])->fetch();
						}
					}
					if(!empty($protuscita['numeroregistrazione'])
							AND !empty($protuscita['dataregistrazione'])){
						if (! file_exists ( FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'] )) {
							$this->logger->alert('Non trovato il file ' . FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'] );
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
							$post ['descrizione'] = htmlspecialchars($oggetto);
							$post ['a_b'] = $provvedimento['a_b']; // Pubblico / Privato
							$post ['nominativo'] = htmlspecialchars($provvedimento ['nominativo'] . (empty($provvedimento['proprietario']) ? '' : ' - ' . $provvedimento['proprietario']));
							$post ['responsabile_adozione'] = Db_Pdo::getInstance ()->query ( 'select valore FROM sys_config where chiave = "KEY_SOPRINTENDENTE"' )->fetchColumn ();
							$post ['decreto'] = $provvedimento['upload_id'] . '_' .$protuscita ['numeroregistrazione'] .'_'. substr($protuscita ['dataregistrazione'],0,4) . '.pdf';
							foreach ($post as $key => $value) {
								$protEl = $dom->createElement($key,$value);
								$protEl = $provvedimentoEl->appendChild($protEl);
							}
							// Loggo l'inserimento
							$this->logger->info('Trasferito il file ' . FILES_PATH . DIRECTORY_SEPARATOR . $provvedimento ['filename'] .
									' | ' .$provvedimento['protentrata'] . ' del ' . $provvedimento['dataentrata']);
							if($this->debug === false){
								$this->db->query('UPDATE uploads set pubblicato_mibact = "Y" WHERE upload_id = :upload_id ',[':upload_id' => $provvedimento['upload_id'] ]);
							}
							$limiteProcedimenti++;
						}
					} else {
						$this->logger->alert('Non trovato protocollo di uscita per la pratica ' . $provvedimento['protentrata'] . ' del ' . $provvedimento['dataentrata']);
					}
					if($limiteProcedimenti >= 40){
						break;
					}
				}

			}
			$zip->addFromString('provvedimentiTrasparenza.xml', $dom->saveXML());
			$zip->close();
 			$this->sendMail();
 			$this->sendLog();
			$this->db->commit();
		} catch (phpmailerException $e) {
			$this->db->rollBack();
			r($e->getMessage());
		} catch (Exception $e) {
			$this->db->rollBack();
			r($e->getMessage(),false);
			r($e->getTrace());
		}


		return $this;
	}

	public function sendLog(){
		$mail = new MailSender();

		$mail->CharSet = 'UTF-8';
		if($this->debug){
			$mail->addAddress('giacomo.fonderico@gmail.com');
			$mail->Subject = 'TEST - Spedizione Log';
			$mail->SMTPDebug = 1;
		} else {
			$mail->addAddress($this->logAddress);

			$mail->Subject = 'Spedizione Log';
		}

		$mail->WordWrap = 80;

		$mail->isHTML(false);                                  // Set email format to HTML


		$mail->Body    = 'Log spedizione file trasparenza';
		$mail->AltBody = 'Log spedizione file trasparenza';

		$mail->addAttachment(LOG_PATH . DIRECTORY_SEPARATOR .$this->logFile);
		$mail->send();

	}

	public function sendMail(){

		try {
			$mail = new MailSender();

			$mail->CharSet = 'UTF-8';
			if($this->debug){
 				$mail->addAddress('giacomo.fonderico@gmail.com');
//				$mail->addAddress('felicegiuseppe.romano@beniculturali.it');
				$mail->Subject = 'TEST - Spedizione procedimenti trasparenza SABAP-VR';
				$mail->SMTPDebug = 1;
			} else {
				$mail->addAddress($this->webAddress);
//				$mail->addCC($this->sbapAddress);
				$mail->Subject = 'Spedizione procedimenti trasparenza SABAP-VR';
			}

			$mail->WordWrap = 80;

			$mail->isHTML(false);                                  // Set email format to HTML


			$mail->Body    = 'In riferimento a quanto in oggetto si trasmette l\'allegato file xml contenente i provvedimenti da caricare nel form del MiBACT  Trasparenza - Inserimento provvedimenti.' . "\n" .
					'Si ringrazia per la collaborazione.';
			$mail->AltBody = 'In riferimento a quanto in oggetto si trasmette l\'allegato file xml contenente i provvedimenti da caricare nel form del MiBACT  Trasparenza - Inserimento provvedimenti.' . "\n" .
					'Si ringrazia per la collaborazione.';
			$mail->addAttachment($this->fileName);
			$mail->send();
			if($mail->isError()){
				throw new Exception('Mail in errore ' . $mail->ErrorInfo);
			}
			$mail->clearAddresses();
            $mail->addAddress($this->sbapAddress);
            $mail->send();
            if($mail->isError()){
                throw new Exception('Mail in errore ' . $mail->ErrorInfo);
            }
        } catch (phpmailerException $e) {
            $this->logger->critical($e->errorMessage()); //Pretty error messages from PHPMailer

		} catch (Exception $e) {
			$this->logger->critical($e->getMessage());
			r($e->getTrace());
		}


		return $this;


	}


	public function testMail(){

		try {


			$mail = new MailSender();

			$mail->CharSet = 'UTF-8';
            $mail->addAddress($this->sbapAddress);
			$mail->addCC('giacomo.fonderico@gmail.com');
			$mail->WordWrap = 80;

			$mail->isHTML(false);                                  // Set email format to HTML

			$mail->Subject = 'Trasparenza - Test trasmissione mail';
			$mail->Body    = 'Test mail spedizione procedimenti trasparenza SABAP-VR';
			$mail->AltBody = 'Test mail procedimenti trasparenza SABAP-VR';
            $this->fileName = TRASPARENZA_PATH . DIRECTORY_SEPARATOR . 'trasparenza_20170828.zip';
            $mail->addAttachment($this->fileName);
			if(!$mail->send()) {
				throw new Exception($mail->ErrorInfo);
			}
        } catch (phpmailerException $e) {
            r($e->errorMessage()); //Pretty error messages from PHPMailer

		} catch (Exception $e) {
			r($e->getMessage());
		}

		return $this;
	}




}