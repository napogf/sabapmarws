<?php
/*
 * Created on 27/ago/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("inc/dbfunctions.php");
include('pageheader.inc');

print('<div dojoType="ContentPane" layoutAlign="client" style=" padding: 10 0 0 0; margin: 2px;">');

if (isSet($upload)){
	copy($_FILES['ODTFILE']['tmp_name'],'./modelli/'.$_FILES['ODTFILE']['name']);

}
if (isSet($mode) and $mode=='delete'){
	unlink('./modelli/'.$basename);
}

print('<div class="dbFormContainer" >
		<!-- Form open -->
		<FORM ACTION="" enctype="multipart/form-data" METHOD="POST" name="uploadXml" >
		<!-- Start bottoniera -->
		<fieldset>
		<legend>Caricamento Modelli ODT</legend>
		<br />
		<br/>
		<label for="XMLFILE" >Modello da caricare</label><input  type="FILE" name="ODTFILE" value="" size="60" maxlength="255" >
		<br/>
		<input  class="buttons"  name="upload" type="SUBMIT" value="Carica File" >
		</form>' .
		'</div>');
print('<div style="padding:20px">');
print('<h3>Modelli caricati</h3>');
if ($handle = opendir('./modelli')) {
    while (false !== ($filename = readdir($handle))) {
        if ($filename != "." && $filename != "..") {
        	$fileInfo=pathinfo($filename);
//        	var_dump($fileInfo);
        	if (strtoupper($fileInfo['extension'])=='ODT') {

				print('<img title="Cancella '.$fileInfo['basename'].'" onclick="location.href=\'uploadOdt.php?mode=delete&basename='.$fileInfo['basename'].'\'" style="margin-right:10px; cursor: pointer;" src="graphics/webapp/deleted.gif"/>');
        		print(basename($filename,'.'.$fileInfo['extension']));
	        	print('<br>');
			}
        }
    }
}

print('</div>');

print('</div>');
include('pagefooter.inc')
?>