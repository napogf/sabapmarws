<?php
/**
 *
 * @author giacomo
 *
 */
class PecMail
{
    // TODO - Insert your code here

    /**
     */

    protected $attachments;
    protected $mailer;
    protected $debugLevel = 3;

    function __construct()
    {


        $this->mailer = new PHPMailer();

        $this->mailer->setLanguage('it');
        $this->mailer->SMTPDebug = 1;                               // Enable verbose debug output
        $this->mailer->isSMTP();                                      // Set mailer to use SMTP
        $this->mailer->Host = PEC_SMTPHOST;                                 // Specify main and backup SMTP servers
        $this->mailer->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mailer->Username = PEC_USERNAME;                 // SMTP username
        $this->mailer->Password = PEC_PASSWORD;                           // SMTP password
        $this->mailer->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->mailer->Port = PEC_SMTPPORT;
        $this->mailer->From = PEC_USERNAME;
        $this->mailer->FromName = PEC_FROMNAME;
        $this->mailer->WordWrap = 80;


        return $this;
    }

    public function setData(array $data){


        return $this;
    }

    public function setAddress($address,$name=''){
        $this->mailer->addAddress($address,$name);

        return $this;
    }

    public function addCC($address){
        $this->mailer->addCC($address);

        return $this;
    }


    public function addAttachments(array $attachments){
        foreach ($attachments as $attachment){
            $this->mailer->addAttachment($attachment);
        }

        return $this;
    }
}

?>