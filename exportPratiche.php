<?php
/*
 * Created on 24/giu/09
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");

	$prQuery='select pr.pratica_id,' .
					'pr.modello modello_id, ' .
					'pr.zona zona_id, ' .
					'null user_id, ' .
					'pr. responsabile, ' .
					'pr.numeroregistrazione, ' .
					'year(dataregistrazione) anno, ' .
					'dataregistrazione data_registrazione, ' .
					'pr.dataarrivo data_arrivo, ' .
					'pr.uscita data_chiusura, ' .
					'pr.scadenza data_scadenza, ' .
					'vs.inizio inizio_sospensione,
					vs.fine fine_sospensione, ' .
					'vs.motivazione motivo_sospensione, ' .
					'pr.condizione as parere, ' .
					'pr.oggetto, ' .
					'pr.note ' .
				'from pratiche pr ' .
					'left join arc_zone az on (az.zona = pr.zona) ' .
					'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
					'left join arc_modelli am on (am.modello = pr.modello) ' .
					'left join v_sospensioni vs on (vs.pratica_id = pr.pratica_id) ' .
				'where 1=1 ';


    $data = mysql_query($prQuery);
    while($dt = mysql_fetch_row($data)):
        $backup .= "INSERT INTO `exp_mibac.Pratica` VALUES('$dt[0]'";
        for($i=1; $i<sizeof($dt); $i++):
            $backup .= ", '$dt[$i]'";
        endfor;
        $backup .= ");\n";
    endwhile;
    $backup .= "\n-- --------------------------------------------------------\n\n";

echo $backup;


?>
