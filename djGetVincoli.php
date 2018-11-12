<?php
/*
 * Created on 29/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
include 'dbfunctions.php';
	$label = 'Monumentali';
	$sql = 'select distinct ' .
			'vin.vm_id , ' .
			'vin.oggetto as "Oggetto", ' .
			'vin.numeri as "Numeri", ' .
			'vin.collocazione as "Collocazione", ' .
			'vin.note as "Note", ' .
			'com.comune  as "Comune", ' .
			'vin.localita  as "Localita", ' .
			'group_concat(distinct fog.foglio  SEPARATOR \',\') as Fogli, ' .
			'group_concat(distinct if(par.lettera > \'\', concat(par.numero,\'/\',par.lettera), par.numero)  SEPARATOR \',\') as Particelle ' .
		'From vin_monumentali as vin ' .
					'left join arc_vincoli_pratiche av on ((av.vincolo_id = vin.vm_id) and (av.tipo = \'M\')) ' .
					'left join vin_leggi as lex on (lex.legge_id = vin.legge_id) ' .
					'left join vin_proprietari as pro on (pro.vm_id = vin.vm_id) ' .
					'left join vin_fogli as fog on (fog.vm_id = vin.vm_id) ' .
					'left join vin_particelle as par on (par.foglio_id = fog.foglio_id) ' .
					'left join arc_comuni as com on (com.id = vin.comune) ' .
					'left join arc_province as prv on (prv.id=vin.prov) or (prv.sigla = com.provincia) ' .
		'where av.pratica_id = ' . $_GET['PRATICA_ID'].
		' group by vin.vm_id ';
		$result=dbselect($sql);
		if($result['NROWS']>0){
			print('<div class="djToolTipContainer" >' .
					'<fieldset ><legend style="border: none; background-color: white; ">' .
					$label .
					'</legend>');
			foreach($result['ROWS'] as  $riga){
				foreach ($riga as $key => $value) {
					if($value>'' and $key <> 'vm_id') print('<LABEL>'.$key.'</LABEL>'.'<span>'.$value.'</span><br />');
				}
				print('<br /><hr />');
			}

			print('' .
			'</fieldset>' .
			'</div>' .
			'');
		} else {
				print('<div class="djToolTipContainer" >' .
						'Non ci sono Vincoli '. $label .
						'</div>');
		}
	$label = 'Paesaggistici';
	$sql = 'SELECT ' .
					'amb.va_id, ' .
					'amb.codice, ' .
					'amb.progressivo, ' .
					'prov.sigla as "Prov.",' .
					'com.comune as Comune, ' .
					'localita as \'Localit&agrave;\', ' .
					'lex.legge as Legge, ' .
					'amb.decreto as Decreto, ' .
					'amb.oggetto as Oggetto, ' .
					'date_format(amb.data_decreto,\'%d-%m-%Y\') as \'Data Decreto\' , ' .
					'fonte_pubblicazione as \'Fonte pub.\', ' .
					'numero_pubblicazione as \'Nr. pub.\', ' .
					'note as Note, ' .
					'mappa ' .
				'from vin_ambientali amb ' .
					'left join arc_vincoli_pratiche av on ((av.vincolo_id = amb.va_id) and (av.tipo = \'P\')) ' .
					'left join arc_comuni as com on (com.id = amb.comune) ' .
					'left join arc_province as prov on (prov.sigla = com.provincia) ' .
					'left join vin_leggi as lex on (lex.legge_id = amb.legge_id) ' .
					'where av.pratica_id='. $_GET['PRATICA_ID'].
					' group by amb.va_id';

		$result=dbselect($sql);
		if($result['NROWS']>0){


			print('<div class="djToolTipContainer" >' .
					'<fieldset ><legend style="border: none; background-color: white; ">' .
					$label .
					'</legend>');
			foreach($result['ROWS'] as  $riga){
				foreach ($riga as $key => $value) {
					if($value>'' and $key <> 'va_id') print('<LABEL>'.$key.'</LABEL>'.'<span>'.$value.'</span><br />');
				}
				print('<br /><hr />');
			}

			print('' .
			'</fieldset>' .
			'</div>' .
			'');
		} else {
				print('<div class="djToolTipContainer" >' .
						'Non ci sono Vincoli '. $label .
						'</div>');
		}
?>