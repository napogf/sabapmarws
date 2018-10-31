<?php
include_once 'login/configsess.php';
class PECSendMailer extends PHPMailer {
    /**
     * Save email to a folder (via IMAP)
     *
     * This function will open an IMAP stream using the email
     * credentials previously specified, and will save the email
     * to a specified folder. Parameter is the folder name (ie, Sent)
     * if nothing was specified it will be saved in the inbox.
     *
     * @author David Tkachuk <http://davidrockin.com/>
     */
    protected  $imapHost;
    protected  $imapPort;
    protected  $sentFolder;


	public function __construct($exceptions = false) {
		// TODO: Auto-generated method stub
	    $this->exceptions = (boolean)$exceptions;
	    $this->setLanguage('it');
	    $this->SMTPDebug = 0;                               // Enable verbose debug output

	    $this->isSMTP();                                      // Set mailer to use SMTP
	                                     // Specify main and backup SMTP servers

	    $this->SMTPAuth = true;                               // Enable SMTP authentication
	    $this->SMTPSecure = 'tls';
	    $this->Host = PEC_SMTPHOST;
	    $this->Port = PEC_SMTPPORT;
	    $this->Username = PEC_USERNAME;                 // SMTP username
	    $this->Password = PEC_PASSWORD;                           // SMTP password
                                  // TCP port to connect to
	    $this->imapHost = PEC_HOSTNAME;
	    $this->imapPort = PEC_HOSTPORT;

	    return $this;

	}
    /*
     * Id messaggio trasmesso viene salvato nel file daticert.xml allegato alla ricevuta di consegna
     */
    public function copyToFolder($folderPath = null) {
        $message = $this->MIMEHeader . $this->MIMEBody;
        $path = 'INBOX' . (isset($folderPath) && !is_null($folderPath) ? '.'.$folderPath : ''); // Location to save the email
        $imapStream = imap_open('{' . $this->imapHost . ':' . $this->imapPort . '/imap/ssl/novalidate-cert}' . $path , $this->Username, $this->Password);

        imap_append($imapStream, '{' . $this->imapHost . ':' . $this->imapPort . '/imap/ssl/novalidate-cert}' . $path, $message);
        imap_close($imapStream);
    }

    public function getMailHeader(){

        return $this->mailHeader;
    }

}