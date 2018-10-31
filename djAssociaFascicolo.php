<?php
/*
 * Created on 11/apr/2011
 *
 * djCreaProgettoa.php
*/

include "login/autentication.php";
$db = Db_Pdo::getInstance();
$result = [
    'status' => 'success',
    'message' => '',
];
/*
 * Parametri fascicolo id della pratica a cui associare
 * praticaId id pratica che si vuole associare
 */
$logger = new Logger(LOG_PATH, 'associazioneProtocolli_' . date('Ymd') . '.log');
$logger->info($_GET['praticaId'] . ' da associare a ' . $_GET['praticaDaAssociare']);

try {
    $db->beginTransaction();

    if(!($fascicolo = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[
        ':pratica_id' => $_GET['praticaDaAssociare'],
    ])->fetchColumn())){
        if(!($fascicolo = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[
            ':pratica_id' => $_GET['praticaDaAssociare'],
        ])->fetchColumn())){
            // non ha un fascicolo neanche la pratica di partenza quindi lo creo e ci associo ambedue le pratiche
            $logger->info('non ha un fascicolo neanche la pratica di partenza quindi lo creo e ci associo ambedue le pratiche');
            $fascicolo = $db->query('SELECT max(fascicolo_id) FROM pratiche_fascicoli')->fetchColumn() +1;
            $db->query('INSERT INTO pratiche_fascicoli (pratica_id, fascicolo_id, tipologia) VALUES 
                        (:pratica_id, :fascicolo_id, :tipologia) ,
                        (:praticaDaAssociare, :fascicolo_id, "E") ',[
                ':fascicolo_id' => $fascicolo,
                ':pratica_id' => $_GET['praticaId'],
                ':praticaDaAssociare' => $_GET['praticaDaAssociare'],
                ':tipologia' => Db_Pdo::getInstance()->query('SELECT tipologia FROM pratiche WHERE PRATICA_ID = :pratica_id' ,[
                                            ':pratica_id' => $_GET['praticaId'],
                                        ])->fetchColumn(),
            ]);

        } else {
            // la pratica di partenza ha un fascicolo ed allora associo la nuova al suo
            $logger->info('la pratica di partenza ha un fascicolo ed allora associo la nuova al suo');
            $db->query('INSERT INTO pratiche_fascicoli (pratica_id, fascicolo_id, tipologia) VALUES 
                        (:praticaDaAssociare, :fascicolo_id, "E") ',[
                ':fascicolo_id' => $fascicolo,
                ':praticaDaAssociare' => $_GET['praticaDaAssociare'],
            ]);
        }
    } else {
        // la pratica da associare ha il fascicolo quindi associo a quel fascicolo i fascicolo di partenza se esiste
        if($fascicoloOriginale = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[
            ':pratica_id' => $_GET['praticaId'],
        ])->fetchColumn()){
            // aggiorno tutte le pratiche con quel fascicolo
            $logger->info('aggiorno tutte le pratiche con quel fascicolo ' . $fascicoloOriginale . ' con ' . $fascicolo);

            $db->query('UPDATE pratiche_fascicoli SET fascicolo_id = :new_fascicolo_id WHERE fascicolo_id = :fascicolo_id',[
                ':fascicolo_id' => $fascicoloOriginale,
                ':new_fascicolo_id' => $fascicolo,
            ]);
        } else {
            // inserisco la pratica nel fascicolo da associare
            $logger->info('inserisco la pratica nel fascicolo da associare');
            $db->query('INSERT INTO pratiche_fascicoli (fascicolo_id, pratica_id, tipologia )
                        VALUES (:fascicolo_id, :pratica_id, :tipologia)',[
                ':fascicolo_id' => $fascicolo,
                ':pratica_id' => $_GET['praticaId'],
                ':tipologia' => $db->query('SELECT tipologia FROM pratiche WHERE pratica_id = :pratica_id',[
                    ':pratica_id' => $_GET['praticaId']
                ])->fetchColumn(),
            ]);
        }

    }

    $pratica = new Pratica();
    $pratica->setId($_GET['praticaDaAssociare']);


    $db->commit();
    $result['message'] = 'Protocollo associato al Fascicolo!';
} catch (Exception $e) {
    $db->rollBack();
    $result = [
        'status' => 'success',
        'message' => $e->getMessage(),
    ];
}


echo json_encode($result);