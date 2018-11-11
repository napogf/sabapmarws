<?php
if($_GET["idrev"] == "") {
	header("Location: index.php");
	die();
};
$livellopagina = 5; //importante, se modificato, modificare anche nel menu in adminfunct.php
$hack = TRUE;
require("adminfunct.php");
bcdb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Amministrazione vincoli SBAP-VR</title>
</head>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
	text-align: center;
	font-size: 13px;
	color: #000000;
}
h1 {
	font-weight: bold;
	font-size: 14px;
}
a {
	color: #0066CC;
	text-decoration: underline;
}
a:hover {
	color: #0000FF;
	text-decoration: none;
	font-weight: bold;
}
</style>
<body>
<p>
<h1>Dettagli precedenti alla modifica</h1>
La finestra pu&ograve; essere chiusa direttamente, alla fine della consultazione.
<?php
$sql = 'SELECT * FROM vincoli_revisioni WHERE idrev = \''.$_GET["idrev"].'\'';
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
?>
<div align="center"><table cellpadding="5" cellspacing="5" border="1px solid black" frame="below" rules="rows">
<tr>
	<td><strong>Comune</strong></td>
	<td><?php echo($row["comune"]); ?></td>
</tr>
<tr>
	<td><strong>Provincia</strong></td>
	<td><?php echo($row["provincia"]); ?></td>
</tr>
<tr>
	<td><strong>Localit&agrave;</strong></td>
	<td><?php echo($row["localita"]); ?></td>
</tr>
<tr>
	<td><strong>Ubicazione</strong></td>
	<td><?php echo($row["ubicazioneinit"]); ?> <?php echo($row["ubicazioneprinc"]); ?></td>
</tr>
<tr>
	<td><strong>Denominazione</strong></td>
	<td><?php echo($row["denominazione"]); ?></td>
</tr>
<tr>
	<td><strong>Provvedimento Ministeriale</strong></td>
	<td><?php echo($row["provvedimentoministeriale"]); ?></td>
</tr>
<tr>
	<td><strong>Trascrizione in Conservatoria</strong></td>
	<td><?php echo($row["trascrizioneinconservatoria"]); ?></td>
</tr>
<tr>
	<td><strong>Foglio catastale</strong></td>
	<td><?php echo($row["fogliocatastale"]); ?></td>
</tr>
<tr>
	<td><strong>Particelle</strong></td>
	<td><?php echo($row["particelle"]); ?></td>
</tr>
<tr>
	<td><strong>Modifiche catastali</strong></td>
	<td><?php echo($row["modifichecatastali"]); ?></td>
</tr>
<tr>
	<td><strong>Tipo di vincolo</strong></td>
	<td><?php
if (($row["vincolodiretto"] == "x") || ($row["vincolodiretto"] == "X")) $direct = TRUE;
if (($row["vincoloindiretto"] == "x") || ($row["vincoloindiretto"] == "X")) $indirect = TRUE;
if (($direct) && (!$indirect)) echo("Diretto");
if ((!$direct) && ($indirect)) echo("Indiretto");
if (($direct) && ($indirect)) echo("Diretto ed indiretto");
if ((!$direct) && (!$indirect)) echo("Nessuno");
	?>
	</td>
</tr>
<tr>
	<td><strong>DLgs 42/2004</strong></td>
	<td><?php echo($row["dlgs422004"]); ?></td>
</tr>
<tr>
	<td><strong>DL 490/1999</strong></td>
	<td><?php echo($row["dl4901999"]); ?></td>
</tr>
<tr>
	<td><strong>L 1089/1939</strong></td>
	<td><?php echo($row["l10891939"]); ?></td>
</tr>
<tr>
	<td><strong>L 364/1909</strong></td>
	<td><?php echo($row["l3641909"]); ?></td>
</tr>
<tr>
	<td><strong>Note</strong></td>
	<td><?php echo($row["note"]); ?></td>
</tr>
<tr>
	<td><strong>Posizione generale comune</strong></td>
	<td><?php echo($row["posizionegeneralecomune"]); ?></td>
</tr>
<tr>
	<td><strong>Cartella progetti monumentale</strong></td>
	<td><?php echo($row["cartellaprogettimonumentale"]); ?></td>
</tr>
<tr>
	<td><strong>Eventuale subposizione</strong></td>
	<td><?php echo($row["eventualesubposizione"]); ?></td>
</tr>
<tr>
	<td><strong>Fascicolo vincolo</strong></td>
	<td><?php echo($row["fascicolovincolo"]); ?></td>
</tr>
<tr>
	<td><strong>Fascicolo progetti</strong></td>
	<td><?php echo($row["fascicoloprogetti"]); ?></td>
</tr>
<tr>
	<td><strong>Visibile al pubblico</strong></td>
	<td><?php 
	if ($row["visibile"]) echo("SI (visibile)");
	else echo("NO (nascosto)"); ?></td>
</tr>
<tr>
	<td><strong>Identificativo nel database</strong></td>
	<td><?php echo($row["id"]); ?> - <?php echo($row["tabella"]); ?></td>
</tr>
<tr>
	<td><strong>Utente (id/user)</strong></td>
	<td><?php echo($row["user_id"]); ?> - <?php echo($row["user_user"]); ?></td>
</tr>
<tr>
	<td><strong>Cognome e nome utente</strong></td>
	<td><?php echo($row["user_cognome"]); ?> <?php echo($row["user_nome"]); ?></td>
</tr>
<tr>
	<td><strong>Data sostituzione/modifica</strong></td>
	<td><?php echo($row["datamod"]); ?></td>
</tr>
</table></div>

</p>
</body>
</html>
<?php
bcdb(1);
?>
