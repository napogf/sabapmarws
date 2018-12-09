<?php
/*
 * Created on 26/giu/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "../login/autentication.php";


Db_Pdo::getInstance()->exec('SET @posizione = 0');
$sql = 'SELECT @posizione:=@posizione+1 as ITEM,
            trim(concat(ifnull(nome,""), " ", cognome," ",ifnull(titolo,""))) as DESCRIPTION,
                titolo, nome, cognome, toponimo, cap, comune, provincia, localita, telefono, fax, codicefiscale, email, pec
            FROM arc_mittenti';
if($_GET['DESCRIPTION'] !== '**'){
    $sql .=  ' WHERE ( cognome REGEXP :description
                    OR nome REGEXP :description
                ) ORDER BY 1';
    $params = [
        ':description' => ($_GET['DESCRIPTION'] == '**' ? '*' : str_replace('*','',$_GET['DESCRIPTION'])),
    ];
} else {
    $params = null;
}

if($_GET['count'] > '' and $_GET['count']<>'Infinity'){
	$limitSql= ' LIMIT '.$_GET['count'].' OFFSET '.$_GET['start'].' ';
} else {
	$limitSql = '';
}


$sql .= $limitSql;


$jsonArray=array("identifier"=>'ITEM',"label"=>'DESCRIPTION',"items"=>Db_Pdo::getInstance()->query($sql,$params)->fetchAll());


header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
 	print(json_encode($jsonArray));

 	exit;
