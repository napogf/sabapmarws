<?php
/*
 * Created on 18/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
// Save in $_SESSION search value, filters and order
if (isSet($_GET['filter'])){
	$searchValues=array();
	$searchValues['keyword']=$_GET['keyword'];
	$searchValues['nregFilter']=$_GET['nregFilter'];
	$searchValues['anagFilter']=$_GET['anagFilter'];
	$searchValues['modFilter']=$_GET['modFilter'];
	$searchValues['filter']=$_GET['filter'];
	$searchValues['daDataRegistrazione']=$_GET['daDataRegistrazione'];
	$searchValues['aDataRegistrazione']=$_GET['aDataRegistrazione'];
	$searchValues['daDataArrivo']=$_GET['daDataArrivo'];
	$searchValues['aDataArrivo']=$_GET['aDataArrivo'];
	$searchValues['daDataUscita']=$_GET['daDataUscita'];
	$searchValues['aDataUscita']=$_GET['aDataUscita'];
	$searchValues['orderBy']=$_GET['orderBy'];
	$searchValues['ORD']=$_GET['ORD'];
	// $searchValues['ufficioFilter']=$_GET['ufficioFilter'];
	$searchValues['orgFilter']=$_GET['orgFilter'];
	$searchValues['esitoFilter']=$_GET['esitoFilter'];
	$searchValues['tipologiaFilter']=$_GET['tipologiaFilter'];
	$searchValues['progettoFilter']=$_GET['progettoFilter'];
	$_SESSION['barraRicerca'] = $searchValues;
} else {
	if($_GET['clearFilter']=='Y'){
		$_SESSION['barraRicerca'] = null;
	}
}

// Build Filters and Order
switch ($_SESSION['barraRicerca']['filter']) {
	case 'active':
		$whereClause=' and  date_add(pr.dataarrivo, INTERVAL (am.scadenza-am.allarme) DAY) >= date(now()) ' .
					 ' and (pr.scadenza > date(now()) ) ' .
					 ' and (pr.uscita is null) ' ;
		break;
	case 'open':
		$whereClause=' and (pr.modello is null or dataarrivo is null) ' .
					 ' and (pr.uscita is null) ' ;
		break;
	case 'suspended':
		$whereClause=' and arc_sospensioni.sospensione_id is not null' ;
		break;
	case 'alarm':
		$whereClause= ' and (pr.scadenza < date(now()) ) ' .
					  ' and (pr.uscita is null) ' ;
		break;
	case 'alert':
		$whereClause=' and  date_add(pr.dataarrivo, INTERVAL (am.scadenza-am.allarme) DAY) <= date(now()) ' .
					 ' and (pr.scadenza > date(now()) ) ' .
					 ' and (pr.uscita is null) ' ;
		break;
	case 'closed':
		$whereClause=' and (pr.uscita is not null) ';
		break;
	case 'progetto':
    	case 'tutte' :
		$whereClause='';
		break;
	default:
		$whereClause= ((isSet($_SESSION['barraRicerca']['keyword']) and ($_SESSION['barraRicerca']['keyword'] > ''))
												or (isSet($_SESSION['barraRicerca']['nregFilter']) and ($_SESSION['barraRicerca']['nregFilter'] > ''))
												or (isSet($_SESSION['barraRicerca']['faldoneFilter']) and ($_SESSION['barraRicerca']['faldoneFilter'] > ''))
												or (isSet($_SESSION['barraRicerca']['daDataUscita']) and ($_SESSION['barraRicerca']['daDataUscita'] > ''))
												or (isSet($_SESSION['barraRicerca']['aDataUscita']) and ($_SESSION['barraRicerca']['aDataUscita'] > ''))
												)?
					'':
					' and (pr.uscita is null) ';
		break;
}

$isAdmin=dbselect('SELECT user_uo_ref.* FROM user_uo_ref
		LEFT JOIN arc_organizzazione ON (arc_organizzazione.uoid = user_uo_ref.uoid)
		WHERE arc_organizzazione.code = "ADMIN" and user_uo_ref.user_id='.$_SESSION['sess_uid']);


if (!$isAdmin){

	$whereClause .= 'and (uor.uoid is not null) '.$whereClause;
}
if (isSet($_SESSION['barraRicerca']['keyword']) and ($_SESSION['barraRicerca']['keyword'] > '')) {
	$whereClause .= ' and ((pr.cognome REGEXP \''.$_SESSION['barraRicerca']['keyword'].'\') or ' .
												'(pr.oggetto REGEXP \''.$_SESSION['barraRicerca']['keyword'].'\') or ' .
												'(pr.comuneogg REGEXP \''.$_SESSION['barraRicerca']['keyword'].'\') or ' .
												'(pr.NUMERORIFERIMENTO REGEXP \''.$_SESSION['barraRicerca']['keyword'].'\') or ' .
												'(pr.pnome REGEXP \''.$_SESSION['barraRicerca']['keyword'].'\') ' .
	') ';
}
if (isSet($_SESSION['barraRicerca']['nregFilter']) and ($_SESSION['barraRicerca']['nregFilter'] > '')) {
	$whereClause .= ' and (pr.numeroregistrazione REGEXP \''.$_SESSION['barraRicerca']['nregFilter'].'\' ) ';
}
if (isSet($_SESSION['barraRicerca']['faldoneFilter']) and ($_SESSION['barraRicerca']['faldoneFilter'] > '')) {
	$whereClause .= ' and (pr.faldone REGEXP \''.$_SESSION['barraRicerca']['faldoneFilter'].'\' ) ';
}
if (isSet($_SESSION['barraRicerca']['modFilter']) and ($_SESSION['barraRicerca']['modFilter'] > '')) {
	$whereClause .= ' and (pr.modello = '.$_SESSION['barraRicerca']['modFilter'].' ) ';
}

//if (isSet($_SESSION['barraRicerca']['ufficioFilter']) and ($_SESSION['barraRicerca']['ufficioFilter'] > '')) {
//	$whereClause .= ' and (pr.ufficio = '.$_SESSION['barraRicerca']['ufficioFilter'].' ) ';
//}
if (isSet($_SESSION['barraRicerca']['orgFilter']) and ($_SESSION['barraRicerca']['orgFilter'] > '')) {
	$whereClause .= ' and (ao.uoid = '.$_SESSION['barraRicerca']['orgFilter'].' ) ';
}
if (isSet($_SESSION['barraRicerca']['esitoFilter']) and ($_SESSION['barraRicerca']['esitoFilter'] > '')) {
	$whereClause .= ' and (pr.esito_id = '.$_SESSION['barraRicerca']['esitoFilter'].' ) ';
}



if (isSet($_SESSION['barraRicerca']['tipologiaFilter']) and !empty($_SESSION['barraRicerca']['tipologiaFilter']) ) {
	$whereClause .= ' and pr.tipologia = "' . $_SESSION['barraRicerca']['tipologiaFilter'] . '" ';
}

if ((isSet($_SESSION['barraRicerca']['daDataUscita']) and $_SESSION['barraRicerca']['daDataUscita']>'') and (isSet($_SESSION['barraRicerca']['aDataUscita']) and $_SESSION['barraRicerca']['aDataUscita']>'')){
	$whereClause .= ' and (pr.uscita between ' .
							' str_to_date(\''.$_SESSION['barraRicerca']['daDataUscita'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_SESSION['barraRicerca']['aDataUscita'].'\',\'%Y-%m-%d\') ) ';
}
if ((isSet($_SESSION['barraRicerca']['daDataArrivo']) and $_SESSION['barraRicerca']['daDataArrivo']>'') and (isSet($_SESSION['barraRicerca']['aDataArrivo']) and $_SESSION['barraRicerca']['aDataArrivo']>'')){
	$whereClause .= ' and (pr.dataarrivo between ' .
							' str_to_date(\''.$_SESSION['barraRicerca']['daDataArrivo'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_SESSION['barraRicerca']['aDataArrivo'].'\',\'%Y-%m-%d\') ) ';
}
if ((isSet($_SESSION['barraRicerca']['daDataRegistrazione']) and $_SESSION['barraRicerca']['daDataRegistrazione']>'') and (isSet($_SESSION['barraRicerca']['aDataRegistrazione']) and $_SESSION['barraRicerca']['aDataRegistrazione']>'')){
    $whereClause .= ' and (pr.dataregistrazione between ' .
        ' str_to_date(\''.$_SESSION['barraRicerca']['daDataRegistrazione'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_SESSION['barraRicerca']['aDataRegistrazione'].'\',\'%Y-%m-%d\') ) ';
}
if (isSet($_SESSION['barraRicerca']['progettoFilter']) and $_SESSION['barraRicerca']['progettoFilter']>''){
	$whereClause = ' and pr.project_id = '.$_SESSION['barraRicerca']['progettoFilter'];
}


$_SESSION['barraRicerca']['wk_page'] = $_SESSION['barraRicerca']['wk_page'] > '' ? $_SESSION['barraRicerca']['wk_page'] : 1;
$_SESSION['barraRicerca']['ORD']= $_SESSION['barraRicerca']['ORD'] > '' ? $_SESSION['barraRicerca']['ORD'] : ' DESC ';
$_SESSION['barraRicerca']['orderBy'] = $_SESSION['barraRicerca']['orderBy'] > '' ? $_SESSION['barraRicerca']['orderBy'] : ' pr.pratica_id ';
$orderBy = ' order by '.$_SESSION['barraRicerca']['orderBy'].' '.$_SESSION['barraRicerca']['ORD'] ;

if($_SESSION['barraRicerca']['filter'] == 'mancata_consegna'){
    $whereClause = ' AND pr.PRATICA_ID IN (select pratiche.PRATICA_ID from pratiche 
            RIGHT JOIN arc_pratiche_pec ON (arc_pratiche_pec.PRATICA_ID = pratiche.PRATICA_ID)
            where 
            pratiche.MAIL_SENT_ID is null 
            AND arc_pratiche_pec.SUBJECT REGEXP \'MANCATA\' 
            AND arc_pratiche_pec.STATUS = \'S\') ';
}


		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_esiti order by description" ' .
		'jsId="selEsito" ' .
		'></div>');

		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select distinct am.MODELLO, am.DESCRIPTION  from arc_modelli am order by 2" ' .
		'jsId="selModello" ' .
		'></div>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
//		'url="xml/jsonSql.php?sql=select CODE, VALUE from sys_fields_validations where field_name = \'PRSTATUS\' and language_id = '.$_SESSION['sess_lang'].' " ' .
		'url="xml/praticheStatus.json" ' .
		'jsId="selFilter" ' .
		'></div>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		    //		'url="xml/jsonSql.php?sql=select CODE, VALUE from sys_fields_validations where field_name = \'PRSTATUS\' and language_id = '.$_SESSION['sess_lang'].' " ' .
		    'url="xml/praticheTipologia.json" ' .
		    'jsId="tipologiaFilter" ' .
		    '></div>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/praticheOrd.json" ' .
		'jsId="setOrder" ' .
		'></div>');
if(!isAdmin){
	print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
			'url="xml/jsonSql.php?nullValue=Y&sql=select ao.* from arc_organizzazione ao right join user_uo_ref uor on (uor.uoid = ao.uoid) where ao.uoid is not null and uor.user_id= '.$_SESSION['sess_uid'].' order by description" ' .
			'jsId="selOrg" ' .
			'></div>');

} else {
	print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
			'url="xml/jsonSql.php?nullValue=Y&sql=select ao.* from arc_organizzazione ao order by description" ' .
			'jsId="selOrg" ' .
			'></div>');

}
//		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
//		'url="xml/jsonSql.php?nullValue=Y&sql=select au.* from arc_uffici au right join user_uffici_ref uur on (uur.ufficio = au.ufficio) where au.ufficio is not null and uur.user_id= '.$_SESSION['sess_uid'].'" ' .
//		'jsId="selUfficio" ' .
//		'></div>');



// Dialog Box Creazione nuovo Procedimento
print('<div id="dlgApriPratica" dojoType="dijit.Dialog" title="Apertura Procedimento" ' .
		'>');

print('</div>');


		print('<span dojoType="dijit.Tooltip" id ="SearchHelp" connectId="Search" style="display:none;">' .
				'<div class="djToolTipContainer" >Inserisci un testo libero da cercare nei campi:<br>' .
				'Oggetto Espi, Oggetto, Comune Mittente, Numero riferimento</div></span>');


	print('<div style="margin-left:20px;" >' ."\n".
				'<form name=searchForm ' .
//						'onSubmit="javascript: return isNotNull(this.keyword.value)" ' .
						'action='.$_SERVER['PHP_SELF'].' method=get style="margin-bottom: 5px">'."\n".
				'<INPUT class=textA id="Search" value="'.$_SESSION['barraRicerca']['keyword'].'" size="25" name="keyword"  >' .
//					'onClick="javascript: document.searchForm.keyword.value =\'\'">' .
					'&nbsp;Prot.<INPUT class=textA id=Search value="'.$_SESSION['barraRicerca']['nregFilter'].'" size=8 maxlength="25" name=nregFilter>' .
	    '&nbsp;Tipologia&nbsp;&nbsp;' .
	    '<div style="width:100px;" dojoType="dijit.form.FilteringSelect" ID="TIPOLOGIAFILTER"
									store="tipologiaFilter"
									labelAttr="VALUE"
									searchAttr="VALUE"
									name="tipologiaFilter" ' .
	    									'value="' . $_SESSION['barraRicerca']['tipologiaFilter'] . '" ' .
	    									'></div>' .
					'&nbsp;Tipo Pratica<div style="width:200px;" dojoType="dijit.form.FilteringSelect" ID="SEL_MODELLO"
									store="selModello"
									labelAttr="DESCRIPTION"
									searchAttr="DESCRIPTION"
							        queryExpr="*${0}*"
	                                searchDelay="500"
									name="modFilter" ' .
						'value="' . $_SESSION['barraRicerca']['modFilter'] . '" ' .
					'></div>' .
					'&nbsp;Status&nbsp;<div style="width:200px;" dojoType="dijit.form.FilteringSelect" ID="SELSTATUS"
									store="selFilter"
									labelAttr="VALUE"
									searchAttr="VALUE"
									name="filter" ' .
						'value="' . $_SESSION['barraRicerca']['filter'] . '" ' .
					'></div>' .
					'<img src="graphics/webapp/filtro.png" onclick="toggleFilter()" style="cursor: pointer;margin: 0px 3px; vertical-align: middle;" title="Ricerca avanzata" >' .
					'<A  href="javascript:document.searchForm.submit()">'."\n".
				'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A> ' .
				'<img src="graphics/refresh.png" width="21" height="20" vspace="1" border="0" align="absbottom" onclick="location.href=\'praticheStatus.php?clearFilter=Y\'" style="cursor: pointer; margin-left: 10px;" title="Elimina Filtri">'.
				'<img src="graphics/add.png" width="21" height="20" vspace="1" border="0" align="absbottom" onclick="location.href=\'creaProtocollo.php\';" style="cursor: pointer; margin-left: 10px;" title="Crea Procedimento">'.
				'<div style="display:none; clear: both; margin: 10px 0px; border: 1px; " id="advSearch" >' .
				'<div style="float: left;">' .
				'<table>' .
				'<tr>' .
					'<td>' .
					'Da data arrivo<input dojoType="dijit.form.DateTextBox" type="text" name="daDataArrivo"  value="'.$_SESSION['barraRicerca']['daDataArrivo'].'" style="margin: 5px; width: 10em;" >' .
					'</td>' .
					'<td>' .
					'A data arrivo<input dojoType="dijit.form.DateTextBox" type="text" name="aDataArrivo"  value="'.$_SESSION['barraRicerca']['aDataArrivo'].'" style="margin: 5px; width: 10em;"><br/>' .
				'</td>' .
				'</tr>' .
				'<tr>' .
					'<td>' .
					'Da data reg.<input dojoType="dijit.form.DateTextBox" type="text" name="daDataRegistrazione"  value="'.$_SESSION['barraRicerca']['daDataRegistrazione'].'" style="margin: 5px; width: 10em;" >' .
					'</td>' .
					'<td>' .
					'A data reg<input dojoType="dijit.form.DateTextBox" type="text" name="aDataRegistrazione"  value="'.$_SESSION['barraRicerca']['aDataRegistrazione'].'" style="margin: 5px; width: 10em;"><br/>' .
				'</td>' .
				'</tr>' .
                '<tr>' .
					'<td>' .
					'Da data uscita<input dojoType="dijit.form.DateTextBox" type="text" name="daDataUscita"  value="'.$_SESSION['barraRicerca']['daDataUscita'].'" style="margin: 5px; width: 10em;">' .
					'</td>' .
					'<td>' .
					'A data uscita<input dojoType="dijit.form.DateTextBox" type="text" name="aDataUscita"  value="'.$_SESSION['barraRicerca']['aDataUscita'].'" style="margin: 5px;width: 10em;">' .
				'</td>' .
				'</tr>' .
				'<tr>' .
					'<td>' .
						'U. Organizzativa&nbsp;<div style="width:200px;" ' .
							'dojoType="dijit.form.FilteringSelect" ID="SEL_ORG"
										store="selOrg"
										labelAttr="DESCRIPTION"
										searchAttr="DESCRIPTION"
										name="orgFilter" ' .
							'value="' . $_SESSION['barraRicerca']['orgFilter'] . '" ' .
						'>' .
					'</td>' .
					'<td>' .
//						'Ufficio&nbsp;<div style="width:200px;" ' .
//							'dojoType="dijit.form.FilteringSelect" ID="SEL_UFFICIO"
//										store="selUfficio"
//										labelAttr="DESCRIPTION"
//										searchAttr="DESCRIPTION"
//										name="ufficioFilter" ' .
//							'value="' . $_SESSION['barraRicerca']['ufficioFilter'] . '" ' .
//						'>' .
					'</td>' .
				'</tr>' .
				'<tr>' .
					'<td>' .
						'Esito&nbsp;<div style="width:200px;" ' .
							'dojoType="dijit.form.FilteringSelect" ID="SEL_ESITO"
										store="selEsito"
										labelAttr="DESCRIPTION"
										searchAttr="DESCRIPTION"
										name="esitoFilter" ' .
							'value="' . $_SESSION['barraRicerca']['esitoFilter'] . '" ' .
						'>' .
					'</td>' .
					'<td>' .
					'<label for="tipologiaFilter">Includi pratiche in uscita&nbsp;</label>' .
					'<input type="checkbox" name="tipologiaFilter" value="U"  '.$uscitaCheck.'/>' .
					'</td>' .
				'</tr>' .
				'</table>' .
				'</div>'."\n".

				'<div style="float:left; padding: 5px;" >' .
				'<label for="orderBy">Ordina per </label>' .
					'<div style="width:200px;" dojoType="dijit.form.FilteringSelect" ' .
					' ID="ordinamento"
									store="setOrder"
									labelAttr="DESCRIPTION"
									searchAttr="DESCRIPTION"
									name="orderBy" ' .
						'value="' . $_SESSION['barraRicerca']['orderBy'] . '" ' .
					'></div>' .
			    '<span style="margin-left:5px;">' .
				'<input dojoType="dijit.form.RadioButton" type="radio" name="ORD" id="ord1" value="ASC">' .
				'<label for="ord1">Crescente</label>' .
				'<input dojoType="dijit.form.RadioButton" type="radio" name="ORD" id="ord2" value="DESC"  checked="checked"/>' .
				'<label for="ord2">Decrescente</label></span>' .
				'</div>'."\n".
				'</div>'."\n".
				'</form>'."\n".
				'</div>'."\n");
				print('<div style=" clear: both;" >');