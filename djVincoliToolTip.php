<?php
include "login/autentication.php";
require_once("dbfunctions.php");

$vincoliQuery='select av.* from arc_vincoli av ' .
				'right join pratiche pr on (pr.vincolo_id = av.vincolo_id) ' .
				'where pr.pratica_id = '.$pratica_id;

$vincoliResult=dbselect($vincoliQuery);


if(!$vincoliResult){
	print('Non ci sono Vincoli per questa Pratica!!!');
} else {
		print('<div class="djToolTipContainer" >');
			print('<fieldset ><legend style="border: none; background-color: white; ">Vincolo</legend>');
			print('<LABEL>Oggetto</LABEL>'.$vincoliResult['ROWS'][0]["OGGETTO"].'<BR/>');
			print('<LABEL>Legge</LABEL>'.$vincoliResult['ROWS'][0]["LEGGE"].'<BR/>');
			print('<LABEL>Decreto</LABEL>'.$vincoliResult['ROWS'][0]["DECRETO"].'<br/>');
      		print('<LABEL>Notifica</LABEL>'.$vincoliResult['ROWS'][0]["NOTIFICA"].'<br/>');
      		print('<LABEL>Trascrizione</LABEL>'.$vincoliResult['ROWS'][0]["TRASCRIZIONE"].'<br/>');
      		print('<LABEL>Numeri</LABEL>'.$vincoliResult['ROWS'][0]["NUMERI"].'<br/>');
			print('</fieldset>'."\n");
		print('</div>');
}
?>
