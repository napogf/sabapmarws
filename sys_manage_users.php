<?php

/**
 *
 *
 * @version $Id: sys_manage_users.php,v 1.1.1.1 2011-08-29 15:30:35 pratiche Exp $
 * @copyright 2003
 **/
include "login/autentication.php";
$db = Db_Pdo::getInstance();


if($_GET['mode'] == 'delete' and isSet($_GET['USER_ID'])){
	$selected = 'selected';
	try {
		$db->query('delete from user_uo_ref where user_id = :user_id',array(
				':user_id' => $_GET['USER_ID']
			));
		$db->query('delete from user_resp_reference where user_id = :user_id',array(
				':user_id' => $_GET['USER_ID']
			));
		$db->query('delete from sys_users where user_id = :user_id',array(
				':user_id' => $_GET['USER_ID']
			));
		$msg = 'Utente cancellato correttamente!';
	} catch (Exception $e) {
		$msg = 'Errori nella cancellazione dell\'utente!<br />' . $e->getMessage();
	}
}


$xlsBar='N';
if(isset($_GET['USER_ID']) and $_GET['mode'] != 'delete'){
	$mode='modify';
}

class dbTable extends htmlETable {
	protected $_formKey;

	public function setFormKey($value){
		$this->_formKey = $value;
	}
	public function GetColValue($column,$i){
		if (is_null($this->GetColumnHref($column,$i))) {
			if (($this->getColSubstring($column)>0) and (strlen($this->_tableData[$column]->GetValue($i))>$this->getColSubstring($column))){
				$content ='<span  id="'.$column.'_'.$i.'" >'.substr($this->_tableData[$column]->GetValue($i),0,$this->getColSubstring($column)).'</span>';
				$content.='<span dojoType="dijit.Tooltip" connectId="'.$column.'_'.$i.'" style="display:none;"><div style="max-width:250px; display:block;">'.$this->_tableData[$column]->GetValue($i).'</div></span>';
				return $content;
			} elseif($column == $this->_formKey){
				$content = '<img title="Modifica il record" onclick="location.href=\'' .
				$_SERVER['PHP_SELF']  . '?' . $column . '=' .  $this->_tableData[$column]->GetValue($i)
				. '\'" style="cursor: pointer" src="graphics/application_edit.png">';
				return $content;
			} else {
				return $this->_tableData[$column]->GetValue($i);
			}
		}  else {
			$value=$this->GetColumnHref($column,$i);
			$pattern='|<([a-zA-Z]{1,3}).*>|';
			preg_match_all($pattern, $value, $match);
			$closeTag='</'.$match[1][0].'>';
			return $value.$this->_tableData[$column]->GetValue($i).$closeTag;
		}
	}
}
class MyDbForm extends formExtended {
			function FormPreValidation(){
				if (($_POST['PASSWORD'] == $_POST['PASSWORD_CONF'])
					and $this->passwordVerify($_POST['PASSWORD'])) {
					if ($this->GetFormFieldValue('PASSWORD')<>$_POST['PASSWORD']){
						$_POST['PASSDATE']=date('Y-m-d');
					}
					return TRUE;
				} else {
					$this->SetFormMessage('Password Errata!');
					return FALSE;
				}
			}

}

$userTable = isSet($userTable)?strtoupper($userTable):'SYS_USERS';
$dbTable = $userTable;
$xlsBar='Y';
if (!isSet($xlsExport) or ($xlsExport <> 'Y')) {
	require_once ("pageheader.inc");
	$recallPage='?dbTable='.$dbTable;
}
if ($mode=='modify'){
	// $xlsBar = 'Y';
	$modifyToolBar = new toolBar();
	$modifyToolBar->SetRightLinks('<a href="assign_userresp.php?USER_ID='.$_GET['USER_ID'].'" class="usersResp">'.get_label('assign_responsability').'</a>','modify');
	$modifyToolBar->SetLeftLinks('<a href="assign_uo.php?USER_ID='.$_GET['USER_ID'].'" class="usersUo">Assegna Unità Organizzative</a>','modify');
}
include(LIB_PATH."/classes/Form/manageDbtable.inc");
include ("pagefooter.inc");
?>