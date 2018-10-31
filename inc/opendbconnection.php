<?php
// Apre un connessione con il database
if(!$linkDB = $dbconn = ociplogon($user, $pwd, $fontedati))
	errore("<font size=+1>Attenzione impossibile aprire una connessione con il DB </font><hr>$fontedati. <BR>");

 ?>
