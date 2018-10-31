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
class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
     */


	function FormPostValidation(){
		if ($_POST['FIELD_NAME']>'') {
			if ($this->GetFormMode()=='insert') {
				$fieldId=$this->GetLastInsertId();
				if (!dbupdate('insert into sys_forms_fields_labels (field_id, description, language_id)
												select '.$fieldId.', \''.ucfirst(strtolower($_POST['FIELD_NAME'])).'\', lan.language_id from languages lan')) {
					errore('Labels non inserite per field '.$fieldId);
				}
			}

		} else {
			errore(' No Field Name '.$_POST['FIELD_NAME']);
		}
		return TRUE;
	}

}


if (isset($dbKey)) {
    $dbKey=stripslashes($dbKey);
	$_action='ACTION="?mode='.$mode.'&dbKey='.$dbKey;
} else {
	$_action='ACTION="?mode=insert';
}

$ManagedTable = new MyDbForm('SYS_FORMS_FIELDS',$_SESSION['sess_lang'] );
$ManagedTable->SetFormMode($mode,stripslashes($dbKey));
if (empty($FORM_ID)) {
    $FORM_ID=$ManagedTable->GetFormFieldValue('FORM_ID');
}
$ManagedTable->AddFormActionParameter("&FORM_ID=$FORM_ID");

$ManagedTable->SetFormTitle($ManagedTable->GetFormTitle());

include('pageheader.inc');
print('<div class="formContainer">'."\n");

$ManagedTable->ShowForm();
print('<hr>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
 			<tr>'."\n");
print('<td valign="top" width="10" class="lista2" ><img src="graphics/back.gif" ></td>');
print('<td valign="middle" class="lista2" nowrap >&nbsp;&nbsp;<a href="sys_manage_forms.php?mode=modify&dbKey=where form_id='.$FORM_ID.'" >'.get_label('go_back').'</a></td>');
if ($ManagedTable->GetFormMode()=="modify") {
	$languages_result=dbselect("select LANGUAGE_ID, DESCRIPTION, FLAG_GIF from languages");
	print('<td valign="top" class="lista2" nowrap width="25" align="center" >&nbsp;</td>');
	if ($languages_result['NROWS']>0) {
		for($i = 0; $i < $languages_result['NROWS']; $i++){
			print('<td valign="middle" class="lista2" nowrap width="50" align="center" style="padding-left: 5px;padding-right: 5px;" >
					<img src="'.$languages_result['ROWS'][$i]['FLAG_GIF'].'" alt="'.get_label('manage_languages').' - '.$languages_result['ROWS'][$i]['DESCRIPTION'].'" STYLE="cursor: pointer"
					onclick="javascript:location.href=\'sys_fields_lang.php?mode=modify&FIELD_ID='.$ManagedTable->GetFormFieldValue('FIELD_ID').'&LANGUAGE_ID='.$languages_result['ROWS'][$i]['LANGUAGE_ID'].'\';"></td>'."\n");
		} // for

	}

}
print('<td valign="middle" width="100%" class="lista2" nowrap >&nbsp;&nbsp;</td>'."\n");
print('</tr></table>'."\n");
print('<hr>'."\n");

	print('<TABLE width="100%">
		   <TR><TD class="TableTitle" >');
	print(get_label('selectkey'));
	print('</TD></TR>
		   <TR><TD>');

		$del_message=get_label('del_message');

		$selectTableQuery="select concat('<a href=\"".$_SERVER['PHP_SELF']."?mode=modify&FORM_ID=',form_id,'&dbKey=where field_id=\'',field_id,'\'\">',field_name,'</a>') AS '".$ManagedTable->GetFormFieldLabel('FIELD_NAME')."'
							, concat(hseq,' ',vseq) as 'H-V-Seq'
							, updatable as 'Updatable'
							, table_order
							, showed as 'Showed'
							, nullvalue as 'Null'
							, listed as 'Listed'
							, data_type as 'Data Type'
							, concat('<center><img src=\"graphics/webapp/deleted.gif\" STYLE=\"cursor: pointer\" onClick=\"submit_del(\'".$_SERVER['PHP_SELF']."?&mode=delete&dbKey=WHERE field_id=',field_id,'\',\'$del_message\')\"></center>') AS cancel
							from sys_forms_fields where form_id=$FORM_ID order by is_key desc, vseq";

		$selectTable = new htmlTable($selectTableQuery);

		// $selectTable->SetColumnHeader(5,'#');
		$selectTable->SetWidth('100%');
		$selectTable->show();
	print('</TD></TR>
		   </TABLE>');
print('</div>'."\n");

include('pagefooter.inc');
?>
