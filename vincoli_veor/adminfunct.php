<?php
//database + globali comuni in provincie
require("funct.php");
//funzione che purifica testo in input
function purt($input) {
	$output = trim($input);
	return $output;
};
//grafica
function bcadminhead($title = "Amministrazione vincoli SBAP-VR", $scriptcomune = FALSE) {
	echo('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Amministrazione vincoli SBAP-VR</title>
<style type="text/css">
body {
	font-family: Arial, Helvetica, sans-serif;
	text-align: left;
	font-size: 12px;
	color: #000000;
}
h1 {
	font-weight: bold;
	font-size: 17px;
}
h2 {
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
	/*font-weight: bold;*/
}
table {
	width: 100%;
	padding: 0px;
	border-spacing: 0px;
	border: none;
}
td, tr {
	padding: 5px;
	border-spacing: 0px;
}
img {
	border: none;
	width: auto;
	height: auto;
}
input, select {
	border: 1px solid #000000;
	background-color: #E6E6FF; 
	font-size: 12px;
}
</style>');
if ($scriptcomune) echocomune();
echo('
</head>
<body>
<table>
  <tr>
    <td><a href="index.php"><img src="logo_locale.jpg" alt="Sbap-Vr" /></a></td>
    <td width="100%"><h1>Amministrazione vincoli</h1></td>
  </tr>
  <tr>
    <td valign="top"><ul>
		<li><a href="elencovincoli.php">ELENCO VINCOLI</a></li>
		<li><a href="copiaserver.php">ESEGUI COPIA</a></li>
	</ul></td>
    <td><h2>'.$title.'</h2>
	');
};
function bcadminfoot() {
	echo('</td>
  </tr>
</table>

</body>
</html>
');
};
?>