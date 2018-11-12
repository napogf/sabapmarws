<?php
require("adminfunct.php");
bcdb();
bcadminhead("Home"); //bcadminhead("TITOLO", TRUE/*se script comuni*/);
?>
Benvenuto.<br />Questa &egrave; la copia in locale della pagina di amministrazione dei vincoli.<br /><br />Per accedere alle varie funzioni puoi premere sui collegamenti nel menu a sinistra.<br /><br />
Data ed ora dell'ultima copia: 
<?php
$fpr = fopen("last.txt", "r");
echo(fgets($fpr));
fclose($fpr);
bcadminfoot();
bcdb(1);
?>