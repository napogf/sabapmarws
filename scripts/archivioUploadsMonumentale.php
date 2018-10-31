<?php
include dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'login/configsess.php';

if (!function_exists('createDirectories')) {
    function createDirectories($upload_path = null)
    {
        if ($upload_path == null) return false;
        $upload_directories = explode(DIRECTORY_SEPARATOR, $upload_path);
        $dir = '';
        foreach ($upload_directories as $upload_directory) {
            if (!empty($upload_directory)) {
                $dir .= DIRECTORY_SEPARATOR . $upload_directory;
                if (!is_dir($dir)) {
                    mkdir($dir, 0777);// Create the folde if not exist and give permission
                }
            }
        }
        return true;
    }
}

$db = Db_Pdo::getInstance();
$logFile = 'archivio_documenti_' . date('Ymd') . '.log';
$logger = new Logger(LOG_PATH, $logFile);
$logger->info('Iniziato script d archiviazione documenti');
$time_start = microtime(true);

$fileDaCopiare = $db->query('SELECT
    uploads.upload_id,
    uploads.filename,
    pratiche.numeroregistrazione,
    pratiche.dataregistrazione,
    vincoli.cartellaprogettimonumentale,
    vincoli.provincia,
    vincoli.comune
FROM pratiche
    RIGHT JOIN vincoli ON ( vincoli.vincolo_id = pratiche.vincolo_id)
    RIGHT JOIN uploads on (uploads.pratica_id = pratiche.pratica_id)
WHERE (vincoli.comune is not null AND vincoli.comune > "")
AND (vincoli.provincia is not null AND vincoli.provincia > "")
AND (vincoli.cartellaprogettimonumentale is not null AND vincoli.cartellaprogettimonumentale > "")
order by vincoli.provincia, vincoli.comune, vincoli.cartellaprogettimonumentale');


while ($file = $fileDaCopiare->fetch()) {
    try {

        $archiveDir = ARCHIVIO_PATH . DIRECTORY_SEPARATOR .
            sanitizePath($file['provincia']) . DIRECTORY_SEPARATOR .
            sanitizePath($file['comune']) . DIRECTORY_SEPARATOR .
            sanitizePath($file['cartellaprogettimonumentale']);



        $fileFrom = FILES_PATH . DIRECTORY_SEPARATOR . $file['upload_id'] . '_' . $file['filename'] ;
        if (file_exists($fileFrom)) {
            $fileTo = $archiveDir . DIRECTORY_SEPARATOR . $file['filename'] ;
            if (!file_exists($archiveDir)) {
                createDirectories($archiveDir);
            }
            if(!file_exists($fileTo)){
                copy($fileFrom, $fileTo);
            }

        } else {
            throw new Exception('File: ' . $fileFrom . ' Prot: ' .
                $file['numeroregistrazione'] . ' ' . $file['dataregistrazione'] . 'non trovato!');
        }
        $logger->info('Copiato il File: ' . $file['filename'] . ' Prot: ' .
            $file['numeroregistrazione'] . ' ' . $file['dataregistrazione'] .
            ' in ' . $fileTo);

    } catch (Exception $e) {
        $logger->error($e->getMessage());
    }
}


$time_end = microtime(true);
$logger->info('Script terminato in ' . ($time_end - $time_start) . ' secondi');