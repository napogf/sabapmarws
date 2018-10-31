<?php
// Chiude una connessione con il server MySql specificata dal link passato,
// nel caso il link sia errato verrà chiusa la connessione dell'ultimo link utilizzato.

   if(!ocilogoff($linkDB))
	die("<font size=+1>Attenzione, Problemi di chiusura del DB </font><hr><b>$fontedati</b>. <br>");
 ?>
