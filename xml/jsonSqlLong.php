<?php
/*
 * Created on 26/giu/07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "../login/autentication.php";
 require_once("dbfunctions.php");
$sql = stripslashes($_GET['sql']);


function makeWhere($getArray){
	$qPrefix=' where ';
	foreach($getArray as $key=>$value){
		if($key!='sql' and $key != 'start' and $key != 'count' ){
			str_replace('*','%',$value);
			$whereResult.=$qPrefix."$key='$value' ";
			$qPrefix=' and ';
		}
	}
	return $whereResult;
}

//$sql .= makeWhere($_GET);

if ($_GET['count'] > '' and $_GET['count']<>'Infinity'){
	$limitSql= ' LIMIT '.$_GET['count'].' OFFSET '.$_GET['start'].' ';
} else {
	$limitSql = '';
}

preg_match('|select .*\, (.*) as|',$sql,$match);

 if (isSet($sql)){
	if ($_GET['DESCRIPTION']>'' and $_GET['DESCRIPTION']<> '*'){
		$sql .= ' having DESCRIPTION  regexp "'.str_replace('*','',$_GET['DESCRIPTION']).'" ';
	}


	$sql.=$limitSql;




 	if(!$result=dbselect($sql)) {
 		print('[{
					VALUE: "",
					DESCRIPTION: "-------"}
				]');
		exit;
 	}
 	foreach($result['ROWS'][0] as $key=>$value){
		if ( is_null($identifier)){
			$identifier=$key;
		} elseif (is_null($label)) {
			$label=$key;
		} else {
			break;
		}
	}

	if ($_GET['nullValue']=='Y') {
		$nullValueArray=$result['ROWS'][0];
		$firstIsKey=true;
		foreach ($nullValueArray as $key => $value){
			if ($firstIsKey){
				$nullValueArray[$key]='';
				$firstIsKey=false;
			} else {
				$nullValueArray[$key]='-----------';
			}
		}

		$arrayResult=array_merge((array)array(0 => $nullValueArray),(array)$result['ROWS']);
	} else {
		for ($i = 0; $i < $result['NROWS']; $i++) {
			foreach ($result['ROWS'][$i] as $key=>$value){
				$result['ROWS'][$i][$key]=$value;
			}
		}
		$arrayResult=$result['ROWS'];
	}
	$jsonArray=array("identifier"=>$identifier,"label"=>$label,"items"=>$arrayResult);
 } else {
     $jsonArray = array(
     	'reult' => 'error'
     );
 }
 header('Cache-Control: no-cache, must-revalidate');
 header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
 header('Content-type: application/json');
 print(json_encode($jsonArray));

 exit;