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
    $searchValues['subject']=$_GET['subject'];
    $searchValues['type']=$_GET['type'];
	$searchValues['mittente']=$_GET['mittente'];
	$searchValues['numeroregistrazione']=$_GET['numeroregistrazione'];
    $searchValues['pecId']=$_GET['pecId'];
	$searchValues['status']=$_GET['status'];
	$searchValues['daDataArrivo']=$_GET['daDataArrivo'];
	$searchValues['aDataArrivo']=$_GET['aDataArrivo'];
	$searchValues['order']['field']=$_GET['orderBy'];
	$searchValues['order']['type']=$_GET['orderType'];
	$_SESSION['barraPec'] = $searchValues;
} else {
	if($_GET['clearFilter']=='Y'){
		$_SESSION['barraPec'] = null;
	}
}

if(!isSet($whereClause)){
	switch ($_SESSION['barraPec']['status']) {
		case 'U':
			$whereClause= ' where pec.status = "U" ' ;
			break;
		case 'R':
			$whereClause = ' where pec.status = "R" ' ;
			break;
		case 'A':
			$whereClause= ' where pec.status = "A" ' ;
			break;
		case 'P':
			$whereClause= ' where pec.pratica_id is not null ' ;
			break;
		case 'T':
			$whereClause= ' where 1 ' ;
			break;
		default:
			$whereClause= ' where pec.pratica_id is null and pec.status <> "A"';
			break;
	}
}

if (isSet($_SESSION['barraPec']['subject']) and ($_SESSION['barraPec']['subject'] > '')) {
	$whereClause .= ' and (pec.subject REGEXP \''.$_SESSION['barraPec']['subject'].'\')' ;
}
if (isSet($_SESSION['barraPec']['type']) and ($_SESSION['barraPec']['type'] > '')) {
    $whereClause .= ' and (pec.type = \''.$_SESSION['barraPec']['type'].'\')' ;
}

if (isSet($_SESSION['barraPec']['mittente']) and ($_SESSION['barraPec']['mittente'] > '')) {
    $whereClause .= ' and (pec.mittente REGEXP \''.$_SESSION['barraPec']['mittente'].'\')' ;
}
if (isSet($_SESSION['barraPec']['numeroregistrazione']) and ($_SESSION['barraPec']['numeroregistrazione'] > '')) {
    $whereClause .= ' and (pec.numeroregistrazione REGEXP \''.$_SESSION['barraPec']['numeroregistrazione'].'\')' ;
}

if ((isSet($_SESSION['barraPec']['daDataArrivo']) and $_SESSION['barraPec']['daDataArrivo']>'') and (isSet($_SESSION['barraPec']['aDataArrivo']) and $_SESSION['barraPec']['aDataArrivo']>'')){
	$whereClause .= ' and (pec.dataarrivo between ' .
							' str_to_date(\''.$_SESSION['barraPec']['daDataArrivo'].'\',\'%Y-%m-%d\') and str_to_date(\''.$_SESSION['barraPec']['aDataArrivo'].'\',\'%Y-%m-%d\') ) ';
}
if (isSet($_SESSION['barraPec']['pecId']) and ($_SESSION['barraPec']['pecId'] > '')) {
	$whereClause .= ' and (pec.pec_id = '. (integer) $_SESSION['barraPec']['pecId'].') ';
	}


print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
				'url="xml/pecStatus.json" ' .
				'jsId="selFilter" ' .
				'></div>');




print('<span dojoType="dijit.Tooltip" id ="SearchHelp" connectId="Search" style="display:none;">' .
				'<div class="djToolTipContainer" >Inserisci un testo libero da cercare nei campi:<br>' .
				'Protocollo, Oggetto o Mittente</div></span>');


