<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$logger = new Logger(LOG_PATH, 'fascicoliPratiche.log');
try {
    $db->beginTransaction();
    $fascicoloId = (integer) $db->query(
            'select max(fascicolo_id) from pratiche_fascicoli')->fetchColumn() + 1 ;

    $pratiche = $db->query('SELECT pratiche_uscita.pratica_id protouscita, pratiche.pratica_id FROM pratiche
            LEFT JOIN pratiche as pratiche_uscita ON (
                pratiche_uscita.pratica_id = pratiche.protuscita
                AND pratiche_uscita.pratica_id > pratiche.pratica_id
			)
            WHERE pratiche_uscita.pratica_id is not null');
    while ($pratica = $pratiche->fetch()){
        // se non esiste il fascicolo lo creo
        if(! $fascicolo = $db->query('select fascicolo_id from pratiche_fascicoli where pratica_id = :pratica_id',[
            ':pratica_id' => $pratica['pratica_id']
        ])->fetchColumn()){
            $fascicolo = $fascicoloId++;
            $db->query('insert into pratiche_fascicoli (fascicolo_id, pratica_id, tipologia)
                                values (:fascicolo_id, :pratica_id, "E"),
                                (:fascicolo_id, :protouscita, "U")
                     ',
                    [
                        ':fascicolo_id' => $fascicolo,
                        ':pratica_id' => $pratica['pratica_id'],
                        ':protouscita' => $pratica['protouscita'],
                    ]);

        }
    }
    $db->commit();




} catch (Exception $e) {
    $db->rollBack();
    r($e->getMessage());
}