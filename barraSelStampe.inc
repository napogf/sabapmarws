<?php
/*
 * Created on 18/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
// Save in $_SESSION search value, filters and order
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_esiti order by description" ' .
		'jsId="selEsito" ' .
		'/>');


		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_modelli order by description" ' .
		'jsId="selModello" ' .
		'></div>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
//		'url="xml/jsonSql.php?sql=select CODE, VALUE from sys_fields_validations where field_name = \'PRSTATUS\' and language_id = '.$_SESSION['sess_lang'].' " ' .
		'url="xml/praticheStatus.json" ' .
		'jsId="selFilter" ' .
		'></div>');

		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/praticheOrd.json" ' .
		'jsId="setOrder" ' .
		'></div>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?nullValue=Y&sql=select * from arc_zone where description <> \'ADMIN\' "  ' .
		'jsId="selZona" ' .
		'></div>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?nullValue=Y&sql=select * from arc_uffici" ' .
		'jsId="selUfficio" ' .
		'></div>');



// Dialog Box Creazione nuovo Procedimento
print('<div id="dlgApriPratica" dojoType="dijit.Dialog" title="Apertura Procedimento" ' .
		'>');

print('</div>');


		print('<span dojoType="dijit.Tooltip" id ="SearchHelp" connectId="Search" style="display:none;">' .
				'<div class="djToolTipContainer" >Inserisci un testo libero da cercare nei campi:<br>' .
				'Oggetto Espi, Oggetto, Comune Mittente</div></span>');


	print('<div style="margin-left:20px;" >' ."\n".
				'<form name=searchForm ' .
//						'onSubmit="javascript: return isNotNull(this.keyword.value)" ' .
						'action='.$PHP_SELF.' method=get style="margin-bottom: 5px">'."\n".
				'<INPUT class=textA id="Search" value="'.$_SESSION['barraStampe']['keyword'].'" size="25" name="keyword"  >' .
					'<input type="hidden" name="xlsSave" value="N" id="xlsSave" >' .
//					'onClick="javascript: document.searchForm.keyword.value =\'\'">' .
					'&nbsp;Prot.<INPUT class=textA id=Search value="'.$_SESSION['barraStampe']['nregFilter'].'" size=8 maxlength="25" name=nregFilter>' .
					'&nbsp;Anag.<INPUT class=textA id=Search value="'.$_SESSION['barraStampe']['anagFilter'].'" size=8 maxlength="25" name=anagFilter>' .
					'&nbsp;Tipo Pratica<div style="width:200px;" dojoType="dijit.form.FilteringSelect" ID="SEL_MODELLO"
									store="selModello"
									labelAttr="DESCRIPTION"
									searchAttr="DESCRIPTION"
									name="modFilter" ' .
						'value="' . $_SESSION['barraStampe']['modFilter'] . '" ' .
					'></div>' .
					'&nbsp;Status<div style="width:100px;" dojoType="dijit.form.FilteringSelect" ID="SELSTATUS"
									store="selFilter"
									labelAttr="VALUE"
									searchAttr="VALUE"
									name="filter" ' .
						'value="' . $_SESSION['barraStampe']['filter'] . '" ' .
					'></div>' .
					'<img src="graphics/webapp/filtro.png" onclick="toggleFilter()" style="cursor: pointer;margin: 0px 3px; vertical-align: middle;" title="Ricerca avanzata" >' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'N\';document.searchForm.submit()">'."\n".
				'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'Y\';document.searchForm.submit()">'."\n".
				'<img src="graphics/mime/msexcel.gif" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
				'<img src="graphics/refresh.png" width="21" height="20" vspace="1" border="0" align="absbottom" onclick="location.href=\'praticheReport.php?clearFilter=Y\'" style="cursor: pointer; margin-left: 10px;" title="Elimina Filtri">'.
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
					'Da data uscita<input dojoType="dijit.form.DateTextBox" type="text" name="daDataUscita"  value="'.$_SESSION['barraRicerca']['daDataUscita'].'" style="margin: 5px; width: 10em;">' .
					'</td>' .
					'<td>' .
					'A data uscita<input dojoType="dijit.form.DateTextBox" type="text" name="aDataUscita"  value="'.$_SESSION['barraRicerca']['aDataUscita'].'" style="margin: 5px;width: 10em;">' .
				'</td>' .
				'</tr>' .
				'<tr>' .
					'<td>' .
						'Zona&nbsp;<div style="width:200px;" ' .
							'dojoType="dijit.form.FilteringSelect" ID="SEL_ZONA"
										store="selZona"
										labelAttr="DESCRIPTION"
										searchAttr="DESCRIPTION"
										name="zonaFilter" ' .
							'value="' . $_SESSION['barraRicerca']['zonaFilter'] . '" ' .
						'>' .
					'</td>' .
					'<td>' .
						'Ufficio&nbsp;<div style="width:200px;" ' .
							'dojoType="dijit.form.FilteringSelect" ID="SEL_UFFICIO"
										store="selUfficio"
										labelAttr="DESCRIPTION"
										searchAttr="DESCRIPTION"
										name="ufficioFilter" ' .
							'value="' . $_SESSION['barraRicerca']['ufficioFilter'] . '" ' .
						'>' .
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
					'</td>' .
				'</tr>' .
				'</table>' .
				'</div>'."\n".
				'</div>'."\n".
				'</form>'."\n".
				'</div>'."\n");
print('<div style=" clear: both;" >');
?>