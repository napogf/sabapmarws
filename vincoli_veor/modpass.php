<?php
$livellopagina = 1; //importante, se modificato, modificare anche nel menu in adminfunct.php
$hack = TRUE;
require("adminfunct.php");
bcdb();
bcadminhead("Modifica password per la copia dei dati"); //bcadminhead("TITOLO", TRUE/*se script comuni*/);
if($_GET["mod"]) {
	$fpk=fopen("pass.txt", "r+");
	$respass=trim(fgets($fpk));
	fclose($fpk);
	if (md5($_POST["pold"]) != $respass) {
		echo("<strong>PASSWORD ERRATA!</strong><br />");
		bcadminfoot();
		bcdb(1);
		die();
	};
	if ($_POST["pnew"] != $_POST["pnew2"]) {
		echo("<strong>PASSWORD NON UGUALI!</strong><br />");
		bcadminfoot();
		bcdb(1);
		die();
	};
	$fp = fopen("pass.txt", "w");
	fwrite($fp, md5($_POST["pnew"]));
	fclose($fp);

	echo("<strong>MODIFICA ESEGUITA CON SUCCESSO!</strong><br />");
};

?>
<form action="modpass.php?mod=TRUE" method="post">
	Vecchia password: <input name="pold" type="password" size="30" /><br /><br />
	Nuova password: <input name="pnew" type="password" size="30" /><br />
	Ripeti: <input name="pnew2" type="password" size="30" /><br /><br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="send" type="submit" value="Modifica" />
</form>
<?php
bcadminfoot();
bcdb(1);
?>