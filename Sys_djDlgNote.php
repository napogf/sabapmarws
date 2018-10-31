<?php
/*
 * Created on 23/feb/10
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
include "login/autentication.php";
//require_once("dbfunctions.php");
		print('<div class="djFormContainer scrollContent" style="width: 450px; height:250px; overflow-y: auto; display: block;">');
			print('<fieldset style="border:none">'."\n");
				print('<label for="SEL_NOTE_'.$_GET['id'].'">Note</label>');
				print ('<div dojoType="dojo.data.ItemFileReadStore" ' .
//							'url="xml/jsonSql.php?sql=select DISTINCT TIPO as ID, TIPO from arc_note where field_id='.$_GET['id'].' or field_id is null&nullValue=Y" ' .
							'url="xml/jsonSql.php?sql=select DISTINCT TIPO as ID, TIPO from arc_note right join sys_fields_validations on (
											sys_fields_validations.value = arc_note.tipo) order by sys_fields_validations.code" ' .
							'jsId="jNote_'.$_GET['id'].'" ' .
							'>
						</div>');
				print ('<div dojoType="dijit.form.FilteringSelect" ID="SEL_NOTE_'.$_GET['id'].'"
										store="jNote_'.$_GET['id'].'"
										labelAttr="TIPO"
										onChange="loadCpaneNote('.$_GET['id'].')" 
										searchAttr="TIPO"
										name="SEL_NOTE_'.$_GET['id'].'" ' .
										'>
						</div>');
				
					  print('<br/>');
			print('</fieldset>'."\n");
			print ('<div dojoType="dijit.layout.ContentPane" id="cPaneNote_'.$_GET['id'].'" href="Sys_djGetNote.php" parseOnload="true" >');
			print('</div>');
		print('</div>');
?>