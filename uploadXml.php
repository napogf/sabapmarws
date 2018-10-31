<?php
/*
 * Created on 27/ago/08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
$savedPar = ini_get('max_execution_time');
ini_set('max_execution_time', 3000);
//require_once("inc/dbfunctions.php");
include('pageheader.inc');

print('<div dojoType="ContentPane" layoutAlign="client" style=" padding: 10 0 0 0; margin: 2px;">');

if (isSet($_POST['upload'])){
	copy($_FILES['XMLFILE']['tmp_name'],getcwd().'/dacaricare/'.$_FILES['XMLFILE']['name']);
	if ($handle = opendir('./dacaricare')) {
		while (false !== ($filename = readdir($handle))) {
			if ($filename != "." && $filename != "..") {
				$fileInfo=pathinfo($filename);
				if(strtoupper($fileInfo['extension'])=='ZIP') {
					print('Caricamento File '.$filename.'</br>'."\n");
					$fileToParse = new loadXml($filename);
					$fileToParse->loadPratiche();
				}
			}
		}
	}	

} else {

?>

<div class="dbFormContainer" >
<!-- Form open -->
<FORM ACTION="" enctype="multipart/form-data" METHOD="POST" name="uploadXml" >
<!-- Start bottoniera -->
<fieldset>
<legend>Caricamento Files XML</legend>
<br />
<br/>
<label for="XMLFILE" >File Xml da Importare</label><input  type="FILE" name="XMLFILE" value="" size="60" maxlength="255" >
<br/>
<input  class="buttons"  name="upload" type="SUBMIT" value="Carica File" >
</form>

</div>


<?php
}
print('</div>');
include('pagefooter.inc');
ini_set('max_execution_time',$savedPar);
?>
