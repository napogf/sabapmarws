<?php
/*
 * Created on 10/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
    try {
        if (strpos($_SERVER['HTTP_HOST'] ,'.localnet')) {
            $ultimoProtocollo = str_pad(((integer) Db_Pdo::getInstance()->query('
        	    SELECT max(numeroregistrazione) FROM pratiche
            WHERE numeroregistrazione NOT REGEXP "-" AND dataregistrazione >= DATE_FORMAT(NOW() ,"%Y-01-01")'
            )->fetchColumn() + 1), 7, '0', STR_PAD_LEFT);
            if(!isSet($_POST['ws_pratica_id']) or empty($_POST['ws_pratica_id'])){
                $db->query('INSERT INTO pratiche (numeroregistrazione, dataregistrazione, tipologia, titolo, nome, cognome, codicefiscale, toponimo, localita, cap, comune, provincia, telefono, fax, email, oggetto, numeroriferimento, dataarrivo, datadocumento, responsabile_id  )
                                                    VALUES (:numeroregistrazione, now(), "E", :titolo, :nome, :cognome, :codicefiscale, :toponimo, :localita, :cap, :comune, :provincia, :telefono, :fax, :email, :oggetto, :numeroriferimento, :dataarrivo, :datadocumento, :responsabile_id)',array(
                                                                        ':numeroregistrazione' => $this->_wsResult->NumProtocollo,
                                                                        ':titolo' => $post['clsTMittenteDestinatario']['DesTipoAnagrafica'],
                                                                        ':cognome' => $post['clsTMittenteDestinatario']['Cognome'],
                                                                        ':nome' => $post['clsTMittenteDestinatario']['Nome'],
                                                                        ':codicefiscale' => $post['clsTMittenteDestinatario']['CF'],
                                                                        ':toponimo' => $post['clsTMittenteDestinatario']['Indirizzo'],
                                                                        ':localita' => $post['clsTMittenteDestinatario']['Localita'],
                                                                        ':cap' => $post['clsTMittenteDestinatario']['CAP'],
                                                                        ':comune' => $post['clsTMittenteDestinatario']['Comune'],
                                                                        ':provincia' => $post['clsTMittenteDestinatario']['Provincia'],
                                                                        ':telefono' => $post['clsTMittenteDestinatario']['Telefono'],
                                                                        ':fax' => $post['clsTMittenteDestinatario']['Fax'],
                                                                        ':email' => $post['clsTMittenteDestinatario']['Email'],
                                                                        ':oggetto' => $post['clsTestataDocumento']['Oggetto'],
                                                                        ':numeroriferimento' => $post['clsTestataDocumento']['Numero'],
                                                                        ':dataarrivo' => (new Date($post['clsTestataDocumento']['Arrivo']))->toMysql(),
                                                                        ':datadocumento' => (new Date($post['clsTestataDocumento']['Data']))->toMysql(),
                                                                        ':datadocumento' => $post['RESPONSABILE_ID'],
                                                                    ));
            } else {
                Db_Pdo::getInstance()->query('update pratiche set numeroregistrazione = :numeroregistrazione,
                                                          dataregistrazione = now()
                                        where pratica_id = :pratica_id', array(
                                                            ':numeroregistrazione' => $ultimoProtocollo,
                                                            ':pratica_id' => $_POST['ws_pratica_id']
                                                        ));                
            }
            
            // TODO Assegnazione UO
            
            $response = array(
                'status' => 'success',
                'message' => 'Creato il protocollo: ' . $ultimoProtocollo . ' - ' . Date('d-m-Y')
            );

        } else {

            $espiWs = new EspiWS();

            $espiWs->setTipoProtocollo('Entrata');

            $espiWs->setFascicolo($_POST['classifica2'] , $_POST['fascicolo']);
            // Titolario
            if(isSet($_POST['classifica'])){
                $titolario = Db_Pdo::getInstance()  ->query('SELECT
                    classificazione as ClasseTitolario, description as DesTitolario
                FROM arc_modelli WHERE modello = :modello', [
                    ':modello' => $_POST['classifica']
                ])->fetch();
                $espiWs->setTitolario($titolario);
            } else {
                $espiWs->setTitolario(array(
                    'ClasseTitolario' => $_POST['ClasseTitolario'],
                    'DesTitolario' => $_POST['DesTitolario']
                ));
            }


            $espiWs->setTestataDocumento($_POST['clsTestataDocumento']);
            $mittenti = array();


            $espiWs->setCodiceUfficioCompetente($_POST['CodUfficioCompetente']);


            $espiWs->setMittenteDestinatario(array( 0 => $_POST['clsTMittenteDestinatario']));

            $espiWs->setCodiceUfficioCompetente($_POST['CodUfficioCompetente']);



            $espiWs->protocollaDocumento();
            $wsResult = $espiWs->getWsresult();
            $espiWs->logConnection($_POST['ws_pec_id']);
            if($wsResult->CodError != 'OK'){
                $response = array(
                    'status' => 'error',
                    'message' =>'Errore nella protocollazione in entrata: ' . $wsResult->DesError,
                    'request' => $espiWs->getLastrequest(),
                    'response' => $espiWs->getLastresponse(),
                );
            } else {
                if(isset($_POST['ws_pec_id'])){
                    $espiWs->protocollaPecEntrata($_POST['ws_pec_id'],$_POST);
                } else {
                    $espiWs->protocollaPratica((isset($_POST['ws_pratica_id']) ? $_POST['ws_pratica_id'] : null),$_POST);
                }
                
                $indirizzoArray = [
                    'titolo' => $_POST['clsTMittenteDestinatario']['DesTipoAnagrafica'] ,
                    'nome' => $_POST['clsTMittenteDestinatario']['Nome'] ,
                    'cognome' => $_POST['clsTMittenteDestinatario']['Cognome'] ,
                    'toponimo' => $_POST['clsTMittenteDestinatario']['Indirizzo'] ,
                    'cap' => $_POST['clsTMittenteDestinatario']['CAP'] ,
                    'localita' => $_POST['clsTMittenteDestinatario']['Localita'] ,
                    'comune' => $_POST['clsTMittenteDestinatario']['Comune'] ,
                    'provincia' => $_POST['clsTMittenteDestinatario']['Provincia'] ,
                    'telefono' => $_POST['clsTMittenteDestinatario']['Telefono'] ,
                    'fax' => $_POST['clsTMittenteDestinatario']['Fax'] ,
                    'codicefiscale' => $_POST['clsTMittenteDestinatario']['CF'] ,
                    'email' => $_POST['clsTMittenteDestinatario']['Email'] ,
                    'pec' => $_POST['pec'],
                ];

                $indirizzo = new Indirizzo($indirizzoArray);
                if(!$errori = $indirizzo->getError()){
                    $indirizzo->save();
                } else {
                    throw new Exception(implode('|',$errori));
                }

                $response = array(
                    'status' => 'success',
                    'message' => 'Creato il protocollo: ' . $wsResult->NumProtocollo . ' - ' . $wsResult->AnnoProtocollo,
                );
            }
        }

    } catch (Exception $e) {
        $response = array(
            'status' => 'error',
            'message' => $e->getMessage(),
        );

    }
echo json_encode($response);
exit;
