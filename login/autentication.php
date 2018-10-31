<?php
/*AutenticaDB v1.0
Leggere il file README
*/

require_once "configsess.php";
require_once('dbfunctions.php');
session_start();


if (isset($_SESSION['AUTENTICATO']) && $_SESSION['AUTENTICATO'] === true){
	$now=time();
	$ip=getenv('REMOTE_ADDR');
		
	if ($ip != $_SESSION['ip_sess'] || $now > $_SESSION['sess_time']){
		session_destroy();
		@header ("Location: $login");
		exit;
	} else {
	    if(!isset($_SESSION['config'])){
            $configArray = Db_Pdo::getInstance()->query('select chiave, valore from sys_config')->fetchAll();
            foreach ($configArray as $value) {
                $_SESSION['config'][$value['chiave']] = $value['valore'];
            }
        }
		$_SESSION['sess_time']=$now + $sess_time_limit;
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");                                                     // always modified
        header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");                          // HTTP/1.0
	}

 } else {
	session_destroy();
	@header ("Location: $login");
	exit;
 }

