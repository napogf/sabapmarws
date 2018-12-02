<?php
include 'login/configsess.php';

error_reporting(E_ALL);




$pecHostname = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_HOSTNAME"')->fetchColumn();

$hostPort = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_HOSTPORT"')->fetchColumn();
$userName = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_USERNAME"')->fetchColumn();
$password = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_PASSWORD"')->fetchColumn();

r($pecHostname,false);


		$base_uri = $userName.':'.$password.'@'.$pecHostname;
		$connection = 'imaps://'.$base_uri.':'.$hostPort.'/';


$connect = '{' .$pecHostname. ':993/imap/ssl/novalidate-cert}INBOX.archiviati';
r($connect,false);
r($userName,false);


$mailbox = imap_open($connect, $userName, $password);
r(imap_list($mailbox,'{' .$pecHostname. ':993/imap/ssl/novalidate-cert}','*'),false);
r(imap_last_error(),false);
r(imap_alerts(),false);
$MC = imap_check($mailbox);
r($MC->Nmsgs,false);
$result = imap_fetch_overview($mailbox,"1:{$MC->Nmsgs}",0);
foreach ($result as $overview) {
    echo "#{$overview->msgno} ({$overview->date}) - From: {$overview->from} {$overview->subject}\n";
    $messageHeader = imap_fetchheader($mailbox, $overview->msgno);
    r($messageHeader,false);
}
imap_close($mailbox);



