<?php

/**
 * 
 *
 * @version $Id: activityclosedDiv.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003 
 **/


function PrintWork($WORK_ID,$workTitle=TRUE){
	global $sess_lang, $sess_uid;

	$work_status_query="SELECT DISTINCT wrk_activities.WORK_ID, 
									wrk_steps.STEP_ID,
									wrk_steps.ON_APPROVE,
									wrk_activities.ACTIVITY_ID,								
					                sys_forms_fields.FIELD_NAME, 
									sys_forms_fields.HSEQ,
					                sys_forms_fields.VSEQ, 
									sys_forms_fields.SHOWED,
					                wrk_works.WORK_STATUS, 
									wrk_activities.STEP_STATUS,
					                wrk_activities.NOTES, 
									wrk_data.DATA_TEXT,
					                wrk_data.DATA_NUMBER, 
									date_format(wrk_data.DATA_DATE,'%d-%m-%Y') DATA_DATE, 
									sys_forms.FORM_NAME,
									sys_forms_fields.LENGTH,
					                sys_forms_fields.DATA_TYPE, 
									sys_forms_fields.VALIDATION,
					                sys_forms_fields_labels.DESCRIPTION FIELD_LABEL,
					                wrk_works_types_lang.DESCRIPTION WORK_TYPE_DESC,
					                wrk_steps_lang.DESCRIPTION STEP_DESC,							
									date_format(wrk_activities.CREATION,'%d-%m-%Y') STEP_CREATION,
									concat(persons_from.FIRST_NAME , ' ' , persons_from.LAST_NAME) STEP_CREATED_BY,							
									concat(persons_to.FIRST_NAME , ' ' , persons_to.LAST_NAME) STEP_OWNED_BY,
									persons_from.USER_ID CREATED_BY,
									persons_to.USER_ID OWNED_BY,
									concat(last_updated_by.first_name,' ',last_updated_by.last_name) as OWNER,
									wrk_data.WRKDATA_ID, sys_forms_fields.FIELD_ID, 
									(CASE WHEN (sys_forms_fields.DATA_TYPE = 'FILE') THEN wrk_data.DATA_TEXT
									ELSE ''
									END) AS FILENAME,
									wrk_works.DESCRIPTION WORK_DESC,
									wrk_steps.FLOWED_BY,
									wrk_steps.PRINT_BODY,
									concat(wrk_steps.APPROVED_BY,', ',wrk_steps.STARTED_BY,', ',wrk_steps.DESTINATED_TO) as RESPLIST,
									wrk_works.ORIGIN_ID
							from wrk_works,  user_resp_reference urr
							left join wrk_activities on (wrk_activities.work_id = wrk_works.work_id)								
							left join users persons_from on (persons_from.user_id = wrk_activities.person_from)
							left join users persons_to on (persons_to.user_id = wrk_activities.person_to)
							left join users last_updated_by on (last_updated_by.user_id = wrk_activities.updated_by)
							left join wrk_works_types on (wrk_works_types.work_type_id = wrk_works.work_type_id )
							left join wrk_works_types_lang on ((wrk_works_types_lang.work_type_id = wrk_works_types.work_type_id) 
																and (wrk_works_types_lang.language_id = $sess_lang))
							left join wrk_steps on (wrk_steps.step_id = wrk_activities.step_id)
							left join wrk_steps_lang on ((wrk_steps_lang.step_id = wrk_steps.step_id) and 
														(wrk_steps_lang.language_id = $sess_lang))
							left join sys_forms on (sys_forms.form_id = wrk_steps.form_id)
							left join sys_forms_fields on (sys_forms_fields.form_id = wrk_steps.form_id)
							left join sys_forms_fields_labels on ((sys_forms_fields_labels.field_id = sys_forms_fields.field_id) and
																(sys_forms_fields_labels.language_id = $sess_lang))
							left join wrk_data on ((wrk_data.activity_id = wrk_activities.activity_id) and 
													(wrk_data.field_id = sys_forms_fields.field_id))
							where (		(wrk_works.work_id = $WORK_ID)
									and (urr.user_id = $sess_uid)
									and (sys_forms_fields.showed = 'Y')
									) order by wrk_activities.activity_id, sys_forms_fields.hseq, sys_forms_fields.vseq";								
									
	
	print('<div id="workFlow">');
	if (!$result=dbselect($work_status_query)) {
	    print('<div id="activtyHeader">'.get_label('nosteps').'</div>'."\n");
		return FALSE;
	} else {
		print("<div id=\"workTitle\">".$result['ROWS'][0]['WORK_DESC'].'</div>'."\n");
		$step_desc='';
		$owner='';
		$headerActivity='';
		$subActions=FALSE;
		print('<ul>'."\n");
		for($i = 0; $i < $result['NROWS']; $i++){
			if ($subActions) {	
				if (!$actionsWorks=dbselect('select WORK_ID, WORK_NUMBER from wrk_works where origin_id = '.$WORK_ID)) {
				    return null;
				} else {
					for($w = 0; $w < $actionsWorks['NROWS']; $w++){
						print('<ul><li><h1>'.$actionsWorks['ROWS'][$w]['WORK_NUMBER'].'</h1></li><li>');
						$actionBody.=PrintWork($actionsWorks['ROWS'][$w]['WORK_ID']);									  
						print('</li></ul>'."\n");
					} // for
					$subActions=FALSE;
				}
			}
			if ($result['ROWS'][$i]['ACTIVITY_ID']<> $headerActivity) {
				if ($headerActivity>'') {
				    print('</ul>');
				}
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
						$owner .= ' '.$result['ROWS'][$i]['OWNER'];
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

				// Test se l'azione � un passo di approvazione......
				if ($result['ROWS'][$i]['FIELD_NAME']=='FEEDBACK') {
				    if (trim($result['ROWS'][$i]['DATA_TEXT']=='Y')) {
				        $activityHeaderClass=' id="approvato" ';
				    } else {
						$activityHeaderClass=' id="rifiutato" ';
					} 
				} else {
						$activityHeaderClass='';			
				}
				
				
				
				print('<li '.$activityHeaderClass.'><h5>'.$result['ROWS'][$i]['STEP_DESC'].'</h5><h6>'.$duplicate.$result['ROWS'][$i]['STEP_CREATION'].' - '.$owner.'</h6></li>'."\n");
				print("\t".'<ul>'."\n");
				$labelHeader=$result['ROWS'][$i]['STEP_STATUS']=='A'?'h2':'h1';
				$fieldHeader=$result['ROWS'][$i]['STEP_STATUS']=='A'?'h4':'h3';
			}
			if ($result['ROWS'][$i]['PRINT_BODY']=='Y') {
				print("\t".'<li>'."\n");
					print("\t".'<'.$labelHeader.'>'.$result['ROWS'][$i]['FIELD_LABEL'].'</'.$labelHeader.'>'."\n");
					print("\t".'<'.$fieldHeader.'>');
					switch($result['ROWS'][$i]['DATA_TYPE']){
						case 'SELECT':
							$key_filter='';
							$splarray=split(',',$result['ROWS'][$i]['VALIDATION']);
								$searched_value=null;
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
									$sess_var=null;
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
							$brk='';
							for($z = 0; $z < $value_result['NROWS']; $z++){
								print($brk.$value_result['ROWS'][$z]['VALUE']);
								$brk='<br>';
							} // for
		
							break;
						case 'NUMBER': 
							print($result['ROWS'][$i]['DATA_NUMBER']);
							break;
						case 'DATE': 
							print($result['ROWS'][$i]['DATA_DATE']);
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
								print($result['ROWS'][$i]['DATA_TEXT'].'&nbsp;&nbsp;&nbsp;'.$blobLink);
							break;
						default:
							print($result['ROWS'][$i]['DATA_TEXT']);
					} // switch
					print('</'.$fieldHeader.'>'."\n");					 					
				print("\t".'</li>'."\n");			    
			}
		} // for
		print('</ul>'."\n");
	}
	
	print('</div>');
	return TRUE;	

}

?>