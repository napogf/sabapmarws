<?php
/*
 * Created on 03/ago/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function dirList ($directory,$search)
{
	$pattern = '/^'.$search.'/';
    // create an array to hold directory list
    $results = array();

    // create a handler for the directory
    $handler = opendir($directory);

    // keep going until all files in directory have been read
    while ($file = readdir($handler)) {

        // if $file isn't this directory or its parent,
        // add it to the results array

        if ($file != '.' && $file != '..' && preg_match($pattern,$file) )
            $results[] = $file;
    }

    // tidy up: close the handler
    closedir($handler);

    // done!
    return $results;

}




include "login/autentication.php";
//require_once('dbfunctions.php');
//require_once 'fdataentry.php';
//require_once('Etable_c.inc');

	$isAdmin=dbselect('select * from user_zone_ref where zona=1 and user_id='.$_SESSION['sess_uid']);
	if (isSet($_POST['print'])){
		include('reportPaesaggistichePdf.inc');
				$whereClause=' where (ae.tipo = \'Y\')  ' ;
				if($_POST['ZONA']>''){
					$whereClause .= 'and (pr.zona = '.$_POST['ZONA'].') ';
				} else {
					if (!$isAdmin){
						$whereClause .= 'and (pr.zona in  (select uzr.zona from user_zone_ref uzr where uzr.user_id ='.$_SESSION['sess_uid'].')  or  ' .
											'  pr.ufficio in  (select ufr.ufficio from user_uffici_ref ufr where ufr.user_id ='.$_SESSION['sess_uid'].') ) ';
					} else {
						$whereClause .= 'and ((pr.zona is not null) or (pr.ufficio is not null))';
					}
				}
				$reportPaesaggisticheQuery ='select ' .
						'if(pr.COMUNE_OG > \'\',upper(pr.COMUNE_OG),upper(pr.COMUNE)) as COMUNE, ' .
						'pr.NUMERORIFERIMENTO,' .
						'date_format(pr.DATAARRIVO,\'%d-%m-%Y\') as DATAARRIVO, ' .
						'date_format(pr.DATADOCUMENTO,\'%d-%m-%Y\') as DATADOCUMENTO, ' .
						'date_format(pr.DATAREGISTRAZIONE,\'%d-%m-%Y\') as DATAREGISTRAZIONE, ' .
						'pr.NUMEROREGISTRAZIONE, ' .
						'pr.OGGETTO as OGGETTO_ESPI, ' .
						'ae.esito, ae.description, ' .
						'IF(ae.esito=\'Y\',\'Positivo\',if(ae.esito=\'S\',\'Sospensione\',\'Negativo\')) as ESITO ' .
						'' .
						'from pratiche pr ' .
						'left join arc_zone az on (az.zona = pr.zona) ' .
						'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
						'left join arc_modelli am on (am.modello = pr.modello) ' .
						'left join arc_vincoli_pratiche avp on (avp.pratica_id = pr.pratica_id)' .
						'left join arc_vincoli av on (av.vincolo_id = avp.vincolo_id) ' .
						'right join arc_esiti ae on (ae.esito_id = pr.esito_id) ' .
		//				'left join v_sospensioni vs on (vs.pratica_id = pr.pratica_id) ' .
						$whereClause;
			if($_POST['tipoReport']=='USCITA'){
				$reportPaesaggisticheQuery .= ' and pr.uscita between \''.$_POST['FROM_DATE'].'\' and \''.$_POST['TO_DATE'].'\' ';
			} else {
				$reportPaesaggisticheQuery .= ' and pr.dataarrivo between \''.$_POST['FROM_DATE'].'\' and \''.$_POST['TO_DATE'].'\' ';
			}

				$reportPaesaggisticheQuery .= ' order by 1, pr.numeroregistrazione ';
		// registro la stampa e stampo il registro
			$reportPaesagResults = dbselect($reportPaesaggisticheQuery);


			$comune=null;
			$data=array();
	    	if (is_array($reportPaesagResults)) {
				for ($index = 0; $index < $reportPaesagResults['NROWS']; $index++) {
					if($comune==$reportPaesagResults['ROWS'][$index]['COMUNE']){
						$data[]=$reportPaesagResults['ROWS'][$index];
					} else {
						if(!is_null($comune)){
							$pdf=new PDF();
							$pdf->AliasNbPages();
							//Column titles
							$pdf->SetTitle('Report Pratiche paesaggistiche');
							$pdf->SetFont('Arial','',14);
							$pdf->SetAuthor('SBAP-VR');
							// $pdf->Cover();
				    		$pdf->SetReportTitle(array(0 => 'Pratiche paesaggistiche - '.$comune, 1 => 'Periodo dal '.$_POST['FROM_DATE'].' al '.$_POST['TO_DATE']));
				    		$pdf->AddPage();
				    		$pdf->printBody($data);
							$pdf->output(getcwd().'/tmp/ReportPaesaggistiche_'.$comune.'.pdf','F');
							$data=array();
						}
						$data[]=$reportPaesagResults['ROWS'][$index];
						$comune=$reportPaesagResults['ROWS'][$index]['COMUNE'];
					}
				}
				$pdf=new PDF();
				$pdf->AliasNbPages();
				//Column titles
				$pdf->SetTitle('Report Pratiche paesaggistiche');
				$pdf->SetFont('Arial','',14);
				$pdf->SetAuthor('SBAP-VR');
				// $pdf->Cover();
	    		$pdf->SetReportTitle(array(0 => 'Pratiche paesaggistiche - '.$comune, 1 => 'Pratiche chiuse dal '.$_POST['FROM_DATE'].' al '.$_POST['TO_DATE']));
	    		$pdf->AddPage();
	    		$pdf->printBody($data);
				$pdf->output(getcwd().'/tmp/ReportPaesaggistiche_'.$comune.'.pdf','F');
				$arrayFile=dirList(getcwd().'/tmp','ReportPaesaggistiche_');
				$zip = new ZipArchive();
				$filename = getcwd().'/tmp/ReportPaesaggistiche_'.$_POST['PRINT_DATE'].'.zip';


				if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
				    exit("Non Posso Creare il file <$filename>\n");
				}

				for ($index = 0; $index < sizeof($arrayFile); $index++) {
					$zip->addFile(getcwd().'/tmp/'.$arrayFile[$index],$arrayFile[$index]);
//					unlink(getcwd().'/tmp/'.$arrayFile[$index]);
				}
				$zip->close();
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Content-type: application/zip;\n"); //or yours?
				header("Content-Transfer-Encoding: binary");
				$len = filesize($filename);
				header("Content-Length: $len;\n");
				$outname='ReportPaesaggistiche_'.$_POST['PRINT_DATE'].'.zip';
				header("Content-Disposition: attachment; filename=\"$outname\";\n\n");
				readfile($filename);
				unlink($filename);
				for ($index = 0; $index < sizeof($arrayFile); $index++) {
					unlink(getcwd().'/tmp/'.$arrayFile[$index]);
				}
			} else {
				$messageNorecords='Non ci sono pratiche nella selezione!';
			}
	}

 include('pageheader.inc');
// Form
	print('<div class="dbFormContainer" id="antreport" >'."\n");

		if ($messageNorecords>'') print('<div class="DbFormMessage">'.$messageNorecords.'</div>');

		print('<fieldset><legend>Satmpa Pratiche Paesaggistiche</legend><br>'."\n");
		print('<form action="reportPaesaggistiche.php" method="post" name="PrinForm" id="printForm">');


		print('<div id="dataselezione" >');
			print('<label for="PRINT_DATE">Data di Stampa</label>');
			print('<div dojoType="dijit.form.DateTextBox"  name="PRINT_DATE" value="'.date('Y-m-d').'" ></div>');
			print('<br>');
			print('<label for="PRINT_DATE">Data di Selezione</label>');
			print('<input  type="radio" name="tipoReport" id="tr1" value="ARRIVO" /> Arrivo');
			print('&nbsp;&nbsp;&nbsp;<input  type="radio" name="tipoReport" id="tr2" value="USCITA" checked="checked" /> Uscita');
			print('<br>');
			print('<label for="FROM_DATE">Dalla data</label>');
			print('<div dojoType="dijit.form.DateTextBox" name="FROM_DATE" value="'.date('Y-m-d').'" ></div>');
			print('<br>');
			print('<label for="TO_DATE">Alla data</label>');
			print('<div dojoType="dijit.form.DateTextBox"  name="TO_DATE" value="'.date('Y-m-d').'" ></div>');
			print('<br>');
			if($isAdmin){
				print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
				'url="xml/jsonSql.php?sql=select * from arc_zone where tipo = \'Z\' " ' .
				'jsId="zoneSel" ' .
				'/>');
			} else {
				print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
				'url="xml/jsonSql.php?sql=select * from arc_zone where tipo = \'Z\' and zona in (select zona from user_zone_ref where user_id = '.$_SESSION['sess_uid'].')" ' .
				'jsId="zoneSel" ' .
				'/>');
			}

			print ('<label for="ZONA">Zona</label>');
			print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_ZONE"
									store="zoneSel"
									labelAttr="DESCRIPTION"
									searchAttr="DESCRIPTION"
									name="ZONA" ' .
			'value="' . $_POST['ZONA'] . '" ' .
			'></div>');



		print('</div>');

		print('<div id="stampaReport" >');

		print('<input style="margin: 10px;" type="submit" value="Stampa Registro" name="print"/>');
		print('</div>');

		print('</form>'."\n");
		print('</fieldset>'."\n");
	print('</div>'."\n");



 include('pagefooter.inc');
?>