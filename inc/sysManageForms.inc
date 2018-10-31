<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
foreach ($_GET as $key => $value) {
	$$key = $value;
}

if (isSet($duplicate) and ($duplicate=='Y')) {
	$insForm='insert into sys_forms (FORM_NAME, TABLE_NAME, METHOD, ACTION, IS_TABLE_FORM, PAGED) ' .
			' select concat(\'copia_di_\',FORM_NAME), TABLE_NAME, METHOD, ACTION, IS_TABLE_FORM, PAGED from sys_forms where form_id='.$FORM_ID;
	if (dbupdate($insForm)) {
		$newFormId=dbLastId();
		$titleFormQuery='insert into sys_forms_titles (form_id, language_id, title) ' .
						'select '.$newFormId.', sf.language_id, sf.title from sys_forms_titles sf where sf.form_id='.$FORM_ID;
		dbupdate($titleFormQuery);
		if ($fieldsResult=dbselect('select FIELD_ID from sys_forms_fields where form_id='.$FORM_ID)) {
			for ($index = 0; $index < $fieldsResult['NROWS']; $index++) {
				$insField='insert into sys_forms_fields (FIELD_NAME, FORM_ID, DATA_TYPE, TYPE, LENGTH, HSEQ, VSEQ, SHOWED, DEFAULT_VALUE, VALIDATION, MAXLENGTH, IS_KEY, UPDATABLE, NULLVALUE, TEXTAREA_ROWS, IN_TABLE, LISTED, TABLE_ORDER, TABLE_ORDER_TYPE, IN_LINK, LOOKUP) ' .
						'  select FIELD_NAME, '.$newFormId.', DATA_TYPE, TYPE, LENGTH, HSEQ, VSEQ, SHOWED, DEFAULT_VALUE, VALIDATION, MAXLENGTH, IS_KEY, UPDATABLE, NULLVALUE, TEXTAREA_ROWS, IN_TABLE, LISTED, TABLE_ORDER, TABLE_ORDER_TYPE, IN_LINK, LOOKUP from' .
						'  sys_forms_fields where field_id = '.$fieldsResult['ROWS'][$index]['FIELD_ID'];
				if (dbupdate($insField)) {
					$newFieldId=dbLastId();
					$insFieldLabels='insert into sys_forms_fields_labels (FIELD_ID, LANGUAGE_ID, DESCRIPTION, SHORTDESC) ' .
							'		select '.$newFieldId.', LANGUAGE_ID, DESCRIPTION, SHORTDESC from sys_forms_fields_labels where field_id='.$fieldsResult['ROWS'][$index]['FIELD_ID'];
					dbupdate($insFieldLabels);
				}
			}
		}
	}
	header("location: ".$_SERVER['PHP_SELF']);
}



class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
	 *
	 *
	 *
     */
	function FormPostValidation(){
		if ($_POST['FORM_NAME']>'' or $_GET['FORM_NAME']>'') {
			if ($this->GetFormMode()=='insert') {

				$formId = $this->GetLastInsertId();
				if (!dbupdate('insert into sys_forms_titles (form_id, title, language_id)
												select '.$formId.', \''.ucfirst(strtolower($_POST['FORM_NAME'])).'\', lan.language_id from languages lan')) {
					errore('Labels non inserite per form '.$formId);
				}
			}
			if ($this->GetFormMode()=='delete') {

				dbupdate('delete FROM sys_forms_titles WHERE form_id not in (select form_id from sys_forms)');
				dbupdate('delete FROM sys_forms_fields WHERE form_id not in (select form_id from sys_forms)');
				dbupdate('delete FROM sys_forms_fields_labels WHERE field_id not in (select field_id from sys_forms_fields)');


			}
		} else {
			errore(' No Form Name '.$_POST['FORM_NAME']);
		}
		return TRUE;
	}
}

if (isset($_GET['dbKey'])) {
    $_GET['dbKey']=stripslashes($_GET['dbKey']);
	$_action='ACTION="?mode='.$mode.'&dbKey='.$_GET['dbKey'];
} else {
	$_action='ACTION="?mode=insert';
}

$ManagedTable = new MyDbForm('SYS_FORMS',$_SESSION['sess_lang']);

$ManagedTable->SetFormMode($mode,stripslashes($_GET['dbKey']));

$ManagedTable->SetFormTitle($ManagedTable->GetFormTitle());

include('pageheader.inc');
print('<div class="formContainer">'."\n");

$ManagedTable->ShowForm();

