<?php

class EspiWS
{

    public function __construct()
    {
        $confWs = array();
        if ($initWsArray = Db_Pdo::getInstance()->query('SELECT chiave, valore FROM sys_config WHERE chiave LIKE "WS_%" ')->fetchAll()) {
            foreach ($initWsArray as $conf) {
                $confWs[$conf['chiave']] = $conf['valore'];
            }
            $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['UserID'] = $confWs['WS_UserID'];
            $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['Password'] = $confWs['WS_Password'];
            $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['CodUtenteProtocollatore'] = (! isset($_SESSION['ws_user']) or empty($_SESSION['ws_user'])) ? $confWs['WS_CodUtenteProtocollatore'] : $_SESSION['ws_user'];
            $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['CodUfficioCompetente'] = $confWs['WS_CodUfficioCompetente'];
            $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputAOO']['C_DES_AOO'] = $confWs['WS_C_DES_AOO'];
            // set if trace in database connection request/response
            $this->_debugTrace = $confWs['WS_debug'];
            return $this;
        }

        return false;
    }

    public function __get($key)
    {
        return $this->$key;
    }

    protected $_pecSuap;

    protected $_debugTrace = 1;

    protected $_wsResponse;

    protected $_wsError;

    protected $_lastRequestHeaders;

    protected $_lastRequest;

    protected $_lastResponseHeaders;

    protected $_lastResponse;

    protected $_wsResult;

    protected $pratica_id;

    public function setTipoProtocollo($tipoProtocollo)
    {
        switch ($tipoProtocollo) {
            case 'Entrata':

                $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['TipoProtocollo'] = 'Entrata';
                $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['enOperazioneProtocollo'] = 'CreaProtocolloSenzaClassifica';
                unset($this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputFascicolo']);
                unset($this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputTitolario']);
                break;
            default:
                $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['TipoProtocollo'] = $tipoProtocollo;
                $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['Data'] = Date('d/m/Y');
                break;
        }

        return $this;
    }

    public function getError()
    {
        return $this->_wsError;
    }

    public function setCodiceUfficioCompetente($codUfficio)
    {

            $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['CodUfficioCompetente'] = $codUfficio;

    }

    public function setCodUtenteProtocollatore($codiceUtente)
    {
        $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['CodUtenteProtocollatore'] = $codiceUtente;

        return $this;
    }

    public function setFascicolo($desFascicolo)
    {
        $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputFascicolo']['DesFascicolo'] = $desFascicolo;

        return $this;
    }

    public function setTitolario($titolario,$classifica2 = null)
    {

        $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputTitolario']['ClasseTitolario'] = $titolario['ClasseTitolario'] . (empty($classifica2) ? '' : '.' . $classifica2);
        $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputTitolario']['DesTitolario'] = $titolario['DesTitolario'];

        return $this;
    }

    public function setTestataDocumento($testataDocumento)
    {
        foreach ($testataDocumento as $key => $value) {
            switch ($key) {
                case 'Data':
                case 'Arrivo':
                    $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['clsTestataDocumento'][$key] = (new Date($value))->toReadable();
                    break;

                default:
                    $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['clsTestataDocumento'][$key] = $value;
                    break;
            }

        }

        return $this;
    }

    protected $_clsTMittenteDestinatario = array();

    protected function getMittenteDestinatario()
    {
        return $this->_clsTMittenteDestinatario;
    }

    public function setMittenteDestinatario($mittentiDestinatari)
    {
        $elementi = count($mittentiDestinatari);
        if ($elementi > 1) {
            for ($i = 0; $i < $elementi; $i ++) {
                $mittenteDestinatario = $mittentiDestinatari[$i];
                $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario'][] = array(
                    'DesTipoAnagrafica' => $mittenteDestinatario['DesTipoAnagrafica'],
                    'Cognome' => $mittenteDestinatario['Cognome'],
                    'Nome' => $mittenteDestinatario['Nome'],
                    'CF' => $mittenteDestinatario['CF'],
                    'Indirizzo' => $mittenteDestinatario['Indirizzo'],
                    'Localita' => $mittenteDestinatario['Localita'],
                    'CAP' => $mittenteDestinatario['CAP'],
                    'Comune' => $mittenteDestinatario['Comune'],
                    'Provincia' => $mittenteDestinatario['Provincia'],
                    'Telefono' => $mittenteDestinatario['Telefono'],
                    'Fax' => $mittenteDestinatario['Fax'],
                    'PerConoscenza' => (isset($mittenteDestinatario['PerConoscenza']) ? 1 :0),
                    'Email' => $mittenteDestinatario['Email']
                );
            }
        } else {
            $mittenteDestinatario = $mittentiDestinatari[0];
            $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari'] = array(
                'clsTMittenteDestinatario' => array(
                    'DesTipoAnagrafica' => $mittenteDestinatario['DesTipoAnagrafica'],
                    'Cognome' => $mittenteDestinatario['Cognome'],
                    'Nome' => $mittenteDestinatario['Nome'],
                    'CF' => $mittenteDestinatario['CF'],
                    'Indirizzo' => $mittenteDestinatario['Indirizzo'],
                    'Localita' => $mittenteDestinatario['Localita'],
                    'CAP' => $mittenteDestinatario['CAP'],
                    'Comune' => $mittenteDestinatario['Comune'],
                    'Provincia' => $mittenteDestinatario['Provincia'],
                    'Telefono' => $mittenteDestinatario['Telefono'],
                    'Fax' => $mittenteDestinatario['Fax'],
                    'PerConoscenza' =>  (isset($mittenteDestinatario['PerConoscenza']) ? 1 :0),
                    'Email' => $mittenteDestinatario['Email']
                )
            );
        }

        return $this;
    }

