<?php
/*
 * Created on 11/giu/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .  "login/configsess.php";
$db = Db_Pdo::getInstance();

$fileToRead = ROOT_PATH . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'esempio_tabella_procediment.csv';

if (($handle = fopen($fileToRead, "r")) !== FALSE) {
    while (($data = fgetcsv($handle,null,';')) !== FALSE) {
        if($data[1] > ''){
            $foundPrefix = preg_match('/(\d+).\(ESPI\)(.*)/', $data[2],$match);
            $db->query('insert into arc_modelli_classifica (codice, classificazione , descrizione) values (:codice, :classificazione , :descrizione)',[
                ':codice' => (integer) $match[1],
                ':classificazione' => $data[1],
                ':descrizione' => $foundPrefix ? trim($match[2],' -') : trim($data[2]),
            ]);
        }
    }
    fclose($handle);
}