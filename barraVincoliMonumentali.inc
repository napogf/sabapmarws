<?php
/*
 * Created on 20/lug/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/



print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?nullValue=Y&sql=select * from arc_province " ' .
'jsId="sProvince" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?nullValue=Y&sql=select * from arc_comuni " ' .
'jsId="sComuni" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?nullValue=Y&sql=select legge_id, legge from vin_leggi order by legge " ' .
'jsId="sLeggi" ' .
'></div>');
		print('<script language="JavaScript" type="text/javascript">
			dojo.addOnLoad(function(){
	    new dijit.form.FilteringSelect({
	                store: sProvince,
	                labelAttr: \'PROVINCIA\',
	                searchAttr: \'PROVINCIA\',
	                name: "SIGLA",
	                autoComplete: true,
	                style: "width: 100px;",
	                value : \''.$_GET['SIGLA'].'\',
	                id: "SIGLA",
	                onChange: function(SIGLA) {
	                	dijit.byId(\'COMUNE\').query.PROVINCIA = dijit.byId(\'SIGLA\').item.SIGLA[0] ;
						return true;
	                }
	            },
	            "SIGLA");

	    new dijit.form.FilteringSelect({
	                store: sComuni,
	                labelAttr: \'COMUNE\',
	                searchAttr: \'COMUNE\',
	                name: "COMUNE",
	                autoComplete: true,
	                style: "width: 150px;",
	                value : \''.$_GET['COMUNE'].'\',
	                query : { PROVINCIA : "*"},
	                id: "COMUNE",
	                onChange: function(ID) {
						return true;
	                }
	            },
	            "COMUNE");
			    new dijit.form.FilteringSelect({
			                store: sLeggi,
			                labelAttr: \'legge\',
			                searchAttr: \'legge\',
			                name: "LEGGE",
			                autoComplete: true,
			                style: "width: 100px;",
			                value : \''.$_GET['LEGGE_ID'].'\',
			                id: "LEGGE"
			            },
			            "LEGGE");

			});
		</script>
		');



// Dialog Box Creazione nuovo Vincolo
print('<div id="dlgAddVincoloMonumentale" dojoType="dijit.Dialog" title="Creazione Vincolo" ' .
		'>');
print('</div>');
// Barra di ricerca Vincoli Monumentali



print('<div id="topOwner" >' ."\n".
	'<form name=searchForm ' .
			'action="'.$PHP_SELF.'" method="get" style="margin-bottom: 5px">'."\n");
print ('Provincia: <input id="SIGLA">&nbsp;');
print ('Comune: <input id="COMUNE">&nbsp;');
print ('Legge: <input id="LEGGE">&nbsp;');
print('' .
		'<INPUT type="hidden"  value="search" size=11 maxlength="25" name="mode" >' .
		'<INPUT type="hidden"  value="1" size=11 maxlength="25" name="wk_page" >' .
		'<INPUT type="hidden"  value="'.$_GET['PRATICA_ID'].'" size=11 maxlength="25" name=PRATICA_ID >' .
		'&nbsp;Oggetto: <INPUT class=textA id=Search value="'.$_GET['keyword'].'" size=25 name=keyword >' .
		'&nbsp;Particelle<INPUT class=textA id=Search value="'.$_GET['anaFilter'].'" size=8 maxlength="25" name=anaFilter >' .
		'&nbsp;Foglio<INPUT class=textA id=Search value="'.$_GET['foglioFilter'].'" size=8 maxlength="25" name=foglioFilter >' .
//		'&nbsp;Mappale<INPUT class=textA id=Search value="'.$_GET['mappaleFilter'].'" size=8 maxlength="25" name=mappaleFilter >' .
		'&nbsp;<A  href="javascript:document.searchForm.submit()">'."\n".
	'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>'.

	$addButton .

	'</form>'."\n".
'</div>'."\n");
print('<div style="margin:10px; clear: both;">');


$whereClause = '';
foreach ($_GET as $key => $value){
	switch ($key) {
		case 'SIGLA':
			$whereClause .= $value == '' ? '' : ' and (prv.id = \''.$value.'\') ';
			break;
		case 'COMUNE':
			$whereClause .= $value == '' ? '' : ' and (com.id = \''.$value.'\') ';
			break;
		case 'ZONA':
			$whereClause .= $value == '' ? '' : ' and (loc.gruppo_localita = \''.$value.'\') ';
			break;
		case 'LOCALITA':
			$whereClause .= $value == '' ? '' : ' and (loc.id = \''.$value.'\') ';
			break;
		case 'keyword':
			$whereClause .= $value == '' ? '' : ' and (vin.oggetto REGEXP \''.$value.'\') ';
			break;
		case 'anaFilter':
			$whereClause .= $value == '' ? '' : ' and ((par.numero = \''.$value.'\') or (fog.catasto regexp \''.$value.'\') or (vin.numeri regexp \''.$value.'\')) ';
			break;
		case 'foglioFilter':
			$whereClause .= $value == '' ? '' : ' and (fog.foglio = \''.$value.'\') ';
			break;
	}
}


?>