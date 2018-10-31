<?php
include 'login/configsess.php';
error_reporting(E_ALL);

$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

$opts = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
    ),
    'https' => array(
        'curl_verify_ssl_peer' => false,
        'curl_verify_ssl_host' => false,
    )
);

$streamContext = stream_context_create($opts);

$test =file_get_contents("https://10.199.3.4/WSProtEspiVX/wsespiaspvx.asmx?WSDL", false, $streamContext);
r($test,false);


$ws = new EspiWS();

$test = $ws->testWs('Test_MTA_STA');


r($test);


