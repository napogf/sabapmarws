<?php
$hack = TRUE;
require("adminfunct.php");
bcdb();
	$bakcontent = file_get_contents("http://sbap-vr.beniculturali.it/vincoli/webservice/backup.php?password=backsbap357gfadh546yq2tyfdzyu76i25322");
	$filename = "backups/".date("Ymd-His").".sql";
	$fp = fopen($filename, "w+");
	fwrite($fp, $bakcontent);
	fclose($fp);
	$fp2 = fopen("last.txt", "w");
	fwrite($fp2, date("H:i:s, d/m/Y"));
	fclose($fp2);
	mysql_query("DROP TABLE IF EXISTS vincoli_db");
	mysql_query("DROP TABLE IF EXISTS vincoli_revisioni");
	$SQL = explode(";", $bakcontent);
	for ($i=0;$i<count($SQL)-1;$i++) 		
		mysql_query($SQL[$i]) or die ($SQL[$i]." - ".mysql_error());
	echo("COPIA ESEGUITA CON SUCCESSO!");
bcdb(1);
?>