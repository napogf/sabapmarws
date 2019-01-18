<?php
/*
 * Created on 11/giu/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .  "login/configsess.php";

// Windows
$attribute = 'text:name';
// Linux
// $attribute = 'text:name';
function emptyDir($tmpDir)
{
    if (file_exists($tmpDir)){
        // delete all tmp contents
        if (is_dir($tmpDir)) {
            if ($dh = opendir($tmpDir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file <> '.'  and  $file <> '..'){
                        unlink($tmpDir.'/'.$file);

                    }
                }
                closedir($dh);
            }
        }
    } else {
        // create it
        mkdir($tmpDir);
    }

    return $tmpDir;
}


$tmpDir = emptyDir(TMP_PATH . DIRECTORY_SEPARATOR . 'conversione_modelli');
$destDir = emptyDir(TMP_PATH . DIRECTORY_SEPARATOR . 'modelli_convertiti');
$styleXmlChange = file_exists(DOC_PATH . DIRECTORY_SEPARATOR . 'styles.xml');

$filesTochange = glob(DOC_PATH . DIRECTORY_SEPARATOR . '*.odt');
foreach($filesTochange as $filename){
	$tmpZipFile = TMP_PATH . DIRECTORY_SEPARATOR . "newtextXmlOO.odt";
	$zip = zip_open("$filename");
	$newZip = new ZipArchive;
	$newZipFile = $newZip->open($tmpZipFile, ZipArchive :: CREATE);
	if ($zip) {
		while ($zip_entry = zip_read($zip)) {
			$buf = null;
			$unzippedFileEntry = zip_entry_name($zip_entry);
			switch ($unzippedFileEntry) {
				case 'content.xml':
    				$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
    				$dom = new DOMDocument();
    				$dom->loadXML($buf);
    				$found = $dom->getElementsByTagNameNS("urn:oasis:names:tc:opendocument:xmlns:text:1.0", '*');
    				foreach ($found as $node) {
                        switch ($node->nodeValue) {
                            case 'Venezia':
                                $node->nodeValue = 'Genova';
                                break;
                        	case 'DEL VENETO':
                        	   $node->nodeValue = 'DELLA LIGURIA';
                        	   break;
                        }
    				}
    				$newZip->addFromString('content.xml', $dom->saveXML());
    				zip_entry_close($zip_entry);
    				break;
				case 'styles.xml':
				    if($styleXmlChange){
				        $buf = file_get_contents(DOC_PATH . DIRECTORY_SEPARATOR . 'styles.xml');
				        file_put_contents($tmpDir  . DIRECTORY_SEPARATOR .  basename(zip_entry_name($zip_entry)), $buf);
				        $newZip->addFile($tmpDir  . DIRECTORY_SEPARATOR . basename(zip_entry_name($zip_entry)), zip_entry_name($zip_entry));
				        zip_entry_close($zip_entry);
				    } else {
				        if (zip_entry_open($zip, $zip_entry, "r")) {
				            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				            file_put_contents($tmpDir  . DIRECTORY_SEPARATOR .  basename(zip_entry_name($zip_entry)), $buf);
				            $newZip->addFile($tmpDir  . DIRECTORY_SEPARATOR . basename(zip_entry_name($zip_entry)), zip_entry_name($zip_entry));
				            zip_entry_close($zip_entry);
				        }
				    }
				    break;
				default:
    				if (zip_entry_open($zip, $zip_entry, "r")) {
    					$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
    					file_put_contents($tmpDir  . DIRECTORY_SEPARATOR .  basename(zip_entry_name($zip_entry)), $buf);
    					$newZip->addFile($tmpDir  . DIRECTORY_SEPARATOR . basename(zip_entry_name($zip_entry)), zip_entry_name($zip_entry));
    					zip_entry_close($zip_entry);
    				}
				break;
			}
		}
		zip_close($zip);
	}
	$newZip->close();
 	rename($tmpZipFile,$destDir . DIRECTORY_SEPARATOR . basename($filename));
// 	unlink($tmpZipFile);
    echo $filename . ' convertito!' .PHP_EOL;
}