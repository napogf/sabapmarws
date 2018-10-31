<?php

$showTable='N';
class MyDbForm extends formExtended {
	/**
     * Constructor
     * @access protected
     */



}

$dbTable='DIR_LINKS';
$recallPage='?dbTable='.$dbTable.'&wkDirId='.$wkDirId;

$ManagedTable = new MyDbForm($dbTable,$_SESSION['sess_lang']);
//

$ManagedTable->setAfterInsertLocation('/cmsPage.php?expandedFolder='.$wkDirId);
$ManagedTable->SetafterUpdateLocation('/cmsPage.php?expandedFolder='.$wkDirId);

if (isSet($formTitleAdd)) $ManagedTable->SetFormTitle($ManagedTable->GetFormTitle().' - '.$formTitleAdd);

$ManagedTable->SetFormMode($mode,stripslashes($dbKey));

if (isSet($recallPage)) {
	$_action='&'.substr($recallPage,1);
	$ManagedTable->AddFormActionParameter($_action);
	$recallPage = $recallPage.'&';
} else {
	$recallPage = '';
}
include('pageheader.inc');
print('<div class="formContainer">'."\n");
$ManagedTable->ShowForm();
print('</div>'."\n");

include('pagefooter.inc');
?>