<?php
include '../login/configsess.php';
$db = Db_Pdo::getInstance();
$log = new Logger(LOG_PATH, 'convertProcedimentoPratiche.log');



    $pratiche = $db->query('select PRATICA_ID, MODELLO, concat(arc_titolario.LIV01,"." ,arc_titolario.LIV02, ".", arc_titolario.LIV03) as TITOLAZIONE  FROM pratiche
                                LEFT JOIN arc_titolazioni ON (arc_titolazioni.ID = pratiche.TITOLAZIONE)
                                LEFT JOIN arc_titolario ON (arc_titolario.TITOLO = arc_titolazioni.TITOLO)
                                WHERE MODELLO IS NOT NULL');

    while ($pratica = $pratiche->fetch()){
        try {
            $newModello = $db->query('SELECT modello FROM arc_modelli WHERE CLASSIFICAZIONE = :classificazione',[
                ':classificazione' => $pratica['TITOLAZIONE']
            ])->fetchColumn();
            if(empty($newModello)){
                // allora cerco il modello e mi prendo la classifica di default
                $classificaDidefault = $db->query('SELECT CLASSIFICAZIONE FROM arc_modelli_old WHERE MODELLO = :modello',[
                    ':modello' => $pratica['MODELLO'],
                ])->fetchColumn();

                $classificazioneArray = explode(';', $classificaDidefault);

                $newModello = $db->query('SELECT modello FROM arc_modelli WHERE CLASSIFICAZIONE = :classificazione',[
                    ':classificazione' => $classificazioneArray[0],
                ])->fetchColumn();
            }



            if(empty($newModello)){
                throw new Exception('Modello non aggiornato per pratica : ' . json_encode($pratica));
            }
            $db->query('UPDATE pratiche SET MODELLO = :modello WHERE PRATICA_ID = :pratica_id',[
                ':modello' => $newModello,
                ':pratica_id' => $pratica['PRATICA_ID'],
            ]);

        } catch (Exception $e) {


            $log->alert($e->getMessage());
        }


    }

