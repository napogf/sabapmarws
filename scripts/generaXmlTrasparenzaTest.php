<?php
include ('../login/configsess.php');

	$docGenerate = new XmlTrasparenza();
    $docGenerate->setDebug(true);
	$docGenerate->testMail();





