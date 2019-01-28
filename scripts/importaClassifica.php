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
            $classifica_des1 = trim(substr($data[0],strpos($data[0],'-')+1));
            $classifica_des2 = trim(substr($data[2],strpos($data[2],'-')+1));
//            r($data);
            if($db->query('select * from arc_modelli where CLASSIFICAZIONE = :classifica',[
                ':classifica' => $data[1],
            ])->fetch()){
                $db->query('update arc_modelli set classifica_des = :classifica_des where CLASSIFICAZIONE = :classificazione',[
                    ':classificazione' => $data[1],
                    ':classifica_des' => $classifica_des1,
                ]);

            }
            $db->query('insert into arc_modelli_classifica (codice, classificazione , descrizione) values (:codice, :classificazione , :descrizione)',[
                ':codice' => (integer) $data[3],
                ':classificazione' => $data[1],
                ':descrizione' => $classifica_des2,
//                ':descrizione' => $foundPrefix ? trim($match[2],' -') : trim($data[2]),
            ]);
        }
    }
    fclose($handle);
}