<?php
/*
 * Created on 26/giu/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "../login/autentication.php";


$sql = 'select * from arc_modelli_classifica amc 
        right join arc_modelli am on (amc.classificazione = am.CLASSIFICAZIONE) 
        where am.MODELLO = :modello';



$params = [ ':modello' => $_GET['modello']];


$jsonArray=array("identifier"=>'id',"label"=>'description',"items"=>Db_Pdo::getInstance()->query($sql,$params)->fetchAll());


header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
 	print(json_encode($jsonArray));

 	exit;
