<?php

/**
 * 
 *
 * @version $Id: genForm.inc,v 1.1.1.1 2009/02/13 09:28:20 cvsuser Exp $
 * @copyright 2003 
 **/

if (IsSet($table)) {
		$insertFormQuery='insert into sys_forms (FORM_NAME,TABLE_NAME) values (\''.strtoupper($table).'\',\''.strtoupper($table).'\')';
		if (!dbupdate($insertFormQuery)) {
		    errore($insertFormQuery);
		}
		$form_id=mysql_insert_id();		
		$form_name=ucfirst(strtolower($table));
		$form_title_query="insert into sys_forms_titles (language_id, form_id, title) select l.language_id, $form_id, '$form_name' from languages l";
		dbupdate($form_title_query);
		$fieldStruct=dbselect('show columns from '.$table);
		for($i = 0; $i < $fieldStruct['NROWS']; $i++){
			$fieldName=$fieldStruct['ROWS'][$i]['Field'];
			if (preg_match("[(.+har)\((.+)\)]",$fieldStruct['ROWS'][$i]['Type'],$type)>0) {
			    $fieldType='TEXT';
				$fieldLength=$type[2]>60?60:$type[2];
				$fieldMaxLength=$type[2];
				$fieldTextareaRows=1;
			} elseif (preg_match("[(int)\((.+)\)]",$fieldStruct['ROWS'][$i]['Type'],$type)>0){
			    $fieldType='NUMBER';
				$fieldLength=$type[2]>60?60:$type[2];
				$fieldMaxLength=$type[2];
				$fieldTextareaRows=1;
			} elseif(preg_match("[(text)]",$fieldStruct['ROWS'][$i]['Type'],$type)>0){
			    $fieldType='TEXTAREA';
				$fieldLength=80;
				$fieldMaxLength=1024;			
				$fieldTextareaRows=3;
			} elseif(preg_match("[(float|double)]",$fieldStruct['ROWS'][$i]['Type'],$type)>0){
			    $fieldType='NUMBER';
				$fieldLength=20;
				$fieldMaxLength=30;			
				$fieldTextareaRows=1;
			} elseif(preg_match("[(date)]",$fieldStruct['ROWS'][$i]['Type'],$type)>0){
			    $fieldType='DATE';
				$fieldLength=10;
				$fieldMaxLength=10;			
				$fieldTextareaRows=1;
			} else {
				$fieldLength=1;
				$fieldMaxLength=1;
				$fieldTextareaRows=1;
				$fieldType='UNKNOWN';
			}			
			if ($fieldStruct['ROWS'][$i]['Key']=='PRI') {
			    $fieldShowed='N';
				$fieldUpdatable='N';
				$fieldIsKey='Y';		
				$fieldListed='N';
			} else {
			    $fieldShowed=$fieldType=='UNKNOWN'?'N':'Y';
				$fieldUpdatable='Y';
				$fieldIsKey='N';
				$fieldListed='Y';
			}
			$fieldNullValue=substr($fieldStruct['ROWS'][$i]['Null'],1,1);
			$vseq=$i*10;
			$insert_field_query="insert into sys_forms_fields (field_name, form_id, data_type, length, hseq, vseq, showed, maxlength, is_key, updatable, textarea_rows, validation, listed) values
															  ('$fieldName', '$form_id', '$fieldType', '$fieldLength', '10', '$vseq', '$fieldShowed', '$fieldMaxLength', '$fieldIsKey', '$fieldUpdatable', '$fieldTextareaRows', 'TRUE' , '$fieldListed')";
			if (!dbupdate($insert_field_query)) {
			    errore($insert_field_query);
			}
			$field_id=mysql_insert_id();
			$insert_field_label_query="insert into sys_forms_fields_labels (field_id, language_id, description, shortdesc) select '$field_id', l.language_id , '".ucfirst(strtolower($fieldName))."', '".substr(ucfirst(strtolower($fieldName)),0,12)."' from languages l";
			if (!dbupdate($insert_field_label_query)){
				errore($insert_field_label_query);
			}
			
			
		} // for

		header("Location: ".$_SERVER['PHP_SELF']);
} else {

		include('pageheader.inc');
		$TablesArray=ListDbTables();
		// print_r($TablesArray);
		for($i = 0; $i < sizeof($TablesArray['ROWS']); $i++){
			if (!dbselect("select UPPER(FORM_NAME) from sys_forms where form_name='".strtoupper($TablesArray['ROWS'][$i]['Tables_in_'.$fontedati])."' and is_table_form='Y'")) {
			    $TablesArray['ROWS'][$i]['Tables_in_'.$fontedati]='<a href='.$_SERVER['PHP_SELF'].'?table='.$TablesArray['ROWS'][$i]['Tables_in_'.$fontedati].'>'.$TablesArray['ROWS'][$i]['Tables_in_'.$fontedati].'</a>';
			} 
		} // for
		$selectTable = new htmlTable($TablesArray,FALSE);

		$selectTable->SetColumnHeader(0,'Tables');
		$selectTable->show();
		include('pagefooter.inc');
} 

?>