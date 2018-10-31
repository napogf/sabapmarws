<?php

class MailSender extends PHPMailer {


	public function __construct($exceptions = false) {
		// TODO: Auto-generated method stub
	    $this->exceptions = (boolean)$exceptions;
	    $this->setLanguage('it');
	    $this->SMTPDebug = 0;                               // Enable verbose debug output

	    $this->isSMTP();                                      // Set mailer to use SMTP

	    $this->setFrom(SMTPSENDER, 'SABAP-VR');
	    $this->addReplyTo(SMTPSENDER, 'SABAP-VR');

	    $this->SMTPAuth = false;                               // Enable SMTP authentication

	    $this->Host = SMTPHOST;
	    $this->Port = 25;


	    return $this;

	}


}