<?php
include "login/autentication.php";
//require_once("dbfunctions.php");
//require_once("Etable_c.inc");

class myHtmlETable extends htmlETable {


}



$wk_page=isset($wk_page)?$wk_page:1;
$anno=isset($anno)?$anno:date('Y');
$whereCondition=null;
// verifica parametri

$groupBy = 'group by 1 ';

$contaPratiche = '(select count(pr2.pratica_id) from pratiche pr2 where year(pr2.dataregistrazione) = year(pr2.dataregistrazione) and (pr2.modello = pr.modello) ';

if ($_POST['grpMese']=='Y') {
	$meseField='date_format(pr.dataregistrazione,\'%m\') Mese, ';
	$groupBy.=' , month(pr.dataregistrazione) ';
	$contaPratiche .= ' and (month(pr2.dataregistrazione) = month(pr.dataregistrazione)) ';
	$meseChecked = 'checked';
} else {
	$meseField='';
}
if ($_POST['grpZona']=='Y') {
	$zonaField='az.description as Zona,';
	$groupBy.=' ,pr.zona ';
	$contaPratiche .= 'and (pr2.zona = pr.zona) ';
	$zonaChecked = 'checked';
} else {
	$zonaField='';
}

if ($_POST['grpUfficio']=='Y') {
	$ufficioField='au.description as Ufficio, ';
	$groupBy.=' ,pr.ufficio ';
	$contaPratiche .= 'and (pr2.ufficio = pr.ufficio) ';
	$ufficioChecked = 'checked';
} else {
	$ufficioField='';
}



if (strlen($daData)>0 and strlen($aData)>0) {
//	$whereCondition = ' where (pr.uscita is not null or (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) and ' .
//							'(pr.dataregistrazione between ' .
//							' str_to_date(\''.$daData.'\',\'%Y-%m-%d\') and str_to_date(\''.$aData.'\',\'%Y-%m-%d\') ) ';
	$whereCondition = ' where (pr.tipologia = \'E\') and ' .
							' (pr.dataregistrazione between ' .
							' str_to_date(\''.$daData.'\',\'%Y-%m-%d\') and str_to_date(\''.$aData.'\',\'%Y-%m-%d\') ) ';
}

if (strlen($zona)>0) {
	$whereToken=is_null($whereCondition)?' where ':' and ';
	$whereCondition .= $whereToken.'( pr.zona = '.$zona.') ';
}
if (strlen($ufficio)>0) {
	$whereToken=is_null($whereCondition)?' where ':' and ';
	$whereCondition .= $whereToken.' (pr.ufficio = '.$ufficio.') ';
}

if (strlen($modello)>0) {
	$whereToken=is_null($whereCondition)?' where ':' and ';
	$whereCondition .= $whereToken.' (pr.modello = '.$modello.') ';
}

if (strlen($_POST['ESITO_ID'])>0) {
	$whereToken=is_null($whereCondition)?' where ':' and ';
	$whereCondition .= $whereToken.' (pr.esito_id = '.$_POST['ESITO_ID'].') ';
}



$contaPratiche .= ') as "Tot. Pratiche", ';

$groupBy .= ',pr.modello ';



$serviceQuery='select   date_format(pr.dataregistrazione,\'%Y\') Anno, ' .
						$meseField.
						$zonaField .
						$ufficioField .
						'sum(if(ae.esito=\'Y\',1,0)) as Positive,' .
						'sum(if(ae.esito=\'N\',1,0)) as Negative,' .
						'sum(if(ae.esito=\'S\',1,0)) as Sospese,' .
						'am.description as \'Tipo Pratica\', ' .
						'count(pr.pratica_id) as "Tot.Pratiche", ' .
						'count(pr1.pratica_id) as "Pratiche aperte", ' .
						'count(pr2.pratica_id) as "Pratiche chiuse", ' .
						'max(datediff(pr2.uscita, ifnull(pr2.dataarrivo,pr2.dataregistrazione))+1) "Durata max" , ' .
						'min(datediff(pr2.uscita, ifnull(pr2.dataarrivo,pr2.dataregistrazione))+1) "Durata min" , ' .
						'round(avg(datediff(pr2.uscita, ifnull(pr2.dataarrivo,pr2.dataregistrazione))+1)) "Durata med" ' .
				'from pratiche pr ' .
				'left join arc_zone az on (az.zona = pr.zona) ' .
				'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
				'left join arc_modelli am on (am.modello = pr.modello) ' .
				'left join pratiche pr1 on ((pr1.pratica_id = pr.pratica_id) and (pr1.uscita is null) )' .
				'left join pratiche pr2 on ((pr2.pratica_id = pr.pratica_id) and (pr2.uscita is not null and pr2.uscita > str_to_date(\'0000-00-00\',\'%Y-%m-%d\')) )' .
				'left join arc_esiti ae on (ae.esito_id = pr2.esito_id) ' .
				$whereCondition.
				$groupBy;

//var_dump($serviceQuery);

$yearStart=date('Y')-5;
$yearEnd=date('Y')+5;

