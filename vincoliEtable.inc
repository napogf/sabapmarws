<?php

class paesaggioHtmlETable extends vincoliHtmlETable {

		function vincoliSelezionati(){
			if($vincoliResult=dbselect('select vincolo_id from arc_vincoli_pratiche where tipo = \'P\' and pratica_id ='.$_GET['PRATICA_ID'])){
				foreach ($vincoliResult['ROWS'] as $key => $value){
					$this->_vincoliChecked[]=$value['vincolo_id'];
				}
			}

		}


}
class monumentaliHtmlETable extends vincoliHtmlETable {

		function vincoliSelezionati(){
			if($vincoliResult=dbselect('select vincolo_id from arc_vincoli_pratiche where tipo = \'M\' and pratica_id ='.$_GET['PRATICA_ID'])){
				foreach ($vincoliResult['ROWS'] as $key => $value){
					$this->_vincoliChecked[]=$value['vincolo_id'];
				}
			}
		}
}





class vincoliHtmlETable extends HtmlETable {
		private $_vincoliChecked = array();

		function vincoliSelezionati(){
			return true;
		}


		function SetqueryArray($value,$IsStatement=TRUE){
			if (($IsStatement) and ($value > '')) {
				$rowResult=dbselect($value);
				$this->vincoliSelezionati();
				if($rowResult['NROWS']==0) return true;
				foreach ($rowResult['ROWS'] as $riga ) {
					$this->_TableRows++;
					if (is_null($this->_tableData)) {
						$i=0;
						if (is_array($riga)){
							foreach ($riga as $key => $value) {
								switch ($key) {
									case 'va_id':
									case 'vm_id':
									case 'vincolo_id':
									case 'vincolo_id_del':
									case 'va_amb_del':
									   $this->_tableData[$key] = new HtmlECol('#');
									   $this->_tableData[$key]->SetColAlign('center');
									   $ftype='TEXT';
										break;
									case 'vincolo_lex':
									   $this->_tableData['vincolo_lex'] = new HtmlECol('Leggi');
									   $this->_tableData[$key]->SetColAlign('center');
									   $ftype='TEXT';
										break;
									case 'vincolo_pro':
									   $this->_tableData['vincolo_pro'] = new HtmlECol('Propietari');
									   $this->_tableData[$key]->SetColAlign('center');
									   $ftype='TEXT';
										break;
									case 'vincolo_ambcom':
									case 'vincolo_ambloc':
									   $this->_tableData['vincolo_amb'] = new HtmlECol('V.ambientali');
									   $ftype='TEXT';
										break;
									default:
									   $this->_tableData[$key] = new HtmlECol(ucfirst($key));
									   $ftype='TEXT';
									break;
								}
							   $i++;
							}
						}
					}
					if (is_array($riga)){
						foreach ($riga as $key => $value) {
							$toolVincoli='';
							switch ($key) {
								case 'va_id': // check box per vincoli Ambientali
									$checkedValue=in_array($riga[$key],$this->_vincoliChecked) ?' checked ':'';
									$value = '<input type="checkbox" name="VINCOLO_ID[]" value="'.$riga[$key].'" '.$checkedValue.' > ';
									$this->_tableData[$key]->SetValue($value);
									break;
								case 'vm_id': // check box per vincoli Ambientali
									$checkedValue=in_array($riga['gid'],$this->_vincoliChecked) ?' checked ':'';
									$value = '<input type="checkbox" name="VINCOLO_ID[]" value="'.$riga[$key].'" '.$checkedValue.' > ';
									$this->_tableData[$key]->SetValue($value);
									break;
								case 'vincolo_id_del': // check box per vincoli Monumentali
									$value = '<img src="graphics/webapp/deleted.gif" onClick="eliminaVincolo(\'M\','.$riga[$key].','.$_GET['PRATICA_ID'].');" '.$checkedValue.' title="Rimuovi il vincolo"> ';
									$this->_tableData[$key]->SetValue($value);
									break;
								case 'va_amb_del': // check box per vincoli Monumentali
									$value = '<img src="graphics/webapp/deleted.gif" onClick="eliminaVincolo(\'P\','.$riga[$key].','.$_GET['PRATICA_ID'].');" '.$checkedValue.' title="Rimuovi il vincolo"> ';
									$this->_tableData[$key]->SetValue($value);
									break;
								case 'vincolo_id': // edit vincolo Monumentale
									$editValue = '<img src="graphics/application_edit.png" style="cursor: pointer" onclick="location.href=\'editVincoloMonumentale.php?VM_ID='.$riga[$key].'\';" title="Modifica del Vicolo" > ' ;
									$this->_tableData['vincolo_id']->SetValue($editValue);
									break;
								case 'vincolo_amb_id': // edit vincolo Monumentale
									$editValue = '<img src="graphics/application_edit.png" style="cursor: pointer" onclick="location.href=\'editVincoloAmbientale.php?VA_ID='.$riga[$key].'\';" title="Modifica del Vicolo" > ' ;
									$this->_tableData['vincolo_amb_id']->SetValue($editValue);
									break;
								case 'vincolo_lex':

									$toolVincoli='<span id="lex_'.$riga['vincolo_id'].'"><img src="graphics/book_open.png" style="cursor: pointer" ></span>' .
									'<span dojoType="dijit.Tooltip" id="ttlex_'.$riga['vincolo_id'].'" connectId="lex_'.$riga['vincolo_id'].'" style="display:none;">' .
									'<div dojoType="dijit.layout.ContentPane" class="djToolTipContainer" href="djGetVincolo.php?type=lex&vincolo_id='.$riga['vincolo_id'].'" style="overflow: hidden;" >' .
									'</div>' .
									'</span>';

								   $this->_tableData['vincolo_lex']->SetValue($toolVincoli);
									break;
								case 'vincolo_pro':

									$toolVincoli='<span id="pro_'.$riga['vincolo_id'].'"><img src="graphics/group.png" style="cursor: pointer" ></span>' .
									'<span dojoType="dijit.Tooltip" id="ttpro_'.$riga['vincolo_id'].'" connectId="pro_'.$riga['vincolo_id'].'" style="display:none;">' .
									'<div dojoType="dijit.layout.ContentPane" class="djToolTipContainer" ' .
									'href="djGetVincolo.php?type=pro&vincolo_id='.$riga['vincolo_id'].'" ' .
									'style="overflow: hidden;" >' .
									'</div>' .
									'</span>';



								   $this->_tableData['vincolo_pro']->SetValue($toolVincoli);
									break;
								case 'vincolo_ambcom':
									break;
								case 'vincolo_ambloc':
									if ($riga['vincolo_ambloc']>'' or $riga['vincolo_ambcom']>''){

									$toolVincoli='<span id="vincolo_amb'.$riga['vincolo_id'].'"><img src="graphics/photo.png" style="cursor: pointer" ></span>' .
									'<span dojoType="dijit.Tooltip" id="ttVinc_amb'.$riga['vincolo_id'].'" connectId="vincolo_amb'.$riga['vincolo_id'].'" style="display:none;">' .
									'<div dojoType="dijit.layout.ContentPane" class="djToolTipContainer" ' .
									'href="djGetVincolo.php?type=amb&vincolo_id='.$riga['vincolo_id'].'" ' .
									'style="overflow: hidden;" >' .
									'</div>' .
									'</span>';


									} else {
										$toolVincoli = '';
									}
								   $this->_tableData['vincolo_amb']->SetValue($toolVincoli);
									break;
								default:
						   		$this->_tableData[$key]->SetValue($value);
								break;
							}

						}
					}
				} // foreach
				if ($this->_TableRows==0) {
					return FALSE;
				}
			} else {

				var_dump($value);
				exit;
			}
		}
}


?>