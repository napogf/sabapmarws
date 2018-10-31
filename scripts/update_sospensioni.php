<?php

include '../login/configsess.php';
$logFile = 'update_sospensioni_' . date('Ymd') . '.log';
$logger = new Logger(LOG_PATH, $logFile);

$logger->info('Iniziato script aggiornamento sospensioni');
$db = Db_Pdo::getInstance();

try {


    $db->beginTransaction();

    /* Inizio sospensione */
    $sospensioni = $db->query(
            'select sospensione_id, pratica_id, protouscita, inizio, protoentrata, fine from arc_sospensioni where protouscita is not null');

    while ($sospensione = $sospensioni->fetch()) {
        if(!$db->query('SELECT pratica_id from pratiche where pratica_id = :pratica_id and dataregistrazione between :datauscita AND :datauscitaFm ',[
            ':pratica_id' => (integer) $sospensione['protouscita'],
            ':datauscita' => (new Date($sospensione['inizio']))->format('Y-m-') . '01',
            ':datauscitaFm'  => (new Date($sospensione['inizio']))->format('Y-m-t'),
        ])->fetch()){
            // cerco per protocollo
            if($praticaId = $db->query('SELECT pratica_id from pratiche where numeroregistrazione = :numeroregistrazione
                    and dataregistrazione between :datauscita AND :datauscitaFm ',[
            ':numeroregistrazione' =>  str_pad(trim($sospensione['protouscita']), 7,'0'),
                ':datauscita' => (new Date($sospensione['inizio']))->format('Y-m-') . '01',
                ':datauscitaFm'  => (new Date($sospensione['inizio']))->format('Y-m-t'),
            ])->fetchColumn()){
                $logger->info('Aggiornata sospensione protuscita' . $praticaId);
                $db->query('update arc_sospensioni set PROTOUSCITA = :protouscita WHERE sospensione_id = :sospensione_id',[
                    ':sospensione_id' => $sospensione['sospensione_id'],
                    ':protouscita' => $praticaId,
                ]);

            } else {
                $logger->info('Non trovata protuscita x sospensione' . implode(',', $sospensione));
                $db->query('update arc_sospensioni set PROTOUSCITA = :protouscita WHERE sospensione_id = :sospensione_id',[
                    ':sospensione_id' => $sospensione['sospensione_id'],
                    ':protouscita' => NULL,
                ]);
            }
        }
    }

    /* Fine sospensione */
    $sospensioni = $db->query(
        'select sospensione_id, pratica_id, protouscita, inizio, protoentrata, fine from arc_sospensioni where protoentrata is not null');
    while ($sospensione = $sospensioni->fetch()) {
        if(!$db->query('SELECT pratica_id from pratiche where pratica_id = :pratica_id 
              and dataregistrazione between :dataentrata AND :dataentrataFm',[
            ':pratica_id' => (integer) $sospensione['protoentrata'],
            ':dataentrata' => (new Date($sospensione['fine']))->format('Y-m-') . '01',
            ':dataentrataFm'  => (new Date($sospensione['fine']))->format('Y-m-t'),
        ])->fetch()){
            // cerco per protocollo
            if($praticaId = $db->query('SELECT pratica_id from pratiche where numeroregistrazione = :numeroregistrazione
                    and dataregistrazione between :dataentrata AND :dataentrataFm ',[
                ':numeroregistrazione' =>  str_pad(trim($sospensione['protoentrata']), 7,'0'),
                ':dataentrata' => (new Date($sospensione['fine']))->format('Y-m-') . '01',
                ':dataentrataFm'  => (new Date($sospensione['fine']))->format('Y-m-t'),
            ])->fetchColumn()){
                $logger->info('Aggiornata sospensione protentrata' . $praticaId);
                $db->query('update arc_sospensioni set protouscita = :protoentrata WHERE sospensione_id = :sospensione_id',[
                    ':sospensione_id' => $sospensione['sospensione_id'],
                    ':protoentrata' => $praticaId,
                ]);
            } else {
                $logger->info('Non trovata protoentrata x sospensione' . implode(',', $sospensione));
                $db->query('update arc_sospensioni set PROTOENTRATA = :protoentrata WHERE sospensione_id = :sospensione_id',[
                    ':sospensione_id' => $sospensione['sospensione_id'],
                    ':protoentrata' => NULL,
                ]);

            }
        }
    }

    $db->commit();
} catch (Exception $e){

    $db->rollBack();
    r($e->getMessage());
}
$logger->info('Terminato script aggiornamento sospensioni');
