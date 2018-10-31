<?php
/*
 * Created on 11/giu/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once ("dbfunctions.php");

function fixEncoding($in_str)
{
  $cur_encoding = mb_detect_encoding($in_str) ;
  if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8"))
    return $in_str;
  else
    return utf8_encode($in_str);
} // fixEncoding

// Windows
//$attribute = 'name';
// Linux

$attribute = $ooSearchAtrr > '' ? $ooSearchAtrr : 'text:name' ;

$tmpDir = getcwd().'/tmp/'.$_SESSION['sess_uid'];

// $tmpDir = sys_get_temp_dir().'/'.$_SESSION['sess_uid'];


if (file_exists($tmpDir)){
	// delete all tmp contents
	if (is_dir($tmpDir)) {
	    if ($dh = opendir($tmpDir)) {
	        while (($file = readdir($dh)) !== false) {
	        	if ($file <> '.'  and  $file <> '..'){
		              unlink($tmpDir.'/'.$file);

	        	}
	        }
	        closedir($dh);
	    }
	}
} else {
	// create it
	mkdir($tmpDir);
}


$query = 'select ' .
'pr.PRATICA_ID, ' .
'pr.numeroregistrazione, ' .
'pr.Cdataregistrazione, ' .
'DATE_FORMAT(pr.dataregistrazione,\'%Y\') annoreg, ' .
'DATE_FORMAT(pr.dataregistrazione,\'%d-%m-%Y\') dataregistrazione, ' .
'pr.modello, ' .
'DATE_FORMAT(pr.funzionario,\'%d-%m-%Y\') funzionario, ' .
'DATE_FORMAT(pr.firma,\'%d-%m-%Y\') firma, ' .
'DATE_FORMAT(pr.uscita,\'%d-%m-%Y\') uscita, ' .
'DATE_FORMAT(pr.scadenza,\'%d-%m-%Y\') scadenza, ' .
'az.moddesc as zona, ' .
'az.sigla, ' .
'au.sigla as SIGLA_UFF, ' .
'au.description as ufficio, ' .
'pr.nome, ' .
'pr.responsabile, ' .
'ifnull(pr.cognome, pr.pnome) cognome, ' .
'pr.titolo, ' .
'pr.codicefiscale, ' .
'pr.toponimo, ' .
'pr.civico, ' .
'pr.cap, ' .
'pr.comune, ' .
'pr.provincia, ' .
'pr.localita, ' .
'pr.nazione, ' .
'pr.telefono, ' .
'pr.fax, ' .
'pr.uodenominazione, ' .
'pr.oggetto, ' .
'pr.indirizzo_og, ' .
'pr.comune_og, ' .
'pr.cladenominazione, ' .
'pr.descrizione, ' .
'pr.fasidentificativo, ' .
'pr.fasoggetto, ' .
'pr.datadocumento, ' .
'pr.Cdataarrivo, ' .
'DATE_FORMAT(pr.dataarrivo,\'%d-%m-%Y\') dataarrivo, ' .
'pr.numeroriferimento, ' .
'pr.classifica, ' .
'pr.allegatinumero, ' .
'pr.idscheda, ' .
'pr.mappale, ' .
'pr.foglio, ' .
'pr.anagrafico, ' .
'pr.Cscadenza, ' .
'pr.docstampato, ' .
'pr.datastmp, ' .
'pr.condizione, ' .
'pr.pnome, ' .
'pr.ptoponimo, ' .
'pr.pcivico, ' .
'pr.pcap, ' .
'pr.pcomune, ' .
'pr.pprovincia, ' .
'pr.comuneogg, ' .
'pr.vincolo, ' .
'pr.anno, ' .
'pr.note, ' .
'pr.email, ' .
'am.description as nome_modello, ' .
'pr.numeroregistrazione as NUMERO_PROTOCOLLO, ' .
'DATE_FORMAT(pr.dataregistrazione,\'%Y\') as ANNO, ' .
'DATE_FORMAT(pr.dataregistrazione,\'%d-%m-%Y\') DATA_REGISTRAZIONE, ' .
'DATE_FORMAT(pr.dataregistrazione,\'%Y%m%d\') DATA_AAAAMMDD, ' .
'DATE_FORMAT(pr.dataarrivo,\'%d-%m-%Y\') DATA_ARRIVO, ' .
'am.description as TIPO_PRATICA, ' .
'DATE_FORMAT(pr.funzionario,\'%d-%m-%Y\') DATA_FUNZIONARIO, ' .
'DATE_FORMAT(pr.firma,\'%d-%m-%Y\') DATA_FIRMA, ' .
'DATE_FORMAT(pr.uscita,\'%d-%m-%Y\') DATA_USCITA, ' .
'DATE_FORMAT(pr.scadenza,\'%d-%m-%Y\') DATA_SCADENZA, ' .
'az.moddesc as ZONA, ' .
'pr.RESPONSABILE, ' .
'az.SIGLA, ' .
'au.description as UFFICIO, ' .
'if( pr.nome IS NULL , pr.cognome, concat( pr.cognome, \' \', pr.nome ) ) AS MITTENTE, ' .
'pr.titolo as MIT_TITOLO, ' .
'pr.codicefiscale as MIT_CODICEFISCALE, ' .
'pr.toponimo as MIT_TOPONIMO, ' .
'pr.civico as MIT_CIVICO, ' .
'pr.cap as MIT_CAP, ' .
'pr.comune as MIT_COMUNE, ' .
'pr.provincia as MIT_PROV, ' .
'pr.localita as MIT_LOCALITA, ' .
'pr.nazione as MIT_NAZIONE, ' .
'pr.telefono as MIT_TELEFONO, ' .
'pr.fax as MIT_FAX, ' .
'pr.email as MIT_EMAIL, ' .
'pr.uodenominazione, ' .
'pr.NUMERORIFERIMENTO, ' .
'date_format(pr.DATADOCUMENTO,\'%d-%m-%Y\') as DATADOCUMENTO, ' .
'pr.oggetto as OGGETTO_ESPI, ' .
'pr.comuneogg OGGETTO_INSERITO, ' .
'pr.indirizzo_og as OGG_INDIRIZZO, ' .
'pr.comune_og as OGG_COMUNE, ' .
'pr.mappale as OGG_MAPPALE, ' .
'pr.foglio as OGG_FOGLIO, ' .
'pr.anagrafico AS OGG_ANAGRAFICO, ' .
'pr.condizione as OGG_PARERE, ' .
'pr.allegatinumero as OGG_ALLEGATI_NUMERO, ' .
'pu.NUMEROREGISTRAZIONE as OGG_PROTUSCITA, ' .
'date_format(pu.DATAREGISTRAZIONE,\'%d-%m-%Y\') as OGG_DATAUSCITA, ' .
'pr.note as OGG_NOTE, ' .
'pr.pnome as PROP_NOME, ' .
'pr.ptoponimo as PROP_TOPONIMO, ' .
'pr.pcivico as PROP_CIVICO, ' .
'pr.pcap as PROP_CAP, ' .
'pr.pcomune as PROP_COMUNE, ' .
'pr.pprovincia as PROP_PROVINCIA, ' .
'pr.ISTR01, ' .
'pr.ISTR02, ' .
'pr.ISTR03, ' .
'pr.NOTE01 as INTEGRAZIONE_01,' .
'pr.NOTE02 as INTEGRAZIONE_02,' .
'date_format(pr.PAE_DATA_PARERE,\'%d-%m-%Y\') as PAE_DATA_PARERE, ' .
'pr.PAE_LOC_INTERVENTO, ' .
'pr.PAE_VAL_NCONDIVISE, ' .
'pr.PAE_DESC_INCOMPATIBILITA, ' .
'pr.PAE_MOTIVAZIONI, ' .
'pr.PAE_NOTE_PRESCRIZIONI, ' .
'pr.PAE_INTEGRAZIONI, ' .
'pr.CONTRIBUTI as TOT_CONTRIBUTI, ' .
'pr.CONT_RIFAUTLAV, ' .
'pr.CONT_TIPINT, ' .
'pr.CONT_TIPNONAMM, ' .
'ae.description as ESITO, ' .
'date_format(pr.CONT_DATAIST,\'%d-%m-%Y\') as CONT_DATAIST,' .
'av.denominazione as VINCOLO, ' .
'trim(concat(av.ubicazioneinit,\' \',av.ubicazioneprinc)) as VINCOLI_INDIRIZZO, ' .
'av.localita as VINCOLI_LOC, ' .
'concat(av.comune,\'/\',av.provincia) as VINCOLI_COM, ' .
'av.fogliocatastale as VINCOLI_FOGLIO, ' .
'av.particelle as VINCOLI_MAPPALE, ' .
'av.provvedimentoministeriale as VINCOLI_DM, ' .
'av.trascrizioneinconservatoria as VINCOLI_TRASCR, ' .
'av.posizionemonumentale as VINCOLI_POSMON, ' .
'av.posizionevincoli as VINCOLI_POSVIN, ' .
'IF(at.id = null,\'\',concat(al1.liv01,\'.\',al2.liv02,\'.\',al3.liv03,\' -> \',al1.description,\' - \',al2.description,\' - \',al3.description))  as TITOLAZIONE,' .
'IF(at.id = null,\'\',concat(al1.liv01,\'.\',al2.liv02,\'.\',al3.liv03))  as TITO_NUMERO, ' .
'ac.comune as TITO_COMUNE,' .
'ac.PROVINCIA as TITO_PROVINCIA, ' .
'at.fascicolo as TITO_FASCICOLO,' .
'ae.DESCRIPTION as ESITO_L, ' .
//'substring(ae.DESCRIPTION,7) as ESITO, ' .
'pr.SER_TIPOLOGIA, ' .
'pr.SER_ORA, ' .
'pr.SER_LUOGO, ' .
'pr.SER_DESCRIZIONE, ' .
'pr.SER_AMBITO, ' .
'pr.SER_PARERI, ' .
'pr.SER_DOCUMENTAZIONE, ' .
'pr.SER_OSSERVAZIONI, ' .
'pr.SER_VALUTAZIONI ' .
'from pratiche pr ' .
'left join pratiche pu on (pu.PRATICA_ID = pr.PRATICA_USCITA_ID) ' .
'left join arc_esiti ae on (ae.esito_id = pr.esito_id) ' .
'left join arc_zone az on ((az.zona = pr.zona) and (az.tipo = \'Z\')) ' .
'left join arc_uffici au on ((au.ufficio = pr.ufficio) and (au.tipo = \'U\')) ' .
'left join vincoli av on (av.vincolo_id = pr.vincolo_id)  ' .
'left join arc_modelli am on (am.modello = pr.modello)  ' .
'left join arc_titolazioni at on (at.id = pr.titolazione) ' .
'left join arc_comuni ac on (ac.id = at.comune) ' .
'left join arc_titolario al3 on (al3.titolo = at.titolo) ' .
'left join arc_tito02 al2 on ((al2.liv01 = al3.liv01) and (al2.liv02 = al3.liv02)) ' .
'left join arc_tito01 al1 on (al1.liv01 = al2.liv01) ' .
'where pr.pratica_id = ' . $_GET['nReg'];


if ($queryResult = dbselect($query)) {
//	foreach($queryResult['ROWS'][0] as $key => $result){
//		print($key."\n");
//	}
//	exit;
	if (!$modelloResult = dbselect('select FILE_OO from arc_documenti where DOC_ID=' . $_GET['DOC_ID'])) {
		print ('Modello no trovato! - Avvisare l\'amministratore del sistema ');
	} else {
		$modello = $_GET['DOC_ID'] . '-FILE_OO-' . $modelloResult['ROWS'][0]['FILE_OO'];
	}
	$altreDestinazioni = '';
	$altreDestinazioniQuery = 'select * from arc_destinazioni where pratica_id = ' . $_GET['nReg'];
	if ($altreDestinazioniResult = dbselect($altreDestinazioniQuery)) {
		for ($index = 0; $index < $altreDestinazioniResult['NROWS']; $index++) {
			$altreDestinazioni .= trim($altreDestinazioniResult['ROWS'][$index]['NOME'] .  ' ' . $altreDestinazioniResult['ROWS'][$index]['COGNOME']) . "\n" ;
			$altreDestinazioni .= $altreDestinazioniResult['ROWS'][$index]['PER_CONOSCENZA']>' '?$altreDestinazioniResult['ROWS'][$index]['PER_CONOSCENZA'] . "\n":'' ;
			$altreDestinazioni .= $altreDestinazioniResult['ROWS'][$index]['TOPONIMO'] . "\n" .
			$altreDestinazioniResult['ROWS'][$index]['CAP'] . " - " .
			$altreDestinazioniResult['ROWS'][$index]['COMUNE'] . " " .
			$altreDestinazioniResult['ROWS'][$index]['PROVINCIA'] . "\n\r\n";

		}
	}

	$vociContributi = '';
	$vociContributiQuery = 'select RIF_ART ,' .
	'DESCRIPTION ,' .
	'if(AMMISSIBILE=\'Y\',\'S\',\'N\') as "AMMISSIBILE", ' .
	'DETRAZIONE ,' .
	'(INCIDENZA*100) as "INCIDENZA", ' .
	'(DETRAZIONE*INCIDENZA) as "INDETRAIBILE" ' .
	'From arc_contributi ' .
	'where pratica_id = ' . $_GET['nReg'];

	if ($vociContributiResult = dbselect($vociContributiQuery)) {
		$vociContributi = '#' . "\t" . 'R.A.' . "\t" . 'Descr. Voce' . "\t" . 'Imp. Voce' . "\t" . '%' . "\t" . 'Imp. Detr.' . "\n\r\n";
		$detrazioniNonAmmesse = 0;
		$vociContributiArray=array();
		for ($index = 0; $index < $vociContributiResult['NROWS']; $index++) {
			$detrazioniNonAmmesse = $detrazioniNonAmmesse + $vociContributiResult['ROWS'][$index]['INDETRAIBILE'];
			$vociContributi .= $index +1 . "\t" .
			$vociContributiResult['ROWS'][$index]['RIF_ART'] . "\t" .
			$vociContributiResult['ROWS'][$index]['DESCRIPTION'] . "\t" .
			number_format($vociContributiResult['ROWS'][$index]['DETRAZIONE'], 2, ',', '.') . "\t" .
			number_format($vociContributiResult['ROWS'][$index]['INCIDENZA'], 2, ',', '.') . "\t" .
			number_format($vociContributiResult['ROWS'][$index]['INDETRAIBILE'], 2, ',', '.') . "\n\r\n";
			$vociContributiArray[]=array(0=>$index +1,
										 1=>$vociContributiResult['ROWS'][$index]['RIF_ART'],
										 2=>$vociContributiResult['ROWS'][$index]['DESCRIPTION'],
										 3=>number_format($vociContributiResult['ROWS'][$index]['DETRAZIONE'], 2, ',', '.'),
										 4=>number_format($vociContributiResult['ROWS'][$index]['INCIDENZA'], 2, ',', '.'),
										 5=>number_format($vociContributiResult['ROWS'][$index]['INDETRAIBILE'], 2, ',', '.')
										 );
		}
	}

	$recFind = $queryResult['ROWS'][0];
	$recFind['ALTRE_DESTINAZIONI'] = $altreDestinazioni;
	$recFind['NOME_SOPRINTENDENTE'] = Db_Pdo::getInstance()->query('select valore FROM sys_config where chiave = "KEY_SOPRINTENDENTE"')->fetchColumn();
	$recFind['VOCI_CONTRIBUTI'] = $vociContributi;
	$recFind['TOT_NON_AMMESSE'] = number_format($detrazioniNonAmmesse, 2, ',', '.');
    if(preg_match('/ - /',$recFind['ESITO'])){
        $recFind['ESITO'] = substr($recFind['ESITO'],6);
    }
    IF(!empty($recFind['OGG_PROTUSCITA'])){
        $recFind['DATA_USCITA'] = $recFind['OGG_DATAUSCITA'];
    }
	$recFind['TOT_AMMESSE'] = number_format($recFind['TOT_CONTRIBUTI'] - $detrazioniNonAmmesse, 2, ',', '.');
	$recFind['TOT_CONTRIBUTI'] = number_format($recFind['TOT_CONTRIBUTI'] , 2, ',', '.');
	$configValues = Db_Pdo::getInstance()->query('SELECT chiave, valore FROM sys_config')->fetchAll();
	foreach ($configValues as $configValue ) {
		$recFind[$configValue['chiave']] = $configValue['valore'];
	}


	$filename = "$dir_upload$modello";
	$tmpZipFile = "$tmpDir/newtextXmlOO.odt";
	$zip = zip_open("$filename");
	if(!file_exists($filename)){
		print('Il modello ha un nome sbagliato ('.$filename.')- rifai l\'upload!' );
		exit;
	}
	$newZip = new ZipArchive;
	$newZipFile = $newZip->open($tmpZipFile, ZipArchive :: CREATE);
	if ($zip) {
		while ($zip_entry = zip_read($zip)) {
			$buf = null;
			if (zip_entry_name($zip_entry) == 'content.xml' and zip_entry_open($zip, $zip_entry, "r")) {
				//echo "File Contents:\n";
				$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				$dom = new DOMDocument();
				$dom->loadXML($buf);
				$xpath = new DOMXpath($dom);
				$cTable=$xpath->query("//table:table[@table:name='CONTRIBUTI']");
				if (!is_null($cTable)) {
					foreach ($cTable as $element) {
						$nodes = $element->childNodes;
						foreach ($nodes as $node) {
							if ($node->nodeName=='table:table-row'){
								for ($x=0;$x < sizeof($vociContributiArray);$x++){
									// se è la prima riga la riempio coi valori senza crearne una nuova
									if($x==0){
										$cellList=$xpath->query(".//text:span",$node);
										if ($cellList->length==0) $cellList=$xpath->query(".//text:p",$node);
										for($z=0; $z<sizeof($vociContributiArray[$x]); $z++){
											$cellList->item($z)->nodeValue=fixEncoding($vociContributiArray[$x][$z]);
										}
									} else {
										$newNode = $node->cloneNode(true);
										$cellList=$xpath->query(".//text:span",$newNode);
										if ($cellList->length==0) $cellList=$xpath->query(".//text:p",$newNode);
										for($z=0; $z<sizeof($vociContributiArray[$x]); $z++){
											$cellList->item($z)->nodeValue=fixEncoding($vociContributiArray[$x][$z]);
						      			}
								      	$node->parentNode->appendChild($newNode);
					      			}
					      		}
					      		break;
							}
						}
					}
				}
//				exit;
				foreach($recFind as $key=>$value){
					$textValue=$dom->createTextNode($value);
					$path="//*[@text:name='".strtoupper($key)."']";
					$userFields = $xpath->query($path);
					if (!is_null($userFields)) {
					    foreach ($userFields as $node) {
					    	//$node->nodeValue=$textValue;
					    	$node->setAttribute('office:string-value', fixEncoding(str_replace('&', '-', $value)));
					    }
					}
				}

				$newZip->addFromString('content.xml', $dom->saveXML());
				zip_entry_close($zip_entry);
			} else {
				if (zip_entry_open($zip, $zip_entry, "r")) {
					$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					file_put_contents($tmpDir  . basename(zip_entry_name($zip_entry)), $buf);
					$newZip->addFile($tmpDir . basename(zip_entry_name($zip_entry)), zip_entry_name($zip_entry));
					zip_entry_close($zip_entry);

				}
			}

		}
		zip_close($zip);
	}
	$newZip->close();
	Header('Content-Length: ' . filesize($tmpZipFile));
	header('Content-Type: application/octet-stream');
	//header('Content-Disposition: inline; filename="'.$queryResult['ROWS'][0]['anagrafico'].'-'.$queryResult['ROWS'][0]['cognome'].'-'.substr(trim($queryResult['ROWS'][0]['oggetto']),strlen(trim($queryResult['ROWS'][0]['anagrafico']))-10,10).'.odt"');
	header('Content-Disposition: inline; filename="' . $queryResult['ROWS'][0]['DATA_AAAAMMDD'] . '-' . $queryResult['ROWS'][0]['numeroregistrazione'] . '-' . trim($queryResult['ROWS'][0]['TITO_COMUNE']).'-' . trim($queryResult['ROWS'][0]['TITO_FASCICOLO']) . '.odt"');
	readfile($tmpZipFile);
	unlink($tmpZipFile);
}
?>