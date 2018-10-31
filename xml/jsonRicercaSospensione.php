<?php
/*
 * Created on 26/giu/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "../login/autentication.php";
 require_once("dbfunctions.php");


$sql = 'SELECT DISTINCT concat(date_format(pr.dataregistrazione,"%Y") , "-" , pr.numeroregistrazione , ": " , pr.oggetto) as DESCRIPTION,
                arc_sospensioni.sospensione_id as ITEM FROM arc_sospensioni
        RIGHT JOIN pratiche pr ON (pr.pratica_id = arc_sospensioni.pratica_id)
        WHERE arc_sospensioni.pratica_id is not null
        AND arc_sospensioni.protoentrata IS NULL
        AND pr.dataregistrazione IS NOT NULL
        AND pr.numeroregistrazione IS NOT NULL
        AND pr.oggetto IS NOT NULL ';

if ($_GET['DESCRIPTION']>'' and $_GET['DESCRIPTION']<> '*'){
    $sql .= ' HAVING ( DESCRIPTION REGEXP "' . str_replace('*', '', $_GET['DESCRIPTION']) . '")';
}

$sql .= ' ORDER BY 2 DESC';

if($_GET['count'] > '' and $_GET['count']<>'Infinity'){
	$limitSql= ' LIMIT '.$_GET['count'].' OFFSET '.$_GET['start'].' ';
} else {
	$limitSql = '';
}


$sql .= $limitSql;


$jsonArray=array("identifier"=>'ITEM',"label"=>'DESCRIPTION',"items"=>Db_Pdo::getInstance()->query($sql)->fetchAll());


header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
 	print(json_encode($jsonArray));

 	exit;