print('<div class="barraRicerca" >' ."\n".
				'<form name=searchForm ' .
//						'onSubmit="javascript: return isNotNull(this.keyword.value)" ' .
						'action='.$_SERVER['PHP_SELF'].' method=get style="margin-bottom: 5px">'."\n".
				'<INPUT type="hidden" name ="filter" value="Y" />' .
				'<INPUT dojoType="dijit.form.TextBox" placeholder="Oggetto"
				        id="Search1" value="'.$_SESSION['barraPec']['subject'].'" size="25" name="subject"  >' .
				'<INPUT dojoType="dijit.form.TextBox" placeholder="Mittente"
				        id="Search2" value="'.$_SESSION['barraPec']['mittente'].'" size="25" name="mittente"  >' .
				'<INPUT dojoType="dijit.form.TextBox" placeholder="Protocollo" style="width: 7em;"
				        id="Search3" value="'.$_SESSION['barraPec']['numeroregistrazione'].'" size="10" name="numeroregistrazione"  >' .
                '<input name="pecId" value="' . $_SESSION['barraPec']['pecId']. '" dojoType="dijit.form.TextBox"  
                            placeholder="PEC_ID" style="width: 7em;"
                            >' .

				'<span>Da data arrivo</span><input dojoType="dijit.form.DateTextBox" type="text" name="daDataArrivo"  value="'.$_SESSION['barraPec']['daDataArrivo'].'" style="margin: 5px; width: 10em;" >' .
				'<span>A data arrivo</span><input dojoType="dijit.form.DateTextBox" type="text" name="aDataArrivo"  value="'.$_SESSION['barraPec']['aDataArrivo'].'" style="margin: 5px; width: 10em;">' .
					'&nbsp;Status <div style="width:200px;" dojoType="dijit.form.FilteringSelect" ID="SELSTATUS"
									store="selFilter"
									labelAttr="VALUE"
									searchAttr="VALUE"
									name="status" ' .
						'value="' . $_SESSION['barraPec']['status'] . '" ' .
					'></div>' .

//    '&nbsp;Tipo
//                    <select dojoType="dijit.form.FilteringSelect" id="type" name="type" style="width:100px;">
//                        <option value="" ' . ( empty($_SESSION['barraPec']['type']) ? 'selected' : '') . ' >Tutte</option>
//                        <option value="pec" ' . ($_SESSION['barraPec']['type'] == 'pec' ? 'selected' : '') . ' >PEC</option>
//                        <option value="mail" ' . ($_SESSION['barraPec']['type'] == 'mail' ? 'selected' : '') . ' >Mail</option>
//                    </select>
    
    
                    'Ordina
                    <select dojoType="dijit.form.FilteringSelect" id="order" name="orderBy" style="width:100px;">
                        <option value="pec.dataarrivo" ' . ($searchValues['order']['field'] == 'pec.dataarrivo' ? 'selected' : '') . ' >Data Arrivo PEC</option>
                        <option value="pec.mittente" ' . ($searchValues['order']['field'] == 'pec.mittente' ? 'selected' : '') . '>Mittente</option>
                        <option value="pec.subject" ' . ($searchValues['order']['field'] == 'pec.subject' ? 'selected' : '') . '>Oggetto</option>
                        <option value="pec.dataregistrazione" ' . ($searchValues['order']['field'] == 'pec.dataregistrazione' ? 'selected' : '') . '>Datat Reg.</option>
                    </select>
                    &nbsp;
                    <select dojoType="dijit.form.FilteringSelect" id="orderType" name="orderType" style="width:100px;">
                        <option value="DESC" ' . ($searchValues['order']['type'] == 'DESC' ? 'selected' : '') . '>Decrescente</option>
                        <option value="ASC" ' . ($searchValues['order']['type'] == 'ASC' ? 'selected' : '') . '>Crescente</option>
                    </select>' .
//					'onClick="javascript: document.searchForm.keyword.value =\'\'">' .
				'<img src="graphics/refresh.png" width="21" height="20" vspace="1" border="0" align="absbottom" onclick="location.href=\'?clearFilter=Y\'" style="cursor: pointer; margin-left: 10px;" title="Elimina Filtri">'.
				'<A  href="javascript:document.searchForm.submit()">'."\n".
				'<img src="graphics/webapp/20px_search.jpg" width="21" height="20" vspace="1" border="0" align="absbottom" /></A> ' .
				'</form>'."\n".
				'</div>'."\n");


?>