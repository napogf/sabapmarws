<?php
if($_GET["id"] == "") {
	header("Location: index.php");
	die();
};
require("adminfunct.php");
bcdb();
bcadminhead("Modifiche del vincolo"); //bcadminhead("TITOLO", TRUE/*se script comuni*/);
?>
Elenco delle modifiche:<br /><br />
<?php
$sql = 'SELECT * FROM vincoli_revisioni WHERE id = \''.$_GET["id"].'\' ORDER BY idrev DESC';
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res))
	echo("<strong>".$row["datamod"]."</strong> - <a href=\"dettaglirevisione.php?idrev=".$row["idrev"]."\" target=\"_blank\">Dettagli precedenti alla modifica-&gt;</a><br />");
?>
<br />
Tornare alla <a href="elencovincoli.php?dettagli=TRUE&id=<?php echo($_GET["id"]); ?>">pagina dei dettagli</a>.

<?php
bcadminfoot();
bcdb(1);
?>