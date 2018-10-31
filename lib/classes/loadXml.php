<?php
/*
 * Created on 26/nov/2013
 *
 * loadXml.php
*/
class loadXml {
	private $_zipFile;
	private $_praticheArray ;
	private $_praticaArray;
	private $_praticaId;
	private $_praticaUnitaOrganizzativa;
	private $_praticaStoria ;
	function __construct($zipFile) {
		$this->_zipFile = $zipFile;
		$zip= zip_open('./dacaricare/'.$this->_zipFile);
		if ($zip) {
		    while ($zip_entry = zip_read($zip)) {
		        if (zip_entry_open($zip, $zip_entry, "r")) {
		            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					$praticheOby = new xml2array($buf);
					$this->_praticheArray = $praticheOby->getResult();
		            zip_entry_close($zip_entry);
				}
		    }
		    zip_close($zip);
		    $this->moveZip();
		}
	}
	function loadPratiche(){
		if (is_array($this->_praticheArray['Protocolli']['Segnatura']) and !isSet($this->_praticheArray['Protocolli']['Segnatura']['Intestazione'])){
			foreach($this->_praticheArray['Protocolli']['Segnatura'] as $pratica){
				$this->loadElement($pratica);
			}
		} else {
			$this->loadElement($this->_praticheArray['Protocolli']['Segnatura']);
		}
		
		return $this;
	}