if (!isSet($xlsExport) or ($xlsExport <> 'Esporta')) {
	include('pageheader.inc');
	print('<div class="djFormContainer">' ."\n");

		print('<form name="form1" method="post" action="?anno='.$anno.'">');

		print('<div style="float:left;">');
		print('<fieldset><legend>Raggruppamento</legend>');
		print('<label for="grpMese">Mese</label><input type="checkbox" name="grpMese" value="Y"><br/>');
		print('<label for="grpZona">Zona</label><input type="checkbox" name="grpZona" value="Y"><br/>');
		print('<label for="grpUfficio">Ufficio</label><input type="checkbox" name="grpUfficio" value="Y"><br/>');
		print('</fieldset></div>');

		print('<div style="float:left;">');
		print('<fieldset><legend>Periodo/Esito</legend>');
		print('<label for="daData">Da data</label>');
		if($_SESSION['dojoVersion']=='1.x'){
			print('<input dojoType="dijit.form.DateTextBox" type="text" name="daData"  value="' . $_POST['daData'] . '" >');
		} else {
			print('<div dojoType="dropdowndatepicker" displayFormat="dd-MM-yyyy" lang="it-it" name="daData" value="'.$_POST['daData'].'" ></div>');
		}
		print('<br/><label for="aData">A data</label>');
		if($_SESSION['dojoVersion']=='1.x'){
			print('<input dojoType="dijit.form.DateTextBox" type="text" name="aData"  value="' . $_POST['aData'] . '" >');
		} else {
			print('<div dojoType="dropdowndatepicker" displayFormat="dd-MM-yyyy" lang="it-it" name="aData" value="'.$_POST['aData'].'" ></div>');
		}

		print ('<br><div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_esiti order by description" ' .
		'jsId="arcEsiti" ' .
		'/>');
		print ('<label for="ESITO_ID">Esito</label>');
		print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_ESITO"
								store="arcEsiti"
								labelAttr="DESCRIPTION"
								searchAttr="DESCRIPTION"
								name="ESITO_ID" ' .
		'value="' . $_POST['ESITO_ID']. '" ' .
		'></div>');

		print('</fieldset></div>');

		print('<div style="float:left;">');
		print('<fieldset style="width: 30em;"><legend>Altre selezioni</legend>');


		print('<label for="modello" >Tipo pratica</label>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_modelli order by description " ' .
		'jsId="arcModelli" ' .
		'></div>');
		print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_MODELLO"
								store="arcModelli"
								labelAttr="DESCRIPTION"
								searchAttr="DESCRIPTION"
								name="modello" ' .
		'value="' . $_POST['modello']. '" ' .
		'></div>');

		print('<br/><label for="zona">Zona</label>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_zone where tipo = \'Z\' " ' .
		'jsId="arcZone" ' .
		'></div>');
		print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_ZONA"
								store="arcZone"
								labelAttr="DESCRIPTION"
								searchAttr="DESCRIPTION"
								name="zona" ' .
		'value="' . $_POST['zona']. '" ' .
		'></div>');


		print('<br/><label for="modello">Ufficio</label>');
		print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_zone where tipo = \'U\' " ' .
		'jsId="arcUffici" ' .
		'/>');
		print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_UFFICIO"
								store="arcUffici"
								labelAttr="DESCRIPTION"
								searchAttr="DESCRIPTION"
								name="ufficio" ' .
		'value="' . $_POST['ufficio']. '" ' .
		'></div>');

		print('</fieldset></div>');

		print('<div style="margin:10px; float:right;">');
		print('<input type="submit" name="Submit" value="Seleziona">');
		print('<input type="submit" name="xlsExport" value="Esporta">');
		print('</div>'."\n");


		print('</form>'."\n");
	print('</div>'."\n");

		print('<div style="margin:10px; clear: both;">');


		$praticaTable=new myHtmlETable($serviceQuery);

		if ($praticaTable->getTableRows()>0) {
			$praticaTable->SetTableCaption($row['description']);

			$praticaTable->getColumn('Tot.Pratiche')->SetColumnType('number',0,true);
			$praticaTable->getColumn('Pratiche chiuse')->SetColumnType('number',0,true);
			$praticaTable->getColumn('Pratiche aperte')->SetColumnType('number',0,true);
			$praticaTable->getColumn("Durata max")->SetColumnType('number');
			$praticaTable->getColumn("Durata min")->SetColumnType('number');
			$praticaTable->getColumn("Durata med")->SetColumnType('number');

 			$praticaTable->printTotal(true,0);


			$praticaTable->show($wk_page);

		} else {
			print('<h3>Non ci sono Pratiche per la selezione effettuata </h3>');
		}
		print('</div>');

	//}


	include('pagefooter.inc');
} else {
//	var_dump($serviceQuery);
	$praticaTable=new myHtmlETable($serviceQuery);
			$praticaTable->getColumn('Tot.Pratiche')->SetColumnType('number',0,true);
			$praticaTable->getColumn('Pratiche chiuse')->SetColumnType('number',0,true);
			$praticaTable->getColumn("Durata max")->SetColumnType('number');
			$praticaTable->getColumn("Durata min")->SetColumnType('number');
			$praticaTable->getColumn("Durata med")->SetColumnType('number');

	$praticaTable->saveAsXls('statisticaPratiche.xls');
}


?>