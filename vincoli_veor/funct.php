<?php
//database
function bcdb($int = 0) {
global $dbconn, $dbselz;
if ($int == 0) {
$dbuser = "vincoli";
$dbpass = "vincoli";
$dbhost = "localhost";
$dbname = "vincoli";
$dbconn = mysql_connect($dbhost, $dbuser, $dbpass) or die("Errore connessione server mysql.");
$dbselz = mysql_select_db($dbname, $dbconn) or die("Errore selezione database.");
} else {
mysql_close($dbconn);
};
};
//grafica
function echocomune() {

global $ro, $vi, $vr;

$ro[]="Rovigo";
$ro[]="Adria";
$ro[]="Ariano nel Polesine";
$ro[]="Arquà Polesine";
$ro[]="Badia Polesine";
$ro[]="Bagnolo di Po";
$ro[]="Bergantino";
$ro[]="Bosaro";
$ro[]="Calto";
$ro[]="Canaro";
$ro[]="Canda";
$ro[]="Castelguglielmo";
$ro[]="Castelmassa";
$ro[]="Castelnovo Bariano";
$ro[]="Ceneselli";
$ro[]="Ceregnano";
$ro[]="Corbola";
$ro[]="Costa di Rovigo";
$ro[]="Crespino";
$ro[]="Ficarolo";
$ro[]="Fiesso Umbertiano";
$ro[]="Frassinelle Polesine";
$ro[]="Fratta Polesine";
$ro[]="Gaiba";
$ro[]="Gavello";
$ro[]="Giacciano con Baruchella";
$ro[]="Guarda Veneta";
$ro[]="Lendinara";
$ro[]="Loreo";
$ro[]="Lusia";
$ro[]="Melara";
$ro[]="Occhiobello";
$ro[]="Papozze";
$ro[]="Pettorazza Grimani";
$ro[]="Pincara";
$ro[]="Polesella";
$ro[]="Pontecchio Polesine";
$ro[]="Porto Tolle";
$ro[]="Porto Viro";
$ro[]="Rosolina";
$ro[]="Salara";
$ro[]="San Bellino";
$ro[]="San Martino di Venezze";
$ro[]="Stienta";
$ro[]="Taglio di Po";
$ro[]="Trecenta";
$ro[]="Villadose";
$ro[]="Villamarzana";
$ro[]="Villanova del Ghebbo";
$ro[]="Villanova Marchesana";

$vi[]="Vicenza";
$vi[]="Agugliaro";
$vi[]="Albettone";
$vi[]="Alonte";
$vi[]="Altavilla Vicentina";
$vi[]="Altissimo";
$vi[]="Arcugnano";
$vi[]="Arsiero";
$vi[]="Arzignano";
$vi[]="Asiago";
$vi[]="Asigliano Veneto";
$vi[]="Barbarano Vicentino";
$vi[]="Bassano del Grappa";
$vi[]="Bolzano Vicentino";
$vi[]="Breganze";
$vi[]="Brendola";
$vi[]="Bressanvido";
$vi[]="Brogliano";
$vi[]="Caldogno";
$vi[]="Caltrano";
$vi[]="Calvene";
$vi[]="Camisano Vicentino";
$vi[]="Campiglia dei Berici";
$vi[]="Campolongo sul Brenta";
$vi[]="Carrè";
$vi[]="Cartigliano";
$vi[]="Cassola";
$vi[]="Castegnero";
$vi[]="Castelgomberto";
$vi[]="Chiampo";
$vi[]="Chiuppano";
$vi[]="Cismon del Grappa";
$vi[]="Cogollo del Cengio";
$vi[]="Conco";
$vi[]="Cornedo Vicentino";
$vi[]="Costabissara";
$vi[]="Creazzo";
$vi[]="Crespadoro";
$vi[]="Dueville";
$vi[]="Enego";
$vi[]="Fara Vicentino";
$vi[]="Foza";
$vi[]="Gallio";
$vi[]="Gambellara";
$vi[]="Gambugliano";
$vi[]="Grancona";
$vi[]="Grisignano di Zocco";
$vi[]="Grumolo delle Abbadesse";
$vi[]="Isola Vicentina";
$vi[]="Laghi";
$vi[]="Lastebasse";
$vi[]="Longare";
$vi[]="Lonigo";
$vi[]="Lugo di Vicenza";
$vi[]="Lusiana";
$vi[]="Malo";
$vi[]="Marano Vicentino";
$vi[]="Marostica";
$vi[]="Mason Vicentino";
$vi[]="Molvena";
$vi[]="Monte di Malo";
$vi[]="Montebello Vicentino";
$vi[]="Montecchio Maggiore";
$vi[]="Montecchio Precalcino";
$vi[]="Montegalda";
$vi[]="Montegaldella";
$vi[]="Monteviale";
$vi[]="Monticello Conte Otto";
$vi[]="Montorso Vicentino";
$vi[]="Mossano";
$vi[]="Mussolente";
$vi[]="Nanto";
$vi[]="Nogarole Vicentino";
$vi[]="Nove";
$vi[]="Noventa Vicentina";
$vi[]="Orgiano";
$vi[]="Pedemonte";
$vi[]="Pianezze";
$vi[]="Piovene Rocchette";
$vi[]="Pojana Maggiore";
$vi[]="Posina";
$vi[]="Pove del Grappa";
$vi[]="Pozzoleone";
$vi[]="Quinto Vicentino";
$vi[]="Recoaro Terme";
$vi[]="Roana";
$vi[]="Romano d'Ezzelino";
$vi[]="Rosà";
$vi[]="Rossano Veneto";
$vi[]="Rotzo";
$vi[]="Salcedo";
$vi[]="San Germano dei Berici";
$vi[]="San Nazario";
$vi[]="San Pietro Mussolino";
$vi[]="San Vito di Leguzzano";
$vi[]="Sandrigo";
$vi[]="Santorso";
$vi[]="Sarcedo";
$vi[]="Sarego";
$vi[]="Schiavon";
$vi[]="Schio";
$vi[]="Solagna";
$vi[]="Sossano";
$vi[]="Sovizzo";
$vi[]="Tezze sul Brenta";
$vi[]="Thiene";
$vi[]="Tonezza del Cimone";
$vi[]="Torrebelvicino";
$vi[]="Torri di Quartesolo";
$vi[]="Trissino";
$vi[]="Valdagno";
$vi[]="Valdastico";
$vi[]="Valli del Pasubio";
$vi[]="Valstagna";
$vi[]="Velo d'Astico";
$vi[]="Villaga";
$vi[]="Villaverla";
$vi[]="Zanè";
$vi[]="Zermeghedo";
$vi[]="Zovencedo";
$vi[]="Zugliano";

$vr[]="Verona";
$vr[]="Affi";
$vr[]="Albaredo d'Adige";
$vr[]="Angiari";
$vr[]="Arcole";
$vr[]="Badia Calavena";
$vr[]="Bardolino";
$vr[]="Belfiore";
$vr[]="Bevilacqua";
$vr[]="Bonavigo";
$vr[]="Boschi Sant'Anna";
$vr[]="Bosco Chiesanuova";
$vr[]="Bovolone";
$vr[]="Brentino Belluno";
$vr[]="Brenzone";
$vr[]="Bussolengo";
$vr[]="Buttapietra";
$vr[]="Caldiero";
$vr[]="Caprino Veronese";
$vr[]="Casaleone";
$vr[]="Castagnaro";
$vr[]="Castel d'Azzano";
$vr[]="Castelnuovo del Garda";
$vr[]="Cavaion Veronese";
$vr[]="Cazzano di Tramigna";
$vr[]="Cerea";
$vr[]="Cerro Veronese";
$vr[]="Cologna Veneta";
$vr[]="Colognola ai Colli";
$vr[]="Concamarise";
$vr[]="Costermano";
$vr[]="Dolcè";
$vr[]="Erbè";
$vr[]="Erbezzo";
$vr[]="Ferrara di Monte Baldo";
$vr[]="Fumane";
$vr[]="Garda";
$vr[]="Gazzo Veronese";
$vr[]="Grezzana";
$vr[]="Illasi";
$vr[]="Isola della Scala";
$vr[]="Isola Rizza";
$vr[]="Lavagno";
$vr[]="Lazise";
$vr[]="Legnago";
$vr[]="Malcesine";
$vr[]="Marano di Valpolicella";
$vr[]="Mezzane di Sotto";
$vr[]="Minerbe";
$vr[]="Montecchia di Crosara";
$vr[]="Monteforte d'Alpone";
$vr[]="Mozzecane";
$vr[]="Negrar";
$vr[]="Nogara";
$vr[]="Nogarole Rocca";
$vr[]="Oppeano";
$vr[]="Palù";
$vr[]="Pastrengo";
$vr[]="Pescantina";
$vr[]="Peschiera del Garda";
$vr[]="Povegliano Veronese";
$vr[]="Pressana";
$vr[]="Rivoli Veronese";
$vr[]="Roncà";
$vr[]="Ronco all'Adige";
$vr[]="Roverchiara";
$vr[]="Roverè Veronese";
$vr[]="Roveredo di Guà";
$vr[]="Salizzole";
$vr[]="San Bonifacio";
$vr[]="San Giovanni Ilarione";
$vr[]="San Giovanni Lupatoto";
$vr[]="San Martino Buon Albergo";
$vr[]="San Mauro di Saline";
$vr[]="San Pietro di Morubio";
$vr[]="San Pietro in Cariano";
$vr[]="San Zeno di Montagna";
$vr[]="Sanguinetto";
$vr[]="Sant'Ambrogio di Valpolicella";
$vr[]="Sant'Anna d'Alfaedo";
$vr[]="Selva di Progno";
$vr[]="Soave";
$vr[]="Sommacampagna";
$vr[]="Sona";
$vr[]="Sorgà";
$vr[]="Terrazzo";
$vr[]="Torri del Benaco";
$vr[]="Tregnago";
$vr[]="Trevenzuolo";
$vr[]="Valeggio sul Mincio";
$vr[]="Velo Veronese";
$vr[]="Veronella";
$vr[]="Vestenanova";
$vr[]="Vigasio";
$vr[]="Villa Bartolomea";
$vr[]="Villafranca di Verona";
$vr[]="Zevio";
$vr[]="Zimella";

$rof=""; foreach($ro as $com) $rof.="<option>".$com."</option>";
$vif=""; foreach($vi as $com) $vif.="<option>".$com."</option>";
$vrf=""; foreach($vr as $com) $vrf.="<option>".$com."</option>";

echo ("<script language=\"javascript\" type=\"text/javascript\">
	var dinamico = new Array();
	dinamico[\"nessuno\"] = \"<select name='comune'></select>\";
	dinamico[\"VR\"] = \"<select name='comune'>".$vrf."</select>\";
	dinamico[\"VI\"] = \"<select name='comune'>".$vif."</select>\";
	dinamico[\"RO\"] = \"<select name='comune'>".$rof."</select>\";
	function update(value){
		document.getElementById(\"dinamicocomune\").innerHTML=dinamico[value];
	}
</script>");
// foreach <option>nomecomune</option>
};
function bchead($titolo, $scriptcomune = FALSE, $paginamappe = FALSE) {
echo ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
	<title>Vincoli - SBAP Verona</title>
	<meta http-equiv="content-language" content="it" />
	
	<!--[if IE 5]>
	<link href="static/org_minervaeurope_museoweb/templates/T3mod/assets/css/ie5.css" rel="stylesheet" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE 6]>
	<link href="static/org_minervaeurope_museoweb/templates/T3mod/assets/css/ie6.css" rel="stylesheet" type="text/css" media="screen" />
	<![endif]-->
	<!--[if IE]>
	<link href="static/org_minervaeurope_museoweb/templates/T3mod/assets/css/ie7.css" rel="stylesheet" type="text/css" media="screen" />
	<![endif]-->
	<link href="../static/org_minervaeurope_museoweb/templates/T3mod/assets/css/print.css" rel="stylesheet" type="text/css" media="print" />
	<script type="text/javascript" src="../static/org_minervaeurope_museoweb/templates/T3mod/../../../org_glizy/assets/js/Glizy.js"></script>
	<script type="text/javascript" src="../static/org_minervaeurope_museoweb/templates/T3mod/../../../org_glizy/assets/js/locale/it.js"></script>
	<link rel="stylesheet" href="../static/org_minervaeurope_museoweb/templates/T3mod/assets/css/styles.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="../static/org_minervaeurope_museoweb/templates/T3mod/assets/css/stilefinale.css" type="text/css" media="screen" />
	');
	if ($scriptcomune) echocomune();
	echo('
</head>
<body>
<div id="wrap">
	
	<div id="headerpiccolo"><img alt="Sito web istituzionale ad uso informativo per l&#8217;utenza" title="Sito web istituzionale ad uso informativo per l&#8217;utenza" style="margin-top: 0px; margin-left: 0px;" src="../static/org_minervaeurope_museoweb/templates/T3mod/assets/images/headerImagepiccolo.jpg"/></div>
	
	<div id="header">
		<div class="headerLogo"><a href="../index.php" title="SBAP - VR" class=""><img alt="mibac" title="MiBAC, SBAP VR" style="margin-top: 0px; margin-left: 0px;" src="../static/org_minervaeurope_museoweb/templates/T3mod//assets/images/logo_new.jpg"/></a></div>
    </div>

	<div id="leftSidebar">
		<ul class="navigationMenu" id="navigation0">
		<li><a href="../index.php" title="Home page">Torna alla Home</a></li>
		<li><a href="index.php" title="Archivio vincoli">Archivio vincoli</a>
			<ul>
			<li><a href="mappe.php" title="Mappe">Mappe</a>
				');
	if ($paginamappe) echo('<ul>
				<li><a href="http://sbap-vr.beniculturali.it/index.php?it/137/mappe-di-verona-centro-storico" title="Verona centro storico">Verona centro storico</a></li>
				</ul>');
	echo('
			</li>
			<li><a href="index.php" title="Ricerca vincoli">Ricerca vincoli</a></li>
			<li><a href="admin.php" title="Area riservata">Area riservata</a></li>
			</ul>
		</li>
		</ul>

    </div>
	
    <div id="content">
		<div id="internalWrap">
			<p id="breadcrumbs"><a href="../index.php" title="Home">Home</a> &gt; Vincoli</p>
			<h2>'.$titolo.'</h2><p style="text-align:left">');
};
function bcfoot() {
echo ('</p>
		</div>
		<div class="clear"></div>
	</div>
	<div id="footer">
		<div class="right"> <!-- in questo caso right = sinistra -->
			<p><strong>Soprintendenza per i Beni Architettonici e Paesaggistici<br />per le province di Verona, Rovigo e Vicenza</strong><br />Piazza San Fermo 3a<br />37121 VERONA</p><p>Soprintendente <em>ad interim</em>: architetto Andrea Alberti<a href="mailto:sbap-vr@beniculturali.it"><br /></a><a href="http://sbap-vr.beniculturali.it"></a> </p><p>&nbsp;</p><p><a href="http://www.adobe.com/go/IT-H-GET-READER"><img alt="Scarica Adobe Reader" height="33" src="../getImage.php?id=61&amp;w=112&amp;h=33" style="width: 112px; height: 33px" title="Scarica Adobe Reader" width="112" /></a></p>		</div>

		<div class="left"> <!-- in questo caso left = destra -->
			<p>Copyright 2008 Ministero per I beni e le attività culturali - <a href="../index.php?it/95/note-legali" title="Note Legali">Note Legali</a> | <a href="../index.php?it/129/crediti" title="Crediti">Crediti</a></p><br />
			<a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10-blue" style="border:0;width:88px;height:31px" alt="Valid XHTML 1.0 Strict" /></a>
			&nbsp;&nbsp;&nbsp;
			<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="Valid CSS 3" /></a>

			<br /><br />
			<a href="../index.php?it/94/dichiarazone-accessibilit%E0" title="Dichiarazione di accessibilit&agrave;">Dichiarazione di accessibilit&agrave;</a>
		</div>
		<div class="clear"></div>
	</div>
</div>
</body>
</html>');
};
?>