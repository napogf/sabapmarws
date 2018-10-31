<?php
/*
 * Created on 11/09/2015
 *
 * djsendPec.php
*/

include "login/autentication.php";
$db = Db_Pdo::getInstance();
$result = array(
	'status' => 'error',
    'post' => $_POST,
    'get' => $_GET,
    'logs' => [],
);


try {
    $db->beginTransaction();
    $mail = new PECSendMailer();


    $mail->CharSet = 'UTF-8';
    $testName = strpos($_SERVER['HTTP_HOST'], 'localnet') ? 'Giacomo fonderico' : 'Open Source Solutions sas';
    $_POST['ToAddress'] = str_replace(';', ',', $_POST['ToAddress']);
    $destinatari = explode(',', $_POST['ToAddress']);
    if(is_array($destinatari)){
        foreach ($destinatari as $indirizzo) {
            $result['logs'][] = 'ToAddress ' . $indirizzo;
            $mail->addAddress($indirizzo);     // Add a recipient
        }
    } else {
        $mail->addAddress($_POST['ToAddress']);
    }
    $mail->From = $_SESSION['config']['PEC_USERNAME'];
    $mail->FromName = $_SESSION['config']['PEC_NAME'];

    if(!empty(trim($_POST['CCAddress']))){
        $ccAddress = explode(',', $_POST['CCAddress']);
        if(is_array($ccAddress)){
            foreach ($ccAddress as $indirizzo) {
                $result['logs'][] = 'CCAddress ' . $indirizzo;
                $mail->addCC($indirizzo);     // Add a recipient
            }
        } else {
            if($_POST['CCAddress'] > ''){

                $mail->addCC($_POST['CCAddress']);
            }
        }
    }

    $mail->WordWrap = 80;


    if(file_exists(TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml')){
        $suapEnteDom = new DOMDocument();
        $suapEnteDom->loadXML(file_get_contents(TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml'));
        $suapEnteXpath = new DOMXPath($suapEnteDom);
        if($testoComunicazione = $suapEnteXpath->query('//ns2:cooperazione-suap-ente/intestazione/testo-comunicazione')){
            $pratica = $db->query('SELECT numeroregistrazione, dataregistrazione FROM pratiche where pratica_id = :pratica_id',
                [':pratica_id' => $_GET['praticaId']])->fetch();
            $testoComunicazione->item(0)->nodeValue = 'Ns protocollo: ' . $pratica['numeroregistrazione'] . 'del ' . $pratica['dataregistrazione'] . "\n" .
                $_POST['bodyMessage'];
        }

        $suapEnteDom->save(TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml');

        $mail->addAttachment(TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml','entesuap.xml');
        $result['logs'][] = 'SUAPXML ' . TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml';
    } else {
        $result['logs'][] = 'Non allegato ' . TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml';
    }
    $allegati = [];
    if(isSet($_POST['attachment'])){
        foreach ($_POST['attachment'] as $attach) {
            $attachment = $db->query('SELECT 
                description, 
                concat(upload_id, "_",filename) as file 
                FROM uploads WHERE upload_id = :upload_id',[
                    ':upload_id' => $attach,
            ])->fetch();
            $result['logs'][] = 'attach file ' . FILES_PATH . DIRECTORY_SEPARATOR .$attachment['file'];      // Add attachments
            if(file_exists(FILES_PATH . DIRECTORY_SEPARATOR .$attachment['file'])){
                $mail->addAttachment(FILES_PATH . DIRECTORY_SEPARATOR .$attachment['file']);      // Add attachments
                $allegati[] = $attachment;
            } else {
                $result['logs'][] = 'non esiste file ' . FILES_PATH . DIRECTORY_SEPARATOR .$attachment['file'];      // Add attachments
            }

        }
    }

    if(file_exists(TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_segnatura.xml')){
        $pratica = new Pratica();
        $pratica->setId($_GET['praticaId']);
        if($pratica->getSegnatura($allegati)){
            $mail->addAttachment(TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_segnatura.xml','Segnatura.xml');
        }
    }

    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(false);                                  // Set email format to HTML

    $mail->Subject = $_POST['Subject'];
    $mail->Body    = $_POST['bodyMessage'];
    $mail->AltBody = $_POST['bodyMessage'];

    if(!$mail->send()) {
        throw new Exception($mail->ErrorInfo);
    }
    // $mail->copyToFolder("Trash");

    $db->query('UPDATE pratiche SET uscita = now(), mail_sent_id = :mail_sent_id
            WHERE pratica_id = :pratica_id',array(
    	':pratica_id' => $_GET['praticaId'],
        ':mail_sent_id' => $mail->getLastMessageID(),
    ));
    if(file_exists(TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml')){
        unlink( TMP_PATH . DIRECTORY_SEPARATOR . $_GET['praticaId'] . '_entesuap.xml');
    }

    $result['status'] = 'success';
    $result['message'] = $mail->getMailHeader();
    $result['test'] = $mail->getLastMessageID();

    $db->commit();
} catch (phpmailerException $e) {
    $db->rollBack();
    $result['status'] = 'error';
    $result['message'] = $e->getMessage();
    $result['form'] = $_POST;
} catch (Exception $e) {
    $db->rollBack();
    $result['status'] = 'error';
    $result['message'] = $e->getMessage();
    $result['form'] = $_POST;
}

print(json_encode($result));
exit;
