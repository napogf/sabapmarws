<?php
/*
 * Created on 01/ott/2012 djDisplayPec.php
 */
include "login/autentication.php";

$dispEmlFilesQuery = 'select * from arc_pratiche_pec ' . 'where pec_id = ' . $_GET['PEC_ID'];

if (! $emlResult = dbselect($dispEmlFilesQuery)) {
    print('<div class="DbFormMessage">Attenzione! File non trovato contattare l\'assistenza</div>');
} else {
    $pecFile = PEC_PATH . '/' . $emlResult['ROWS'][0]['PEC_ID'] . "_pec_" . $emlResult['ROWS'][0]['MAIL_HASH'] . '.eml';
    if (file_exists($pecFile)) {
        $Parser = new displayMail();
        $Parser->setText(file_get_contents($pecFile));
        $attachments = $Parser->getAttachments();

        foreach ($attachments as $attachment) {
            if ($attachment->filename == 'postacert.eml') {
                $newParser = new displayMail();
                $newParser->setText($attachment->content);
                $attachments = $Parser->getAttachments();
                break;
            }
        }

        $content = $attachments[$_GET['INDEX']]->content;
        $file_ext = strtolower($attachments[$_GET['INDEX']]->extension);
//         if($file_ext == 'p7m'){
//         	// salvo in file temporaneo
//         	file_put_contents(TMP_PATH . DIRECTORY_SEPARATOR . $attachments[$_GET['INDEX']]->filename, $content);
//             // estraggo il pdf dal file firmato ed il nome lo cambio in .pdf
//             $attchFile = new SplFileObject(TMP_PATH . DIRECTORY_SEPARATOR . $attachments[$_GET['INDEX']]->filename);
//             $content = $Parser->getP7mContent($attchFile);
//             unlink($attchFile);
//             $fname = str_ireplace('.p7m', '', $attachments[$_GET['INDEX']]->filename);
//         } else {
            $fname = $attachments[$_GET['INDEX']]->filename;
//         }
		file_put_contents(TMP_PATH . DIRECTORY_SEPARATOR . $fname , $content);
		ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fname.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
//         header('Pragma: public');
        Header('Content-Length: ' . filesize(TMP_PATH . DIRECTORY_SEPARATOR . $fname));
        header("Pragma: no-cache");
        readfile(TMP_PATH . DIRECTORY_SEPARATOR . $fname);
        // dbupdate("insert into link_audit (user_id, link_id, filename, audit_date) values ('$sess_uid', '$wk_link_id', '$fname', NOW())");
        // header("Cache-control: private");
        unlink(TMP_PATH . DIRECTORY_SEPARATOR . $fname);
    } else {
        r($pecFile);
    }
}
exit();