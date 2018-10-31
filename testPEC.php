<?php
include_once 'login/configsess.php';
class Mailer extends PHPMailer {
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
    public $imapHost;
    public $imapPort;


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
}





$mail = new Mailer;
$mail->setLanguage('it');
$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP

$mail->Host = PEC_SMTPHOST;                                 // Specify main and backup SMTP servers

$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = PEC_USERNAME;                 // SMTP username
$mail->Password = PEC_PASSWORD;                           // SMTP password
$mail->SMTPSecure = strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'tls' : 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = PEC_SMTPPORT;                                    // TCP port to connect to

$mail->imapHost = PEC_HOSTNAME;
$mail->imapPort = PEC_HOSTPORT;


r(PEC_SMTPHOST,false);
r(PEC_USERNAME,false);
r(PEC_PASSWORD,false);
r(PEC_SMTPPORT,false);
r(PEC_HOSTNAME,false);
r(PEC_HOSTPORT,false);

$mail->From = strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'giacomo.fonderico@gmail.com' : 'mbac-sabap-vr@mailcert.beniculturali.it';
$mail->FromName = 'PHP Mailer';
$testAddress = strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'giacomo.fonderico@opensourcesolutions.it' : 'opensourcesolutions@pec.it';
r($testAddress,false);
$testName = strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'Giacomo fonderico' : 'Open Source Solutions sas';
$mail->addAddress($testAddress, $testName);     // Add a recipient
// $mail->addAddress('ellen@example.com');               // Name is optional
// $mail->addReplyTo('info@example.com', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->addAttachment(ROOT_PATH . DIRECTORY_SEPARATOR .'/immagini/headerImage.jpg');      // Add attachments

// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Prova PEC';
$mail->Body    = 'Prova di invio Pec <b>in grassetto!</b>';
$mail->AltBody = 'Text version del body';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $mail->copyToFolder("INVIATE");
    echo 'Message has been sent';
}