if ($ManagedTable->GetFormMode()=="modify") {
	print('<hr>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>'."\n");
	print('<td valign="middle" width="10" class="lista2" style="padding: 5px;" ><img src="graphics/list.gif" ></td>'."\n");
	print('<td valign="middle" class="lista2" nowrap style="padding-right: 5px;padding-left: 5px;" >&nbsp;&nbsp;<a href="sys_manage_fields.php?FORM_ID='.$ManagedTable->GetFormFieldValue('FORM_ID').'" >Manage Forms Fields</a></td>'."\n");

	print('<td valign="middle" align="center" class="lista2" style="padding-right: 5px;padding-left: 5px;">&nbsp;&nbsp;<img src="graphics/folders.gif" title="Duplica Form" onclick="javascript:location.href=\''.$_SERVER['PHP_SELF'].'?FORM_ID='.$ManagedTable->GetFormFieldValue('FORM_ID').'&duplicate=Y\'" ></td>'."\n");
	$languages_result=dbselect("select LANGUAGE_ID, DESCRIPTION, FLAG_GIF from languages");
	print('<td valign="top" class="lista2" nowrap width="25" align="center" >&nbsp;</td>');
	if ($languages_result['NROWS']>0) {
		for($i = 0; $i < $languages_result['NROWS']; $i++){
			print('<td valign="middle" class="lista2" nowrap width="50" align="center"  style="padding: 5px;">
					<img src="'.$languages_result['ROWS'][$i]['FLAG_GIF'].'" title="'.get_label('manage_languages').' - '.$languages_result['ROWS'][$i]['DESCRIPTION'].'" STYLE="cursor: pointer"
					onclick="javascript:location.href=\'sys_forms_lang.php?mode=modify&FORM_ID='.$ManagedTable->GetFormFieldValue('FORM_ID').'&LANGUAGE_ID='.$languages_result['ROWS'][$i]['LANGUAGE_ID'].'&dbTable=SYS_FORMS_TITLES\';"></td>'."\n");
		} // for

	}
	print('<td valign="middle" width="100%" class="lista2" nowrap >&nbsp;&nbsp;</td>'."\n");
	print('</tr></table>'."\n");
}

	print('<hr>'."\n");

	print('<TABLE width="100%">
		   <TR><TD class="TableTitle" >');
	print(get_label('selectkey'));
	print('</TD></TR>');
	print('<form name="filterTable" action="'.$_SERVER['PHP_SELF'].'" method="post">');
	print('<tr><td><table width="100%"><tr class="ButtonBar" >');
		print('<td>');
		print('<input name="b_search" type="submit" value="'.get_label('wrkfilter').'">');
		print('</td>'."\n");
		print('<td align="right">');
		print('<input name="b_clear" type="submit" value="'.get_label('delfilter').'">');
		print('</td>'."\n");
	print('</tr></table></td></tr>'."\n");

	print('<TR><TD>');

		$del_message=get_label('del_message');

		$selectTableQuery="select form_name ORDER1 ," .
				"concat('<a href=\"".$_SERVER['PHP_SELF']."?mode=modify&dbKey=where form_id=\'',form_id,'\'\">',form_name,'</a>') AS ".$ManagedTable->GetFormFieldLabel('FORM_NAME').", " .
				"concat('<center><img src=\"graphics/database_save.png\" STYLE=\"cursor: pointer\" onClick=\"javascript:location.href=\'sysFormExport.php?formName=',form_name,'\'\" title=\"Esporta ',form_name,' definizione campi\" ></center>') AS Export , " .
				"concat('<center><img src=\"graphics/list.gif\" STYLE=\"cursor: pointer\" onClick=\"javascript:location.href=\'sys_manage_fields.php?FORM_ID=',form_id,'\'\" title=\"Manage ',form_name,' Fields\" ></center>') AS F_Fields , " .
				"concat('<center><img src=\"graphics/webapp/deleted.gif\" STYLE=\"cursor: pointer\" onClick=\"submit_del(\'".$_SERVER['PHP_SELF']."?&FORM_NAME=',form_name,'&mode=delete&dbKey=WHERE form_id=',form_id,'\',\'$del_message\')\" title=\"Delete ',form_name,'\" ></center>') AS cancel " .
				"from sys_forms ";

		$selectTableQuery.= (isSet($filter_form) and $filter_form > '')?" where form_name like upper('$filter_form%') ":'';


		$selectTableQuery .= ' order by ORDER1 desc';


		$selectTable = new htmlETable($selectTableQuery);

		if ($selectTable->getTableRows()>0) {
			$selectTable->SetTableFilter();
			$selectTable->_tableData['Form_name']->SetFiltercontent('TEXT','filter_form',30);
			$selectTable->HideCol('ORDER1');
//			$selectTable->SetColumnHeader(1,'Forms Fields');
//			$selectTable->SetColumnHeader(2,'#');
			$selectTable->SetWidth('100%');
			$selectTable->show();
		}


	print('</TD></TR>
		   </TABLE>');
	print('</form>');
	print('</div>'."\n");
	include('pagefooter.inc');
?>
