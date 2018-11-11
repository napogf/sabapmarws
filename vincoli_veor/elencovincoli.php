<?php
require("adminfunct.php");
bcdb();
bcadminhead("Ricerca vincoli", TRUE); //bcadminhead("TITOLO", TRUE/*se script comuni*/);

if ($_GET["cerca"]) {
?>

<table cellpadding="5" cellspacing="5" border="1px solid black" frame="below" rules="rows">
<tr>
	<td><strong>Comune</strong></td>
	<td><strong>Ubicazione</strong></td>
	<td><strong>Denominazione</strong></td>
	<td><strong>Provvedimento Ministeriale</strong></td>
	<td>&nbsp;</td>
</tr>
<?php
if ($_GET["last"]) {
	$last = explode("-", $_SESSION["bclast"]);
	$sql = 'SELECT * FROM vincoli_db WHERE (id = \'1000000000000\'';
	foreach($last as $iddd)
		$sql.=' OR id = \''.$iddd.'\'';
	$sql.=')';
} else {
	$fcatast=explode(" ", $_POST["fogliocatastale"]);
	$fcatast=explode("/", $fcatast[0]);
	$fcatast=$fcatast[0];
$sql = 'SELECT * FROM vincoli_db WHERE provincia = \''.$_POST["provincia"].'\' AND (visibile = \'1\' OR visibile = \'0\') AND MATCH (comune) AGAINST (\'+'.str_replace(" ", " +", trim($_POST["comune"])).'\' IN BOOLEAN MODE)';
if ($_POST["vincolo"] == "1")
	$sql .= ' AND (vincolodiretto = \'X\' OR vincolodiretto = \'x\')';
if ($_POST["vincolo"] == "2")
	$sql .= ' AND (vincoloindiretto = \'X\' OR vincoloindiretto = \'x\')';
if ($_POST["fogliocatastale"] != "")
	$sql .= ' AND (fogliocatastale LIKE \''.$fcatast.' %\' OR fogliocatastale LIKE \''.$fcatast.'/%\' OR fogliocatastale = \''.$fcatast.'\')';
if ($_POST["filtro"] != "")
	$sql .= ' AND MATCH(ubicazioneinit,ubicazioneprinc,localita,denominazione) AGAINST(\'+'.str_replace(" ", " +", $_POST["filtro"]).'\' IN BOOLEAN MODE)';
};
$_SESSION["bclast"] = "100000000000";
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res)) {
	if ($_POST["particelle"] != "") {
		$post = str_replace(" ", "-", $_POST["particelle"]);
		$post = str_replace("_", "-", $post);
		$post = str_replace("/", "-", $post);
		$post = explode("-", $post);
		$part = str_replace(" ", "-", $row["particelle"]."-".$row["modifichecatastali"]);
		$part = str_replace("_", "-", $part);
		$part = str_replace("/", "-", $part);
		$part = explode("-", $part);
		$erepart=0;
		foreach($part as $parti)
			foreach($post as $posti)
				if ($parti==$posti)
					$erepart++;
		if ($erepart) {
			echo('<tr>
				<td>'.$row["comune"].' ('.$row["provincia"].')</td>
				<td>'.$row["ubicazioneinit"].' '.$row["ubicazioneprinc"].'</td>
				<td>'.$row["denominazione"].'</td>
				<td>'.$row["provvedimentoministeriale"].'</td>
				<td><a href="elencovincoli.php?dettagli=TRUE&id='.$row["id"].'">Dettagli-&gt;</a></td>
				</tr>
			');
			$_SESSION["bclast"].=("-".$row["id"]."");
		};
	} else {
		echo('<tr>
				<td>'.$row["comune"].' ('.$row["provincia"].')</td>
				<td>'.$row["ubicazioneinit"].' '.$row["ubicazioneprinc"].'</td>
				<td>'.$row["denominazione"].'</td>
				<td>'.$row["provvedimentoministeriale"].'</td>
				<td><a href="elencovincoli.php?dettagli=TRUE&id='.$row["id"].'">Dettagli-&gt;</a></td>
				</tr>
		');
		$_SESSION["bclast"].=("-".$row["id"]."");
	};
};
?>
</table>
<br />
<div align="center"><a href="elencovincoli.php">Nuova ricerca</a></div>

<?php
} elseif ($_GET["dettagli"]) {
?>

<?php
$sql = 'SELECT * FROM vincoli_db WHERE id = \''.$_GET["id"].'\'';
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
?>
<table cellpadding="5" cellspacing="5" border="1px solid black" frame="below" rules="rows">
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
	<td><strong>Particelle *</strong></td>
	<td><?php echo($row["particelle"]); ?></td>
</tr>
<tr>
	<td><strong>Modifiche catastali **</strong></td>
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
	if ($row["visibile"] == 2) echo("ELIMINATO! Se lo si vuole far tornare nuovamente visibile crearne uno nuovo con gli stessi dati");
	if ($row["visibile"] == 1) echo("SI (visibile)");
	if ($row["visibile"] == 0) echo("NO (nascosto)"); ?></td>
</tr>
<tr>
	<td><strong>Identificativo nel database</strong></td>
	<td><?php echo($row["id"]); ?> - <?php echo($row["tabella"]); ?></td>
</tr>
</table>
<div align="left">
* Le particelle sono quelle riportate nei decreti di vincolo, salvo per quelli antecedenti l'entrata in
vigore della legge 1° giugno 1939 n. 1089, privi di indicazioni catastali, per i quali l'individuazione
delle particelle, effettuata - quando possibile - dalla Soprintendenza, è pertanto puramente indicativa.
<br />
** Per comodità si indicano alcuni aggiornamenti catastali.
<br /></div>
<div align="center">
<br /><a href="revisionivincoli.php?id=<?php echo($row["id"]); ?>"><h2>VISUALIZZA REVISIONI</h2></a>
<br /><a href="elencovincoli.php">Nuova ricerca</a><br /><br /><a href="elencovincoli.php?cerca=TRUE&last=TRUE">Torna all'elenco precedente</a>
</div>

<?php
} else {
?>

<form action="elencovincoli.php?cerca=TRUE" method="post">
<input name="ok" type="hidden" value="ok" />

<table border="0" cellpadding="5" cellspacing="5" style="vertical-align:middle">
<tr>
<td valign="top">Provincia</td>
<td><select name="provincia" onChange="update(this.value)" >
  <option value="nessuno">Selezionare</option>
  <option value="VR">Verona</option>
  <option value="VI">Vicenza</option>
  <option value="RO">Rovigo</option>
</select>&nbsp;&nbsp;&nbsp;(campo necessario)</td>
</tr>
<tr>
<td valign="top">Comune</td>
<td><div id="dinamicocomune"><select name="comune">
</select></div>&nbsp;&nbsp;&nbsp;(campo necessario)</td>
</tr>
<tr>
<td valign="top">Foglio catastale</td>
<td><input name="fogliocatastale" type="text" size="30" /></td>
</tr>
<tr>
<td valign="top">Filtro aggiuntivo</td>
<td><input name="filtro" type="text" size="30" />&nbsp;&nbsp;&nbsp;(per ubicazione, denominazione, localit&agrave;)</td>
</tr>
<tr>
<td valign="top">Particella</td>
<td><input name="particelle" type="text" size="30" /></td>
</tr>
<tr>
<td valign="top">Tipo di vincolo</td>
<td><select name="vincolo">
  <option value="0">Entrambi</option>
  <option value="1">Diretto</option>
  <option value="2">Indiretto</option>
</select></td>
</tr>
<tr>
<td valign="top">&nbsp;</td>
<td><input name="send" type="submit" value="Cerca"></td>
</tr>
</table>
</form>

<?php
};
bcadminfoot();
bcdb(1);
?>