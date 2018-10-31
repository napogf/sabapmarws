<?php
/*
 * Created on 29-set-06
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$languages=dbselect('select LANGUAGE_ID from languages');

$inLink=true;
if (IsSet($_GET['table'])) {
		if (!IsSet($_GET['regen'])){
			$insertFormQuery='insert into sys_forms (FORM_NAME, TABLE_NAME) values (\''.strtoupper($_GET['table']).'\' , \''.strtoupper($_GET['table']).'\')';
			if (!dbupdate($insertFormQuery)) {
			    errore($insertFormQuery);
			}
			$formId=array(dbLastId());
			$form_name=ucfirst(strtolower($_GET['table']));
			$form_title_query="insert into sys_forms_titles (language_id, form_id, title) select l.language_id, ".$formId[0].", '$form_name' from languages l";
			dbupdate($form_title_query);
		} else {
			$form_id_result = dbselect("select FORM_ID from sys_forms where FORM_NAME = upper('" . $_GET['table'] . "')");
			for ($z = 0; $z < $form_id_result['NROWS']; $z++) {
				$formId[]=$form_id_result['ROWS'][$z]['FORM_ID'];
			}
		}
		$fieldStruct=dbselect('show columns from '.$_GET['table']);
		for ($index = 0; $index < sizeof($formId); $index++) {
			$form_id=$formId[$index];
			for($i = 0; $i < $fieldStruct['NROWS']; $i++){
				$fieldName=$fieldStruct['ROWS'][$i]['Field'];
				if (!dbselect("select * from sys_forms_fields where (form_id = $form_id) and (field_name = '$fieldName') ")){
					$fieldDataType='';
					$fieldIsKey='N';
					$fieldMaxLength=60;
					$fieldTextareaRows=1;
					$fieldUpdatable='Y';
					$fieldIsKey='N';
					$fieldType='';
					$fieldListed=$inLink?'Y':'N';
					$fieldInLink='N';
					if (preg_match("[(.+har)\((.+)\)]",$fieldStruct['ROWS'][$i]['Type'],$type)>0) {
					    $fieldDataType='TEXT';
						$fieldLength=$type[2]>60?60:$type[2];
						$fieldMaxLength=$type[2];
						$fieldTextareaRows=1;
						if ($inLink) {
							$fieldInLink='Y';
							$inLink=false;
						}
					} elseif (preg_match("[(int)\((.+)\).*]",$fieldStruct['ROWS'][$i]['Type'],$type)>0){
					    $fieldDataType='NUMBER';
						$fieldLength=$type[2]>60?60:$type[2];
						$fieldMaxLength=$type[2];
						$fieldTextareaRows=1;
					} elseif ($fieldStruct['ROWS'][$i]['Type']=='float'){
					    $fieldDataType='NUMBER';
						$fieldLength=20;
						$fieldMaxLength=20;
						$fieldTextareaRows=1;
						$fieldType="number,2";
					} elseif ($fieldStruct['ROWS'][$i]['Type']=='date' or $fieldStruct['ROWS'][$i]['Type']=='datetime'){
					    $fieldDataType='DATE';
						$fieldLength=10;
						$fieldMaxLength=10;
						$fieldTextareaRows=1;
						$fieldType="date,%d-%m-%Y";
					} elseif(preg_match("[(text)]",$fieldStruct['ROWS'][$i]['Type'],$type)>0){
					    $fieldDataType='TEXTAREA';
						$fieldLength=80;
						$fieldMaxLength=1024;
						$fieldTextareaRows=3;
					} else {
						$fieldLength=1;
						$fieldMaxLength=1;
						$fieldTextareaRows=1;
						$fieldDataType='UNKNOWN';
						$fieldListed='N';
					}
					if ($fieldStruct['ROWS'][$i]['Key']=='PRI') {
					    $fieldShowed='N';
						$fieldUpdatable='N';
						$fieldIsKey='Y';
						$fieldInLink='N';
						$fieldListed='N';
						$fieldNullValue='Y';
					} else {
					    $fieldShowed=$fieldType=='UNKNOWN'?'N':'Y';
					}
					switch ($fieldName) {
						case 'CREATED_BY':
							$fieldDefaultValue='<sess_uid/>';
							$fieldShowed='N';
							$fieldListed='N';
							break;
						case 'UPDATED_BY':
							$fieldDefaultValue='<sess_uid/>';
							$fieldShowed='N';
							$fieldListed='N';
							break;
						case 'CREATED':
							$fieldDefaultValue="date(\'Y-m-d\')";
							$fieldShowed='N';
							$fieldListed='N';
							break;
						case 'UPDATED':
							$fieldDefaultValue="date(\'Y-m-d\')";
							$fieldShowed='N';
							$fieldListed='N';
							break;
						default:
							$fieldDefaultValue='';
							break;
					}
					$fieldNullValue=substr($fieldStruct['ROWS'][$i]['Null'],1,1);
					$vseq=$i*10;
					$insert_field_query="insert into sys_forms_fields (field_name, form_id, data_type, type, length, hseq, vseq, showed, default_value, maxlength, is_key, updatable, textarea_rows, listed, in_link, validation) values
																	  ('$fieldName', '$form_id', '$fieldDataType','$fieldType', '$fieldLength', '10', '$vseq', '$fieldShowed', '$fieldDefaultValue', '$fieldMaxLength', '$fieldIsKey', '$fieldUpdatable', '$fieldTextareaRows', '$fieldListed', '$fieldInLink', 'TRUE')";
					if (!dbupdate($insert_field_query)) {
					    errore($insert_field_query);
					}
					$field_id=dbLastId();
					$insert_field_label_query="insert into sys_forms_fields_labels (field_id, language_id, description, shortdesc) select '$field_id', l.language_id , '".ucfirst(strtolower($fieldName))."', '".substr(ucfirst(strtolower($fieldName)),0,12)."' from languages l";
					if (!dbupdate($insert_field_label_query)){
						errore($insert_field_label_query);
					}
				}
			}
		} // for
		header("Location: ". $_SERVER['PHP_SELF']);
} else {
		include('pageheader.inc');
		print('<div class="formContainer">'."\n");
		print('<a href="'.$_SERVER['PHP_SELF'].'?regen=Y">Rigenera Le Form</a>');
		$TablesArray=ListDbTables();
		if (isSet($_GET['regen']) and ($_GET['regen']=='Y')){
			for($i = 0; $i < sizeof($TablesArray['ROWS']); $i++){
				if (dbselect("select UPPER(FORM_NAME) from sys_forms where form_name='".strtoupper($TablesArray['ROWS'][$i]['Tables_in_'.$fontedati])."' and is_table_form='Y'")) {
				    $TablesArray['ROWS'][$i]['Tables_in_'.$fontedati]='<a href='.$_SERVER['PHP_SELF'].'?regen=Y&table='.$TablesArray['ROWS'][$i]['Tables_in_'.$fontedati].'>'.$TablesArray['ROWS'][$i]['Tables_in_'.$fontedati].'</a>';
				}
			} // for
		} else {
			for($i = 0; $i < sizeof($TablesArray['ROWS']); $i++){
				if (!dbselect("select UPPER(FORM_NAME) from sys_forms where form_name='".strtoupper($TablesArray['ROWS'][$i]['Tables_in_'.$fontedati])."' and is_table_form='Y'")) {
				    $TablesArray['ROWS'][$i]['Tables_in_'.$fontedati]='<a href='.$_SERVER['PHP_SELF'].'?table='.$TablesArray['ROWS'][$i]['Tables_in_'.$fontedati].'>'.$TablesArray['ROWS'][$i]['Tables_in_'.$fontedati].'</a>';
				}
			} // for
		}



		$selectTable = new htmlTable($TablesArray,FALSE);

		$selectTable->SetColumnHeader(0,'Tables');
		$selectTable->show();
		print('</div>'."\n");
		include('pagefooter.inc');
}
?>
