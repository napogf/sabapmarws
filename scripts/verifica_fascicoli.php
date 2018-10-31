<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$log = new Logger(LOG_PATH, 'verificaFascicolo.log');

try {

    $praticheChiuse = $db->query('SELECT * from pratiche WHERE PRATICA_USCITA_ID IS NOT NULL');
    while ($pratica = $praticheChiuse->fetch()) {
        $praticaObj = new Pratica();
        $praticaObj->setId($pratica['PRATICA_ID']);
        $praticaObj->verificaFascicolo();
        $log->info('Fascicolo verificato per il protocollo ' . $pratica['NUMEROREGISTRAZIONE'] .
            ' del ' . $pratica['DATAREGISTRAZIONE']);
    }
} catch (Exception $e) {
    r($e->getMessage());
}

exit;