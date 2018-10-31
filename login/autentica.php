<?php
include "configsess.php";
include "dbfunctions.php";
function checkUser($username,$password)
{


	$db = Db_Pdo::getInstance();
	$res = $db->query('SELECT * from sys_users where user_name = :user_name and password = :password',
						array(
						':user_name' => $username,
						':password' => $password,
						))->fetch();
	if (($res['PASSWORD']==$password) and ($res['ENABLED']=='Y')) {
	    return $res;
	}
	return null;
}

$result=checkUser($_POST['username'],$_POST['password']);

if ($result <> null) $AUTENTICATO=true;

if ($AUTENTICATO==true)
 {

	session_start();

	$_SESSION['AUTENTICATO']=$AUTENTICATO;

	$_SESSION['ip_sess'] =getenv('REMOTE_ADDR');

	if (isSet($dojoVersion)){
		$_SESSION['dojoVersion'] = $dojoVersion;
	}

	$_SESSION['sess_style'] = $css_style;

	$_SESSION['sess_time'] = time()+$sess_time_limit;
	$_SESSION['sess_user'] = $_POST['username'];

	$_SESSION['sess_lang'] = $result['LANGUAGE_ID'];


	$_SESSION['sess_uid'] = $result['USER_ID'];
	$_SESSION['sess_name'] = $result['USER_NAME'];
	$_SESSION['sess_mainpage'] = $result['MAIN_PAGE'];


	$_SESSION['sess_dirUpload'] = $dir_upload;
	$_SESSION['sess_audit_login'] = $sess_audit_login;

        if ($_SESSION['sess_audit_login'] == 'Y') {
            // $dbconn = ociplogon($user, $pwd, $fontedati);       // connessione permanente da distruggere al termine di sessione
            dbupdate("insert into log_audit (log_user_id, user_name ,log_ip_address, log_sess_name, log_date) values
                                                    ('" .$_SESSION['sess_uid']. "', '".$_SESSION['sess_name']."' , '".$_SESSION['ip_sess']."', '".session_id()."', now() )",false);
        }
        if (IsSet($wk_dir_id) and IsSet($Livello)) {
           $index_page='/index.php?wk_dir_id='.$wk_dir_id.'&Livello='.$Livello;
           @header ("Location: $index_page");
        } else {
           @header ("Location: $index_page");
        }
 }
else	{
    $ip_sess=getenv(REMOTE_ADDR);
    $query='insert into log_audit (bad_user_name, bad_password, log_ip_address, log_date) values (\''.$_POST['username'].'\', \''.$_POST['password'].'\', \''.$ip_sess.'\', now())';
	if ($sess_audit_login=='Y') {
	    dbupdate($query, false);
	}
    @header ("Location: $baddata");
}