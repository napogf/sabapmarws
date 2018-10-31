<?php
/*
 * Created on 17/ott/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/configsess.php";
//require_once('dbfunctions.php');
//require_once "Mail.php";
//require_once('Mail/mime.php');


$alarmQuery='select pr.PRATICA_ID, ' .
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
						'am.description as \'MODELLO\', ' .
						'pr.comuneogg as "OGGETTO", ' .
						'pr.pnome as "PROPIETARIO", ' .
						'substring(pr.cognome,1,40) COGNOME, ' .
						'date_format(pr.dataarrivo,\'%d-%m-%Y\') as "ARRIVO", ' .
						'date_format(pr.firma,\'%d-%m-%Y\') as "FIRMA", ' .
						'date_format(pr.uscita,\'%d-%m-%Y\') as "USCITA", ' .
						'date_format(pr.scadenza,\'%d-%m-%Y\') as "SCADENZA", ' .
						'am.ALLARME, ' .
						'az.code as ZONACOD, ' .
						'az.email as EMAIL, ' .
						'substring(az.moddesc,1,20) ZONADES ' .
				'from pratiche pr ' .
				'left join arc_zone az on (az.zona = pr.zona) ' .
				'left join arc_modelli am on (am.modello = pr.modello) ' .

				'where (pr.zona is not null)  ' .
					 'and date_add(pr.dataarrivo, INTERVAL (am.scadenza-am.allarme) DAY) <= now() ' .
					 'and ((pr.uscita is null) or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))) ' .
					 'and (pr.scadenza > \'00-00-0000\' or pr.scadenza is not null) ' .
				' order by pr.zona, pr.dataregistrazione desc, pr.numeroregistrazione ';


$alarmResults=dbselect($alarmQuery);
$zona=null;
$index=0;


$from = "sbappsae-ve.ufficioinformatico@beniculturali.it";
//$host = "smtps.beniculturali.it";
//$username = "sbappsae-ve.ufficioinformatico@beniculturali.it";
//$password = "Ducale01";
//$smtpAuth = true;

$host = "10.96.0.38";
$username = "ferdinando.rizzardo@beniculturali.it";
$password = "Ducale01";
$smtpAuth = false;

$smtp = Mail::factory('smtp',
			  array ('host' => $host,
				    'auth' => $smtpAuth,
				    'username' => $username,
				    'password' => $password));

do {
	if ($zona<>$alarmResults['ROWS'][$index]['ZONA']) {
		$bodyMail = '<html>
					<head>
					<meta http-equiv="content-type" content="text/html; charset=UTF-8">
					<title>Pratiche in scadenza</title>
					</head>
					<body><table border="1"><tr bgcolor="silver">' .
					'<th>Nr. Reg.</th>' .
					'<th>Data.Reg.</th>' .
					'<th>Mittente</th>' .
					'<th>Oggetto</th>' .
					'<th>Arrivo</th>' .
					'<th>Scadenza</th>' .
					'</tr>';
		$subjectMail = 'Pratiche in scadenza al '.date('d-m-Y').' '.$alarmResults['ROWS'][$index]['ZONADES'] ;
		$crlf="\n";
		$toMail=$alarmResults['ROWS'][$index]['EMAIL'];
	    	$message = new Mail_mime($crlf);
		$zona=$alarmResults['ROWS'][$index]['ZONA'];

		$headers = array ('From' => $from,
					  'To' => $toMail,
					  'Subject' => $subjectMail,
					  'Content-Transfer-Encoding' => 'quoted-printable',
					  'charset' => 'UTF-8' ,
					  'Content-Type'=> 'text/html');
	}
	$bodyMail .= '<tr>' .
					'<td>'.$alarmResults['ROWS'][$index]['NUMEROREGISTRAZIONE'].'</td>' .
					'<td>'.$alarmResults['ROWS'][$index]['DATAREGISTRAZIONE'].'</td>' .
					'<td>'.$alarmResults['ROWS'][$index]['COGNOME'].'</td>' .
					'<td>'.$alarmResults['ROWS'][$index]['OGGETTO'].'</td>' .
					'<td>'.$alarmResults['ROWS'][$index]['ARRIVO'].'</td>' .
					'<td>'.$alarmResults['ROWS'][$index]['SCADENZA'].'</td>' .
				  '</tr>';
	$index++;
	if ($zona<>$alarmResults['ROWS'][$index]['ZONA']) {
		if ($message instanceof Mail_mime) {
			$bodyMail .= '</table></body></html>';
		    //$message->setTXTBody($bodyMailTxt);
		    $message->setHTMLBody($bodyMail);
		    $body = $message->get();
			if (trim($toMail) > '') {
				print('Mail spedita a '.$toMail."\n");
				$mail = $smtp->send(trim($toMail), $headers, $body);
				//$mail = $smtp->send('ferdinando.rizzardo@beniculturali.it,giacomo.fonderico@gmail.com', $headers, $body);
			} else {
				var_dump($alarmResults['ROWS'][$index-1]);
			}
		}
	}
} while ($index < $alarmResults['NROWS']);

?>