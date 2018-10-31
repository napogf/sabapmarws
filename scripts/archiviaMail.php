<?php
/**
 * Created by PhpStorm.
 * User: giacomo
 * Date: 18/05/17
 * Time: 17.19
 */
include dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'login/configsess.php';
$type = isset($argv[1]) ? $argv[1] : 'PEC';
$logger = new Logger(LOG_PATH,'archiviazione_mail_'.date('Ymd').'.log');
set_time_limit(0);
ini_set('memory_limit', '512M');

$db = Db_Pdo::getInstance();

$mailDaArchiviare = $db->query('SELECT * FROM arc_pratiche_pec 
                                            WHERE ARCHIVIATA = :archiviata 
                                            AND (PRATICA_ID IS NOT NULL OR STATUS = :status)', [
    ':archiviata' => 'N',
    ':status' => 'A',
]);
$logger->info(date('Y-m-d H:i:s') . ' Iniziata archiviazione');

while ($mail = $mailDaArchiviare->fetch()){
    $dir = ARC_PATH . DIRECTORY_SEPARATOR . str_replace('-', DIRECTORY_SEPARATOR, $mail['DATAARRIVO']);
    $filename =  $mail['PEC_ID']. '_' . $mail['TYPE'] . '_' .$mail['MAIL_HASH'].'.eml';
    if(file_exists(PEC_PATH . DIRECTORY_SEPARATOR . $filename)){
        if(!is_dir( $dir)){
            mkdir($dir,0777,true);
        }
        copy(PEC_PATH . DIRECTORY_SEPARATOR . $filename , $dir . DIRECTORY_SEPARATOR .$filename);
        $db->query('UPDATE arc_pratiche_pec SET ARCHIVIATA = :archiviata WHERE PEC_ID = :pec_id',[
            ':pec_id' => $mail['PEC_ID'],
            ':archiviata' => 'Y',
        ]);
        $logger->info('Archiviata mail ' . $filename . ' in ' . $dir);
    } else {
        $logger->info('Non Trovata mail ' . $filename );
        $db->query('UPDATE arc_pratiche_pec SET ARCHIVIATA = :archiviata WHERE PEC_ID = :pec_id',[
            ':pec_id' => $mail['PEC_ID'],
            ':archiviata' => 'A',
        ]);
    }
}