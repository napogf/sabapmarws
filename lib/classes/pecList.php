<?php
/*
 * Created on 19/gen/2013
 *
 * pecList.php
*/
// require_once 'PEAR.php';
// require_once 'Net/URL.php';
// require_once('Mail/IMAPv2.php');
// require_once('Mail/mimeDecode.php');
// //require_once('Mail/IMAP/Debug/Debug.php');
// require_once('Mail/IMAPv2/ManageMB/ManageMB.php');


class pecList {

	public function __construct()
	{}

	protected function __clone()
	{}

	protected $_mailMessages;

	private $_errors;

	private static $__instance = null;

	protected $_pecHostname;
	protected $_hostPort;
	protected $_userName;
	protected $_password;
	protected $connection;


	public function setHostname($hostname){
		$this->_pecHostname = $hostname;

		return $this;
	}
	public function setPort($port) {
		$this->_hostPort = $port;

		return $this;
	}
	public function setUsername($username) {
		$this->_userName = $username;

		return $this;
	}
	public function setPassword($password) {
		$this->_password = $password;

		return $this;
	}


	public function closeConnection()
	{
		$sess = $this->getSession();
		$sess->connection = null;

		return $this;
	}

	public static function getInstance()
	{

		if (self::$__instance === null) {
			 self::$__instance = new self;
		}

		return self::$__instance;
	}

	protected $_session;

	protected function getSession()
	{
		if ($this->_session === null) {
			$this->_session = new Session_Namespace(__CLASS__);
		}

		return $this->_session;
	}
	public function hasConnection()
	{
		$pecMail = $this->getSession();

		return !empty($pecMail->connection);
	}

	public static function setInstance(pecList $instance)
	{
		self::$__instance = $instance;

		return self::$__instance;
	}


	public function connect(){
		if(!$this->hasConnection()){
			$this->_pecHostname = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_HOSTNAME"')->fetchColumn();
			$this->_hostPort = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_HOSTPORT"')->fetchColumn();
			$this->_userName = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_USERNAME"')->fetchColumn();
			$this->_password = Db_Pdo::getInstance()->query('select valore from sys_config where chiave = "PEC_PASSWORD"')->fetchColumn();
			define('PEC_USERNAME', $this->_userName);
			define('PEC_PASSWORD', $this->_password);
			define('PEC_HOSTNAME', $this->_pecHostname);
			define('PEC_HOSTPORT', $this->_hostPort);


			$pecMail = $this->getSession();

			$base_uri = $this->_userName.':'.$this->_password.'@'.$this->_pecHostname;
			$connection = 'imaps://'.$base_uri.':'.$this->_hostPort.'/INBOX';
			if ($connection <> $pecMail->connection){
				$pecMail->connection = $connection;
				$this->refresh();
			}
		}

		return $this;
	}

	public function connectArchiviati(){
		if(!$this->hasConnection()){
			$pecMail = $this->getSession();

			$base_uri = $this->_userName.':'.$this->_password.'@'.$this->_pecHostname;
			$connection = 'imaps://'.$base_uri.':'.$this->_hostPort.'/INBOX.archiviati';
			if ($connection <> $pecMail->connection){
				$pecMail->connection = $connection;
				$this->refresh();
			}
		}

		return $this;
	}


	public function getErrors() {
		if (is_array($this->_errors)){
			return $this->_errors;
		}

		return false;
	}
	public function getMail(){
		$pecMail = $this->getSession();

		return $pecMail->messages;
	}
	public function getMsgs(){
		return $this->_mailMessages;
	}

	public function setMailProtocollata($mailId)
	{
		$pecSess = $this->getSession();

		unset($pecSess->messages[$mailId]);

		return $this;
	}

	public function getMessage($mid){
		$msg =& new Mail_IMAPv2_ManageMB($pecSess->connection);
		$pecSess = $this->getSession();
		$messageStream = null;
		// Open up a mail connection
		if (!$msg->connect($pecSess->connection)) {
			$this->_errors[] =  "Errore: Impossibile connetersi al server di P.E.C..";
			$this->_errors[] =  $msg->alerts();
			$this->_errors[] =  $msg->errors();
			r($this->_errors);
		} else {
			$h1 = $msg->getRawHeaders($mid);
			$b1 = $msg->getRawMessage($mid);
			$messageStream = $h1 . "\n" . $b1;
		}
		$msg->close();

		return $messageStream;
	}

