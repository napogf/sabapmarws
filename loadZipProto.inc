<?php
/*
 * Created on 18/mag/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
//include "login/autentication.php";
require_once('dbfunctions.php');

function updateProtocollo($protocollo){
	$dreg=$protocollo['DataRegistrazione'][1];
	$nreg=$protocollo['NumeroRegistrazione'][1];
	$dbTab='pratiche';
	$pecRegExp = '/^PEC_ID_([0-9]{1,})[\s|\-](.*)/i';
	preg_match_all($pecRegExp,$protocollo['OggettoESPI'][1],$pecMail);
	if(isSet($pecMail[1][0]) and (integer) $pecMail[1][0] > 0){
		$pecAttach = array('numeroregistrazione' => $protocollo['NumeroRegistrazione'][1],
				'dataregistrazione' => implode('-',array_reverse(explode('-', $protocollo['DataRegistrazione'][1]))),
				'pec_id' => (integer) $pecMail[1][0],
		);
		$protocollo['OggettoESPI'][1] = $pecMail[2][0];
	} else {
		$pecAttach = null;
	}
	
	if ($qResult=dbselect('select * from '.$dbTab.' where numeroregistrazione = \''.$nreg.'\' and dataregistrazione = str_to_date(\''.$dreg.'\',\'%d-%m-%Y\')')){
		$updQuery=' update '.$dbTab.' set ';
		$endQuery=' where pratica_id = '.$qResult['ROWS'][0]['PRATICA_ID'];
		$qToken='';
		$uoIndex=0;
		foreach ($protocollo as $key => $value){
			if($key == 'Storia'){
				if (sizeof($value)>0){
					dbupdate('delete from pratiche_storia where pratica_id = '.$qResult['ROWS'][0]['PRATICA_ID']);
					for ($index = 0; $index < sizeof($value); $index++) {
						$storyQuery='insert into pratiche_storia (pratica_id, tipologia, azione, ufficio, utente, daora, aora ) values ' .
									'( '.$qResult['ROWS'][0]['PRATICA_ID'].',' .
									' \''.$protocollo['Tipologia'][1].'\', ' .
									' \''.$value[$index]['Azione'].'\', ' .
									' \''.$value[$index]['Ufficio'].'\', ' .
									' \''.$value[$index]['Utente'].'\', ' .
									' str_to_date(\''.$value[$index]['DaOra'].'\',\'%d-%m-%Y\'), ' .
									' str_to_date(\''.$value[$index]['AOra'].'\',\'%d-%m-%Y\') ' .
											')';
						dbupdate($storyQuery);
					}
				}
			} else {
				$fieldNames = '';
				$fieldValues = '';
				$zonaFound = false;
				if ($key=='UnitaOrganizzativa'){
					for ($index = 0; $index < sizeof($value); $index++) {
						$zonaQuery="select zona, tipo from arc_zone where tipo = 'Z' and description = '".addslashes($value[$index]['Denominazione'])."'";
						$zonaResult=dbselect($zonaQuery);
						if($zonaResult['NROWS']>0){
							if (!preg_match('|zona|',$updQuery)){
								$fieldNames ='zona';
								$fieldValues =$zonaResult['ROWS'][0]['zona'];
								$updQuery .=  $fieldNames > '' ?$qToken.$fieldNames . '=' . $fieldValues:'';
								$zonaFound = true;
								$qToken = ' , ';
								$fieldNames = '';
								$fieldValues = '';
							}
						}
						$ufficioQuery="select ufficio, tipo from arc_uffici where description REGEXP '".addslashes($value[$index]['Denominazione'])."'";
						$ufficioResult=dbselect($ufficioQuery);
						if($ufficioResult['NROWS']>0 ){
							if (!preg_match('|ufficio|',$updQuery)){
								$fieldNames ='ufficio';
								$fieldValues =$ufficioResult['ROWS'][0]['ufficio'];
								$updQuery .=  $fieldNames > '' ?$qToken.$fieldNames . '=' . $fieldValues:'';
								$qToken = ' , ';
								$fieldNames = '';
								$fieldValues = '';
							}
						}
						if ($index==0){
							$fieldNames ='uodenominazione';
							$fieldValues ="'".addslashes($value[$index]['Denominazione'])."'";
						} elseif ($index==1){
							$fieldNames ='cladenominazione';
							$fieldValues ="'".addslashes($value[$index]['Denominazione'])."'";
						}
					}
					if (!$zonaFound) $updQuery .=  $qToken. ' zona = null ' ;
				} else {
					if(!is_null($value[1])){
						if ($value[0]=='dataregistrazione' or $value[0]=='dataarrivo' or $value[0]=='datadocumento'){
							$fieldNames = $value[0];
							$fieldValues = "str_to_date('".$value[1]."','%d-%m-%Y')";
						} else {
							$fieldNames = $value[0];
							$fieldValues = "'".addslashes($value[1])."'";
						}
					}
				}
					$updQuery .=  $fieldNames > '' ?$qToken.$fieldNames . '=' . $fieldValues:'';
					$qToken = ' , ';
			}
		}
		$updQuery .= $endQuery;
//		var_dump($updQuery);
		dbupdate($updQuery);
	} else {
		$fieldNames='anno, ';
		$fieldValues=date('Y').", ";
		$qToken='';
		$uoIndex=0;
		foreach ($protocollo as $key => $value){
			switch ($key) {
				case 'Storia':
					$storiaArray=$value;
					break;
				case 'UnitaOrganizzativa':
					for ($index = 0; $index < sizeof($value); $index++) {
						$zonaQuery="select zona, tipo from arc_zone where tipo = 'Z' and description = '".addslashes($value[$index]['Denominazione'])."'";
						$zonaResult=dbselect($zonaQuery);
						if($zonaResult['NROWS']>0){
							if (!preg_match('|zona|',$fieldNames)){
								$fieldNames.=$qToken.'zona';
								$fieldValues.=$qToken.$zonaResult['ROWS'][0]['zona'];
							}
						}
						$ufficioQuery="select ufficio, tipo from arc_uffici where description = '".addslashes($value[$index]['Denominazione'])."'";
						$ufficioResult=dbselect($ufficioQuery);
						if($ufficioResult['NROWS']>0 ){
							if (!preg_match('|ufficio|',$fieldNames)){
								$fieldNames.=$qToken.'ufficio';
								$fieldValues.=$qToken.$ufficioResult['ROWS'][0]['ufficio'];
							}
						}
						if ($index==0){
							$fieldNames.=$qToken.'uodenominazione';
							$fieldValues.=$qToken."'".addslashes($value[$index]['Denominazione'])."'";
						} elseif ($index==1){
							$fieldNames.=$qToken.'cladenominazione';
							$fieldValues.=$qToken."'".addslashes($value[$index]['Denominazione'])."'";
						}
					}
					break;
				default:
					if(!is_null($value[1])){
						if ($value[0]=='dataregistrazione' or $value[0]=='dataarrivo' or $value[0]=='datadocumento' ){
							$fieldNames.=$qToken.$value[0];
							$fieldValues.=$qToken."str_to_date('".$value[1]."','%d-%m-%Y')";
						} else {
// Importo anche le pratiche in uscita e aggiungo la tipologia al database
//							if ($key <> 'Tipologia'){
								$fieldNames.=$qToken.$value[0];
								$fieldValues.=$qToken."'".addslashes($value[1])."'";
//							}
						}
					}
					break;
			}
//			if ($key <> 'Tipologia'){
				$qToken = ' , ';
//			}
		}
		$insQuery='insert into '.$dbTab.' ('.$fieldNames.') values ( '.$fieldValues.' )';
		if (dbupdate($insQuery)){
			$praticaId=dbLastId();
			if (sizeof($storiaArray)>0){
				for ($index = 0; $index < sizeof($storiaArray); $index++) {
					$storyQuery='insert into pratiche_storia (pratica_id, tipologia, azione, ufficio, utente, daora, aora ) values ' .
								'( '.$praticaId.',' .
								' \''.$protocollo['Tipologia'][1].'\', ' .
								' \''.$storiaArray[$index]['Azione'].'\', ' .
								' \''.$storiaArray[$index]['Ufficio'].'\', ' .
								' \''.$storiaArray[$index]['Utente'].'\', ' .
								' str_to_date(\''.$value[$index]['DaOra'].'\',\'%d-%m-%Y\'), ' .
								' str_to_date(\''.$value[$index]['AOra'].'\',\'%d-%m-%Y\') ' .
										')';
					dbupdate($storyQuery);
				}
			}
		}
	}
	if(is_array($pecAttach) 
			and isset($pecAttach['numeroregistrazione'])
			and isset($pecAttach['dataregistrazione'])
			and isset($pecAttach['pec_id'])
			){
		$pecQuery = 'update arc_pratiche_pec set numeroregistrazione = \'' . $pecAttach['numeroregistrazione'] . '\', 
												 dataregistrazione = \'' . $pecAttach['dataregistrazione'] . '\' 
					where pec_id = ' . $pecAttach['pec_id'];
		dbupdate($pecQuery);
	}
	return true;	
}

//include("pageheader.inc");
print('Importazione del '.date('d-m-Y')."\n");
if ($handle = opendir(getcwd().'/dacaricare')) {
    while (false !== ($filename = readdir($handle))) {
        if ($filename != "." && $filename != "..") {
        	$fileInfo=pathinfo($filename);
        	if(strtoupper($fileInfo['extension'])=='ZIP') {
        		print('Caricamento File '.$filename.'</br>'."\n");
				$zip= zip_open(getcwd().'/dacaricare/'.$filename);
				if ($zip) {
				    while ($zip_entry = zip_read($zip)) {
				        //echo "Name:               " . zip_entry_name($zip_entry) . "</br>\n";
				        //echo "Actual Filesize:    " . zip_entry_filesize($zip_entry) . "\n";
				        //echo "Compressed Size:    " . zip_entry_compressedsize($zip_entry) . "\n";
				        //echo "Compression Method: " . zip_entry_compressionmethod($zip_entry) . "\n";
				        if (zip_entry_open($zip, $zip_entry, "r")) {
				            //echo "File Contents:\n";
				            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							$dom = new DOMDocument('1.0', 'iso-8859-1');
							$dom->loadXML($buf);
							$found = $dom->getElementsByTagName("Segnatura");
							foreach ($found as $nodeSegnatura) {
										$protoStruct=array(
											'Tipologia' =>  array (0 => 'tipologia') ,
//											'Annullato' => '' ,
											'NumeroRegistrazione' => array (0 => 'numeroregistrazione'),
											'DataRegistrazione' => array (0 => 'dataregistrazione'),
											'DataArrivo' => array (0 => 'dataarrivo'),
											'Nome' => array (0 => 'nome'),
											'Cognome' => array (0 => 'cognome'),
											'Titolo' => array (0 => 'titolo'),
											'CodiceFiscale' => array (0 => 'codicefiscale'),
											'Toponimo' => array (0 => 'toponimo'),
											'Civico' => array (0 => 'civico'),
											'Cap' => array (0 => 'cap'),
											'Comune' => array (0 => 'comune'),
											'Provincia' => array (0 => 'provincia'),
											'Localita' => array (0 => 'localita'),
											'Nazione' => array (0 => 'nazione'),
											'Telefono' => array (0 => 'telefono'),
											'Fax' => array (0 => 'fax'),
											'UnitaOrganizzativa' => array(),
											'Oggetto' => array (0 => 'descrizione'),
											'OggettoESPI' => array (0 => 'oggetto'),
											'1' => array (0 => 'fasidentificativo'),
											'2' => array (0 => 'fasoggetto'),
											'DataDocumento' => array (0 => 'datadocumento'),
											'DataArrivo' => array (0 => 'dataarrivo'),
											'NumeroRiferimento' => array (0 => 'numeroriferimento'),
											'Classifica' => array (0 => 'classifica'),
											'AllegatNumero' => array (0 => 'allegatinumero'),
											'IdScheda' => array (0 => 'idscheda'),
											'Mappale' => array (0 => 'mappale'),
											'Foglio' => array (0 => 'foglio'),
											'Anagrafico' => array (0 => 'anagrafico'),
											'Scadenza' => array (0 => 'scadenza'),
											'DocStampato' => array (0 => 'docstampato'),
											'DataStmp' => array (0 => 'datastmp'),
											'Condizione' => array (0 => 'condizione'),
											'Utente' => array (0 => 'pnome'),
											'Note' => array (0 => 'note'),
											'3' => array (0 => 'ptoponimo'),
											'4' => array (0 => 'pcivico'),
											'5' => array (0 => 'pcap'),
											'6' => array (0 => 'pcomune'),
											'7' => array (0 => 'pprovincia'),
											'8' => array (0 => 'comuneogg'),
											'9' => array (0 => 'vincolo',
											'Storia' => array ())
										);
								        if ($nodeSegnatura->hasChildNodes()) {
									            foreach ($nodeSegnatura->childNodes as $nodo) {
									                switch ($nodo->nodeName) {
									                	case 'Storia':
															foreach ($nodo->childNodes as $nodoStoria){
																switch ($nodoStoria->nodeName) {
																	case 'Azione':
																		$azioneArray=array('Azione'=> $nodoStoria->getAttribute('tipo') ,
																							'Utente'=> null,
																							'Ufficio'=> null,
																							'DaOra' => null,
																							'AOra' => null);
																		foreach	($nodoStoria->childNodes as $nodoAzione){
																			switch ($nodoAzione->nodeName) {
																				case 'Ufficio':
																					$azioneArray['Ufficio']=$nodoAzione->nodeValue;
																					break;
																				case 'Utente':
																					$azioneArray['Utente']=$nodoAzione->nodeValue;
																					break;
																				case 'DaOra':
																					$azioneArray['DaOra']=$nodoAzione->nodeValue;
																					break;
																				case 'AOra':
																					$azioneArray['AOra']=$nodoAzione->nodeValue;
																					break;
																			}
																		}
																		$protoStruct['Storia'][]=$azioneArray;
																}
															}
									                	case 'Descrizione':
															foreach ($nodo->childNodes as $nodoDescrizione){
																switch ($nodoDescrizione->nodeName) {
																	case 'Documento':
																		foreach	($nodoDescrizione->childNodes as $nodoDocumento){
																			switch ($nodoDocumento->nodeName) {
																				case 'DataArrivo':
																					$protoStruct['DataArrivo'][]=$nodoDocumento->nodeValue;
																					break;
																				case 'DataDocumento':
																					$protoStruct['DataDocumento'][]=$nodoDocumento->nodeValue;
																					break;
																				case 'NumeroRiferimento':
																					$protoStruct['NumeroRiferimento'][]=$nodoDocumento->nodeValue;
																					break;
																			}
																		}
																}
															}
														case 'Intestazione':
//															$protoStruct['Annullato']=$nodo->getAttribute('annullato');
															if ($nodo->hasAttribute('tipologia')) {
																$protoStruct['Tipologia'][]=$nodo->getAttribute('tipologia');
															}
															foreach ($nodo->childNodes as $nodoIntestazione){
																switch ($nodoIntestazione->nodeName) {
																	case 'Note':
																		$protoStruct['Note'][]=$nodoIntestazione->nodeValue;
																		break;
																	case 'Oggetto':
																		$protoStruct['OggettoESPI'][]=$nodoIntestazione->nodeValue;
																		break;
																	case 'Identificatore':
																		foreach	($nodoIntestazione->childNodes as $nodoIdentificatore){
																			switch ($nodoIdentificatore->nodeName) {
																				case 'CodiceAmministrazione':
																					$protoStruct['CodiceAmministrazione'][]=$nodoIdentificatore->nodeValue;
																					break;
																				case 'NumeroRegistrazione':
																					$protoStruct['NumeroRegistrazione'][]=$nodoIdentificatore->nodeValue;
																					break;
																				case 'DataRegistrazione':
																					$protoStruct['DataRegistrazione'][]=$nodoIdentificatore->nodeValue;
																					break;
																			}
																		}
																		break;
																	case 'Origine':
																		foreach	($nodoIntestazione->childNodes as $nodoOrigine){
																			switch ($nodoOrigine->nodeName) {
																				case 'Amministrazione':
																					foreach ($nodoOrigine->childNodes as $nodoAmministrazione){
																						if ($nodoAmministrazione->nodeName=='UnitaOrganizzativa') {
																								$protoStruct['UnitaOrganizzativa'][]=array('Denominazione'=>$nodoAmministrazione->nodeValue);
																						}
																					}
																				case 'Mittente':
																					foreach ($nodoOrigine->childNodes as $nodoMittente){
																						switch ($nodoMittente->nodeName) {
																							case 'Nome':
																								$protoStruct['Nome'][]=$nodoMittente->nodeValue;
																								break;
																							case 'Cognome':
																								$protoStruct['Cognome'][]=$nodoMittente->nodeValue;
																								break;
																							case 'Titolo':
																								$protoStruct['Titolo'][]=$nodoMittente->nodeValue;
																								break;
																							case 'CodiceFiscale':
																								$protoStruct['CodiceFiscale'][]=$nodoMittente->nodeValue;
																								break;
																							case 'IndirizzoPostale':
																								if ($nodoMittente->hasChildNodes()){
																									foreach ($nodoMittente->childNodes as $indirizzoPostale){
																										switch ($indirizzoPostale->nodeName) {
																											case 'Toponimo':
																												$protoStruct['Toponimo'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Civico':
																												$protoStruct['Civico'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Cap':
																												$protoStruct['Cap'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Cap':
																												$protoStruct['Cap'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Comune':
																												$protoStruct['Comune'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Provincia':
																												$protoStruct['Provincia'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Localita':
																												$protoStruct['Localita'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Nazione':
																												$protoStruct['Nazione'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Telefono':
																												$protoStruct['Telefono'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Fax':
																												$protoStruct['Fax'][]=$indirizzoPostale->nodeValue;
																												break;																																												}
																									}
																								}
																								break;
																						}
																					}
																			}
																		}
																		break;
																	case 'Destinazione':
																		foreach	($nodoIntestazione->childNodes as $nodoOrigine){
																			switch ($nodoOrigine->nodeName) {
																				case 'Destinatario':
																					$protoStruct['Titolo'][]=$nodoOrigine->getAttribute('tipologia');
																					foreach ($nodoOrigine->childNodes as $nodoMittente){
																						switch ($nodoMittente->nodeName) {
																							case 'Nome':
																								$protoStruct['Nome'][]=$nodoMittente->nodeValue;
																								break;
																							case 'Cognome':
																								$protoStruct['Cognome'][]=$nodoMittente->nodeValue;
																								break;
																							case 'Titolo':
																								$protoStruct['Titolo'][]=$nodoMittente->nodeValue;
																								break;
																							case 'CodiceFiscale':
																								$protoStruct['CodiceFiscale'][]=$nodoMittente->nodeValue;
																								break;
																							case 'IndirizzoPostale':
																								if ($nodoMittente->hasChildNodes()){
																									foreach ($nodoMittente->childNodes as $indirizzoPostale){
																										switch ($indirizzoPostale->nodeName) {
																											case 'Toponimo':
																												$protoStruct['Toponimo'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Civico':
																												$protoStruct['Civico'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Cap':
																												$protoStruct['Cap'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Cap':
																												$protoStruct['Cap'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Comune':
																												$protoStruct['Comune'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Provincia':
																												$protoStruct['Provincia'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Localita':
																												$protoStruct['Localita'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Nazione':
																												$protoStruct['Nazione'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Telefono':
																												$protoStruct['Telefono'][]=$indirizzoPostale->nodeValue;
																												break;
																											case 'Fax':
																												$protoStruct['Fax'][]=$indirizzoPostale->nodeValue;
																												break;																																												}
																									}
																								}
																								break;
																						}
																					}
																			}
																		}
																		break;
																	case 'Descrizione':
																		if ($nodoIntestazione->hasChildNodes()){
																			$uoNode=array();
																			foreach ($nodoIntestazione->childNodes as $descrizione){
																				if ($descrizione->nodeName == 'Documento' and $descrizione->hasChildNodes()){
																					foreach ($descrizione->childNodes as $documento){
																						switch ($documento->nodeName) {
																							case 'AllegatiNumero':
																								$protoStruct['AllegatiNumero'][]=$documento->nodeValue;
																								break;
																						}
																					}
																				}
																			}
																		}
																		break;

																	case 'UnitaOrganizzativa':
																		if ($nodoIntestazione->hasChildNodes()){
																			$uoNode=array();
																			foreach ($nodoIntestazione->childNodes as $unitaOrg){
																				if ($unitaOrg->nodeName == 'Denominazione'){
																					$uoNode['Denominazione']=$unitaOrg->nodeValue;
																				} elseif ($unitaOrg->nodeName == 'OggettoDiCompetenza') {
																					$uoNode['Oggetto']=$unitaOrg->nodeValue;
																				}
																			}
																		}
																		$protoStruct['UnitaOrganizzativa'][]=$uoNode;
																		break;
																}
															}

															break;


														default:
															break;
													}

									            }
									        }
								if (updateProtocollo($protoStruct)){
//									var_dump($protoStruct);
									print('Caricata La registrazione '.$protoStruct['NumeroRegistrazione'][1].' del '.$protoStruct['DataRegistrazione'][1].'</br>');
									print("\n");
								}
							}
				            zip_entry_close($zip_entry);
				        }
				    }
				    zip_close($zip);
				}
	        	copy('./dacaricare/'.$filename,'./caricati/'.$filename);
	        	unlink('./dacaricare/'.$filename);
        	}
        }
    }
    closedir($handle);
}
//include "pagefooter.inc";
?>
