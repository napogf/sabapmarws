<?php
/*
 * Created on 30/ago/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select * from arc_province " ' .
'jsId="sProvince" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select * from arc_comuni " ' .
'jsId="sComuni" ' .
'></div>');
print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select legge_id, legge from vin_leggi order by legge " ' .
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
print('<div id="dlgAddVincoloAmbientale" dojoType="dijit.Dialog" title="Creazione Vincolo" ' .
		'>');
print('</div>');

// Barra di ricerca Vincoli paesaggistici
print('<div id="topOwner" >' ."\n".
	'<form name=searchForm ' .
			'action="'.$PHP_SELF.'" method="get" style="margin-bottom: 5px">'."\n");
print ('Provincia: <input id="SIGLA">&nbsp;');
print ('Comune: <input id="COMUNE">&nbsp;');
print ('Legge: <input id="LEGGE">&nbsp;');

print('' .
		'<INPUT type="hidden"  value="'.$_GET['PRATICA_ID'].'" size=11 maxlength="25" name=PRATICA_ID >' .
		'<INPUT type="hidden"  value="search" size=11 maxlength="25" name="mode" >' .
		'&nbsp;Oggetto: <INPUT class=textA id=Search value="'.$_GET['OGGETTO'].'" size=25 name=OGGETTO >' .
		'&nbsp;<A  href="javascript:document.searchForm.submit()">'."\n".
	'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>'.
	$addButton .
	'</form>'."\n".
'</div>'."\n");

$whereClause = '';

foreach ($_GET as $key => $value){
	switch ($key) {
		case 'SIGLA':
			$whereClause .= $value == '' ? '' : ' and (prov.id = \''.$value.'\') ';
			break;
		case 'COMUNE':
			$whereClause .= $value == '' ? '' : ' and (com.id = \''.$value.'\') ';
			break;
		case 'OGGETTO':
			$whereClause .= $value == '' ? '' : ' and (amb.oggetto similar to \''.$value.'\') ';
			break;
		case 'LEGGE':
			$whereClause .= $value == '' ? '' : ' and (lex.legge_id = \''.$value.'\') ';
			break;
		case 'DECRETO':
			$whereClause .= $value == '' ? '' : ' and (amb.decreto similar to \''.$value.'\') ';
			break;
	}
}




?>
