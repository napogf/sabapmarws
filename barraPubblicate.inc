<?php
/*
 * Created on 18/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
print('</div>');


		print('<span dojoType="dijit.Tooltip" id ="SearchHelp" connectId="Search" style="display:none;">' .
				'<div class="djToolTipContainer" >Inserisci un testo libero da cercare nei campi:<br>' .
				'Oggetto Espi, Oggetto, Comune Mittente</div></span>');


	print('<div style="margin-left:20px;" >' ."\n".
				'<form name=searchForm ' .
//						'onSubmit="javascript: return isNotNull(this.keyword.value)" ' .
						'action='.$PHP_SELF.' method=get style="margin-bottom: 5px">'."\n".
				'<INPUT class=textA id="Search" value="'.$_SESSION['barraPubblicate']['keyword'].'" size="25" name="keyword"  >' .
					'<input type="hidden" name="xlsSave" value="N" id="xlsSave" >' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'N\';document.searchForm.submit()">'."\n".
				'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
					'<A  href="javascript:dojo.byId(\'xlsSave\').value=\'Y\';document.searchForm.submit()">'."\n".
				'<img src="graphics/mime/msexcel.gif" width="21" height="20" vspace="1" border="0" align="absbottom" /></A>' .
				'<img src="graphics/refresh.png" width="21" height="20" vspace="1" border="0" align="absbottom" onclick="location.href=\'pratichePubblicate.php?clearFilter=Y\'" style="cursor: pointer; margin-left: 10px;" title="Elimina Filtri">'.
				'</form>'."\n".
				'</div>'."\n");
print('<div style=" clear: both;" ></div>');