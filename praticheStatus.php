<?php
include "login/autentication.php";
$time_start = microtime(true);

class myhtmlETable extends htmlETable {

    public function __construct($selectTableQuery, $IsStatement=TRUE,$queryParams=null){

        if (!$this->SetqueryArray($selectTableQuery, $IsStatement,$queryParams)) {
            return FALSE;
        }

        return TRUE;
    }


    protected $_recRetrived = 0;

		function GetColValue($column,$i){
			if (is_null($this->GetColumnHref($column,$i))) {
				if (($this->getColSubstring($column)>0) and (strlen($this->_tableData[$column]->GetValue($i))>$this->getColSubstring($column))){
					$content ='<span  id="'.$column.'_'.$i.'" >'.substr($this->_tableData[$column]->GetValue($i),0,$this->getColSubstring($column)).'</span>';
					$content.='<span dojoType="dijit.Tooltip" connectId="'.$column.'_'.$i.'" style="display:none;"><div style="max-width:250px; display:block;">'.$this->_tableData[$column]->GetValue($i).'</div></span>';
					return $content;
				} else {
					if($column=='Vincoli'){
						return utf8_encode($this->_tableData[$column]->GetValue($i));
					}
			    	return $this->_tableData[$column]->GetValue($i);
				}

			} else {
				$value=$this->GetColumnHref($column,$i);
				$pattern='|<([a-zA-Z]{1,3}).*>|';
			    preg_match_all($pattern, $value, $match);
				$closeTag='</'.$match[1][0].'>';
				return $value.$this->_tableData[$column]->GetValue($i).$closeTag;

			}
		}




		function SetRowClass($index){
			if (preg_match('/' . $this->GetColValue('TIPOLOGIA',$index) .'/', 'UE')) {
				$this->_RowClass=$this->GetColValue('rowclass',$index);
			} else {
				$this->_RowClass='praExit';
			}

		}

		function SetqueryArray($value,$IsStatement=TRUE,$params=null){

			if (($IsStatement) and ($value > '')) {
				$db = Db_Pdo::getInstance();
				$result = $db->query($value,$params);
				$this->_TableRows = $db->query('select FOUND_ROWS() as numRows')->fetchColumn();
				while($riga = $result->fetch()){
					$this->_recRetrived++;
					if (is_null($this->_tableData)) {
						$i=0;
						foreach ($riga as $key => $value) {
							$this->_tableData[$key] = new htmlECol($key);
							$this->_tableData[$key]->SetValue($value);
							$ftype = $result->getColumnMeta($i);
							//						   $ftype=mysql_field_type($result, $i);
							$i++;
						}
					} elseif (is_array($riga)){
						foreach ($riga as $key => $value) {
							$this->_tableData[$key]->SetValue($value);
						}
					}
				} // while

				if ($this->_TableRows==0) {
					return FALSE;
				}
			} else {
				r($value);
			}
		}


