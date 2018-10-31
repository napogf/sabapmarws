<?php
include('../login/configsess.php');


$data = $argv[1] ? (new Date($argv[1]))->format('Y-m-d') : (new Date())->format('Y-m-d');

r($data);
$logFile = 'eliminazionePec_' . date('Ymd') . '.log';
$logger = new Logger(LOG_PATH, $logFile);
$logger->info('Iniziato script eliminazione pec con file inesistenti dal: ' . $data);
$time_start = microtime(true);

$db = Db_Pdo::getInstance();
$pecDaCancellare = $db->query('select * from arc_pratiche_pec where PRATICA_ID is not NULL and CREATION_DATETIME >= :date',[
    ':date' => $data . ' 00:00:00',
]);


while ($pec = $pecDaCancellare->fetch()) {
    try {
        $fileFrom = PEC_PATH . DIRECTORY_SEPARATOR . $pec['PEC_ID'] . '_pec_' . $pec['MAIL_HASH'] . '.eml';
        if (!file_exists($fileFrom)) {
//            $db->query('delete from arc_pratiche_pec where PEC_ID = :pec_id',[
//                ':pec_id' => $pec['PEC_ID'],
//            ]);
            $logger->alert('Cancellata PEC_ID ' . $pec['PEC_ID']);
        }
    } catch (Exception $e) {
        $logger->error($e->getMessage());
    }
}


$time_end = microtime(true);
$logger->info('Script terminato in ' . ($time_end - $time_start) . ' secondi');