    protected $_wsStruct = array(
        'Test_MTA_STA' => array(),
        'GetAOOAbilitateWsEspiAspV2' => array(
            'sMsgErr' => 1
        ),
        'ProtocollaDocumentoV2' => array(
            'clsInputOperazione' => array(
                'VersioneWS' => 3,
                'UserID' => null,
                'Password' => null,
                'CodUtenteProtocollatore' => null,
                'CodUfficioCompetente' => null,
                'clsInputAOO' => array(
                    'N_COD_AOO' => '0',
                    'C_DES_AOO' => null
                ),
                'clsInputFascicolo' => array(
                    'DesFascicolo' => '2015'
                ),
                'clsInputTitolario' => array(
                    'ClasseTitolario' => '34.01.10',
                    'DesTitolario' => 'Disposizioni e direttive'
                ),
                'clsInputProtocollo' => array(
                    'TipoProtocollo' => 'Uscita',
                    'clsTestataDocumento' => array(
                        'Oggetto' => 'Prova protocollazione in uscita da programma 29/01/2015',
                        'Data' => '29/01/2015',
                        'Allegati' => 0,
                        'Note' => ''
                    ),
                    'enOperazioneProtocollo' => 'CreaProtocolloConClassifica',
                    'ListaMittentiDestinatari' => null
                ),
                'UpdateProtoStoria' => 'Si'
            )
        )
    );

    public function debugWs()
    {
        return $this->_wsStruct;
    }

    public function protocollaDocumento()
    {
        return $this->soapCall('ProtocollaDocumentoV2');
    }

    public function soapCall($service)
    {
        try {
            // array_walk_recursive($this->_wsStruct, 'purify');
            $this->_wsResponse = null;
            $this->_wsError = null;
            $opts = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ),
                'https' => array(
                    'curl_verify_ssl_peer' => false,
                    'curl_verify_ssl_host' => false,
                )
            );

            $streamContext = stream_context_create($opts);
            $wsClient = new SoapClient("https://10.199.3.4/WSProtEspiVX/wsespiaspvx.asmx?WSDL", array(
                'trace' => 1,
                'stream_context' => $streamContext,
                'soap_version' => SOAP_1_1
            ));

