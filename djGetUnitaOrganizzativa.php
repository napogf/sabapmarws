<?php
/*
 * Created on 29/giu/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";

$label = 'Unità Organizzativa';
	$sql = 'select  
			ao.description as "Ufficio/Zona",
			ao.tipo as Tipo ' .
		'From arc_pratiche_uo apu ' .
					'left join arc_organizzazione ao on (ao.uoid = apu.uoid)  ' .
		'where apu.pratica_id = ' . $_GET['praticaId'];

	$result=dbselect($sql);
		if($result['NROWS']>0){
			print('<div class="djToolTipContainer" >' .
					'<fieldset ><legend style="border: none; background-color: white; ">' .
					$label .
					'</legend>');
			foreach($result['ROWS'] as  $riga){
				foreach ($riga as $key => $value) {
					print('<LABEL>'.$key.'</LABEL>'.'<span>'.$value.'</span><br />');
				}
				print('<br /><hr />');
			}

			print('' .
			'</fieldset>' .
			'</div>' .
			'');
		} else {
				print('<div class="djToolTipContainer" >' .
						'La pratica non è associata a nessuna '. $label .
						'</div>');
		}
?>