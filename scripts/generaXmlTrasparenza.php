<?php
include ('../login/configsess.php');

	$docGenerate = new XmlTrasparenza();
	if($argv[1] == 'DEBUG'){
		$docGenerate->setDebug(true);
	}
	$docGenerate->generateZipFile();





