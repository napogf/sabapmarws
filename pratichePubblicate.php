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
$db = Db_Pdo::getInstance();
class myHtmlETable extends htmlETable {
}

if (isSet($_GET['filter'])){
	$searchValues=array();
	$searchValues['keyword']=$_GET['keyword'];
	$_SESSION['barraPubblicate'] = $searchValues;
} else {
	if($_GET['clearFilter']=='Y'){
		$_SESSION['barraPubblicate'] = null;
	}
}


if (isSet($_SESSION['barraPubblicate']['keyword']) and ($_SESSION['barraStampe']['keyword'] > '')) {
	$whereClause .= ' and ((pr.cognome REGEXP \''.$_SESSION['barraStampe']['keyword'].'\') or ' .
												'(pr.oggetto REGEXP \''.$_SESSION['barraStampe']['keyword'].'\') or ' .
												'(pr.comuneogg REGEXP \''.$_SESSION['barraStampe']['keyword'].'\') ' .
														') ';
}

$_SESSION['barraPubblicate']['wk_page']=$_SESSION['barraPubblicate']['wk_page']>'' ? $_SESSION['barraPubblicate']['wk_page'] : 1;

$praticheSbapvr = 'SELECT pr.oggetto, pr.dataarrivo, date_format(scadenza,"%d/%m/%Y"), pr.uscita ,substr(es.description,6),
                                    concat(upd.upload_id,"_",upd.filename) FROM uploads upd
                                    LEFT JOIN pratiche pr ON (pr.pratica_id = upd.pratica_id)
                                    LEFT JOIN arc_esiti es ON (es.esito_id = pr.esito_id)
                                    WHERE upd.pubblica = "Y" AND PUBBLICATO="Y" ';



$serviceTable=new myHtmlETable($praticheSbapvr);
	if ($serviceTable->getTableRows()>0) {
		if (!isSet ($_GET['xlsSave']) or ($_GET['xlsSave'] <> 'Y')) {
			include('pageheader.inc');
			include('barraPubblicate.inc');
			$wkPage=isSet($_GET['wk_page'])?$_GET['wk_page']:1;
			print('<div style="margin:10px;">');
			$serviceTable->SetPageDivision(TRUE);
			$serviceTable->show($wkPage);
			print('</div>');
			include('pagefooter.inc');
		} else {
			$serviceTable->saveAsXls();
		}
	} else {
			include('pageheader.inc');
			include('barraPubblicate.inc');
			print('<div style="margin:10px;">');
                

				print('<h1>Non sono state trovate pratiche nella selezione!</h1>');
			print('</div>');
			include('pagefooter.inc');
	}
?>