<?php
include dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'login/configsess.php';

set_time_limit(0);
ini_set('memory_limit', '512M');
$logger = new Logger(LOG_PATH,'allegaRicevuteConsegnaPec.log');

$db = Db_Pdo::getInstance();
$ricevuteDiAccettazioneConsegna = $db->query('SELECT * FROM arc_pratiche_pec WHERE pratica_id is null AND subject regexp "ACCETTAZIONE:|CONSEGNA:"');

while($ricevuta = $ricevuteDiAccettazioneConsegna->fetch()){
    $Parser = new displayMail();
    $consegnaFile = PEC_PATH . DIRECTORY_SEPARATOR . $ricevuta['PEC_ID'] . '_pec_' . $ricevuta['MAIL_HASH'] . '.eml';
    if(file_exists($consegnaFile)){
        try {
            $Parser->setText(file_get_contents($consegnaFile));
            /*
             * Estrarre da daticert.xml identificativo e cercare
             * con regexp in mail_sent_id la pratica che l'ha generato
             * ed allegarci il messaggio
             *
            */
            if($datiCert = $Parser->getAttachedFile('daticert.xml')){
                $datiCertDom = new DOMDocument();

                $datiCertDom->loadXML($datiCert);

                $datiCertXpath = new DOMXPath($datiCertDom);
                $identificativo = $datiCertXpath->query('//postacert/dati/msgid');


                if($identificativo->length > 0){
                    $praticaOrigine = $db->query('SELECT * FROM pratiche WHERE mail_sent_id = :mail_sent_id',[
                        ':mail_sent_id' => $identificativo->item(0)->nodeValue
                    ])->fetch();
                    if($praticaOrigine){
                        $db->query('UPDATE arc_pratiche_pec
                    SET pratica_id = :pratica_id,
                    numeroregistrazione = :numeroregistrazione,
                    dataregistrazione = :dataregistrazione
                    WHERE pec_id = :pec_id ',[
                                        ':pec_id' => $ricevuta['PEC_ID'],
                                        ':pratica_id' => $praticaOrigine['PRATICA_ID'],
                                        ':numeroregistrazione' => $praticaOrigine['NUMEROREGISTRAZIONE'],
                                        ':dataregistrazione' => $praticaOrigine['DATAREGISTRAZIONE'],
                                    ]);
                        $logger->info('Allegata la ricevuta di consegna ' .
                                $ricevuta['PEC_ID'] .
                                ' al protocollo ' .
                                $praticaOrigine['NUMEROREGISTRAZIONE'] . '-' . $praticaOrigine['DATAREGISTRAZIONE']);
                    }
                } else {
                    throw new Exception('daticert.xml non presente nella mail');
                }

            }
        } catch (Exception $e) {
            $logger->error('Errore File ' . $consegnaFile . ' PEC ' .$ricevuta['PEC_ID'] .
                    ' protocollo ' .
                    $praticaOrigine['NUMEROREGISTRAZIONE'] . '-' . $praticaOrigine['DATAREGISTRAZIONE'] . ' | ' .
                    $e->getMessage());
        }

    } else {
        $logger->error('Non trovato file: ' . $consegnaFile);
    }
}
