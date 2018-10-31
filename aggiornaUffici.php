<?php
/*
 * Created on 25/feb/09
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
include('pageheader.inc');

$zoneDaAggiornare=dbselect('select distinct pr.zona, az.description from pratiche pr ' .
											'right join arc_zone az on (az.zona=pr.zona) ' .
							'where upper(az.tipo) = \'U\'');

for ($index = 0; $index < $zoneDaAggiornare['NROWS']; $index++) {
	if ($ufficioResult=dbselect('select ufficio, description from arc_uffici where description regexp "'.$zoneDaAggiornare['ROWS'][$index]['description'].'"  ')) {
		print('Ufficio :'.$ufficioResult['ROWS'][0]['ufficio'].' Zona:'.$zoneDaAggiornare['ROWS'][$index]['description'].'</br>');
		dbupdate('update pratiche set ufficio = '.$ufficioResult['ROWS'][0]['ufficio'].' where zona = '.$zoneDaAggiornare['ROWS'][$index]['zona']);
	} else {
		print('Codificare ufficio '.$zoneDaAggiornare['ROWS'][$index]['description'].'</br>');
	}
}



include('pagefooter.inc')
?>
