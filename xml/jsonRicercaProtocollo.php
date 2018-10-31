<?php
/*
 * Created on 26/giu/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "../login/autentication.php";




Db_Pdo::getInstance()->exec('SET @posizione = 0');
$sql = 'select pratiche.pratica_id as ITEM, 
               concat(date_format(pratiche.dataregistrazione,"%Y"), "-", pratiche.numeroregistrazione, "-", pratiche.oggetto ) as DESCRIPTION 
        from pratiche 
        where 1 ';
if($_GET['DESCRIPTION'] !== '**'){
    $sql .=  ' AND (numeroregistrazione REGEXP :description) 
               AND (oggetto is not null)
               AND (dataregistrazione is not null)
               ';
    $params = [
        ':description' => ($_GET['DESCRIPTION'] == '**' ? '*' : str_replace('*','',$_GET['DESCRIPTION'])),
    ];
} else {
    $params = null;
}

$sql .= ' order by pratica_id desc ';



if($_GET['count'] > '' and $_GET['count']<>'Infinity'){
	$limitSql= ' LIMIT '.$_GET['count'].' OFFSET '.$_GET['start'].' ';
} else {
	$limitSql = '';
}


$sql .= $limitSql;


$items = Db_Pdo::getInstance()->query($sql,$params)->fetchAll();

$jsonArray=array('numrows' => count($items) , 'items' =>$items , 'identity' => 'ITEM');



header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
 	echo json_encode($jsonArray);

 	exit;
