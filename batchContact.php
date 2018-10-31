<?php
      function createPassword($length=8,$use_upper=1,$use_lower=1,$use_number=1,$use_custom=""){
          $upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
          $lower = "abcdefghijklmnopqrstuvwxyz";
          $number = "0123456789";
          if($use_upper){
              $seed_length += 26;
              $seed .= $upper;
          }
          if($use_lower){
              $seed_length += 26;
              $seed .= $lower;
          }
          if($use_number){
              $seed_length += 10;
              $seed .= $number;
          }
          if($use_custom){
              $seed_length +=strlen($use_custom);
              $seed .= $use_custom;
          }
          for($x=1;$x<=$length;$x++){
              $password .= $seed{rand(0,$seed_length-1)};
          }
          return($password);
      }
/*
 * Created on 17/ott/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/configsess.php";
//require_once ('dbfunctions.php');
//require_once "Mail.php";
//require_once ('Mail/mime.php');

$alarmQuery = 'select pr.PRATICA_ID, ' .
						'pr.ZONA, ' .
						'(case ' .
						'	when (pr.modello is null) then \'praOpen\' ' .
						'	when pr.dataarrivo is null then \'praOpen\' ' .
						'	when (pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\')) ) then \'praClosed\' ' .
						'	when date_add(pr.dataarrivo, INTERVAL am.scadenza DAY) <= now() then \'praAllarm\' ' .
						'	when date_add(pr.dataarrivo, INTERVAL (am.scadenza-am.allarme) DAY) <= now() then \'praAlert\' ' .
						'end) as ROWCLASS , ' .
						'pr.NUMEROREGISTRAZIONE, ' .
						'date_format(pr.dataregistrazione,\'%d-%m-%Y\') as "DATAREGISTRAZIONE", ' .
						'date_format(pr.dataregistrazione,\'%Y\') as "ANNO", ' .
						'am.description as \'MODELLO\', ' .
						'pr.nome  NOME,' .
						'pr.comuneogg as "OGGETTO", ' .
						'pr.pnome as "PROPIETARIO", ' .
						'substring(pr.cognome,1,40) COGNOME, ' .
						'date_format(pr.dataarrivo,\'%d-%m-%Y\') as "ARRIVO", ' .
						'date_format(pr.firma,\'%d-%m-%Y\') as "FIRMA", ' .
						'date_format(pr.uscita,\'%d-%m-%Y\') as "USCITA", ' .
						'date_format(pr.scadenza,\'%d-%m-%Y\') as "SCADENZA", ' .
						'am.ALLARME, ' .
						'az.code as ZONACOD, ' .
						'pr.email as EMAIL, ' .
						'pr.email_flag as EMAILFLAG, ' .
						'substring(az.moddesc,1,20) ZONADES ' .
					'from pratiche pr ' .
					'left join arc_zone az on (az.zona = pr.zona) ' .
					'left join arc_modelli am on (am.modello = pr.modello) ' .
//					'where (pr.scadenza > \'00-00-0000\' or pr.scadenza is not null) ';
					'where ' .
					' (pr.scadenza > \'00-00-0000\' and pr.scadenza is not null) ' .
					 'and (pr.modello is not null or pr.modello > 0) ' .
					 'and (pr.zona is not null or pr.zona > 0) ';


$alarmResults = dbselect($alarmQuery);
$zona = null;
$index = 0;

$from = "sbap-ve@beniculturali.it";
$host = "10.96.0.38";
$port = '25';
$username = "ferdinando.rizzardo@beniculturali.it";
$password = "Ducale01";
$smtpAuth = false;
$debug= false;


$smtp = Mail::factory('smtp',
			  array ('host' => $host,
			  		'port' => $port,
				    'auth' => $smtpAuth,
				    'username' => $username,
				    'password' => $password,
				    'debug' => $debug ));

do {
	if ($alarmResults['ROWS'][$index]['PRATICA_ID']>'') {
		$newPassword=createPassword();
		if(!$passResult=dbselect('select * from arc_password where pratica_id='.$alarmResults['ROWS'][$index]['PRATICA_ID'])){
			$passQuery='insert into arc_password (pratica_id, ' .
													'numeroregistrazione, ' .
													'password, ' .
													'email, nome_cognome) values ' .
												'('.$alarmResults['ROWS'][$index]['PRATICA_ID'].',\''.
													$alarmResults['ROWS'][$index]['NUMEROREGISTRAZIONE'].'-'.$alarmResults['ROWS'][$index]['ANNO'].'\', \''.
													$newPassword.'\', \''.
													$alarmResults['ROWS'][$index]['EMAIL'].'\', \''.
													addslashes($alarmResults['ROWS'][$index]['COGNOME'].' '.$alarmResults['ROWS'][$index]['NOME']).'\'' .
															')';
		}
//		else {
//			$passQuery = 'update arc_password set password = \''.$newPassword.'\' , ' .
//												'numeroregistrazione =  \''.$alarmResults['ROWS'][$index]['NUMEROREGISTRAZIONE'].'-'.$alarmResults['ROWS'][$index]['ANNO'].'\',' .
//												'email =  \''.$alarmResults['ROWS'][$index]['EMAIL'].'\', ' .
//												'nome_cognome = \''.addslashes($alarmResults['ROWS'][$index]['COGNOME'].' '.$alarmResults['ROWS'][$index]['NOME']).'\' ' .
//												'where pratica_id = '.$alarmResults['ROWS'][$index]['PRATICA_ID'];
//		}
		dbupdate($passQuery);
		if ($alarmResults['ROWS'][$index]['EMAILFLAG']=='Y') {
			dbupdate('update pratiche set email_flag = \'N\' where pratica_id = ' . $alarmResults['ROWS'][$index]['PRATICA_ID']);
			$bodyMail = '<html>
								<head>
								<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
								<title>La sua Pratica � stata aggiornata</title>
								</head>
								<body>' .
			'<p>Gentile ' . $alarmResults['ROWS'][$index]['COGNOME'] . ' la sua pratica è stata aggiornata </p>' .
			'<p>Nr. Reg.:' . $alarmResults['ROWS'][$index]['NUMEROREGISTRAZIONE'].'-'.$alarmResults['ROWS'][$index]['ANNO']. '</p>' .
			'<p>Data.Reg.:' . $alarmResults['ROWS'][$index]['DATAREGISTRAZIONE'] . '</p>' .
			'<p>Oggetto:' . $alarmResults['ROWS'][$index]['OGGETTO'] . '</p>' .
			'<p>Arrivo:' . $alarmResults['ROWS'][$index]['ARRIVO'] . '</p>' .
			'<p>Scadenza:' . $alarmResults['ROWS'][$index]['SCADENZA'] . '</p>' .
			'<p><b>user:' . $alarmResults['ROWS'][$index]['NUMEROREGISTRAZIONE'] .'-'.$alarmResults['ROWS'][$index]['ANNO']. '</p>' .
			'<p>password:' . $newPassword . '</b></p>' .
			'</br>' .
			'<p>Può verificarne lo stato al seguente indirizzo:' .
			'<a href="http://www.soprintendenza.venezia.beniculturali.it/soprive/visualizzazione_avanzamento_pratiche/login_pratica_form">Soprintendenza B.A.P. di Venezia e Laguna</a></p>';
			$subjectMail = 'Aggiornamento Pratica Nr.' . $alarmResults['ROWS'][$index]['NUMEROREGISTRAZIONE'] . ' del ' . $alarmResults['ROWS'][$index]['DATAREGISTRAZIONE'];
			$crlf = "\n";
			if (($toMail=$alarmResults['ROWS'][$index]['EMAIL'])>'') {
//				$toMail = 'frizzardo@gmail.com';

				$headers = array ('From' => $from,
							  'To' => $toMail,
							  'Subject' => $subjectMail,
							  'Content-Transfer-Encoding' => 'quoted-printable',
							  'charset' => 'iso-8859-1' ,
							  'Content-Type'=> 'text/html');

				$message = new Mail_mime($crlf);
				$index++;
				if ($message instanceof Mail_mime) {
					$bodyMail .= '</table></body></html>';
					//$message->setTXTBody($bodyMailTxt);
					$message->setHTMLBody($bodyMail);
					$body = $message->get();
					print(date('d-m-Y H:i').' - Mail spedita a :'.$toMail.'-'.$alarmResults['ROWS'][$index]['NUMEROREGISTRAZIONE'].'-'.$alarmResults['ROWS'][$index]['COGNOME'].' '.$alarmResults['ROWS'][$index]['NOME']."</br>\n");
					 $mail = $smtp->send($toMail, $headers, $body);
					if (PEAR::isError($mail)) {
						echo("<p>" . $mail->getMessage() . "</p>");
						exit;
					}
				}
			}

		}
	}
	$index++;
} while ($index < $alarmResults['NROWS']);


// reloading pratiche
dbupdate('delete from tmp_mibac.pratiche');
	$prQuery='insert into tmp_mibac.pratiche select ' .
					'pr.pratica_id,' .
					'pr.modello modello_id, ' .
					'pr.zona zona_id, ' .
					'pr.pratica_id+1000 user_id, ' .
					'pr. responsabile, ' .
					'pr.numeroregistrazione, ' .
					'year(dataregistrazione) anno, ' .
					'dataregistrazione data_registrazione, ' .
					'pr.dataarrivo data_arrivo, ' .
					'pr.uscita data_chiusura, ' .
					'pr.scadenza data_scadenza, ' .
					'vs.inizio inizio_sospensione,' .
					'vs.fine fine_sospensione, ' .
					'vs.motivazione motivo_sospensione, ' .
					'pr.condizione as parere, ' .
					'pr.oggetto, ' .
					'pr.note ' .
				'from pratiche pr ' .
					'left join arc_zone az on (az.zona = pr.zona) ' .
					'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
					'left join arc_modelli am on (am.modello = pr.modello) ' .
					'left join v_sospensioni vs on (vs.pratica_id = pr.pratica_id) ' .
				'where  ' .
					' (pr.scadenza > \'00-00-0000\' and pr.scadenza is not null) ' .
					 'and (pr.modello is not null or pr.modello > 0) ' .
					 'and (pr.zona is not null or pr.zona > 0) ';

dbupdate($prQuery);


dbupdate('delete from tmp_mibac.passwords');
dbupdate('insert into tmp_mibac.passwords select * from arc_password');

dbupdate('delete from tmp_mibac.modelli');
dbupdate('insert into tmp_mibac.modelli select MODELLO, DESCRIPTION, SCADENZA from arc_modelli');

dbupdate('delete from tmp_mibac.zone');
dbupdate('insert into tmp_mibac.zone select ZONA, CODE, DESCRIPTION from arc_zone');


?>