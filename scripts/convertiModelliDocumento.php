<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$logger = new Logger(LOG_PATH, 'convertiModelli.log');
try {
    $modelliDaAssociare = $db->query('SELECT * FROM arc_documenti_old');
    /*
     * Aggiornamento modelli per procedimento
     *  per ogni modello vecchio ricavo le classificazioni a cui associare
     *  i modelli di documento
     */
    while ($modello = $modelliDaAssociare->fetch()){
        try {
            $db->beginTransaction();
            $logger->info('Aggiornamento modello ' . $modello['MODELLO'] . ' - ' . $modello['DESCRIPTION']);
            /*
             * Trovo il modello vecchio e mi ricavo quelli corrispondenti nuovi tramite la classifica
            */
            $nomeFile = DOC_PATH . DIRECTORY_SEPARATOR . $modello['DOC_ID'] . '-FILE_OO-' . $modello['FILE_OO'];
            if(!file_exists($nomeFile)){
                $logger->error('File modello ' . $nomeFile . ' non esistente!');
                $db->commit();
                continue;
            }
            if(!empty($modello['MODELLO'])){
                $procedimentoOld = $db->query('SELECT * from arc_modelli_old WHERE MODELLO = :modello ',[
                    ':modello' => $modello['MODELLO'],
                ])->fetch();
                $procedimentiNuovi = explode(';', trim($procedimentoOld['CLASSIFICAZIONE'],' ;'));
                foreach ($procedimentiNuovi as $classifica){
                    if($nuovoModello = $db->query('SELECT MODELLO FROM arc_modelli WHERE CLASSIFICAZIONE = :classificazione',[
                        ':classificazione' => $classifica
                    ])->fetchColumn()){
                        $db->query('INSERT INTO arc_documenti (MODELLO,DESCRIPTION,FILE_OO) values (:MODELLO,:DESCRIPTION,:FILE_OO)',[
                            ':MODELLO' => $nuovoModello,
                            ':DESCRIPTION' => $modello['DESCRIPTION'],
                            ':FILE_OO' => $modello['FILE_OO'],
                        ]);
                        $newId = $db->lastInsertId();
                        copy($nomeFile, DOC_PATH . DIRECTORY_SEPARATOR . $newId . '-FILE_OO-' . $modello['FILE_OO']);
                    } else {
                        $logger->error('Non trovato per procedimento vecchio' . $modello['MODELLO'] . ' la classifica ' . $classifica);
                    }
                }
            } else {
                $db->query('INSERT INTO arc_documenti (MODELLO,DESCRIPTION,FILE_OO) values (:MODELLO,:DESCRIPTION,:FILE_OO)',[
                    ':MODELLO' => null,
                    ':DESCRIPTION' => $modello['DESCRIPTION'],
                    ':FILE_OO' => $modello['FILE_OO'],
                ]);
                $newId = $db->lastInsertId();
                copy($nomeFile, DOC_PATH . DIRECTORY_SEPARATOR . $newId . '-FILE_OO-' . $modello['FILE_OO']);
            }
            copy($nomeFile ,$nomeFile . '.old');

            $db->commit();

        } catch (Exception $e) {
            $db->rollback();
            $logger->error('Errore nella conversione del modello ' . $nomeFile);
            $logger->error($e->getMessage());
        }
    }
} catch (Exception $e) {
    r($e->getMessage());
}