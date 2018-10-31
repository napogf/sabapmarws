<?php
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("Etable_c.inc");
//require_once("fdataentry.php");
if (!empty($_POST['VINCOLO_ID'])) {
	if (!is_null($_POST['VINCOLO_ID']) and !is_null($_POST['PRATICA_ID'])) {
// 		r($_POST);
		$sql = 'update pratiche set VINCOLO_ID = '.$_POST['VINCOLO_ID'].' where pratica_id = '.$_POST['PRATICA_ID'];
		dbupdate($sql);
	}
	 header("Location: editPratica.php?PRATICA_ID=".$_POST['PRATICA_ID']);
}

$wk_page=isset($_GET['wk_page'])?$_GET['wk_page']:1;

class myHtmlETable extends htmlETable {

}


if (isSet($_GET['PRATICA_ID'])){
	$PRATICA_ID=$_GET['PRATICA_ID'];
//	$praticaResult=dbselect('select ANAGRAFICO, MAPPALE, ZONA from pratiche where pratica_id ='.$PRATICA_ID);
//	if($praticaResult['NROWS']>0){
//		$partFilter=$partFilter>''?$partFilter:$praticaResult['ROWS'][0]['ANAGRAFICO'];
//		$foglioFilter=$foglioFilter>''?$mappFilter:$praticaResult['ROWS'][0]['MAPPALE'];
//		$zona=$zona>''?$zona:$praticaResult['ROWS'][0]['ZONA'];
//	}
} else {
	$PRATICA_ID=$_POST['PRATICA_ID'];
	$partFilter=$_POST['partFilter'];
	$foglioFilter=$_POST['foglioFilter'];
	$zona=$_POST['zona'];
}



include('pageheader.inc');

		$praticaResult=dbselect('select pr.numeroregistrazione, ' .
						'date_format(pr.dataregistrazione,\'%d-%m-%Y\') as "dataregistrazione", ' .
						'pr.oggetto,' .
						'az.description as zonaDesc from ' .
						'pratiche pr ' .
						'left join arc_zone az on (az.zona = pr.zona) ' .
						'where pratica_id = ' . $PRATICA_ID);


			$formTitle='<span id="oggettoEspi" style="cursor: pointer" >Nr Reg.: ' . $praticaResult['ROWS'][0]['numeroregistrazione'] .
						' - Data Reg.: ' . $praticaResult['ROWS'][0]['dataregistrazione'] . ' - Zona: '.
						$praticaResult['ROWS'][0]['zonaDesc'] .'</span>';
			$formTitle .= '<span dojoType="dijit.Tooltip" id ="ttOggettoEspi" connectId="oggettoEspi" style="display:none;"><div class="djToolTipContainer" >'.
						$praticaResult['ROWS'][0]['oggetto'].'</div></span>';




		print ('<div style="background-color: azure; font-size: 1.5em; margin-top:20px; margin-bottom:5px;">' . $formTitle . '</div>' . "\n");
		print('<div id="topOwner" >' ."\n".
			'<form name=searchForm method="POST" ' .
					'onSubmit="javascript: return isNotNull(this.keyword.value)" ' .
					'action=praticaVincoli.php?PRATICA_ID=' . $_GET['PRATICA_ID'] . '&mode=search method=post style="margin-bottom: 5px">'."\n");

		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select SIGLA, PROVINCIA from arc_province " ' .
		'jsId="sProvince" ' .
		'></div>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select COMUNE, comune as DESCRIPTION, PROVINCIA from arc_comuni " ' .
		'jsId="sComuni" ' .
		'></div>');
?>

<script language="JavaScript" type="text/javascript">
	dojo.addOnLoad(function(){
	    new dijit.form.FilteringSelect({
	                store: sProvince,
	                labelAttr: 'PROVINCIA',
	                searchAttr: 'PROVINCIA',
	                name: "SIGLA",
	                autoComplete: true,
	                style: "width: 100px;",
	                id: "SIGLA",
	                onChange: function(SIGLA) {
	                	dijit.byId('COMUNE').query.PROVINCIA = dijit.byId('SIGLA').item.SIGLA[0] ;
						return true;
	                }
	            },
	            "SIGLA");

	    new dijit.form.FilteringSelect({
	                store: sComuni,
	                labelAttr: 'COMUNE',
	                searchAttr: 'COMUNE',
	                name: "COMUNE",
	                autoComplete: true,
	                style: "width: 150px;",
	                query : { PROVINCIA : "*"},
	                id: "COMUNE",
	                onChange: function(ID) {
						return true;
	                }
	            },
	            "COMUNE");

	});
