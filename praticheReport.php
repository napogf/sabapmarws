<?php
/*
 * Created on 27/set/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("Etable_c.inc");
class myHtmlETable extends htmlETable {
}

if (isSet($_GET['filter'])){
	$searchValues=array();
	$searchValues['keyword']=$_GET['keyword'];
	$searchValues['nregFilter']=$_GET['nregFilter'];
	$searchValues['anagFilter']=$_GET['anagFilter'];
	$searchValues['modFilter']=$_GET['modFilter'];
	$searchValues['filter']=$_GET['filter'];
	$searchValues['daDataArrivo']=$_GET['daDataArrivo'];
	$searchValues['aDataArrivo']=$_GET['aDataArrivo'];
	$searchValues['daDataUscita']=$_GET['daDataUscita'];
	$searchValues['aDataUscita']=$_GET['aDataUscita'];
	$searchValues['orderBy']=$_GET['orderBy'];
	$searchValues['ORD']=$_GET['ORD'];
	$searchValues['ufficioFilter']=$_GET['ufficioFilter'];
	$searchValues['zonaFilter']=$_GET['zonaFilter'];
	$_SESSION['barraStampe'] = $searchValues;
} else {
	if($_GET['clearFilter']=='Y'){
		$_SESSION['barraStampe'] = null;
	}
}

// Build Filters and Order
switch ($_SESSION['barraStampe']['filter']) {
	case 'open':
		$whereClause=' and (pr.modello is null or dataarrivo is null) ' .
					 ' and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ' ;
		break;
	case 'suspended':
		$whereClause=' and ((pr.scadenza = \'00-00-0000\' or pr.scadenza is null) and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) and pr.modello is not null) ' ;
		break;
	case 'alarm':
		$whereClause=' and  date_add(pr.dataarrivo, INTERVAL (am.scadenza) DAY) <= now() ' .
					 ' and (pr.scadenza > \'00-00-0000\' ) ' .
					 ' and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ' ;
		break;
	case 'alert':
		$whereClause=' and  date_add(pr.dataarrivo, INTERVAL (am.scadenza-am.allarme) DAY) <= now() ' .
					 ' and (pr.scadenza > \'00-00-0000\' ) ' .
					 'and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ' ;
		break;
	case 'closed':
		$whereClause=' and (pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ';
		break;
//	default:
//		$whereClause= ((isSet($_SESSION['barraStampe']['keyword']) and ($_SESSION['barraStampe']['keyword'] > ''))
//												or (isSet($_SESSION['barraStampe']['nregFilter']) and ($_SESSION['barraStampe']['nregFilter'] > ''))
//												or (isSet($_SESSION['barraStampe']['anagFilter']) and ($_SESSION['barraStampe']['anagFilter'] > ''))
//												or (isSet($_SESSION['barraStampe']['daDataUscita']) and ($_SESSION['barraStampe']['daDataUscita'] > ''))
//												or (isSet($_SESSION['barraStampe']['aDataUscita']) and ($_SESSION['barraStampe']['aDataUscita'] > ''))
//												)?
//					'':
//					' and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ';
//		break;
}
$isAdmin=dbselect('select * from user_zone_ref where zona=1 and user_id='.$sess_uid);
if (!$isAdmin){
	$whereClause .= 'and (pr.zona in  (select uzr.zona from user_zone_ref uzr where uzr.user_id ='.$sess_uid.')  or  ' .
						'  pr.ufficio in  (select ufr.ufficio from user_uffici_ref ufr where ufr.user_id ='.$sess_uid.') ) '.$whereClause;
} else {
	$whereClause .= 'and ((pr.zona is not null) or (pr.ufficio is not null))';
}
if (isSet($_SESSION['barraStampe']['keyword']) and ($_SESSION['barraStampe']['keyword'] > '')) {
	$whereClause .= ' and ((pr.cognome REGEXP \''.$_SESSION['barraStampe']['keyword'].'\') or ' .
												'(pr.oggetto REGEXP \''.$_SESSION['barraStampe']['keyword'].'\') or ' .
												'(pr.comuneogg REGEXP \''.$_SESSION['barraStampe']['keyword'].'\') ' .
														') ';
}
if (isSet($_SESSION['barraStampe']['nregFilter']) and ($_SESSION['barraStampe']['nregFilter'] > '')) {
	$whereClause .= ' and (pr.numeroregistrazione REGEXP \''.$_SESSION['barraStampe']['nregFilter'].'\' ) ';
}
if (isSet($_SESSION['barraStampe']['anagFilter']) and ($_SESSION['barraStampe']['anagFilter'] > '')) {
	$whereClause .= ' and (pr.anagrafico REGEXP \''.$_SESSION['barraStampe']['anagFilter'].'\' ) ';
}
if (isSet($_SESSION['barraStampe']['modFilter']) and ($_SESSION['barraStampe']['modFilter'] > '')) {
	$whereClause .= ' and (pr.modello = '.$_SESSION['barraStampe']['modFilter'].' ) ';
}

if (isSet($_SESSION['barraStampe']['ufficioFilter']) and ($_SESSION['barraStampe']['ufficioFilter'] > '')) {
	$whereClause .= ' and (pr.ufficio = '.$_SESSION['barraStampe']['ufficioFilter'].' ) ';
}
if (isSet($_SESSION['barraStampe']['zonaFilter']) and ($_SESSION['barraStampe']['zonaFilter'] > '')) {
	$whereClause .= ' and (pr.zona = '.$_SESSION['barraStampe']['zonaFilter'].' ) ';
}

if ((isSet($_SESSION['barraStampe']['daDataUscita']) and $_SESSION['barraStampe']['daDataUscita']>'') and (isSet($_SESSION['barraStampe']['aDataUscita']) and $_SESSION['barraStampe']['aDataUscita']>'')){
	$whereClause .= ' and (pr.uscita between ' .
							' str_to_date(\''.$_SESSION['barraStampe']['daDataUscita'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_SESSION['barraStampe']['aDataUscita'].'\',\'%Y-%m-%d\') ) ';
}
if ((isSet($_SESSION['barraStampe']['daDataArrivo']) and $_SESSION['barraStampe']['daDataArrivo']>'') and (isSet($_SESSION['barraStampe']['aDataArrivo']) and $_SESSION['barraStampe']['aDataArrivo']>'')){
	$whereClause .= ' and (pr.dataarrivo between ' .
							' str_to_date(\''.$_SESSION['barraStampe']['daDataArrivo'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_SESSION['barraStampe']['aDataArrivo'].'\',\'%Y-%m-%d\') ) ';
}

$_SESSION['barraStampe']['wk_page']=$_SESSION['barraStampe']['wk_page']>''?$_SESSION['barraStampe']['wk_page']:1;
$_SESSION['barraStampe']['ORD']=$_SESSION['barraStampe']['ORD']>''?$_SESSION['barraStampe']['ORD']:' DESC ';
$_SESSION['barraStampe']['orderBy']=$_SESSION['barraStampe']['orderBy']>''?$_SESSION['barraStampe']['orderBy']:' pr.dataregistrazione ';
$orderBy = ' order by '.$_SESSION['barraStampe']['orderBy'].' '.$_SESSION['barraStampe']['ORD'].' , pr.numeroregistrazione ';


$serviceQuery='select pr.pratica_id, ' .
						'pr.numeroregistrazione as "N.Reg.", ' .
						'date_format(pr.dataregistrazione,\'%d-%m-%Y\') as "Data Reg.", ' .
						'date_format(pr.dataarrivo,\'%d-%m-%Y\') as "Arrivo", ' .
						' date_format(pr.scadenza,\'%d-%m-%Y\') as Scadenza, ' .
						' date_format(pr.uscita,\'%d-%m-%Y\') as Uscita, ' .
						'pr.protuscita as "Data e Prot. uscita", ' .
						'am.description as \'Tipo Pratica\', ' .
						'au.description as "Ufficio", ' .
						'az.description as "Zona" , ' .
						'pr.oggetto as "Oggetto ESPI", ' .
						'pr.comuneogg as "Oggetto", ' .
						'ae.description as "Esito" , ' .
						'pr.nome as "Nome Mittente" , ' .
						'pr.cognome as "Cognome Mittente" , ' .
						'pr.codicefiscale as "C.Fis./P.IVA", ' .
						'pr.contributi as "Importo Lavori", ' .
						'sum(if(ac.ammissibile=\'Y\',ac.detrazione*incidenza,0)) as "Tot. ammesso", ' .
						'sum(if(ac.ammissibile<>\'Y\',ac.detrazione*incidenza,0)) as "Tot. non ammesso" ' .
				'from pratiche pr ' .
				'left join arc_contributi ac on (ac.pratica_id = pr.pratica_id) ' .
				'left join arc_zone az on (az.zona = pr.zona) ' .
				'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
				'left join arc_modelli am on (am.modello = pr.modello) ' .
				'left join vincoli av on (av.vincolo_id = pr.vincolo_id) ' .
				'left join arc_esiti ae on (ae.esito_id = pr.esito_id) ' .
				'where 1 ' .
				$whereClause. ' group by pr.pratica_id ' .
				$orderBy;
$serviceTable=new myHtmlETable($serviceQuery);
	if ($serviceTable->getTableRows()>0) {
		if (!isSet ($_GET['xlsSave']) or ($_GET['xlsSave'] <> 'Y')) {
			include('pageheader.inc');
			include('barraSelStampe.inc');
			$wkPage=isSet($_GET['wk_page'])?$_GET['wk_page']:1;
			print('<div style="margin:10px;">');
			$serviceTable->SetPageDivision(TRUE);
			$serviceTable->HideCol('pratica_id');
			$serviceTable->HideCol('Oggetto ESPI');
			$serviceTable->HideCol('Oggetto');
			$serviceTable->HideCol('Nome Mittente');
			$serviceTable->HideCol('C.Fis./P.IVA');
			$serviceTable->HideCol('Importo Lavori');
			$serviceTable->HideCol('Tot. ammesso');
			$serviceTable->HideCol('Tot. non ammesso');
			$serviceTable->show($wkPage);
			print('</div>');
			include('pagefooter.inc');
		} else {
			$serviceTable->saveAsXls();
		}
	} else {
			include('pageheader.inc');
			include('barraSelStampe.inc');
			print('<div style="margin:10px;">');
				print('<h1>Non sono state trovate pratiche nella selezione!</h1>');
			print('</div>');
			include('pagefooter.inc');

	}
?>