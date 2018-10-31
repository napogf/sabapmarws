<?php

/**
 * 
 *
 * @version $Id: activityclosed.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003 
 **/


								
// print('<hr>');								
print('<table width="100%" cellspacing="2" cellpadding="5">');

if (!$result=dbselect($work_status_query)) {
    print('<tr><td align="center" width="100%">'.get_label('nosteps').'</td></tr>');
} else {

	$originFilter=$result['ROWS'][0]['ORIGIN_ID']>''?'?ORIGIN_ID='.$result['ROWS'][0]['ORIGIN_ID']:'';
	if ($result['ROWS'][0]['ORIGIN_ID']>'') {
	// Stampo il body del processo di origine 
		if (!$originWork=dbselect('select WORK_NUMBER, DESCRIPTION from wrk_works where work_id ='.$result['ROWS'][0]['ORIGIN_ID'])) {
				print('<tr>
							<td class="wrkStepTitle" colspan="2">Processo d\'origine non trovato!!!!</td>
							<td width="100%" class="wrkStepOwner" align="right" ></td>
					   </tr>'."\n");
		} else {
		
		}
				print('<tr>
							<td class="DbFormTitle" colspan="3">Origine: '.$originWork['ROWS'][0]['WORK_NUMBER'].' - '.$originWork['ROWS'][0]['DESCRIPTION'].'</td>
							<td width="100%" class="wrkStepOwner" align="right" ></td>
					   </tr>'."\n");
				print('<tr><td>&nbsp;</td><td colspan="2" >'.MyWork::workBody($result['ROWS'][0]['ORIGIN_ID']).'</td></tr>'."\n");
	}



	// print_r($result);
	print('<tr><td width="100%" class="DbFormTitle" colspan="3" >'.get_label('wrkActivities').' - '.$result['ROWS'][0]['WORK_DESC'].'</td></tr>'."\n");
	$step_desc='';
	$owner='';
	$headerActivity='';
	$subActions=FALSE;
	for($i = 0; $i < $result['NROWS']; $i++){
		if ($subActions) {

			if (!$actionsWorks=dbselect('select WORK_ID, WORK_NUMBER from wrk_works where origin_id = '.$WORK_ID)) {
			    return null;
			} else {
				$actionBody='';
				for($w = 0; $w < $actionsWorks['NROWS']; $w++){
					$actionBody.='<tr><td width="10" class="wrkLabel">&nbsp;</td>
								  <td class="wrkLabel">'.$actionsWorks['ROWS'][$w]['WORK_NUMBER'].'							  
								  </td>
								  <td width="100%" class="wrkField">';
		
					$actionBody.=MyWork::WorkBody($actionsWorks['ROWS'][$w]['WORK_ID']);
								  
					$actionBody.='</td>
								  </tr>'."\n";
				} // for
				print($actionBody."\n");
				$subActions=FALSE;
			}
		}
		if ($result['ROWS'][$i]['ACTIVITY_ID']<> $headerActivity) {
			if ($result['ROWS'][$i]['ON_APPROVE']=='creaAzioni') {
			    $subActions=TRUE;
			}

		    $step_desc=$result['ROWS'][$i]['STEP_DESC'];
			$headerActivity=$result['ROWS'][$i]['ACTIVITY_ID'];
			if ($result['ROWS'][$i]['FLOWED_BY']=='R' ) {
				$respOwner=dbselect('select DESCRIPTION from resp_lang_descriptions 
										where language_id = '.$sess_lang.'
											and resp_id in('.$result['ROWS'][$i]['RESPLIST'].')');
				$owner='';
				for($y = 0; $y < $respOwner['NROWS']; $y++){
					$owner .= $owner>''?' - '.$respOwner['ROWS'][$y]['DESCRIPTION']:$respOwner['ROWS'][$y]['DESCRIPTION'];
				} // for				
			} else {
				$owner=$result['ROWS'][$i]['STEP_OWNED_BY'] > ' '?$result['ROWS'][$i]['STEP_OWNED_BY']:$result['ROWS'][$i]['STEP_CREATED_BY'];

			}


			$owner_id=$result['ROWS'][$i]['OWNED_BY'] > ' '?$result['ROWS'][$i]['OWNED_BY']:$result['ROWS'][$i]['CREATED_BY'];					

			if ($owner_id==$sess_uid and $result['ROWS'][$i]['STEP_STATUS']=='T') {				
			    $duplicate="<a href=\"$PHP_SELF?WORK_ID=$WORK_ID&ACTIVITY_ID=".$result['ROWS'][$i]['ACTIVITY_ID']."&CLOSE=Y\" >chiudi step</a>&nbsp;&nbsp;&nbsp;<a href=\"$PHP_SELF?WORK_ID=$WORK_ID&ACTIVITY_ID=".$result['ROWS'][$i]['ACTIVITY_ID']."&DUPLICATE=Y\" >Ripeti l'azione</A>&nbsp;&nbsp;";
			} else {
				$duplicate="";
			}

			print('<tr>
						<td class="wrkStepTitle" colspan="2">'.$result['ROWS'][$i]['STEP_DESC'].'</td>
						<td width="100%" class="wrkStepOwner" align="right" >'.$duplicate.$result['ROWS'][$i]['STEP_CREATION'].' - '.$owner.'</td>
				   </tr>'."\n");
			$labelclass=$result['ROWS'][$i]['STEP_STATUS']=='A'?'wrkActiveLabel':'wrkLabel';
			$fieldclass=$result['ROWS'][$i]['STEP_STATUS']=='A'?'wrkActiveField':'wrkField';
		}
		print('<tr>');
			print('<td width="10" class="'.$labelclass.'">&nbsp;</td>'."\n");
			print('<td class="'.$labelclass.'">'.$result['ROWS'][$i]['FIELD_LABEL'].'</td>'."\n");
			
			switch($result['ROWS'][$i]['DATA_TYPE']){
				case 'SELECT':
					$key_filter='';
					$splarray=split(',',$result['ROWS'][$i]['VALIDATION']);
						for($y = 0; $y < sizeof($splarray); $y++){
							if (preg_match('[TABLE=(.+)]',$splarray[$y],$searched_value)) {
							    $table=$searched_value[1];
							}
							if (preg_match('[KEY=(.*)]',$splarray[$y],$searched_value)) {
							    $key=$searched_value[1];
							}
							if (preg_match('[DESCRIPTION=(.*)]',$splarray[$y],$searched_value)) {
								$searched_value[1]=str_replace(' ',' || \' \' || ' , $searched_value[1]);								
							    $field=$searched_value[1];
							}
							if (preg_match('[FILTER=(.*)]',$splarray[$y],$searched_value)) {				
								if (preg_match_all('[<(.+)/>]U',$searched_value[1],$sess_var)) {	
								    for($z = 0; $z < sizeof($sess_var[1]); $z++){
								    	$searched_value[1]=preg_replace('['.$sess_var[0][$z].']',$GLOBALS[$sess_var[1][$z]],$searched_value[1]);
								    } // for
								}					
							    $filter=$searched_value[1];
							}
							if (preg_match('[MULTIPLE=(.*)]',$splarray[$y],$searched_value)) {
								if (trim($result['ROWS'][$i]['DATA_TEXT'])>'') {
								    $key_filter=' in ('.$result['ROWS'][$i]['DATA_TEXT'].')';
								}
								
							}							
						} // for

					if ($key_filter=='') {
						$key_filter='=\''.$result['ROWS'][$i]['DATA_TEXT'].'\'';
					}
											
					$filter=substr(trim(strtoupper($filter)),1,5)=='WHERE'?$filter.' and '.$key.$key_filter:' where '.$key.$key_filter;
					$retrive_value_query='select '.$field.' as VALUE from '.strtolower($table).' '.$filter;

					$value_result=dbselect($retrive_value_query);
					print('<td width="100%" class="'.$fieldclass.'">');
					$brk='';
					for($z = 0; $z < $value_result['NROWS']; $z++){
						print($brk.$value_result['ROWS'][$z]['VALUE']);
						$brk='<br>';
					} // for

					print('</td>'."\n");					 					
					break;
				case 'NUMBER': 
					print('<td width="100%" class="'.$fieldclass.'">'.$result['ROWS'][$i]['DATA_NUMBER'].'</td>'."\n");
					break;
				case 'DATE': 
					print('<td width="100%" class="'.$fieldclass.'">'.$result['ROWS'][$i]['DATA_DATE'].'</td>'."\n");
					break;
				case 'FILE':
					if ($result['ROWS'][$i]['DATA_TEXT']>'') {
					    $blobLink='&nbsp;&nbsp;<img src="graphics/data.gif" ';
						$blobLink.='onclick="javascript:location.href=\'getblobfile.php?WRKDATA_ID='.$result['ROWS'][$i]['WRKDATA_ID'].'&FIELD_ID='.$result['ROWS'][$i]['FIELD_ID'];
						$blobLink.='&FIELD_ID='.$result['ROWS'][$i]['FIELD_ID'].'\';" STYLE="cursor: pointer"';
						$blobLink.=' title="Download Attachment - '.$result['ROWS'][$i]['DATA_TEXT'].'">';
					} else {
						$blobLink='';
					}
						print('<td width="100%" class="'.$fieldclass.'">'.$result['ROWS'][$i]['DATA_TEXT'].'&nbsp;&nbsp;&nbsp;'.$blobLink.'</td>'."\n");
					break;
				default:
					print('<td width="100%" class="'.$fieldclass.'">'.$result['ROWS'][$i]['DATA_TEXT'].'</td>'."\n");
			} // switch
			
		print('</tr>'."\n");
	} // for
}


print('</table>');

if (!$result) {
	print('<hr>');
} else {
	print('<hr>');
	print('<table width="100%" border="0" cellspacing="0" cellpadding="0">');
	print('<tr>');
	print("\n");  
	print('<td valign="top" width="10" class="lista2" ><img src="graphics/back.gif" ></td>'."\n");    
	print('<td valign="middle" width="100%" class="lista2" >&nbsp;&nbsp;<a href="workflow.php'.$originFilter.'" >');
	print(get_label('go_back'));
	print('</a></td>'."\n");
	
	//print('<td valign="middle" align="right" nowrap class="lista2" >&nbsp;&nbsp;<a href="stopWork.php?ACTIVITY_ID='.$ACTIVITY_ID.'" >');
	//print(get_label('stopWork'));
	//print('</a></td>'."\n");
	//print('<td class="lista2" ><img src="graphics/toolbar_icon_stop_active.gif" ></td>'."\n");    
	//
	print('</tr>'."\n");
	print('</table>');
	print('<hr>');		
}





?>