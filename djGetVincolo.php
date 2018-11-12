<?php
/*
 * Created on 29/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
include 'dbfunctions.php';
switch ($_GET['type']) {
	case 'pro':
			$fields = 'pro.nome as Proprietario, ' .
			'pro.particella as Particelle, ' .
			'pro.foglio as Fogli, ' .
			'pro.sub as Subalterni, ' .
			'pro.piani as Piani, ' .
			'date_format(pro.proprietario_dal,"%d-%m%Y") as "Prop. dal",' .
			'date_format(pro.proprietario_al,"%d-%m%Y") as "Prop. al" ' ;
			$label = 'Proprietari';
		break;
	case 'amb':
			$fields= 'loc.nome as "Localit&agrave;", ' .
			'loc.gruppo_localita as Zona , ' .
			'ambloc.codice as Codice, ' .
			'ambloc.oggetto as Oggetto, ' .
			'ambloc.decreto as Decreto, ' .
			'ambloc.data_decreto as "Data Dec.", ' .
			'com.nome as comune, ' .

			'ambcom.codice as va_com_codice, ' .
			'ambcom.oggetto as va_com_oggetto, ' .
			'ambcom.decreto as va_com_decreto, ' .
			'ambcom.data_decreto as va_com_data_decreto ' ;
			$label = 'Vincoli Ambientali';

		break;
	case 'lex':
			$fields = 'lex.legge as Legge,' .
			'date_format(decr.data,"%d-%m-%Y") as "Data Decreto" , ' .
			'decr.description as Decreto,' .
			'decr.fogli as Fogli,' .
			'decr.particelle as Particelle ';
			$label = 'Leggi';

		break;

	default:
		break;
}
$sql = 'select distinct vin.vm_id , ' . $fields .
	'From  vin_monumentali as vin ' .
		'left join arc_comuni as com on (com.id = vin.comune ) ' .
		'left join vin_proprietari as pro on (pro.vm_id = vin.vm_id) ' .
		'left join vin_decreti as decr on (decr.vm_id = vin.vm_id) ' .
		'left join vin_leggi as lex on  (lex.legge_id = vin.legge_id ) ' .
		'left join vin_fogli as fog on (fog.vm_id = vin.vm_id) ' .
		'left join vin_particelle as par on (par.foglio_id = fog.foglio_id) ' .
		'where vin.vm_id='.$_GET['vincolo_id'];
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
						'Non ci sono '.$label.
						'</div>');
		}
?>