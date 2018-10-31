<?php

class formEntrata extends formPratica
{
    /* costruisce la form graficamente a partire dagli oggetti caricati */



    static function generaPraticaUscita($praticaInEntrataId,$mode='U')
    {
        try {
            $db = Db_Pdo::getInstance();
            $db->beginTransaction();
            if($praticaInEntrata = $db->query('SELECT * FROM pratiche 
                    WHERE pratica_id = :pratica_id AND tipologia = "E" ',array(
                ':pratica_id' => $praticaInEntrataId
            ))->fetch()){


                $params = array();
                foreach ($praticaInEntrata as $key => $value) {
                    $params[':'.$key] = $value;
                }
                $params[':PRATICA_ID'] = null;
                $params[':NUMEROREGISTRAZIONE'] = null;
                $params[':DATAREGISTRAZIONE'] = null;
                $params[':USCITA'] = null;
                //$params[':PRATICA_USCITA_ID'] = null;
                $params[':TIPOLOGIA'] = 'U';


                $db->query('insert into pratiche (' . implode(', ', array_keys($praticaInEntrata)) . ')
                            values (:' . implode(', :',array_keys($praticaInEntrata)) . ')',$params);
                $uscitaId = $db->lastInsertId();



                $db->query('insert into arc_pratiche_uo (pratica_id, uoid) select :pratica_id, uoid FROM arc_pratiche_uo WHERE pratica_id = :pratica_in_entrata',array(
                    ':pratica_id' => $uscitaId,
                    ':pratica_in_entrata' => $praticaInEntrataId,
                ));
                if($mode == 'U'){
                    $db->query('update pratiche set uscita = now() where pratica_id = :pratica_id',array(
                        ':pratica_id' => $praticaInEntrataId,
                    ));
                    // se la pratica in entrata è sospesa chiudo la sospensione
                    if($sospensione = $db->query('SELECT sospensione_id FROM arc_sospensioni WHERE PRATICA_ID = :pratica_id',[
                        ':pratica_id' => $praticaInEntrataId,
                        ])->fetchColumn()){
                        $db->query('UPDATE arc_sospensioni SET FINE = now(), PROTOUSCITA = :protouscita WHERE SOSPENSIONE_ID = :sospensione_id',[
                            ':sospensione_id' => $sospensione,
                            ':protouscita' => $uscitaId,
                        ]);
                    }
                    if($fascicoloId = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[
                    	':pratica_id' => $praticaInEntrataId
                    ])->fetchColumn()){
                        $db->query('insert into pratiche_fascicoli (pratica_id, fascicolo_id,tipologia) values
                                                                    (:pratica_id, :fascicolo_id, "U") ',array(
                                                                                                ':pratica_id' => $uscitaId,
                                                                                                ':fascicolo_id' => $fascicoloId,
                                                                                            ));
                    } else {
                        $fascicoloId = (integer) $db->query('SELECT max(fascicolo_id) FROM pratiche_fascicoli')->fetchColumn() +1;
                        $db->query('insert into pratiche_fascicoli (pratica_id, fascicolo_id, funzione, tipologia) values
                                                                    (:pratica_id, :fascicolo_id, null, "E"), (:pratica_sospensione_id, :fascicolo_id,null, "U") ',array(
                                                                                                ':pratica_id' => $praticaInEntrataId,
                                                                                                ':fascicolo_id' => $fascicoloId,
                                                                                                ':pratica_sospensione_id' => $uscitaId
                                                                                            ));



                    }

                } elseif ($mode == 'S') {
                    $db->query('INSERT INTO arc_sospensioni (pratica_id, protouscita, inizio) values (:pratica_id, :protouscita, now())',array(
                        ':pratica_id' => $praticaInEntrataId,
                        ':protouscita' => $uscitaId,
                    ));
                    if($fascicoloId = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[
                            ':pratica_id' => $praticaInEntrataId
                            ])->fetchColumn()){
                        $db->query('insert into pratiche_fascicoli (pratica_id, fascicolo_id,tipologia,funzione) values
                                                                    (:pratica_id, :fascicolo_id, "U", "inizio_sospensione") ',array(
                                                                                            ':pratica_id' => $uscitaId,
                                                                                            ':fascicolo_id' => $fascicoloId,
                                                                                        ));
                    } else {
                        $fascicoloId = (integer) $db->query('SELECT max(fascicolo_id) FROM pratiche_fascicoli')->fetchColumn() +1;
                        $db->query('insert into pratiche_fascicoli (pratica_id, fascicolo_id,  funzione, tipologia) values
                                                                    (:pratica_id, :fascicolo_id, null, "E"), (:pratica_sospensione_id, :fascicolo_id,"inizio_sospensione", "U") ',array(
                                                                                            ':pratica_id' => $praticaInEntrataId,
                                                                                            ':fascicolo_id' => $fascicoloId,
                                                                                            ':pratica_sospensione_id' => $uscitaId
                                                                                        ));


                    }

                }


                $db->commit();
            }
        } catch (Exception $e) {
            $db->rollBack();
            r($e->getMessage());
            return false;
        }

        return $uscitaId;
    }
}
