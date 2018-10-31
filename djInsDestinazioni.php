<?php
/*
 * Created on 04/mar/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");

$_SESSION['paneSelected'] = 'destinatari';
class myhtmlETable extends htmlETable {

}

try {
    $insDestinazioneQuery=' insert into arc_destinazioni (' . implode(',',array_keys($_GET)) . ') 
        values (:' . implode(',:', array_keys($_GET)) . ')';

    $indirizzoArray = $_GET;

    unset($indirizzoArray['PRATICA_ID']);
    $indirizzo = new Indirizzo(array_change_key_case($indirizzoArray,CASE_LOWER));
    if(!$errori = $indirizzo->getError()){
        $indirizzo->save();
    } else {
        throw new Exception(implode('|',$errori));
    }

    $params = array();
    foreach ($_GET as $key => $value) {
        $params[':' . $key] = $value;
    }

    $result = [
        'status' => 'success',
        'message' => null
    ];
    Db_Pdo::getInstance()->query($insDestinazioneQuery,$params);

} catch (Exception $e) {
    $result['status'] = 'error';
    $result['message'] = $e->getMessage();
}
echo json_encode($result);

