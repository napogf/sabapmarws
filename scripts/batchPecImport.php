<?php
/*
 * Created on 15/gen/2013
 *
 */
include dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'login/configsess.php';
$type = isset($argv[1]) ? $argv[1] : 'PEC';
$logger = new Logger(LOG_PATH, 'importazione_pec_' . date('Ymd') . '.log');
$logger->info('Inizio importazione');
set_time_limit(0);
ini_set('memory_limit', '1024M');
$importFolder = isset($argv[1]) ? $argv[1] : 'INBOX';

$db = Db_Pdo::getInstance();

function revertDate($data)
{
    if (strpos($data, '-') > 0) {
        $data = implode('/', array_reverse(explode('-', $data)));
    } else {
        $data = implode('-', array_reverse(explode('/', $data)));
    }
    return $data;
}

try {


    $importFolder = ($type == 'PEC' ? 'INBOX' : 'protocollo');


    $logger->info('Aggiornamento db iniziato');

    $d = dir(TMP_PATH);
    $Parser = new displayMail();
    while (false !== ($entry = $d->read())) {
        try {
            $db->beginTransaction();
            $fileInfo = pathinfo($entry);
            if (strtolower($fileInfo['extension']) == 'eml') {
                $tmpFile = TMP_PATH . DIRECTORY_SEPARATOR . $entry;
                if(filesize($tmpFile) == 0){
                    unlink($tmpFile);
                    throw new Exception($tmpFile . ' ha dimensione zero non inserito a db');
                }
                $Parser->setText(file_get_contents($tmpFile));
                $mailHeader = $Parser->getHeaders();
                $mailDate = (new Date($Parser->getDataArrivo()))->toMysql();

                $subject = mb_decode_mimeheader($Parser->getHeader('subject'));
                $suapEnteTmpFile = $Parser->getAttachedFile('suapente.xml');

                $logger->info(date('Y-m-d H:i:s') . ' importazione mail -> ' . basename($fileInfo['basename'], ".eml"));
                // controllo se è stata importata
                $controllaProtocollazioneQuery = 'SELECT * FROM arc_pratiche_pec WHERE mail_hash = :mail_hash';
                $cntParams = [
                    ':mail_hash' => basename($fileInfo['basename'], ".eml"),
                ];

                if(!preg_match('/^ACCETTAZIONE:|^CONSEGNA:/',$mailHeader['subject'])){
                    $tipoArchiviazione = 'Importata ';
                    $status = 'U';
                } else {
                    $tipoArchiviazione = 'Archiviata ';
                    $status = 'A';
                }


                if ($pecRecord = $db->query($controllaProtocollazioneQuery, $cntParams)->fetch()) {
                    $PecQuery = 'UPDATE arc_pratiche_pec SET
							mittente = :mittente ,
							subject = :subject,
							dataarrivo = :dataarrivo,
							status = :status,
                            suapente =:suapente,
							updated = now(),
							updated_by = 1
						WHERE pec_id = :pec_id';
                    $params = array(
                        ':mittente' => $Parser->getHeader('from'),
                        ':subject' => $subject,
                        ':dataarrivo' => $mailDate,
                        ':status' => $status,
                        ':pec_id' => $pecRecord['PEC_ID'],
                        ':suapente' => $suapEnteTmpFile,
                    );
                    $db->query($PecQuery, $params);
                    $pecId = $pecRecord['PEC_ID'];
                    $logger->info(date('Y-m-d H:i:s') . ' Aggiornata la mail -> ' . basename($fileInfo['basename'], ".eml") . ' con id ' . $pecId . ' - ' . $subject);
                } else {
                    $PecQuery = 'INSERT INTO arc_pratiche_pec (mail_hash, mail_id, type, mittente, subject, dataarrivo, status, suapente, updated, updated_by, archiviata)
					VALUES (:mail_hash, :mail_id, :type, :mittente, :subject, :dataarrivo, :status, :suapente, now(), 1, :archiviata)';

                    $params = array(
                        ':mail_hash' => basename($fileInfo['basename'], ".eml"),
                        ':mail_id' => $mailHeader['message-id'],
                        ':type' => strtolower($type),
                        ':mittente' => $Parser->getHeader('from'),
                        ':subject' => $subject,
                        ':dataarrivo' => $mailDate,
                        ':status' => $status,
                        ':suapente' => $suapEnteTmpFile,
                        ':archiviata' => 'N',
                    );
                    $db->query($PecQuery, $params);

                    $pecId = $db->lastInsertId();
                    $logger->info(date('Y-m-d H:i:s') . ' Inserita la mail -> ' . basename($fileInfo['basename'], ".eml") . ' con id ' . $pecId . ' - ' . $subject);
                }

                if($type == 'PEC'){
                    /*
                     * Nel caso di ricevute di consegna o di mancata consegna verifico se posso
                     * allegare la PEC ad una pratica in questo modo non devo farlo manualmente
                     * e le ricevute di consegna mi vengono archiviate automaticamente
                     */
                    if(!$datiCert = $Parser->getAttachedFile('daticert.xml')){
                        $logger->error('Non trovato daticert.xml per mail MAIL_ID: ' . $mailHeader['message-id']);
                    } else {

                        $datiCertDom = new DOMDocument();
                        $datiCertDom->loadXML($datiCert);
                        $datiCertXpath = new DOMXPath($datiCertDom);
                        $msgid = $datiCertXpath->query('//postacert/dati/msgid');
                        if($msgid->length > 0){
                            /*
                             * se trovo la pratica con mail_sent_id = msgid della mail vuoldire che è una
                             * ricevuta in relazione alla risposta effettuata via PEC dalla pratica
                             */
                            $praticaOrigine = $db->query('SELECT * FROM pratiche WHERE mail_sent_id = :mail_sent_id',[
                                ':mail_sent_id' => $msgid->item(0)->nodeValue
                            ])->fetch();
                            if($praticaOrigine){
                                $status = 'A';
                                $tipoPec = $datiCertXpath->query('//postacert');
                                if($tipoPec->item(0)->getAttribute('tipo') == 'errore-consegna'){
                                    /*
                                     * setto a null mail_sent_id della pratica in modo da poter rispedire la mail
                                     * e metto lo status a S in modo che posso verificare se una ricevuta di mancata consegna
                                     * è arrivata. Quando spedirò di nuovo la mail ci saranno ricevute di mancata consegna
                                     * ma il mail_sent_id non sarà NULL
                                     */
                                    $status = 'S';
                                    $db->query('UPDATE pratiche SET MAIL_SENT_ID = NULL WHERE PRATICA_ID = :pratica_id',[
                                        ':pratica_id' => $praticaOrigine['PRATICA_ID'],
                                    ]);
                                    $logger->info('Allegata la ricevuta di mancata consegna ' .
                                        $ricevuta['PEC_ID'] .
                                        ' al protocollo ' .
                                        $praticaOrigine['NUMEROREGISTRAZIONE'] . '-' . $praticaOrigine['DATAREGISTRAZIONE']);

                                } else {
                                    $logger->info('Allegata la ricevuta di consegna ' .
                                        $ricevuta['PEC_ID'] .
                                        ' al protocollo ' .
                                        $praticaOrigine['NUMEROREGISTRAZIONE'] . '-' . $praticaOrigine['DATAREGISTRAZIONE']);
                                }
                                $db->query('UPDATE arc_pratiche_pec
                                                    SET pratica_id = :pratica_id,
                                                    numeroregistrazione = :numeroregistrazione,
                                                    dataregistrazione = :dataregistrazione,
                                                    status = :status
                                                    WHERE pec_id = :pec_id ',[
                                    ':pec_id' => $pecId,
                                    ':pratica_id' => $praticaOrigine['PRATICA_ID'],
                                    ':numeroregistrazione' => $praticaOrigine['NUMEROREGISTRAZIONE'],
                                    ':dataregistrazione' => $praticaOrigine['DATAREGISTRAZIONE'],
                                    ':status' => $status,
                                ]);

                            }
                        }


                    }
                }


                $pecFile = PEC_PATH . DIRECTORY_SEPARATOR . $pecId . '_' . strtolower($type) . '_' . $fileInfo['basename'];
                if (copy($tmpFile, $pecFile)) {
                    unlink($tmpFile);
                    $logger->info(date('Y-m-d H:i:s') . ' Importata mail -> ' . basename($fileInfo['basename'], ".eml"));
                } else {
                    $logger->error(date('Y-m-d H:i:s') . ' Errore copia file -> ' . basename($fileInfo['basename'], ".eml"));
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            $logger->error($e->getMessage() . ' | ' . $e->getFile() . ' - ' . $e->getLine());
        }

    }
    $d->close();



    $mailDaAllegareQuery = $db->query('SELECT * FROM arc_pratiche_pec 
    WHERE pratica_id IS NULL 
        AND numeroregistrazione > "" 
        AND dataregistrazione > "" ');

    while ($mail = $mailDaAllegareQuery->fetch()) {
        $findPratica = 'SELECT PRATICA_ID FROM pratiche WHERE numeroregistrazione = :numeroregistrazione
        AND dataregistrazione = :dataregistrazione LIMIT 1';
        // echo $findPratica;
        if ($pratica = $db->query($findPratica, [
            ':numeroregistrazione' => str_pad(trim($mail['NUMEROREGISTRAZIONE']), 7, '0'),
            ':dataregistrazione' => trim($mail['DATAREGISTRAZIONE']),
        ])->fetch()
        ) {
            $db->query('UPDATE arc_pratiche_pec SET pratica_id = :pratica_id WHERE pec_id = :pec_id', [
                ':pratica_id' => $pratica['PRATICA_ID'],
                ':pec_id' => $mail['PEC_ID'],
            ]);
            $logger->info(date('Y-m-d H:i:s') . ' allegata la mail -> al protocollo ' . $mail['NUMEROREGISTRAZIONE'] . ' del ' . $mail['DATAREGISTRAZIONE']);
        }
    }

    $mailDaAllegareQuery = $db->query('SELECT * FROM arc_pratiche_pec 
    WHERE pratica_id IS NULL 
        AND numeroregistrazione > "" 
        AND dataregistrazione > "" ');

    while ($mail = $mailDaAllegareQuery->fetch()) {
        $findPratica = 'SELECT PRATICA_ID FROM pratiche WHERE numeroregistrazione = :numeroregistrazione
        AND dataregistrazione = :dataregistrazione LIMIT 1';
        // echo $findPratica;
        if ($pratica = $db->query($findPratica, [
            ':numeroregistrazione' => str_pad(trim($mail['NUMEROREGISTRAZIONE']), 7, '0'),
            ':dataregistrazione' => trim($mail['DATAREGISTRAZIONE']),
        ])->fetch()
        ) {
            $db->query('UPDATE arc_pratiche_pec SET pratica_id = :pratica_id WHERE pec_id = :pec_id', [
                ':pratica_id' => $pratica['PRATICA_ID'],
                ':pec_id' => $mail['PEC_ID'],
            ]);
            $logger->info(date('Y-m-d H:i:s') . ' allegata la mail -> al protocollo ' . $mail['NUMEROREGISTRAZIONE'] . ' del ' . $mail['DATAREGISTRAZIONE']);
        }
    }

    // aggancio tramite PEC_ID

    $pecRegExp = '/^PEC[\s|\-|_]ID[\s|\-|_]([0-9]{1,})[\s|\-|:](.*)/i';

    $praticheDaAllegare = $db->query('SELECT arc_pratiche_pec.pec_id, pratiche.pratica_id, pratiche.numeroregistrazione, pratiche.dataregistrazione, pratiche.oggetto
                FROM pratiche
        left join arc_pratiche_pec on (arc_pratiche_pec.pratica_id = pratiche.pratica_id)
                WHERE oggetto like "PEC_ID%" and arc_pratiche_pec.pec_id is null order by 2 desc');
    while($pratica = $praticheDaAllegare->fetch()){
        preg_match_all($pecRegExp,$pratica['oggetto'],$pecMail);
        if(isSet($pecMail[1][0]) and (integer) $pecMail[1][0] > 0){
            $db->query('update arc_pratiche_pec set
                                                        pratica_id = :pratica_id,
                                                        numeroregistrazione = :numeroregistrazione,
                                                        dataregistrazione = :dataregistrazione,
                                                        status = :status
                                        where pec_id = :pec_id',[
                ':pec_id' => (integer) $pecMail[1][0],
                ':pratica_id' => $pratica['pratica_id'],
                ':numeroregistrazione' => $pratica['numeroregistrazione'],
                ':dataregistrazione' => $pratica['dataregistrazione'],
                ':status' => 'P',
            ]);
            $logger->info(' allegata la mail -> al protocollo ' . $pratica['numeroregistrazione'] . ' del ' . $pratica['dataregistrazione']);
        } else {
            $logger->error('Non allegata la mail ' . $pratica['oggetto'] . ' -> al protocollo ' . $pratica['pratica_id'] . ' - ' . $pratica['numeroregistrazione'] . ' del ' . $pratica['dataregistrazione']);
        }

    }



    $logger->info('Aggiornamento db terminato');
    $logger->info('Importazione terminata');


} catch (Exception $e) {
    $logger->error($e->getMessage() . ' | ' . $e->getFile() . ' - ' . $e->getLine());
    $logger->error('Importazione terminata con errori');

}
