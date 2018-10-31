<?php
/*
 * Created on 23/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
		print('<div class="djFormContainer" style="width: 450px; display: block;">');
			print('<fieldset style="border:none">'."\n");


				print('<label for="SEL_INTEGRAZIONI">Integrazioni</label>');

					print('<script language="JavaScript" type="text/javascript">
						dojo.addOnLoad(function(){
						    new dijit.form.FilteringSelect({
						                store: jIntegrazioni,
						                labelAttr: \'TIPO\',
						                searchAttr: \'TIPO\',
						                value: ""  ,
						                name: "SEL_INTEGRAZIONI",
						                autoComplete: true,
						                style: "width: 250px;",
						                id: "SEL_INTEGRAZIONI",
						                onChange: function(ID) {
						                	console.log(ID);
						                	dijit.byId(\'cPaneIntegrazioni\').href= \'djGetIntegrazioni.php?TIPO=\'+ID ;
						                	dijit.byId(\'cPaneIntegrazioni\').refresh();
											return true;
						                }
						            },
						            "SEL_INTEGRAZIONI");
						});
					</script>');
				print('<input ID="SEL_INTEGRAZIONI" >'.
					  '<br/>');


//					print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_INTEGRAZIONI"
//											store="jIntegrazioni"
//											labelAttr="TIPO"
//											searchAttr="TIPO"
//											onChange="function(ID){console.log(ID);' .
//														'dijit.byId(\'cPaneIntegrazioni\').href= \'djGetIntegrazioni.php?TIPO=\'+ID ;' .
//														'dijit.byId(\'cPaneIntegrazioni\').refresh();' .
//														'return true;' .
//														'}"
//											name="SEL_INTEGRAZIONI" ></div>');


			print('</fieldset>'."\n");


		print ('<div dojoType="dijit.layout.ContentPane" id="cPaneIntegrazioni" href="djGetIntegrazioni.php"  >');
		print('</div>');


		print('</div>');
?>