	protected function getBodyMessage($msg, $mid, $type = 'at'){
		$body = null;
		foreach ((array) $msg->msg[$mid][$type]['pid'] as $i => $inid)
		{
			r($msg->msg[$mid][$type]['ftype'][$i],false);
			r($msg->msg[$mid][$type]['fname'][$i],false);
			r($msg->msg[$mid][$type]['fsize'][$i],false);



            if(preg_match('|rfc|',$msg->msg[$mid][$type]['ftype'][$i])){
	            // $msgBody = $msg->getBody($mid, $msg->msg[$mid][$type]['pid'][$i], 0 , $msg->msg[$mid][$type]['ftype'][$i]);
	            // $Parser = new MimeMailParser();
	            // $Parser->setText($msgBody['message']);
	            // $body = $Parser->getMessageBody('text') ."\n";
	            // $Parser->__destruct();
				$body = 'Messaggio contenuto negli allegati! ';
	            // break;
            } elseif (preg_match('|txt|',$msg->msg[$mid][$type]['ftype'][$i])) {
				$msgArray = $msg->getBody($mid, $msg->msg[$mid][$type]['pid'][$i], 0 , $msg->msg[$mid][$type]['ftype'][$i]);
				$body = $msgArray['message'];
				break;
			}
		}

		return $body;
	}

	protected function fix_text($strHead){

		r($strHead = iconv('UTF-8' ,'ISO-8859-1', $strHead),false);
		r(mb_detect_encoding($strHead),false);
		return $strHead;
	}

	public function refresh(){

		$msg =& new Mail_IMAPv2();
		$pecSess = $this->getSession();
		// Open up a mail connection
		if (!$msg->connect($pecSess->connection)) {
			$this->_errors[] =  "Errore: Impossibile connetersi al server di P.E.C..";
			$this->_errors[] =  $msg->alerts(false);
			$this->_errors[] =  $msg->errors(false);
			r($this->_errors);
		} else {

			$msgcount = $msg->messageCount();
			$this->_mailMessages = array();
			// echo "PEAR :: Mail_IMAP {$msg->mailboxInfo['folder']}: ($msgcount) messages total.\n<br/>";
			$Parser = new MimeMailParser();
			$params['include_bodies'] = false;
			$params['decode_bodies']  = false;
			$params['decode_headers'] = true;

			if ($msgcount > 0) {
				for ($mid = 1; $mid <= $msgcount; $mid++) {
					$decoder = new Mail_mimeDecode($msg->getRawHeaders($mid));
					$structure = $decoder->decode($params);
					// print($structure->headers['subject'].'<br>');
					if($structure->headers['message-id'] > ''){
						$praticaResult = dbselect('select pratiche.numeroregistrazione, pratiche.dataregistrazione from pratiche
							right join arc_pratiche_pec on (arc_pratiche_pec.pratica_id = pratiche.pratica_id)
							where arc_pratiche_pec.mail_hash = sha1("'.
							$structure->headers['message-id'].'")');
						if (!$praticaResult){
							// se l'id non è stato protocollato visualizzo la mail
					        // Recupero il Body del messaggio dalla part = inline
					        $body = null;
							$this->_mailMessages[$structure->headers['message-id']] = array(
								'mid' => $mid,
								'id' => $structure->headers['message-id'],
								'Data' => date('d/m/Y',strtotime($structure->headers['date'])),
								'Mittente'=> $structure->headers['from'],
								'Oggetto' => $structure->headers['subject'],
								// 'Messaggio' => $body,
							);
						}
					}
				}
			}
		//
		}
		$pecSess->messages = $this->_mailMessages;

		// Close the stream
		$msg->close();

		return $this;
	}

	public function archiviaMail(array $mailToMove){

		$pecSess = $this->getSession();
		$msg =& new Mail_IMAPv2_ManageMB($pecSess->connection);
		$msg->manageMail('move', $mailToMove, 'INBOX.archiviati');
		$msg->expunge();
		$msg->close();

		return $this;
	}


	public function ripristinaMail(array $mailToMove){

		$pecSess = $this->getSession();
		$msg =& new Mail_IMAPv2_ManageMB($pecSess->connection);
		$msg->manageMail('move', $mailToMove, 'INBOX');
		$msg->expunge();
		$msg->close();

		return $this;
	}

}
