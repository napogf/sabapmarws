<?php
/*
 * Created on 20-gen-2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include "login/autentication.php";

$db = Db_Pdo::getInstance();
$praticaId = (isSet($_GET['PRATICA_ID']) ? $_GET['PRATICA_ID'] : $_POST['PRATICA_ID']);
/*
 *
 */
if(isSet($_POST['protocollazione']) and $_POST['protocollazione'] > ''){
    if($praticaInUscita = formEntrata::generaPraticaUscita($praticaId)){
        header('Location: ' . $_SERVER['PHP_SELF'] . '?PRATICA_ID=' . $praticaInUscita);
        exit;
    }
}

if(isSet($_GET['sospensione']) and ($_GET['sospensione'] == 'Y')){
        if($praticaInUscita = formEntrata::generaPraticaUscita($praticaId,'S')){
            header('Location: ' . $_SERVER['PHP_SELF'] . '?PRATICA_ID=' . $praticaInUscita);
            exit;

        } else {
            exit;
        }
}

if(isSet($_POST['chiusura']) and $_POST['chiusura'] == 'Chiudi Procedimento'){
    formUscita::chiudiPratica($praticaId);
}

class myhtmlETable extends htmlETable {

}

$dbKey = isset($_GET['dbKey']) ? $_GET['dbKey'] : ' where PRATICA_ID=' . $praticaId;
$tipologia = Db_pdo::getInstance()->query('SELECT TIPOLOGIA FROM pratiche '. $dbKey)->fetchColumn();

if($tipologia == 'U'){
    $ManagedTable = new formUscita('PRATICHE', $_SESSION['sess_lang']);
} else {
    $ManagedTable = new formEntrata('PRATICHE', $_SESSION['sess_lang']);
}


$del_message = get_label('del_message');

//$ManagedTable->setAfterUpdateLocation('praticheStatus.php');

$ManagedTable->setAfterUpdateLocation('editPratica.php?PRATICA_ID=' . $praticaId );

$ManagedTable->SetFormMode("modify", stripslashes($dbKey));

include ('pageheader.inc');
print('<script type="text/javascript" src="javascript/djEditPratica.js?' . filemtime(ROOT_PATH . DIRECTORY_SEPARATOR . 'javascript/djEditPratica.js') .  '"></script>');

$modelloQuery = $ManagedTable->_FormFields['MODELLO']->GetValue() > '' ? ' or modello = ' . $ManagedTable->_FormFields['MODELLO']->GetValue() : '';

print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
'url="xml/jsonSql.php?sql=select DOC_ID, DESCRIPTION, MODELLO from arc_documenti where modello is null ' . $modelloQuery . '" ' .
'jsId="sDocumenti" ' .
'></div>');




    $ManagedTable->editMenu();

	$ManagedTable->ShowForm();

print('<iframe id="printElement" style="display:none; height: 0px; width: 0px; position: absolute"></iframe>');
include ('pagefooter.inc');