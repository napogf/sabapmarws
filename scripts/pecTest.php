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

function fileVerify($importedFile){
    $filename = PEC_PATH . DIRECTORY_SEPARATOR . $importedFile['PEC_ID'] . '_' . $importedFile['TYPE'] . '_' . $importedFile['MAIL_HASH'] . '.eml';
    if(file_exists($filename) && filesize($filename) > 0){
        return true;
    }

    return false;
}

try {


    $importFolder = ($type == 'PEC' ? 'INBOX' : 'protocollo');

    $connect = '{' . constant($type . '_HOSTNAME') . ':993/imap/ssl/novalidate-cert}' . $importFolder;
    if(!($mailbox = imap_open($connect, constant($type . '_USERNAME'), constant($type . '_PASSWORD')))){
        foreach (imap_errors() as $imap_error) {
            $logger->error($imap_error);
        }

        throw new Exception('Errore nella connessione IMAP!');
    }

    $MC = imap_check($mailbox);

    $logger->info(date('Y-m-d H:i:s') . ' Lette ' . $MC->Nmsgs . ' in ' . $importFolder);

    for ($i = 1; $i <= $MC->Nmsgs; $i++) {
        $head = imap_header($mailbox, $i, 0);
        r($head,false);
    }

    imap_close($mailbox);


} catch (Exception $e) {
    r($e);
}
