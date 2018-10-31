<?php
/*
 * Created on 18/mag/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
//include "login/autentication.php";

include(getcwd().'/login/configsess.php');
include(getcwd().'/inc/dbfunctions.php');

if ($handle = opendir('./dacaricare')) {
	while (false !== ($filename = readdir($handle))) {
		if ($filename != "." && $filename != "..") {
			$fileInfo=pathinfo($filename);
			if(strtoupper($fileInfo['extension'])=='ZIP') {
				print('Caricamento File '.$filename.'</br>'."\n");
				$fileToParse = new loadXml($filename);
				$fileToParse->loadPratiche();
			}
		}
	}
}
