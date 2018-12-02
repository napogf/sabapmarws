<?php

class Pratica
{

    protected static $_instance;

    protected $_session;

    protected $pratica;

    public function __construct()
    {}

    public function __get($key){

        return (is_null($key) ? $this->pratica : (isset($this->pratica->$key) ? $this->pratica->$key : null));
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    protected function getSession()
    {
        if ($this->_session === null) {
            $this->_session = new Session_Namespace(__CLASS__);
        }

        return $this->_session;
    }

    public function setId($id)
    {
        $db = Db_Pdo::getInstance();



        $this->pratica = (object) $db->query('select * from pratiche where pratica_id = :pratica_id', array(
            ':pratica_id' => $id
        ))->fetch();


        if (empty( $this->pratica->PRATICA_ID)) {
            return false;
        }

        $praticheFascicolo = [];
        if ($fascicolo = $db->query(
                'select * from pratiche_fascicoli where fascicolo_id = (
                    SELECT distinct fascicolo_id FROM pratiche_fascicoli 
                        WHERE pratica_id = :pratica_id LIMIT 1
        ) ORDER BY pratiche_fascicoli.pratica_id', array(':pratica_id' => $id ))->fetchAll()) {

            $this->pratica->fascicolo = (object) $fascicolo;
            foreach ($this->pratica->fascicolo as $pratica) {
            	$praticheFascicolo[] = $pratica['PRATICA_ID'];

            }
        } else {
            $this->pratica->fascicolo = null;
            $praticheFascicolo[] = $id;
        }





        $files = $db->query('select * from uploads where pratica_id in (' . implode(',', $praticheFascicolo) . ')')->fetchAll();

        $this->pratica->files = $files ? (object) $files : null;





        $destinatari = $db->query('select * from arc_destinazioni where pratica_id in (' . implode(',', $praticheFascicolo) . ')')->fetchAll();

//         $this->pratica->mail = $mail ? (object) $mail : null;
        $mail = $db->query('select * from arc_pratiche_pec where pratica_id in (' . implode(',', $praticheFascicolo) . ') ORDER BY PEC_ID ')
            ->fetchAll();

        $this->pratica->pec =  $mail ? (object) $mail : null;
        $this->pratica->files = $files ? (object) $files : null;

        $this->pratica->destinatari = $destinatari ? (object) $destinatari : null;

        $sospensioni = $db->query('select * from arc_sospensioni where pratica_id = :pratica_id', array(
            ':pratica_id' => $id
        ))->fetchAll();

        $this->pratica->sospensioni = $sospensioni ? (object) $sospensioni : null ;

        //  Trovo il progetto
        if(!empty($this->pratica->PROJECT_ID)){
            $this->pratica->progetto = $db->query('SELECT * FROM arc_pratiche_prj WHERE project_id = :project_id',[
        	   ':project_id' => $this->pratica->PROJECT_ID
            ])->fetch();
        } else {
            if($this->pratica->fascicolo){
                foreach ($this->pratica->fascicolo as $value) {
                    if($progetto = $db->query('SELECT * FROM arc_pratiche_prj WHERE pratica_id = :pratica_id',[
        	           ':pratica_id' => $value['pratica_id'],
                    ])->fetch()){
                        $this->pratica->progetto = $progetto;
                        break;
                    }
                }
            }
        }

        return $this;
    }

    public function getInfo($key=null){

        return (is_null($key) ? $this->pratica : (isset($this->pratica->$key) ? $this->pratica->$key : null));
    }

    public function hasId()
    {
       

        return ! empty($this->pratica->PRATICA_ID);
    }

    public function getId()
    {

        $pratica = $this->pratica;
        if ($this->hasId()) {
            return $pratica->PRATICA_ID;
        }

        $pratica->delete();

        header('Location: ' . BASEURL . '/');
        exit();
    }

    public function getPratica($key = null)
    {
        $this->getId();
        $session = $this->getSession()->pratica;
        if (is_null($key)) {
            return $session;
        } elseif (! empty($session->$key)) {
            return $session->$key;
        }

        return null;
    }

    function getPec()
    {
        $this->getId();
        $session = $this->getSession();

        return $session->pec;
    }

    public function getSuapEnte()
    {
        $this->getId();
        $session = $this->getSession();
        $emlResult = $session->mail;
        $pecFile = PEC_PATH . '/' . $emlResult->PEC_ID . "_pec_" . $emlResult->MAIL_HASH . '.eml';

        $Parser = new displayMail();
        $Parser->setText(file_get_contents($pecFile));

        if ($suapXml = $Parser->getAttachedFile('SUAPENTE.XML')) {
            $result = array(
                'status' => 'error',
                'xml' => $suapXml
            );
        } else {
            $result = array(
                'status' => 'error',
                'message' => 'File non caricato correttamente!'
            );
        }

        return $result;
    }

    public function getSegnatura($attachments = array())
    {

        try {
            $config = [];
            $configurazione = Db_Pdo::getInstance()->query('select * from sys_config');
            while ($conf = $configurazione->fetch()){
                $config[$conf['chiave']] = $conf['valore'];
            }

            $errorReporting = ini_get('error_reporting');
            error_reporting(0);

            $segnaturaDom = new DOMDocument();
            $segnaturaDom->loadXML(file_get_contents(LOGIN_PATH . DIRECTORY_SEPARATOR . 'Segnatura.xml'));
            $segnaturaXPath = new DOMXPath($segnaturaDom);


            $codiceAmministrazione = $segnaturaXPath->query('//Segnatura/Intestazione/Identificatore/CodiceAmministrazione');
            $codiceAmministrazione->item(0)->nodeValue = $config['SEG_XML_CODAOO'];


            $segnaturaXPath->query('//Segnatura/Intestazione/Identificatore/CodiceAOO')
                ->item(0)->nodeValue = $config['SEG_XML_CODAOO'];

            $segnaturaXPath->query('//Segnatura/Intestazione/Identificatore/NumeroRegistrazione')
                ->item(0)->nodeValue = $this->getInfo('NUMEROREGISTRAZIONE');

            $segnaturaXPath->query('//Segnatura/Intestazione/Identificatore/DataRegistrazione')
                ->item(0)->nodeValue = (new Date($this->getInfo('DATAREGISTRAZIONE')))->format('d/m/Y');

            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/IndirizzoTelematico')
                ->item(0)->nodeValue = $config['PEC_USERNAME'];

            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/Denominazione')
                ->item(0)->nodeValue = $config['MOD_SOPRINTENDENZA'];
            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/CodiceAmministrazione')
                ->item(0)->nodeValue = $config['WS_C_DES_AOO'];
            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/UnitaOrganizzativa/Denominazione')
                ->item(0)->nodeValue = $config['WS_C_DES_AOO'];
            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Toponimo')
                ->item(0)->nodeValue = $config['SEGNATURA_Toponimo'];
            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Civico')
                ->item(0)->nodeValue = $config['SEGNATURA_Civico'];
            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/CAP')
                ->item(0)->nodeValue = $config['SEGNATURA_CAP'];
            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Comune')
                ->item(0)->nodeValue = $config['SEGNATURA_Comune'];
            $segnaturaXPath->query('//Segnatura/Intestazione/Origine/Mittente/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Provincia')
                ->item(0)->nodeValue = $config['SEGNATURA_Provincia'];
            // Destinatario
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/IndirizzoTelematico')
                ->item(0)->nodeValue = $this->getInfo('EMAIL') > '' ? $this->getInfo('EMAIL') : ' - ';
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/Amministrazione/Denominazione')
                ->item(0)->nodeValue = trim($this->getInfo('NOME') . ' ' . $this->getInfo('COGNOME')) > '' ?
                                        trim($this->getInfo('NOME') . ' ' . $this->getInfo('COGNOME')) : ' - ' ;
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Toponimo')
                ->item(0)->nodeValue = $this->getInfo('TOPONIMO');
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/Destinatario/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Civico')
                ->item(0)->nodeValue = $this->getInfo('CIVICO');
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/Destinatario/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/CAP')
                ->item(0)->nodeValue = $this->getInfo('CAP');
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/Destinatario/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Comune')
                ->item(0)->nodeValue = $this->getInfo('COMUNE');
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/Destinatario/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Provincia')
                ->item(0)->nodeValue = $this->getInfo('PROVINCIA');
            $segnaturaXPath->query('//Segnatura/Intestazione/Destinazione/Destinatario/Amministrazione/UnitaOrganizzativa/IndirizzoPostale/Provincia')
                ->item(0)->nodeValue = $this->getInfo('PROVINCIA');

            $segnaturaXPath->query('//Segnatura/Intestazione/Oggetto')
                ->item(0)->nodeValue = $this->getInfo('COMUNEOGG');

            $segnaturaXPath->query('//Segnatura/Descrizione/Documento/Oggetto')
                ->item(0)->nodeValue = $this->getInfo('COMUNEOGG');

            $segnaturaXPath->query('//Segnatura/Descrizione/Documento/Oggetto')
                ->item(0)->nodeValue = $_POST['Subject'];//$this->getInfo('COMUNEOGG');

            $allegatiElement = $segnaturaXPath->query('//Segnatura/Descrizione/Allegati')->item(0);
            foreach ($attachments as $attachment) {
                $documento = $segnaturaDom->createElement('Documento');
                $documento->setAttribute('nome',substr($attachment[file], strpos('_', $attachment['file'])));
                $documento->setAttribute('TipoRiferimento','MIME');
                $oggetto = $segnaturaDom->createElement('Oggetto',$attachment['description']);
                $documento->appendChild($oggetto);
                $allegatiElement->appendChild($documento);
            }

            error_reporting($errorReporting);
            $segnaturaDom->saveXML();
            $segnaturaDom->save(TMP_PATH . DIRECTORY_SEPARATOR . $this->getId() . '_segnatura.xml');

        } catch (Exception $e) {

            return false;
        }



        return true;
    }

    public function regenerate()
    {
        if ($this->hasId()) {
            return $this->setId($this->getId());
        }

        return $this;
    }

    public function delete()
    {
        $pratica = $this->getSession();
        $pratica->delete();

        return true;
    }

    public function getAllegati()
    {
        $this->getId();
        $session = $this->getSession();

        return $session->files;
    }

    public function verificaFascicolo(){
        $db = Db_Pdo::getInstance();
        if($this->pratica->TIPOLOGIA == 'E' AND !$fascicolo_id = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id',[
            ':pratica_id' => $this->pratica->PRATICA_ID,
        ])->fetchColumn()){
            $fascicolo_id = (integer) $db->query(
                    'select max(fascicolo_id) from pratiche_fascicoli')->fetchColumn() + 1 ;
            $db->query(
                'insert into pratiche_fascicoli (fascicolo_id, pratica_id,tipologia)
                            values (:fascicolo_id, :pratica_id, :tipologia) ',
                [
                    ':fascicolo_id' => $fascicolo_id,
                    ':pratica_id' => $this->pratica->PRATICA_ID,
                    ':tipologia' => $this->pratica->TIPOLOGIA,
                ]);
        }
        if(!empty($this->pratica->PRATICA_USCITA_ID)){
            if(!$db->query('SELECT pratica_id FROM pratiche_fascicoli 
                                WHERE pratica_id = :pratica_id ',[
                ':pratica_id' => $this->pratica->PRATICA_USCITA_ID,
            ])->fetchColumn()){
                $db->query(
                    'insert into pratiche_fascicoli (fascicolo_id, pratica_id,tipologia)
                            values (:fascicolo_id, :pratica_id, :tipologia) ',
                    [
                        ':fascicolo_id' => $fascicolo_id,
                        ':pratica_id' => $this->pratica->PRATICA_USCITA_ID,
                        ':tipologia' => 'U',
                    ]);
            }
            $db->query('UPDATE pratiche SET MODELLO = :modello, DATAARRIVO = :dataarrivo, USCITA = :uscita 
                        WHERE PRATICA_ID = :pratica_id', [
                ':pratica_id' => $this->pratica->PRATICA_USCITA_ID,
                ':modello' => $this->pratica->MODELLO,
                ':dataarrivo' => $this->pratica->DATAREGISTRAZIONE,
                ':uscita' => (new Date())->format('Y-m-d'),
            ]);
        }

    }


}