            $wService = $wsClient->$service($this->_wsStruct[$service]);

            /*
             * TODO
             * Valorizzo ufficio competente con default
             */


            if ($this->_debugTrace) {
                $this->_lastRequestHeaders = $wsClient->__getLastRequestHeaders();
                $this->_lastRequest = $wsClient->__getLastRequest();

                $this->_lastResponseHeaders = $wsClient->__getLastResponseHeaders();
                $this->_lastResponse = $wsClient->__getLastResponse();
            }
            $this->_wsResult = $wService->ProtocollaDocumentoV2Result;
        } catch (SoapFault $fault) {
            $this->_wsError = $fault->faultstring;
            if (is_soap_fault($wService)) {
                trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
            }
        } catch (Exception $e) {
            $this->_wsError = $e->getMessage();
        }

        return $this;
    }

    public function logConnection($id, $tipo = 'pratica')
    {
        Db_Pdo::getInstance()->query('INSERT INTO sys_espiws (documento_id, tipo, ws_result, ws_request_header, ws_request, ws_response_header, ws_response) VALUES
                                                                (:documento_id, :tipo, :ws_result, :ws_request_header, :ws_request, :ws_response_header, :ws_response)', array(
            ':documento_id' => $id,
            ':tipo' => $tipo,
            ':ws_result' => json_encode($this->_wsResult),
            ':ws_request_header' => $this->_lastRequestHeaders,
            ':ws_request' => $this->_lastRequest,
            ':ws_response_header' => $this->_lastResponseHeaders,
            ':ws_response' => $this->_lastResponse
        ));

        return $this;
    }

    protected function getMittenteProtocolloUscita()
    {
        if (isset($this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['DesTipoAnagrafica'])) {
            return array(
                'titolo' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['DesTipoAnagrafica'],
                'nome' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Nome'],
                'cognome' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Cognome'],
                'toponimo' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Indirizzo'],
                'localita' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Localita'],
                'cap' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Cap'],
                'comune' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Comune'],
                'provincia' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Provincia'],
                'email' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario']['Email']
            );
        } elseif (isset($this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario'][0])) {
            foreach ($this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['ListaMittentiDestinatari']['clsTMittenteDestinatario'] as $mittente) {
                if (! $mittente['PerConoscenza']) {
                    return array(
                        'titolo' => $mittente['DesTipoAnagrafica'],
                        'nome' => $mittente['Nome'],
                        'cognome' => $mittente['Cognome'],
                        'toponimo' => $mittente['Indirizzo'],
                        'localita' => $mittente['Localita'],
                        'cap' => $mittente['Cap'],
                        'comune' => $mittente['Comune'],
                        'provincia' => $mittente['Provincia'],
                        'email' => $mittente['Email']
                    );
                } else {
                    $retvalue = array(
                        'titolo' => $mittente['DesTipoAnagrafica'],
                        'nome' => $mittente['Nome'],
                        'cognome' => $mittente['Cognome'],
                        'toponimo' => $mittente['Indirizzo'],
                        'localita' => $mittente['Localita'],
                        'cap' => $mittente['Cap'],
                        'comune' => $mittente['Comune'],
                        'provincia' => $mittente['Provincia'],
                        'email' => $mittente['Email']
                    );
                }
            }
        }
        if (! isset($retvalue)) {
            throw new Exception('Non è stato selezionato e/o inserito un Mittente (per conoscenza non è mittente valido)!');
        }

        return $retvalue;
    }

    public function protocollaPraticaUscita($praticaId)
    {
        $this->pratica_id = $praticaId;
        $db = Db_Pdo::getInstance();
        try {

            $praticaEntrata = $db->query('SELECT * FROM pratiche WHERE pratica_id = :pratica_id', array(
                ':pratica_id' => $this->pratica_id
            ))->fetch();
            $mittente = $this->getMittenteProtocolloUscita();

            $campiProtocolloUscita = array(
                'numeroregistrazione' => $this->_wsResult->NumProtocollo,
                'dataregistrazione' => date('Y-m-d'),
                'tipologia' => 'U',
                'modello' => $praticaEntrata['MODELLO'],
                'titolo' => $mittente['DesTipoAnagrafica'],
                'nome' => $mittente['nome'],
                'cognome' => $mittente['cognome'],
                'toponimo' => $mittente['toponimo'],
                'localita' => $mittente['localita'],
                'cap' => $mittente['cap'],
                'comune' => $mittente['comune'],
                'provincia' => $mittente['provincia'],
                'email' => $mittente['email'],
                'oggetto' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['clsTestataDocumento']['Oggetto'],
                'note' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['clsTestataDocumento']['Note']
            )
            // 'faldone' => $post['clsInputFascicolo']['DesFascicolo'],
            ;
            $params = array();
            foreach ($campiProtocolloUscita as $key => $value) {
                $params[':' . $key] = $value;
            }

            $db->query('insert into pratiche (' . implode(', ', array_keys($campiProtocolloUscita)) . ')
                            values (:' . implode(', :', array_keys($campiProtocolloUscita)) . ')', $params);
            $uscitaId = $db->lastInsertId();
            $db->query('UPDATE pratiche SET uscita = now(), pratica_uscita_id = :uscita_id, protuscita = :NumProtocollo, oggetto = :oggetto WHERE pratica_id = :pratica_id', array(
                ':uscita_id' => $uscitaId,
                ':NumProtocollo' => $this->_wsResult->NumProtocollo,
                ':pratica_id' => $this->pratica_id,
                ':oggetto' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputProtocollo']['clsTestataDocumento']['Oggetto'],
                ));
            $db->query('insert into arc_pratiche_uo (pratica_id, uoid) select :pratica_id, uoid FROM arc_pratiche_uo WHERE pratica_id = :pratica_in_entrata', array(
                ':pratica_id' => $uscitaId,
                ':pratica_in_entrata' => $this->pratica_id
            ));


        } catch (PDOException $e) {

            $this->logConnection($this->pratica_id);
            $this->_wsError = $e->getTrace();
        } catch (PDOException $e) {
            $this->_wsError = $e->getMessage();
        }

        return $this;
    }

    public function protocollaPratica($pratica_id = null, $post)
    {

        $db = Db_Pdo::getInstance();
        try {
            $tipologia = substr($post['tipologia'], 0,1);
            $db->beginTransaction();
            if (is_null($pratica_id)) {
                $modello = Db_Pdo::getInstance()->query('SELECT
                                modello
                            FROM arc_modelli WHERE classificazione = :classifica', [
                    ':classifica' => $this->_wsStruct['ProtocollaDocumentoV2']['clsInputOperazione']['clsInputTitolario']['ClasseTitolario']
                ])->fetchColumn();
                $db->query('INSERT INTO pratiche (numeroregistrazione, dataregistrazione, uscita, tipologia,  
                                                  modello, titolo, nome, cognome, codicefiscale, toponimo, 
                                                  localita, cap, comune, provincia, telefono, fax, email, 
                                                  oggetto, note, numeroriferimento, dataarrivo, datadocumento, responsabile_id  )
                                                    VALUES (:numeroregistrazione, now(), :uscita, :tipologia, 
                                                    :modello, :titolo, :nome, :cognome, :codicefiscale, :toponimo, 
                                                    :localita, :cap, :comune, :provincia, :telefono, :fax, :email, 
                                                    :oggetto, :note, :numeroriferimento, :dataarrivo, :datadocumento, :responsabile_id)', array(
                    ':tipologia' => $tipologia,
                    ':uscita' => ($tipologia == 'U' ? Date('Y-m-d') : null),
                    ':numeroregistrazione' => $this->_wsResult->NumProtocollo,
                    ':titolo' => $post['clsTMittenteDestinatario']['DesTipoAnagrafica'],
                    ':modello' => ((integer) $modello > 0 ? $modello : null),
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
                    ':note' => $post['clsTestataDocumento']['Note'],
                    ':numeroriferimento' => $post['clsTestataDocumento']['Numero'],
                    ':dataarrivo' => (new Date($post['clsTestataDocumento']['Arrivo']))->toMysql(),
                    ':datadocumento' => (new Date($post['clsTestataDocumento']['Data']))->toMysql(),
                    ':responsabile_id' => $post['RESPONSABILE_ID'],

                ));
                $this->pratica_id = $db->lastInsertId();
            } else {
                $this->pratica_id = $pratica_id;
                $db->query('update pratiche set numeroregistrazione = :numeroregistrazione,
                                            dataregistrazione = now() ,
                                            oggetto = :oggetto,
                                            uscita = :uscita
                        WHERE pratica_id = :pratica_id', [
                    ':pratica_id' => $this->pratica_id,
                    ':numeroregistrazione' => $this->_wsResult->NumProtocollo,
                    ':uscita' => ($tipologia == 'U' ? Date('Y-m-d') : null),
                    ':oggetto' => $post['clsTestataDocumento']['Oggetto'],
                ]);
            }
            $uoEspi = true;
            $assegnazione_uo = $db->query('SELECT uoid FROM arc_organizzazione WHERE CODE = :assegnazione LIMIT 1',[
                ':assegnazione' => $post['assegnazione'],
            ])->fetchColumn();
            if (isSet($post['uoid'])) {
                foreach ($post['uoid'] as $uoid) {
                    $uoEspi = ($assegnazione_uo == $uoid) ? false : $uoEspi;
                    $db->query('INSERT INTO arc_pratiche_uo (uoid, pratica_id) VALUES (:uoid, :pratica_id)', array(
                        ':uoid' => $uoid,
                        ':pratica_id' => $this->pratica_id
                    ));
                }
            }
            if ($uoEspi) {
                $db->query('INSERT INTO arc_pratiche_uo (uoid, pratica_id) VALUES (:uoid, :pratica_id)', array(
                    ':uoid' => $assegnazione_uo,
                    ':pratica_id' => $this->pratica_id
                ));
            }
            $db->commit();
            $this->logConnection($pratica_id);
        } catch (PDOException $e) {
            $db->rollBack();
            $this->logConnection($pratica_id);
            $this->_wsError = $e->getTrace();
        } catch (ErrorException $e) {
            $this->logConnection($pratica_id);
            $this->_wsError = $e->getTrace();
        }

        return $this;
    }

    public function protocollaPecEntrata($pec_id, $post)
    {
        $db = Db_Pdo::getInstance();
        try {
            $db->beginTransaction();
            $db->query('INSERT INTO pratiche (numeroregistrazione, dataregistrazione, tipologia, 
                                              titolo, nome, cognome, codicefiscale, toponimo, localita, 
                                              cap, comune, provincia, telefono, fax, email, oggetto, 
                                              numeroriferimento, dataarrivo, datadocumento, responsabile_id  )
                                                VALUES (:numeroregistrazione, now(), "E", :titolo, :nome, :cognome, 
                                                :codicefiscale, :toponimo, :localita, :cap, :comune, :provincia, 
                                                :telefono, :fax, :email, :oggetto, :numeroriferimento, 
                                                :dataarrivo, :datadocumento, :responsabile_id)', array(
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
                ':responsabile_id' => $post['RESPONSABILE_ID'],
            ));
            $this->pratica_id = $db->lastInsertId();
            $db->query('UPDATE arc_pratiche_pec SET pratica_id = :pratica_id, numeroregistrazione = :numeroregistrazione,
                                dataregistrazione = now() WHERE pec_id = :pec_id', array(
                ':pratica_id' => $this->pratica_id,
                ':numeroregistrazione' => $this->_wsResult->NumProtocollo,
                ':pec_id' => $pec_id
            ));
            $uoEspi = true;
            if (isSet($post['uoid'])) {
                foreach ($post['uoid'] as $uoid) {
                    $uoEspi = ($post['clsInputOperazione']['CodUfficioCompetente'] == $uoid) ? false : $uoEspi;
                    $db->query('INSERT INTO arc_pratiche_uo (uoid, pratica_id) VALUES (:uoid, :pratica_id)', array(
                        ':uoid' => $uoid,
                        ':pratica_id' => $this->pratica_id
                    ));
                }
            }
            if ($uoEspi) {
                $db->query('INSERT INTO arc_pratiche_uo (uoid, pratica_id) VALUES (:uoid, :pratica_id)', array(
                    ':uoid' => $post['clsInputOperazione']['CodUfficioCompetente'],
                    ':pratica_id' => $this->pratica_id
                ));
            }
            $db->commit();
            $this->logConnection($pec_id);
        } catch (PDOException $e) {
            $db->rollBack();
            $this->logConnection($pec_id);
            $this->_wsError = $e->getTrace();
        } catch (ErrorException $e) {
            $this->logConnection($pec_id);
            $this->_wsError = $e->getTrace();
        }

        return $this;
    }

    public function saveWsdl()
    {
        $wsdlFile = new SplFileObject(FILES_PATH . DIRECTORY_SEPARATOR . 'espiwsdl.txt', 'w+');
        $wsdlFile->fwrite(file_get_contents('https://10.199.3.19/WSProtEspiVX/wsespiaspvx.asmx?WSDL'));

        return $this;
    }

    private function dumpResponse($wsService)
    {
        r($wsService, false);
        foreach ($wService as $key => $response) {
            r($key, false);
            r($response, false);
            if (is_object($response)) {
                $this->dumpResponse($response);
            }
        }

        return $this;
    }

    public function getLastrequestheaders()
    {
        return $this->_lastRequestHeaders;
    }

    public function getLastrequest()
    {
        return $this->_lastRequest;
    }

    public function getLastresponseheaders()
    {
        return $this->_lastResponseHeaders;
    }

    public function getLastresponse()
    {
        return $this->_lastResponse;
    }

    public function getWsresult()
    {
        return $this->_wsResult;
    }

    public function setPecsuap($xml)
    {
        $xmlObj = simplexml_load_string($xml);



        return $this;
    }

    public function testWs($service){
    	try {
    		$this->_debugTrace = 1;
    		$this->_wsResponse = null;
    		$this->_wsError = null;


            $opts = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ),
                'https' => array(
                    'curl_verify_ssl_peer' => false,
                    'curl_verify_ssl_host' => false,
                )
            );

            $streamContext = stream_context_create($opts);
            $wsClient = new SoapClient("https://10.199.3.4/WSProtEspiVX/wsespiaspvx.asmx?WSDL", array(
                'trace' => 1,
                'stream_context' => $streamContext,
                'soap_version' => SOAP_1_1
            ));


    		r($this->_wsStruct[$service],false);

    		$wService = $wsClient->$service($this->_wsStruct[$service]);

    		if($this->_debugTrace){
    			$this->_lastRequestHeaders = $wsClient->__getLastRequestHeaders();
    			$this->_lastRequest = $wsClient->__getLastRequest();

    			$this->_lastResponseHeaders = $wsClient->__getLastResponseHeaders();
    			$this->_lastResponse = $wsClient->__getLastResponse();

    			r($this->_lastRequestHeaders,false);
    			r($this->_lastRequest,false);
    			r($this->_lastResponseHeaders,false);
    			r($this->_lastResponse,false);


    		}

    	} catch (SoapFault $fault) {
    		$this->_wsError = $fault->faultstring;
    		if (is_soap_fault($wService)) {
    			trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
    		}
    	} catch (Exception $e) {
    		$this->_wsError = $e->getMessage();
    	}

    	r($this->_wsError,false);



    	return $this->_wsResult;

    }


}

function purifyHtml(&$item, $key)
{
    $item = strip_tags($item);
}