</script>
<?php
		print ('<input id="SIGLA">&nbsp;');
		print ('<input id="COMUNE">&nbsp;');
		print('&nbsp;Denominazione/ubicazione/localit&agrave;: ');
		print('<INPUT class=textA id=Search value="'.$_POST['keyword'].'" size=25 name=keyword >' .
				'<INPUT type="hidden"  value="'.$PRATICA_ID.'" size=11 maxlength="25" name=PRATICA_ID >' .
				'&nbsp;Particelle<INPUT class=textA id=Search value="'.$_POST['partFilter'].'" size=8 maxlength="25" name=partFilter>' .
				'&nbsp;Foglio<INPUT class=textA id=Search value="'.$_POST['foglioFilter'].'" size=8 maxlength="25" name=foglioFilter>' .
				'&nbsp;<A onclick="javascript: return isNotNull(document.searchForm.keyword.value)" href="javascript:document.searchForm.submit()">'."\n".
			'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>'.
			'</form>'."\n".
		'</div>'."\n");


$wk_page=isset($wk_page)?$wk_page:1;


if($_GET['mode']=='search'){
	if (($_POST['partFilter'] > '')) {
		$whereClause .= ' and ((av.particelle REGEXP \''.$_POST['partFilter'].'\' ) OR
		                        (av.modifichecatastali REGEXP \''.$_POST['partFilter'].'\' ))';
	}
} else {
	if (isSet($partFilter) and ($partFilter > '')) {
		$whereClause .= ' and ((av.particelle REGEXP \''.$partFilter.'\' ) OR 
                                (av.modifichecatastali REGEXP \''.$partFilter.'\' ))';
	}
}

if($_GET['mode']=='search'){
	if (($_POST['foglioFilter'] > '')) {
		$whereClause .= ' and (av.fogliocatastale REGEXP \''.$_POST['foglioFilter'].'\' ) ';
	}
} else {
	if (isSet($foglioFilter) and ($foglioFilter > '')) {
	$whereClause .= ' and (av.fogliocatastale REGEXP \''.$foglioFilter.'\' ) ';
	}
}



if (isSet($_POST['keyword']) and ($_POST['keyword'] > '')) {
	$whereClause .= ' and ((av.localita REGEXP \''.$_POST['keyword'].'\' ) ' .
							'or (av.denominazione REGEXP \''.$_POST['keyword'].'\' ) ' .
							'or (av.ubicazioneprinc REGEXP \''.$_POST['keyword'].'\' ) ' .
							')';
}

if($_GET['mode']=='search'){
	if (($_POST['SIGLA'] > '')) {
		$whereClause .= ' and (av.provincia = \''.$_POST['SIGLA'].'\' ) ';
	}
} else {
	if (isSet($SIGLA) and ($SIGLA > '')) {
		$whereClause .= ' and (av.provincia = "'.$SIGLA.'"  ) ';
	}
}
if($_GET['mode']=='search'){
	if (($_POST['COMUNE'] > '')) {
		$whereClause .= ' and (av.comune REGEXP "'.$_POST['COMUNE'].'" ) ';
	}
} else {
	if (isSet($COMUNE) and ($COMUNE > '')) {
		$whereClause .= ' and (av.comune REGEXP "'.$COMUNE.'" ) ';
	}
}

	$vincoliQuery='select distinct ' .
						"concat('<input type=\"radio\" name=\"VINCOLO_ID\" value=\"',av.vincolo_id,'\" ',
								   (case when pr.VINCOLO_ID is null then ''
								   		 when pr.VINCOLO_ID is not null then 'checked'
									end),'>') as '#', " .
						'av.denominazione as Vincolo,  ' .
						'av.comune as Comune,  ' .
						'av.localita as "Loc.",  ' .
						'av.provincia as PR,  ' .
						'av.fogliocatastale as Foglio,  ' .
						'concat(\'<span id="vinc\',av.vincolo_id,\'">\',substr(concat(av.particelle," ",av.modifichecatastali),1,10),\'...</span>' .
									'<span dojoType="dijit.Tooltip" id="ttVinc\',av.vincolo_id,\'" connectId="vinc\',av.vincolo_id,\'" style="display:none;">' .
									'<div class="djToolTipContainer" >\',av.particelle," ",av.modifichecatastali,\'</div></span>\') ' .
						'as "Particelle", ' .
						'trim(concat(av.ubicazioneinit,\' \',av.ubicazioneprinc)) as Indirizzo,' .
						'av.vincolodiretto as D,  ' .
						'av.vincoloindiretto as I,  ' .
						'av.provvedimentoministeriale as "Provv.Min.",  ' .
						'av.trascrizioneinconservatoria as "Tras.Cons.",  ' .
						'av.posizioneMonumentale as "Pos.Mon.",  ' .
						'av.posizioneVincoli as "Pos.Vinc."' .
						'from vincoli av ' .
						'left join pratiche pr on ((pr.VINCOLO_ID = av.vincolo_id) and (pr.pratica_id='.$_GET['PRATICA_ID'].')) ' .
						'where 1 '.$whereClause;


//var_dump($vincoliQuery);



print('<div style="margin:20px; clear: both;">');

if($_GET['mode']!='search'){

	$vincoliQuery='select distinct ' .
						'concat(\'<span ><img src="graphics/close.gif" style="cursor: pointer" title="Rimuovi vincolo" onclick="rimuoviVincolo(\',pr.PRATICA_ID,\')" ></span>\') Rimuovi,' .
						'av.denominazione as Vincolo,  ' .
						'av.comune as Comune,  ' .
						'av.localita as "Loc.",  ' .
						'av.provincia as PR,  ' .
						'av.fogliocatastale as Foglio,  ' .
						'concat(\'<span id="vinc\',av.vincolo_id,\'">\',substr(av.particelle,1,10),\'</span>' .
									'<span dojoType="dijit.Tooltip" id="ttVinc\',av.vincolo_id,\'" connectId="vinc\',av.vincolo_id,\'" style="display:none;">' .
									'<div class="djToolTipContainer" >\',av.particelle,\'</div></span>\') ' .
						'as "Particelle", ' .
						'trim(concat(av.ubicazioneinit,\' \',av.ubicazioneprinc)) as Indirizzo,' .
						'av.vincolodiretto as D,  ' .
						'av.vincoloindiretto as I,  ' .
						'av.provvedimentoministeriale as "Provv.Min.",  ' .
						'av.trascrizioneinconservatoria as "Tras.Cons.",  ' .
						'av.posizioneMonumentale as "Pos.Mon.",  ' .
						'av.posizioneVincoli as "Pos.Vinc."' .
						'from vincoli av ' .
						'right join pratiche pr on (pr.VINCOLO_ID = av.vincolo_id) ' .
						'where (pr.pratica_id='.$PRATICA_ID.') and (av.vincolo_id is not null)' ;
//						var_dump($vincoliQuery);
	$vincoliTable=new myHtmlETable($vincoliQuery);
	if ($vincoliTable->getTableRows()>0) {
		$vincoliTable->show();
	} else {
		print('<h3>Inserisci un criterio di ricerca!</h3>');
	}
} else {

	print('<FORM ACTION="'.$PHP_SELF.'?mode='.$_GET['mode'].'"  METHOD="POST" name="AssociateMenuRespId">'."\n");
	print('<input type="hidden" name="PRATICA_ID" value="'.$_GET['PRATICA_ID'].'" >');

	$vincoliTable=new myHtmlETable($vincoliQuery);
	if ($vincoliTable->getTableRows()>0) {

	MakeButtons('assign');
		//$vincoliTable->SetColumnHref('SER_CODE','<a href="serviceManageRequest.php?STATUS=50&SER_ID=#SER_ID#" title="Attiva Intervento">');
//		$vincoliTable->SetPageDivision(true);
		$vincoliTable->show();

	MakeButtons('assign');

	print('</FORM>'."\n");


	} else {
		print('<h3>Non ci sono Vincoli per questa Pratica</h3>');
	}

}
print('</div>');
print('<div style="float: left;margin:10px;"><img src="graphics/webapp/prevpageR.gif" style="margin-right: 10px;" ><a href="editPratica.php?PRATICA_ID='.$PRATICA_ID.'" style="color: blue;">Torna alla pratica</a></div>');

include('pagefooter.inc');
mysql_set_charset($charset,$linkDB);
?>