	function show($wk_page=1){
		if ($this->_TableRows==0) {
			return FALSE;
		}
		$this->tableInit();
//		$this->SetPageRows(20);
		print($this->GetTableHeader());
		print($this->GetTableFilter());
		// Mostro la riga dei filtri se inizializzata
		if ($this->GetPageDivision()) {
			// TODO
			$pages_counter = new pages($this->_TableRows);
			$pages_counter->SetactualPage($wk_page);
			$pages_counter->SetmaxLines($this->GetPageRows());
			$start = ($pages_counter->GetmaxLines()*($wk_page-1));
			$limit = $start+$pages_counter->GetmaxLines()>$this->_TableRows?$this->_TableRows:$start+$pages_counter->GetmaxLines();

		} else {
			$start=0;
			$limit=$this->GetPageRows();
		}
		$start=0;
		$limit=$this->GetPageRows();
		$sumArray=array();
		for($i = $start; $i < $this->_recRetrived; $i++){
			$this->SetRowClass($i);
			$row ='';
			foreach ($this->_tableData as $key=>$value){
				switch ($key) {
					case 'TIPOLOGIA':
						$row .= 	$this->GetColValue($key,$i) == 'U' ?
								'<td style="background-color:#00aa00; color: white; text-align: center">U</td>' :
								'<td style="background-color:#00ccff; color: white; text-align: center">E</td>' ;

						break;
					case 'PRATICA_ID':
                        $row = '<td>';
                        if($this->GetColValue('USCITA',$i) > '0000-00-00' and !empty($this->GetColValue('numeroregistrazione',$i))){
                            $row .= '<img src="graphics/vcard.png" style="cursor: pointer" onclick="editPratica(' . $this->GetColValue('PRATICA_ID',$i) . ')" title="Visualizza pratica" >';
                        } else {
                            $row .= '<img src="graphics/application_edit.png" style="cursor: pointer" onclick="editPratica(' . $this->GetColValue('PRATICA_ID',$i) . ')" title="Gestione della Pratica" >';
                        }
                        $row .= '</td>';
					    break;
					case 'pec' :

                            $mails = Db_Pdo::getInstance()->query('SELECT * FROM arc_pratiche_pec WHERE PRATICA_ID = :pratica_id ORDER BY PEC_ID',[
                                ':pratica_id' => $this->GetColValue('PRATICA_ID',$i),
                            ])->fetchAll();

                            $uploads = Db_Pdo::getInstance()->query('SELECT * FROM uploads WHERE PRATICA_ID = :pratica_id ORDER BY UPLOAD_ID',[
                                ':pratica_id' => $this->GetColValue('PRATICA_ID',$i),
                            ])->fetchAll();

                            $rowContent = '';
                            $pecClass = null;
                            if(sizeof($mails) or sizeof($uploads)){

                                $rowContent .= '<span dojoType="dijit.Tooltip" connectId="pec_'.$i.'" style="display:none;">';
                                $rowContent .= '<div style="display:block;">';

                                foreach ($mails as $mailRow) {
                                    if( $mailRow['STATUS'] == 'S'
                                            && !($this->GetColValue('PRATICA_ID',$i) > '')){
                                        $pecClass = 'mancataConsegna';
                                    }
                                    $rowContent .= '<img src="graphics/mail_16.png" style="margin-right: 5px;">'.stripslashes($mailRow['MITTENTE']).'<br />'."\n";
                                }
                                foreach ($uploads as $uploadsRow) {
                                    $rowContent .= '<img src="graphics/data.gif" style="margin-right: 5px;">'.$uploadsRow['DESCRIPTION'].'<br />'."\n";
                                }

                                $row .= '<td align="center" ' . (is_null($pecClass) ? '' : 'class="mancataConsegna"' ) . '>
                                                <span  id="pec_'.$i.'" ><img style="cursor: pointer;" src="graphics/16_attachment.png" >
                                                </span>';

                                $row .= $rowContent;
								$row .= '</div>';
								$row .= '</span></td>';
							} else {
                                $row .= "\t".'<td align="center" ></td>'."\n";
                            }
						break;
					case 'UO':
					    $uorg = Db_Pdo::getInstance()->query('SELECT
                                group_concat(ao.description SEPARATOR \', \') as UO
                                 FROM pratiche pr
                                left join arc_pratiche_uo apu on (apu.pratica_id = pr.pratica_id)
                                left join arc_organizzazione ao on (ao.uoid = apu.uoid)
                                where pr.pratica_id = :pratica_id',
				                    array(
				                    ':pratica_id' => $this->GetColValue('PRATICA_ID',$i)
				                    )
					           )->fetchColumn();
						$row .= "\t".'<TD id="uo'.$this->GetColValue('PRATICA_ID',$i).'" align="center" '.$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >' .
								'<span id="UO_'.$this->GetColValue('PRATICA_ID',$i).'"><img src="graphics/group.png" style="cursor: pointer" ></span>' .
								'<span dojoType="dijit.Tooltip" id="ttUO_'.$this->GetColValue('PRATICA_ID',$i).'" connectId="UO_'.$this->GetColValue('PRATICA_ID',$i).'" style="display:none;">' .
								'<div dojoType="dijit.layout.ContentPane" class="djToolTipContainer" style="overflow: hidden;" ><div style="height:5em;float:left;"><LABEL>Ufficio/Zona</LABEL></div><div>' .
								$uorg .
								'</div></div>' .
								'</span>'.
								'</TD>'."\n";
								break;
					case 'USCITA':
						if ($this->GetColValue($key,$i) > '' ) {
							$row .= "\t".'<td align="center" >
							    <span onclick="riapriPratica('.$this->GetColValue('PRATICA_ID',$i).')">
							        <img style="cursor: pointer;" src="graphics/unlock.png" title="Riapri la pratica">
							    </span>
							    </td>'."\n";
						} else {
							$row .= "\t".'<TD id="tp'.$this->GetColValue('PRATICA_ID',$i).'" align="center" '.$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >' .
							'<img src="graphics/quick_edit.png" style="cursor: pointer" title="Modifica veloce della pratica" onclick="impostaTipo(\''.$this->GetColValue('PRATICA_ID',$i).'\')" >'.
							'</TD>'."\n";
						}
						break;
					case 'Avanzamento':
						if ($this->GetColValue($key,$i)> '' ) {
							switch ($this->GetColValue('rowclass',$i)) {
								case 'praAlert':
									$bgColor='yellow';
									$maxVal = $this->GetColValue($key,$i);
									$advVal = $this->GetColValue('ggadv',$i);

									break;
								case 'praAllarm':
									$bgColor='red';
									$advVal = $this->GetColValue($key,$i);
									$maxVal = $this->GetColValue('ggadv',$i);
									break;
								case 'praClosed':
									$maxVal = $this->GetColValue($key,$i);
									$advVal = $this->GetColValue('ggadv',$i);
									$bgColor='white';
									break;
								default:
									$bgColor='lime';
									$maxVal = $this->GetColValue($key,$i);
									$advVal = $this->GetColValue('ggadv',$i);
									break;
							}
							$row .= "\t".'<TD align="center" '.$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >'.
								'<div style="width:100px; background: '.$bgColor.' none repeat;" annotate="true"
						  		maximum="'.$maxVal.'" id="Bar'.$this->GetColValue('PRATICA_ID',$i).'" ' .
						  		'progress="'.$advVal.'" ' .
						  		'label="Prova giorni" ' .
						  		'dojoType="dijit.ProgressBar">' .
						  		'<script type="dojo/method" event="report">
								    return dojo.string.substitute("gg '.$this->GetColValue('ggadv',$i).' di '.$this->GetColValue($key,$i).'", [this.progress, this.maximum]);
								  </script>' .
						  		'</div>'.
							'</TD>'."\n";
						} else {
							$row .= "\t".'<TD '.$value->GetColAlign().$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >'.
							'</TD>'."\n";
						}
						break;
					default:
						if ($value->IsShowed()) {
							if ($value->GetColRowClass()>'') {
							    $this->_RowClass=$value->GetColRowClass();
							}
							if(($value->GetColumnType()=='number' or $value->GetColumnType()=='currency') and $value->getColTotal() ){
								$sumArray[$key] += $value->_Value[$i];
							} else {
								$sumArray[$key] = null;
							}
							$row .= "\t".'<TD '.$value->GetColAlign().$this->GetColumnClass($key,$i).$value->GetColWrap().$value->GetColAttribute().' >'.$this->GetColValue($key,$i).'</TD>'."\n";
						}
						break;
				}

			}
			$row='<TR class="'.$this->GetRowClass().'" id="'.$this->GetColValue('PRATICA_ID',$i).'" >'."\n".$row;
			$row.='</TR>'."\n";
			print($row);
		} // for
		if ($this->printTotal()){
			$sumRow = '<tr class="djSumRow">';
			foreach ($sumArray as $value) {
				$value=$value>0?number_format($value,$this->_decimalsTotal,',','.'):null;
				$sumRow .= '<td align="right" > '.$value.'</td>';
			}
			$sumRow .= '</tr>';
			print($sumRow);
		}
		$this->tableEnd();
		if ($this->GetPageDivision()) {
	        print('<table align="center" width="'.$this->GetWidth().'" border="0" cellspacing="0" cellpadding="0" >');
	        print('<tr height="100%" >');
	        print('    <td width="100%" valign="bottom" align="center">');

			$pages_counter->ShowPages();

	        print('    </td>');
	        print('</tr>');
			print('</table>');
		}

	}
}
include('pageheader.inc');

print('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select * from arc_esiti order by description" ' .
		'jsId="getEsiti" ' .
						'/>');
print('<div dojoType="dojo.data.ItemFileReadStore" ' .
		'url="xml/jsonSql.php?sql=select MODELLO, concat(classificazione, \' - \' , DESCRIPTION) as DESCRIPTION from arc_modelli order by description" ' .
		'jsId="getModelli" ' .
						'/>');



print('<div id="dlgTipoPratica" dojoType="dijit.Dialog" title="Imposta tipo pratica" ' .
		'href="getDialog.php" ' .
		'>');
print('</div>');
include('barraRicerca.inc');

$wkPage=isSet($_GET['wk_page'])?$_GET['wk_page']:1;
$serviceQuery='select SQL_CALC_FOUND_ROWS distinct pr.PRATICA_ID,
                        group_concat(arc_sospensioni.sospensione_id) as sospensioni,
						pr.TIPOLOGIA, ' .
						'(case ' .
						'	when av.vincolo_id is null and (pr.uscita is null or (pr.uscita = STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\')))  then ' .
						'	concat(\'<span id="vinc\',pr.PRATICA_ID,\'"><img src="graphics/error.png" style="cursor: pointer" onclick="viewVincoli(\',pr.PRATICA_ID,\')" ></span>' .
									'<span dojoType="dijit.Tooltip" id="ttVinc\',pr.PRATICA_ID,\'" connectId="vinc\',pr.PRATICA_ID,\'" style="display:none;"><div style="max-width:250px; display:block;">Attenzione.<br>Verificare situazione vincolistica</div></span>\') ' .
						'	when av.vincolo_id is not null then ' .
						'	concat(\'<span id="vinc\',pr.PRATICA_ID,\'"><img src="graphics/link.png" style="cursor: pointer" onclick="viewVincoli(\',pr.PRATICA_ID,\')"></span>' .
									'<span dojoType="dijit.Tooltip" id="ttVinc\',pr.PRATICA_ID,\'" connectId="vinc\',pr.PRATICA_ID,\'" style="display:none;">' .
									'<div class="djToolTipContainer" >' .
									'<fieldset ><legend style="border: none; background-color: white; ">Vincolo</legend>' .
									'<LABEL>Oggetto</LABEL>\',av.denominazione,\'<BR/>' .
									'<LABEL>Indirizzo</LABEL>\',av.ubicazioneinit,\' \',av.ubicazioneprinc,\'<BR/>' .
									'<LABEL>Localit&agrave;</LABEL>\',av.localita,\'<BR/>' .
									'<LABEL>Comune/Prov.</LABEL>\',av.comune,\' \',av.provincia,\'<BR/>' .
									'<LABEL>Foglio</LABEL>\',av.fogliocatastale,\'<BR/>' .
									'<LABEL>Particelle</LABEL>\',av.particelle,\'<BR/>' .
									'<LABEL>Provv.Min.</LABEL>\',av.provvedimentoministeriale,\'<BR/>' .
									'<LABEL>Trascr.Cons.</LABEL>\',av.trascrizioneinconservatoria,\'<BR/>' .
									'<LABEL>Pos. monumentale</LABEL>\',av.posizionemonumentale,\'<BR/>' .
									'<LABEL>Pos. vincoli</LABEL>\',av.posizionevincoli,\'<BR/>' .
									'</fieldset>' .
									'</div></span>\'' .
									') ' .
						'end ) as "Vincoli", ' .
						'pr.USCITA, ' .
						'pr.MAIL_SENT_ID, ' .
						' "pec" as pec, ' .
						' group_concat(ao.description SEPARATOR \', \') as UO, ' .
						'(case ' .
						'	when (pr.modello is null and tipologia = "E" ) then \'praOpen\' ' .
						'	when (pr.dataarrivo is null and tipologia = "E" )then \'praOpen\' ' .
						'	when (pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\')) ) then \'praClosed\' ' .
						'	when length(group_concat(arc_sospensioni.sospensione_id)) > 0  then \'praSuspended\' ' .
						'	when (date_add(now(), INTERVAL am.allarme DAY) >= pr.scadenza and now() < pr.scadenza) then \'praAlert\' ' .
						'	when (now() > pr.scadenza) then \'praAllarm\' ' .
						'else \'praActive\' ' .
						'end) as rowclass , ' .
						'datediff(pr.scadenza , pr.dataarrivo) as Avanzamento,' .
						'if((pr.uscita is not null and (pr.uscita > STR_TO_DATE(\'00-00-0000\', \'%m-%d-%Y\'))),' .
						'									datediff(pr.uscita , pr.dataarrivo), ' .
//						'									datediff(now() , pr.uscita), ' .
						'									datediff(now() , pr.dataarrivo)) as ggadv , ' .
						'if(pr.project_id is null,pr.numeroregistrazione,
												concat(\'<a class="Lblue" href="'.$_SERVER['PHP_SELF'].'?filter=progetto&progettoFilter=\',pr.project_id,\'" title="Filtra il workspace per progetto">\',pr.numeroregistrazione,\'</a>\'))
											as numeroregistrazione, ' .
						'date_format(pr.dataregistrazione,\'%d-%m-%Y\') as "Data Reg.", ' .
						'am.description as \'Tipo Pratica\', ' .
						'pr.oggetto as "Oggetto", ' .
						'pr.pnome as "Proprietario", ' .
						'pr.cognome cognome, ' .
						'date_format(pr.dataarrivo,\'%d-%m-%Y\') as "Arrivo", ' .
						' date_format(pr.scadenza,\'%d-%m-%Y\') as Scadenza, ' .
						'am.allarme ' .
				'from pratiche pr ' .
// 				'left join arc_zone az on (az.zona = pr.zona) ' .
// 				'left join arc_uffici au on (au.ufficio = pr.ufficio) ' .
				'left join arc_sospensioni on (arc_sospensioni.pratica_id = pr.pratica_id and arc_sospensioni.fine is null) ' .
				'left join arc_modelli am on (am.modello = pr.modello) ' .
				'left join vincoli av on (av.vincolo_id = pr.vincolo_id) ' .
				'left join arc_esiti ae on (ae.esito_id = pr.esito_id) ' .
				'left join arc_pratiche_uo apu on (apu.pratica_id = pr.pratica_id) ' .
				'left join arc_organizzazione ao on (ao.uoid = apu.uoid) ' .
				'left join user_uo_ref uor on ((uor.user_id = '.$_SESSION['sess_uid'].' ) and (uor.uoid = apu.uoid)) ' .
				'where annullato = "NO" ' .
				$whereClause .
				' group by pr.pratica_id, Vincoli '.
				$orderBy . ' LIMIT ' . ($wkPage -1) * 20 . ', 20 ' ;


if(isset($_GET['DEBUG']) and $_GET['DEBUG'] == 'Y'){
	r($serviceQuery);
}


//print('<div style="margin:20px;">');
//var_dump($serviceQuery);
$serviceTable=new myhtmlETable($serviceQuery);
if ($serviceTable->getTableRows()>0) {
 	$serviceTable->HideCol('sospensioni');
	$serviceTable->HideCol('ggadv');
	$serviceTable->HideCol('rowclass');
	$serviceTable->HideCol('zonaCod');
	$serviceTable->HideCol('zonaDes');
	$serviceTable->HideCol('Ufficio');
	$serviceTable->HideCol('allarme');
	$serviceTable->HideCol('MAIL_SENT_ID');
//	$serviceTable->HideCol('Alla firma');
//	$serviceTable->HideCol('Al funz.');
//	$serviceTable->HideCol('USCITA');

//	$serviceTable->HideCol('Modello');
	$serviceTable->SetPageDivision(TRUE);
	$serviceTable->SetColumnHeader('numeroregistrazione','Prot.Nr.');
	$serviceTable->SetColumnHeader('dataReg','Registrata');
	$serviceTable->SetColumnHeader('pec','<img src="graphics/attach.png" width="16" >');
	$serviceTable->SetColumnHeader('USCITA','<img src="graphics/page_lightning.png" >');
	$serviceTable->SetColumnHeader('PRATICA_ID','<img src="graphics/page_edit.png" >');
	$serviceTable->SetColumnHeader('Vincoli','<img src="graphics/page_link.png" >');
	$serviceTable->SetColumnHeader('dataArr','Arrivata');
	$serviceTable->SetColumnHeader('dataSca','Scadenza');
	$serviceTable->SetColumnHeader('TIPOLOGIA','T');
    $serviceTable->SetColumnHeader('FALDONE','F');
	$serviceTable->SetColumnHeader('cognome','Mittente');
	$serviceTable->SetColumnAttribute('numeroregistrazione',' style="text-align: center;" ');
	$serviceTable->SetColumnAttribute('PRATICA_ID',' style="text-align: center;" ');
	$serviceTable->SetColumnAttribute('Vincoli',' style="text-align: center;" ');

//	$serviceTable->SetColumnAttribute('Vincoli',' style="text-align: center;" ');

	$serviceTable->setColSubstring('Modello',15);
	$serviceTable->setColSubstring('Oggetto',20);
	$serviceTable->setColSubstring('Tipo Pratica',20);
	$serviceTable->setColSubstring('Proprietario',15);
	$serviceTable->setColSubstring('cognome',15);

	//$serviceTable->SetColumnHref('SER_CODE','<a href="serviceManageRequest.php?STATUS=50&SER_ID=#SER_ID#" title="Attiva Intervento">');
	$serviceTable->show($wkPage);

} else {
	print('<h3>Non ci sono Pratiche</h3>');
}
	print('<div class="praLegend" style=" padding-right:20px; padding:5px; margin-right:20px;">' .
		  	'Legenda: ' .
		  		'<span class="praOpen" style="padding-left:5px; padding-right:5px;" >Da classificare</span>' .
		  		'<span class="praAlert" style="background-color: yellow; padding-left:5px; padding-right:5px;" >In scadenza</span>' .
		  		'<span class="praAllarm" style="background-color: red; padding-left:5px; padding-right:5px;" >Scadute</span>' .
		  		'<span class="praSuspended" style="padding-left:5px; padding-right:5px;" >Sospese</span>' .
		  		'<span class="praClosed" style="padding-left:5px; padding-right:5px;" >Chiuse</span>' .
		  		'<span class="praExit" style="padding-left:5px; padding-right:5px;" >In Uscita</span>' .
	'</div>');

// print('</div>');

$time_end = microtime(true);
$time = $time_end - $time_start;

// var_dump($time);

include('pagefooter.inc');
?>
