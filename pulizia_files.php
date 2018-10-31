<?php
/*
 * Created on 18/gen/11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
include('pageheader.inc');

$d = scandir(getcwd().'/modelli/',0);

$pattern="[FILE_OO-ARC_DOCUMENTI]";
$query="select concat(doc_id,'-FILE_OO-',FILE_OO) as FILES_BUONI from arc_documenti";

	$result = mysql_query($query) or die("Query non valida: " . mysql_error() . '<BR>' . $query . '<br>' . var_dump(debug_backtrace()));

	if (!$result) {
		print ($query . '<br>');
	}
	while ($riga = mysql_fetch_array($result, MYSQL_NUM)) {
		$rows_array[] = $riga[0];
		$nrows++;
	}
	mysql_free_result($result);

foreach($d as $entry){
	if (preg_match($pattern,$entry,$match)) {
		if(array_search($entry,$rows_array)){
			echo $entry."<br>\n";
		} else {
			echo 'cancellato '.$entry."<br>\n";
			unlink(getcwd().'/modelli/'.$entry);
		}
	} else {
		echo $entry."<br>\n";
	}
}


include('pagefooter.inc')
?>
