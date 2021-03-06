<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$log = new Logger(LOG_PATH, 'updateProtuscita.log');

try {

    $praticheChiuse = $db->query('SELECT * from pratiche WHERE PROTUSCITA IS NOT NULL');
    while ($pratica = $praticheChiuse->fetch()) {
        if($uscitaId = normalizzaProtuscita($pratica,$log)){
            $db->query('UPDATE pratiche SET PRATICA_USCITA_ID = :protuscita WHERE PRATICA_ID = :pratica_id',[
                ':pratica_id' => $pratica['PRATICA_ID'],
                ':protuscita' => $uscitaId,
            ]);
        } else {
            $log->alert('Pratica : ' . $pratica['NUMEROREGISTRAZIONE'] . ' - ' . $pratica['DATAREGISTRAZIONE'] . ' uscita non trovata ' . $pratica['PROTUSCITA']);
        }
    }
} catch (Exception $e) {

    r($e->getMessage());
}

function normalizzaProtuscita($pratica,$log){

    $db = Db_Pdo::getInstance();
    /*
     * immagino che in protuscita ci sia il protocollo
     */

    if(!$protuscitaId = $db->query('SELECT PRATICA_ID FROM pratiche WHERE PRATICA_ID = :protouscita',
            [':protouscita' => $pratica['PROTUSCITA']])->fetchColumn()){

        $protuscitaId = $db->query('SELECT PRATICA_ID FROM pratiche WHERE NUMEROREGISTRAZIONE = :protouscita 
                                        AND DATAREGISTRAZIONE between :dataentrata AND :datauscita',[
            ':protouscita' => $pratica['PROTUSCITA'],
            ':dataentrata' => $pratica['DATAREGISTRAZIONE'],
            ':datauscita'  => (new Date($pratica['USCITA']))->format('Y-m-t'),
        ])->fetchColumn();
    }


    return $protuscitaId;
}