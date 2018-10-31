<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$logger = new Logger(LOG_PATH, 'updateFascicoli.log');

try {

    $db->beginTransaction();

    /*
     * Creazione fascoli Pratiche Entrata/Uscita
     */
    $praticheNormalizzate = $db->query('SELECT * FROM pratiche 
        where PRATICA_USCITA_ID IS NOT NULL AND TIPOLOGIA = "E"');
    while ($praticaEntrata = $praticheNormalizzate->fetch()){
        $fascicolo = (integer) $db->query(
                'select max(fascicolo_id) from pratiche_fascicoli')->fetchColumn() + 1 ;
        $db->query(
            'insert into pratiche_fascicoli (fascicolo_id, pratica_id,tipologia)
                            values (:fascicolo_id, :pratica_id, "E"), 
                            (:fascicolo_id, :protuscita, "U") ',
            [
                ':fascicolo_id' => $fascicolo,
                ':pratica_id' => $praticaEntrata['PRATICA_ID'],
                ':protuscita' => $praticaEntrata['PRATICA_USCITA_ID'],
            ]);
        $logger->info('Inserito fascicolo x Protocollo :' . $praticaEntrata['NUMEROREGISTRAZIONE'] .
                                ' Data ' . $praticaEntrata['DATAREGISTRAZIONE'] );

    }

    $db->commit();
    $db->beginTransaction();

    $sospensioni = $db->query(
            'SELECT arc_sospensioni.sospensione_id,
                arc_sospensioni.pratica_id,
                arc_sospensioni.protouscita,
                arc_sospensioni.protoentrata,
                pratiche.dataregistrazione
            FROM arc_sospensioni
            LEFT JOIN pratiche ON (pratiche.pratica_id = arc_sospensioni.pratica_id)
            ');
    while ($sospensione = $sospensioni->fetch()) {
        update_sospensione($db, $sospensione, $logger);

        // se non trovo la pratica nei fascicoli segnalo l'errore
        if (!$fascicolo = $db->query(
                'SELECT fascicolo_id FROM pratiche_fascicoli where pratica_id = :pratica_id',
                [
                    ':pratica_id' => $sospensione['pratica_id']
                ])->fetchColumn()) {
            $fascicolo = (integer) $db->query(
            'select max(fascicolo_id) from pratiche_fascicoli')->fetchColumn() + 1 ;

            $db->query(
                    'insert into pratiche_fascicoli (fascicolo_id, pratica_id,tipologia)
                            values (:fascicolo_id, :pratica_id, :tipologia) ',
                    [
                        ':fascicolo_id' => $fascicolo,
                        ':pratica_id' => $sospensione['pratica_id'],
                        ':tipologia' => 'E',
                    ]);

        }
            // Cerco la pratica inizio e la aggiungo al fascicolo
        if ($praticaInizio = $db->query(
                'select pratica_id, tipologia from pratiche 
                    where pratica_id = :protouscita 
                    and pratica_id > :pratica_id ',
                [
                    ':protouscita' => $sospensione['protouscita'],
                    ':pratica_id' => $sospensione['pratica_id'],
                ])->fetch()) {
            $fascicoloPratica = $db->query(
                    'SELECT * FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',
                    [
                        ':pratica_id' => $praticaInizio['pratica_id']
                    ])->fetch();
            if ($fascicoloPratica) {
                $db->query(
                        'update pratiche_fascicoli set funzione = :funzione, tipologia = :tipologia
                    WHERE fascicolo_id = :fascicolo_id AND pratica_id = :pratica_id',
                        [
                            ':fascicolo_id' => $fascicolo,
                            ':pratica_id' => $praticaInizio['pratica_id'],
                            ':funzione' => 'inizio_sospensione',
                            ':tipologia' => 'U',
                        ]);
            } else {
                $db->query(
                        'insert into pratiche_fascicoli (fascicolo_id, pratica_id,tipologia, funzione)
                            values (:fascicolo_id, :pratica_id, :tipologia, :funzione) ',
                        [
                            ':fascicolo_id' => $fascicolo,
                            ':pratica_id' => $praticaInizio['pratica_id'],
                            ':tipologia' => 'U',
                            ':funzione' => 'inizio_sospensione'
                        ]);
            }
        }
        // Cerco la pratica fine e la aggiungo al fascicolo
        if ($praticaFine = $db->query(
                'select pratica_id, tipologia from pratiche 
                  where pratica_id = :protoentrata 
                  and pratica_id > :pratica_id',
                [
                    ':protoentrata' => $sospensione['protoentrata'],
                    ':pratica_id' => $sospensione['pratica_id'],
                ])->fetch()) {
            $fascicoloPratica = $db->query(
                    'SELECT * FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',
                    [
                        ':pratica_id' => $praticaFine['pratica_id']
                    ])->fetch();
            if ($fascicoloPratica) {
                $db->query(
                        'update pratiche_fascicoli set funzione = :funzione, tipologia = :tipologia
                    WHERE fascicolo_id = :fascicolo_id AND pratica_id = :pratica_id',
                        [
                            ':fascicolo_id' => $fascicolo,
                            ':pratica_id' => $praticaFine['pratica_id'],
                            ':funzione' => 'fine_sospensione',
                            ':tipologia' => 'E',
                        ]);
            } else {
                $db->query(
                        'insert into pratiche_fascicoli (fascicolo_id, pratica_id,tipologia, funzione)
                            values (:fascicolo_id, :pratica_id, :tipologia, :funzione) ',
                        [
                            ':fascicolo_id' => $fascicolo,
                            ':pratica_id' => $praticaFine['pratica_id'],
                            ':tipologia' => 'E',
                            ':funzione' => 'fine_sospensione'
                        ]);
            }
        }

    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    r($e->getTrace(),false);
    r($e->getMessage());
}

function update_sospensione ($db, $sospensione, $logger)
{
    if (! $db->query(
            'SELECT pratica_id FROM pratiche WHERE pratica_id = :pratica_id',
            [
                ':pratica_id' => $sospensione['protouscita']
            ])->fetch()) {
        // provo a cercarla per numeroregistrazione
        if ($protouscita = $db->query(
                'SELECT pratica_id FROM pratiche WHERE numeroregistrazione = :uscita
                AND dataregistrazione > :dataregistrazione',
                [
                    ':uscita' => $sospensione['protouscita'],
                    ':dataregistrazione' => $sospensione['dataregistrazione']
                ])->fetchColumn()) {
            $db->query(
                    'UPDATE arc_sospensioni SET protouscita = :protouscita WHERE sospensione_id = :id',
                    [
                        ':protouscita' => $protouscita,
                        ':id' => $sospensione['sospensione_id']
                    ]);
        } else {
            $logger->info(
                    'Nontrovato protouscita ' . $sospensione['protouscita'] .
                             ' in sospensione ' . $sospensione['sospensione_id']);
        }
    }

    if (! $db->query(
            'SELECT pratica_id FROM pratiche WHERE pratica_id = :pratica_id',
            [
                ':pratica_id' => $sospensione['protoentrata']
            ])->fetch()) {
        // provo a cercarla per numeroregistrazione
        if ($protoentrata = $db->query(
                'SELECT pratica_id FROM pratiche WHERE numeroregistrazione = :uscita
                AND dataregistrazione > :dataregistrazione',
                [
                    ':uscita' => $sospensione['protoentrata'],
                    ':dataregistrazione' => $sospensione['dataregistrazione']
                ])->fetchColumn()) {
            $db->query(
                    'UPDATE arc_sospensioni SET protoentrata = :protoentrata WHERE sospensione_id = :id',
                    [
                        ':protoentrata' => $protoentrata,
                        ':id' => $sospensione['sospensione_id']
                    ]);
        } else {
            $logger->info(
                    'Nontrovato protoentrata ' . $sospensione['protoentrata'] .
                             ' in sospensione ' . $sospensione['sospensione_id']);
        }
    }
}