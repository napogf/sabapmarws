<?php
/**
 * Created by PhpStorm.
 * User: giacomo
 * Date: 06/11/17
 * Time: 14.54
 */

include 'login/configsess.php';
/**
 * character of unicode symbol convert to unicode value
 * @param string $symbol
 * @param integer $bytes
 * @return integer $ascii
 */
function getUnicode($symbol,$bytes = 1)
{
    $offset = 0;
    $highChar = substr($symbol, $offset ,1);
    $ascii = ord($highChar);
    if ($bytes > 1) {
        $code = ($ascii) & (2 ** (7 - $bytes) - 1);
        for ($i = 1;$i<$bytes;$i++) {
            $char = substr($symbol, $offset + $i, 1);
            $code =  ($code << 6) | (ord($char) & 0x3f);
        }
        $ascii = $code;
    }
    return $ascii;
}

/**
 * get Unicode symbol bytes number
 * @param string $symbol
 * @return integer $bytesNumber
 */
function getBytesNumber($symbol)
{
    $ascii = ord($symbol);
    $bytesNumber = 1;
    if ($ascii > 0x7f) {
        switch ($ascii&0xf0) {
            case 0xfd:
                $bytesNumber = 6;
                break;
            case 0xf8:
                $bytesNumber = 5;
                break;
            case 0xf0:
                $bytesNumber = 4;
                break;
            case 0xe0:
                $bytesNumber = 3;
                break;
            case 0xd1:
            case 0xd0:
                $bytesNumber = 2;
                break;
        }
    }
    return $bytesNumber;
}

$str = Db_Pdo::getInstance()->query('select description from arc_modelli where CLASSIFICAZIONE = :cl',[
    ':cl' => '34.10.03'
    ])->fetchColumn();
echo $str . PHP_EOL;
echo mb_detect_encoding($str) . PHP_EOL;
var_dump($str);
echo 'stringa ' . count_chars($str) . PHP_EOL;
var_dump(str_split($str));
exit;
foreach (str_split($str) as $char){
    echo $char . PHP_EOL;
    echo getUnicode($char) . PHP_EOL;

}