	function loadElement($pratica){
		$this->praticaToArray($pratica);
		$praticaQuery='select PRATICA_ID from pratiche where numeroregistrazione = "'.$pratica['Intestazione']['Identificatore']['NumeroRegistrazione'].'" and ' .
															'dataregistrazione = str_to_date(\''.$dataRegistrazione = $pratica['Intestazione']['Identificatore']['DataRegistrazione'].'\',\'%d-%m-%Y\') ';
		if(!$praticaFound=dbselect($praticaQuery)){
			$this->insertPratica();
		} else {
			$this->updatePratica($praticaFound['ROWS'][0]['PRATICA_ID']);
		}
	}
	function maskValue($value){
		if(is_string($value) and strlen($value)>1) return addslashes($value);
	}
	function praticaToArray($pratica){
		switch ($pratica['Intestazione']['@tipologia']) {
			case 'I':
				$this->loadUnitaOrganizzativa($pratica['Intestazione']['Destinazione']['UnitaOrganizzativa']);
				break;
			case 'U':
				$this->loadUnitaOrganizzativaUscita($pratica['Intestazione']['Origine']['Amministrazione']['UnitaOrganizzativa']);
				break;
			default:
				$this->loadUnitaOrganizzativa($pratica['Intestazione']['UnitaOrganizzativa']);
			break;
		}



		$this->_praticaStoria = $pratica['Storia'];
		$this->_praticaArray = array(
				'anno' => 'date_format(str_to_date(\''.$pratica['Intestazione']['Identificatore']['DataRegistrazione'].'\',\'%d-%m-%Y\'),\'%Y\')' ,
				'tipologia' => '\'' .$pratica['Intestazione']['@tipologia']. '\'' ,
		        'annullato' => '\'' .$pratica['Intestazione']['@annullato']. '\'' ,
				'numeroregistrazione' => '\'' .$pratica['Intestazione']['Identificatore']['NumeroRegistrazione']. '\'' ,
				'dataregistrazione' => 'str_to_date(\''.$pratica['Intestazione']['Identificatore']['DataRegistrazione'].'\',\'%d-%m-%Y\')' ,
				// Descrizione
				'dataarrivo' => 'str_to_date(\''.$pratica['Descrizione']['Documento']['DataArrivo'].'\',\'%d-%m-%Y\')' ,
				'datadocumento' => 'str_to_date(\''.$pratica['Descrizione']['Documento']['DataDocumento'].'\',\'%d-%m-%Y\')' ,
				'numeroriferimento' => '\'' .$pratica['Descrizione']['Documento']['NumeroRiferimento']. '\''
				);

				if($pratica['Intestazione']['@tipologia']=='E'){
				// Mittente
					$this->_praticaArray['nome'] = '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['Nome']). '\'';
					$this->_praticaArray['cognome'] = '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['Cognome']). '\'';
					$this->_praticaArray['titolo'] =  '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['@tipologia']). '\'';
					$this->_praticaArray['codicefiscale'] =  '\'' .$pratica['Intestazione']['Origine']['Mittente']['CodiceFiscale']. '\'';
					$this->_praticaArray['toponimo'] =  '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Toponimo']). '\'';
					$this->_praticaArray['civico'] =  '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Civico']). '\'';
					$this->_praticaArray['cap'] =  '\'' .$pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Cap']. '\'';
					$this->_praticaArray['comune'] =  '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Comune']). '\'';
					$this->_praticaArray['provincia'] =  '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Provincia']). '\'';
					$this->_praticaArray['localita'] =  '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Localita']). '\'';
					$this->_praticaArray['nazione'] =  '\'' .$this->maskValue($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Nazione']). '\'';

					$this->_praticaArray['telefono'] = is_string($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Telefono']) ?
														'\'' .$pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Telefono']. '\'':"''";
					$this->_praticaArray['fax'] = is_string($pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Fax']) ?
														'\'' .$pratica['Intestazione']['Origine']['Mittente']['IndirizzoPostale']['Fax']. '\'':"''";
				}

				// Oggetto
				$this->_praticaArray['oggetto'] =  '\'' .$this->maskValue($pratica['Intestazione']['Oggetto']). '\'';
				$this->_praticaArray['note'] =  '\'' .$this->maskValue($pratica['Intestazione']['Note']). '\'';

				$this->_praticaArray['titolazione'] =  '\'' .$this->maskValue($pratica['Intestazione']['Classificazione']['Classifica']['Livello']['@nome']).' - '.$this->maskValue($pratica['Intestazione']['Classificazione']['Classifica']['Livello']['Descrizione']). '\'' ;
	}


	function loadUnitaOrganizzativa($unitaOrganizzativa){
		$this->_praticaUnitaOrganizzativa=array();
		if(is_array($unitaOrganizzativa)){
			if(isset($unitaOrganizzativa['Denominazione'])){
				$this->findUnitaOrganizzativa($unitaOrganizzativa['Denominazione']);
			} else {
				foreach ($unitaOrganizzativa as $value){
					$this->findUnitaOrganizzativa($value['Denominazione']);
				}
			}
		}
	}
	function loadUnitaOrganizzativaUscita($unitaOrganizzativa){
		$this->_praticaUnitaOrganizzativa=array();
		if(is_array($unitaOrganizzativa)){
			r($unitaOrganizzativa);
		} else {
			$this->findUnitaOrganizzativa($unitaOrganizzativa);
		}
	}

	function findUnitaOrganizzativa($value){
		$db = Db_Pdo::getInstance();
		$unitaOrganizzativa = is_array($value) ? $value['Denominazione'] : $value;
		$result=$db->query('select UOID from arc_organizzazione where description = :description',array(':description' => $unitaOrganizzativa))->fetch();
		if (empty($result) and !empty($unitaOrganizzativa)){
			$db->query('insert into arc_organizzazione (tipo, description) values ("X" , :unita_organizzativa) ',array(':unita_organizzativa' => $unitaOrganizzativa));
			$this->_praticaUnitaOrganizzativa[] = $db->lastInsertId();
		} else {
			$this->_praticaUnitaOrganizzativa[] = $result['UOID'];
		}
		return $this;
	}

	function insertPratica(){

		$fieldsNames = '';
		$fieldsValues = '';
		$token = '';
		$this->_praticaId = null;
		foreach($this->_praticaArray as $key => $value){
			$fieldsNames .= $token.$key;
			$fieldsValues .= $token.$value ;
			$token = ', ' ;
		}
		$insQuery = ' insert into pratiche ('.$fieldsNames.') values ( '.$fieldsValues.' ) ';
		dbUpdate($insQuery);
		print('Inserita Pratica -> '.$this->_praticaArray['numeroregistrazione'].'<br>'."\n");
		$this->_praticaId = dbLastId();
		$this->updateStoria();
		$this->updateUnitaOrganizzativa();
	}
	function updatePratica($praticaId){
		$this->_praticaId = $praticaId;
		$updQuery = 'update pratiche set ';
		$token = '';
		foreach($this->_praticaArray as $key => $value){
			$updQuery .= $token.$key.' = '.$value;
			$token = ', ';
		}
		$updQuery .= ' where pratica_id = '.$praticaId ;


		dbupdate($updQuery);
		print('Aggiornata Pratica -> '.$this->_praticaArray['numeroregistrazione'].'<br>'."\n");
		$this->updateStoria();
		$this->updateUnitaOrganizzativa();

	}
	function updateUnitaOrganizzativa() {
		foreach($this->_praticaUnitaOrganizzativa as $value ){

			foreach ($this->_praticaUnitaOrganizzativa as $value) {
			    if(!Db_Pdo::getInstance()->query('select * from arc_pratiche_uo where pratica_id = :pratica_id and uoid = :uoid',array(
			        ':pratica_id' => $this->_praticaId,
			        ':uoid' => $value,
			    ))->fetch()){
			        dbupdate('insert into arc_pratiche_uo (pratica_id, uoid) values ('.$this->_praticaId.', '.$value.' ) ');
			    }

			}
		}
	}

	function updateStoria(){
		dbupdate('delete from pratiche_storia where pratica_id = '.$this->_praticaId);
		foreach((array) $this->_praticaStoria as $value ){
			for ($index = 0; $index < sizeof($value); $index++) {
				$storyQuery='insert into pratiche_storia (pratica_id, ' .
							'tipologia, ' .
							'azione, ' .
							'ufficio, ' .
							'utente, ' .
							'daora, ' .
							'aora ) values ' .
							'( '.$this->_praticaId.',' .
							$this->_praticaArray['tipologia'].', ' .
							' \''.addslashes($value[$index]['@tipo']).'\', ' .
							' \''.addslashes($value[$index]['Ufficio']).'\', ' .
							' \''.addslashes($value[$index]['Utente']).'\', ' .
							' str_to_date(\''.$value[$index]['DaOra'].'\',\'%d-%m-%Y\'), ' .
							' str_to_date(\''.$value[$index]['AOra'].'\',\'%d-%m-%Y\') ' .
									')';
				dbupdate($storyQuery);
			}

		}
	}


	function moveZip(){
    	copy(getcwd().'/dacaricare/'.$this->_zipFile,getcwd().'/caricati/'.$this->_zipFile);
    	unlink(getcwd().'/dacaricare/'.$this->_zipFile);
	}



}