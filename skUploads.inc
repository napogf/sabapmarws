<?php
/**
 * Dialog Box per uploads files
 */
print ('<div id="dlgFilesUploads" dojoType="dijit.Dialog" title="Upload file" >') ;
print ('<script type="text/javascript">
						dojo.require("dijit.layout.ContentPane");
						dojo.require("dojo.io.iframe");
						dojo.require("dijit.ProgressBar");
						dojo.require("dijit.form.Button");
						dojo.require("dojo.parser");
					</script>') ;
print ('<form id="filesForm" enctype="multipart/form-data" accept-charset="utf-8" name="skUploads" action="uploadsIframeUploads.php" method="post" >') ;
print ('<div id="uploadContainer" style="width: 100%; margin-bottom: 2em;" >
						<span id="preamble">Carica il File:</span><br>
					    <span id="inputField">
					    <input type="hidden" id="up_PRATICA_ID" value="' . $this->_FormFields ['PRATICA_ID']->GetValue () . '" name="up_PRATICA_ID">
					    <input type="file" id="up_FILENAME" name="up_FILENAME"></br>
					    <label for="up_DESCRIPTION">Descrizione del file </label><input type="text" id="up_DESCRIPTION" name="up_DESCRIPTION">
						<br>
					    </span>
					    <span id="progressField" style="display:none;">
					      <div dojoType="dijit.ProgressBar" style="width:200px"
					        indeterminate="true"></div>
					    </span>
					  <br/>
					  <button value="upload" dojoType="dijit.form.Button"
					    onclick="inserisciFile()">Upload</button>
					</div>
					') ;
print ('</form>') ;
print ('</div>') ;
