<?php
$livellopagina = 1; //importante, se modificato, modificare anche nel menu in adminfunct.php
$hack = TRUE;
require("adminfunct.php");
bcdb();
bcadminhead("Copia dei dati"); //bcadminhead("TITOLO", TRUE/*se script comuni*/);
if($_GET["log"]) {
	$fpk=fopen("pass.txt", "r+");
	$respass=trim(fgets($fpk));
	fclose($fpk);
	if (md5($_POST["pass"]) != $respass) {
		echo("<strong>PASSWORD ERRATA!</strong><br />");
		bcadminfoot();
		bcdb(1);
		die();
	};
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
	echo("<strong>COPIA ESEGUITA CON SUCCESSO!</strong><br />");
};

?>
<form action="copiaserver.php?log=TRUE" method="post">
	Password accesso: <input name="pass" type="password" size="30" /><br /><br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="send" type="submit" value="Esegui copia" />
</form>
<br /><br />
<a href="modpass.php">MODIFICA PASSWORD</a>
<?php
bcadminfoot();
bcdb(1);
?>