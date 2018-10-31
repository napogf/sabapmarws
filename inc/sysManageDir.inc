<?php include "login/autentication.php";

      $array_labels=get_program_labels('/manage_dir.php');

$wk_dir_target=IsSet($wk_dir_target)?$wk_dir_target:'last_updates.php';
if(($mode=='insert') and (!empty($butadd) or !empty($butapp))) {
       $sql = "insert into directories ( origin_id, dirname, dir_sequence, dir_target, skeleton_flag, created_by) values ( '$wk_origin_id', '$wk_dirname', '$wk_dir_sequence', '$wk_dir_target', '$wk_skeleton_flag','".$_SESSION['sess_uid']."')";
       if (!dbupdate($sql,false)){
           errore("Errore nell'inserimento di una Cartella, si prega di riprovare.");
       } else {
			$wkDirId=dbLastId();
			if ($wk_languages=dbselect("select LANGUAGE_ID from languages")) {
			    for($i = 0; $i < $wk_languages[NROWS]; $i++){
					$wk_lang=$wk_languages[ROWS][$i][LANGUAGE_ID];
					$lang_sql="insert into dir_labels (dir_id , language_id, description) values ('$wkDirId', '$wk_lang', '$wk_dirname')";
					if (!dbupdate($lang_sql)) {
					    errore('Descrizioni in lingua della cartella non inserite! <br>'.$lang_sql.'<br>');
					}
			    } // for
			}
			if ($wk_responsabilities=dbselect("select RESP_ID, READ_ONLY_FLAG from dir_resp_reference where dir_id = '$wk_origin_id'")) {
			    for($i = 0; $i < $wk_responsabilities[NROWS]; $i++){
			    	$wk_responsability=$wk_responsabilities[ROWS][$i][RESP_ID];
					$wk_rof=$wk_responsabilities[ROWS][$i][READ_ONLY_FLAG];
					$resp_sql="insert into dir_resp_reference (resp_id, dir_id, read_only_flag)
														values('$wk_responsability', '$wkDirId', '$wk_rof')";
					if (!dbupdate($resp_sql)) {
					    errore('Responsabilities non inserite correttamente!<br>'.$resp_sql.'<br>');
					}
			    } // for
			}

       }
       header("Location: cmsPage.php?menu_id=$menu_id&expandedFolder=$wkDirId");
} elseif (($mode=='modify') and (!empty($butapp))) {
    $wk_origin_id=($wkDirId==1)?'null':"'$wk_origin_id'";
    $sql = " update directories set dirname='$wk_dirname', dir_sequence='$wk_dir_sequence', origin_id=$wk_origin_id, skeleton_flag='$wk_skeleton_flag', dir_target='$wk_dir_target' where dir_id='$wkDirId'";
       if (!dbupdate($sql,false)){
           errore("Errore nella modifica di una Cartella, si prega di riprovare.");
       } else {
			if (is_array($wk_resp_id)) dbupdate("delete from dir_resp_reference where dir_id = $wkDirId");
            for ($i=0; $i < count($wk_resp_id); $i++) {
                $resp_id=$wk_resp_id[$i];
 	  			if (find_array($select_rwflag,$resp_id)){
 	  				$wk_flag='rw';
 	  			} elseif (find_array($select_roflag,$resp_id)) {
 	  				$wk_flag='ro';
 	  			} else {
 	  				$wk_flag=NULL;
 	  			}
				if ($wk_flag=='ro') {
					dbupdate("insert into dir_resp_reference (resp_id, dir_id, read_only_flag) values ($resp_id, $wkDirId, 'Y')",false);
				} elseif ($wk_flag=='rw') {
					dbupdate("insert into dir_resp_reference (resp_id, dir_id, read_only_flag) values ($resp_id, $wkDirId, 'N')",false);
				}
 			}
            if (count($wk_language_id)<>0){
                for ($i=0; $i<count($wk_language_id); $i++){
                    dbupdate("update dir_labels set description='$wk_description[$i]', scheda='$wk_scheda[$i]' where (dir_id='$wkDirId') and (language_id='$wk_language_id[$i]')",false);
                }
            }
       }
        header("Location: cmsPage.php?menu_id=$menu_id&expandedFolder=$wkDirId");
} elseif (!empty($butdel) and ($butdel <> 'Annullato')) {
	if (dbupdate("delete from dir_resp_reference where dir_id = '$wkDirId'")) {
	    if (dbupdate("delete from dir_labels where dir_id = '$wkDirId'")) {
		    if (dbupdate("delete from directories where dir_id = '$wkDirId'")) {

		    } else {
				errore("Errore nella cancellazione!<br>delete from directories where dir_id = '$wkDirId'");
			}
	    } else {
			errore("Errore nella cancellazione!<br>delete from dir_labels where dir_id = '$wkDirId'");
		}
	} else {
		errore("Errore nella cancellazione!<br>delete from dir_resp_reference where dir_id = '$wkDirId'");
	}
    header("Location: cmsPage.php?menu_id=$menu_id");
} else {
    $lowtab=IsSet($lowtab)?$lowtab:"languages";
    $buttons=get_buttons('/manage_dir.php');
    if (IsSet($wkDirId) and !(IsSet($mode))) {
        // sono appena entrato nella form da una directory selezionata
        $dir_query="select DIRNAME, DIR_SEQUENCE, DIR_TARGET, SKELETON_FLAG from directories where dir_id=$wkDirId";
        $dir_result=dbselect($dir_query);
        if ($dir_result == null) {
            errore("Programma da terminare <br> $dir_query");
        } else {
            $wk_dirname=$dir_result[ROWS][0][DIRNAME];
            $wk_dir_sequence=$dir_result[ROWS][0][DIR_SEQUENCE];
			$wk_dir_target=$dir_result[ROWS][0][DIR_TARGET];
			$wk_skeleton_flag=$dir_result[ROWS][0][SKELETON_FLAG];
            $mode='modify';
        }
    } elseif (IsSet($mode)) {
        if ($buttons <> null){
            //if (test_buttons($buttons,$butadd)){
			if (!empty($butadd)) {
                $mode="insert";
                $wk_dirname="";
                $wk_origin_id=$wkDirId;
                $seq_result=dbselect("select max(dir_sequence) DIR_SEQ from directories where origin_id='$wk_origin_id'");
                $wk_dir_sequence=$seq_result[ROWS][0][DIR_SEQ]+10;
            }
        }
    }

include("pageheader.inc");
print('<div class="formContainer">'."\n");

print("<script>" .
"function conf_del(del_message)
{
	if (confirm(del_message)){
	   if(dhtml == 'NN') {
	       document.forms[0].submit();
	   }else if (dhtml == 'IE'){
	       document.forms[0].submit();
	   }
	} else {
	     document.forms[0].reset();
	}
}
</script>\n");

print('<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">'."\n");
print('<tr><td valign="top" >');



?>
<FORM ACTION="<?php print($_SERVER['PHP_SELF']."?mode=$mode&menu_id=$menu_id"); ?>"  METHOD="POST" name="Manage_Directory">
<table border="0" cellPadding="1" cellSpacing="1" width="100%">

  <tr>
  <td colspan="2" align="left" class="dir_path">
<?php print($path=get_directory_path($wkDirId)); ?>
  </td>
  </tr>

  <tr>
    <td colspan="2"><?php $buttons_array=make_buttons($buttons);
                          // print_r($buttons_array);  ?></td>
  </tr>
    <input type="hidden" name="wkDirId" value=<?php print($wkDirId); ?>>
    <tbody>
    <tr>
      <td>&nbsp; </td>
      <td align="right"> </td>
    </tr>
<?php

if (($wkDirId==1) and empty($butadd)) {
    $wk_origin_id=NULL;
} else {
    print('<tr>');
    print('<td align="right" noWrap vAlign="top" class="form_label">');
	print(get_array_label('origin'));
    print('</td>');

    print('<td class="form_field" >');
	$exclude_dir=$mode=="insert"?null:$wkDirId;
    make_dir_select($wk_origin_id,"wk_dir_id","wk_origin_id",$exclude_dir);
    print('</td>');
    print('</tr>');
}
?>
	<!-- Sequenza -->
    <tr>
      <td align="right" noWrap vAlign="top" class="form_label">
        <?php print(get_array_label('dir_sequence')); ?>
      </td>
      <td class="form_field" >
        <input maxLength="30" name="wk_dir_sequence" value="<?php print($wk_dir_sequence); ?>" type="TEXT" size="30" class="form_field">
      </td>
    </tr>
	<!-- Nome Directory -->
    <tr>
      <td align="right" noWrap vAlign="top" class="form_label">
        <?php print_label('dirname'); ?>
      </td>
      <td class="form_field" >
        <input maxLength="30" name="wk_dirname" value="<?php print($wk_dirname); ?>" type="TEXT" size="30" class="form_field">
      </td>
    </tr>

	<!-- Dir Target -->
    <tr>
      <td align="right" noWrap vAlign="top" class="form_label">
        <?php print_label('dir_target'); ?>
      </td>
      <td class="form_field" >
        <input maxLength="255" name="wk_dir_target" value="<?php print($wk_dir_target); ?>" type="TEXT" size="60" class="form_field">
      </td>
    </tr>

	<!-- Flag directory per cambiare status in dir_links -->
    <tr>
      <td align="right" noWrap vAlign="top" class="form_label">
        Gestione Status Link
      </td>
      <td class="form_field" >
        <input maxLength="255" name="wk_skeleton_flag" value="<?php print($wk_skeleton_flag); ?>" type="TEXT" size="1" class="form_field">
      </td>
    </tr>



    <tr>
      <td align="right" valign="top" class="form_label">&nbsp; </td>
      <td valign="top" class="form_field" >&nbsp; </td>
    </tr>
    <tr>
      <td align="right" valign="top" class="form_label">&nbsp; </td>
      <td valign="top" class="form_field" >&nbsp; </td>
    </tr>
    <tr>
      <td class="form_button" align="left">&nbsp; </td>
      <td class="form_button" align="right" >&nbsp; </td>
    </tr>
    <tr>
      <td class="form_button" align="left" colspan="2">&nbsp;</td>
    </tr>
    <!-- Gestione descrizioni in lingua o assegnazione delle responsabilities -->


<?php
    if ($mode <> "insert"){

        if (!IsSet($lowtab) or ($lowtab=="languages")){
            print('<tr>');
            print('  <td align="center" colspan="2">');
            print('    <table width="60%" border="0">');
            print('      <tr>');
            print('        <td width="50%" class="selected_folder">');
            print(get_array_label('language'));
            print('        </td>');
            print('        <td width="50%" class="unselected_folder"> <a href="');
            print($_SERVER['PHP_SELF']."?wkDirId=$wkDirId&wk_origin_id=$wk_origin_id&mode=$mode&lowtab=responsabilities&wk_dir_sequence=$wk_dir_sequence&wk_dirname=$wk_dirname&wk_skeleton_flag=$wk_skeleton_flag");
            print('" class="unselected_folder" >');
            print(get_array_label('assignresp'));
            print('          </a></td>');
            print('      </tr>');
            print('    </table>');
            print('  </td>');
            print('</tr>');
            print('<tr>');
            print('  <td colspan="2" align="center">');
            print('    <table width="60%" border="0">');
            $query="select languages.LANGUAGE_ID, languages.description LANGUAGE, dir_labels.DESCRIPTION, dir_labels.SCHEDA from (dir_labels, languages) where (languages.language_id=dir_labels.language_id)
                                                                                        and (dir_id=$wkDirId)";
            $result=dbselect($query,false);
            print('<tr>');
            print('  <th class="new_tab_head" colspan="3">');
            print(get_array_label('language'));
            print('  </th>');
            print('</tr>');
            for ($z=0; $z<$result[NROWS]; $z++){
                print('<tr>');
                print('    <td class="tab_label" width="50%">');
                print("<input type=\"hidden\" name=\"wk_language_id[]\" value=\"");
                print($result[ROWS][$z][LANGUAGE_ID]);
                print("\">");
                print($result[ROWS][$z][LANGUAGE]);
                print('    </td>');
                print('    <td class="tab_field" width="50%">');
                print('      <input type="text" size="30" maxLength="30" name="wk_description[]" value="');
                print($result[ROWS][$z][DESCRIPTION]);
                print('" >');
                print('    </td>');
                print('    <td class="tab_field" width="50%">');
                print('      <input type="text" size="30" maxLength="255" name="wk_scheda[]" value="');
                print($result[ROWS][$z][SCHEDA]);
                print('" >');
                print('    </td>');
                print('</tr>');
                print("\n");
            }
        } elseif ($lowtab=="responsabilities") {
            print('<tr>');
            print('  <td align="center" colspan="2">');
            print('    <table width="60%" border="0">');
            print('      <tr>');
            print('        <td width="50%" class="unselected_folder"> <a href="');
            print($_SERVER['PHP_SELF']."?wkDirId=$wkDirId&wk_origin_id=$wk_origin_id&mode=$mode&lowtab=languages&wk_dir_sequence=$wk_dir_sequence&wk_dirname=$wk_dirname");
            print('" class="unselected_folder" >');
            print(get_array_label('language'));
            print('          </a></td>');
            print('        <td width="50%" class="selected_folder">');
            print(get_array_label('assignresp'));
            print('        </td>');
            print('      </tr>');
            print('    </table>');
            print('  </td>');
            print('</tr>');
            print('<tr>');
            print('  <td colspan="2" align="center">');
            print('    <table width="60%" border="0">');

            /*
            $user_resp_query="SELECT responsabilities.resp_id, resp_lang_descriptions.description, users.read_only_default,
                                        (select dir_resp_reference.dir_id from dir_resp_reference where dir_resp_reference.resp_id = responsabilities.resp_id and dir_resp_reference.dir_id='$wkDirId') wk_dir,
	                                    (select drr.read_only_flag from  dir_resp_reference drr where drr.dir_id= '$wkDirId' and drr.resp_id = user_resp_reference.RESP_ID) read_only_flag
	                                 FROM user_resp_reference, responsabilities, users, resp_lang_descriptions
	                                 WHERE ( (responsabilities.resp_id = user_resp_reference.resp_id)
	   		                                  AND (users.user_id = user_resp_reference.user_id)
			                                     AND (responsabilities.resp_id = resp_lang_descriptions.resp_id)
			                                     AND (resp_lang_descriptions.language_id = users.language_id)
			                                     AND (users.user_id = '$_SESSION['sess_uid']') )";
            $user_resp_query="SELECT responsabilities.resp_id,
                                     resp_lang_descriptions.description,
                                     resp_lang_descriptions.language_id,
	                                 (select dir_resp_reference.dir_id from dir_resp_reference where dir_resp_reference.resp_id = responsabilities.resp_id and dir_resp_reference.dir_id='$wkDirId') wk_dir,
	                                 (select drr.read_only_flag from dir_resp_reference drr where drr.dir_id= '$wkDirId' and drr.resp_id = responsabilities.RESP_ID) read_only_flag,
	                                 (select users.READ_ONLY_DEFAULT from users where users.USER_ID = '$_SESSION['sess_uid']') read_only_default
                                  FROM responsabilities, resp_lang_descriptions
                                  WHERE (    (responsabilities.resp_id = resp_lang_descriptions.resp_id)
        	                               AND (resp_lang_descriptions.language_id = '$_SESSION['sess_lang']')
                                        )
                                  order by resp_lang_descriptions.description";
            */
            $user_resp_query="SELECT distinct dir.DIR_ID, rld.DESCRIPTION, drr.READ_ONLY_FLAG, resp.RESP_ID
									FROM " .
									"(responsabilities resp, user_resp_reference urr ) " .
									"LEFT JOIN dir_resp_reference drr ON ((drr.resp_id = resp.resp_id) and (drr.dir_id = '$wkDirId')) " .
									"left join directories dir on (dir.dir_id = drr.dir_id) " .
									"left join dir_labels dl on ((dl.dir_id = dir.dir_id) and (dl.language_id = '$_SESSION['sess_lang']')) " .
									"left join resp_lang_descriptions rld on ((rld.resp_id=resp.resp_id) and (rld.language_id = $_SESSION['sess_lang'])) " .
									" WHERE  urr.user_id = '$_SESSION['sess_uid']' ";



            $result=dbselect($user_resp_query,false);
            print('<tr>');
            print('  <th class="new_tab_head" colspan="3">');
            print(get_array_label('assignresp'));
            print('  </th>');
            print('</tr>');
            print('  <th class="new_tab_head" >');
            print(get_array_label('dirresp'));
            print('  </th>');
            print('  <th class="new_tab_head" >');
            print(get_array_label('assign'));
            print('  </th>');
            print('  <th class="new_tab_head" >');
            print(get_array_label('roflag'));
            print('  </th>');
            print('</tr>');
            print("\n");
            for ($z=0; $z<$result[NROWS]; $z++){
                // print_r($result[ROWS]);
                $row_class=($row_class=="lista1")?"lista2":"lista1";

				switch($result[ROWS][$z][READ_ONLY_FLAG]){
					case ('N'):
						$rw_flag='Y';
						$ro_flag='Y';
						$rw_checked="checked";
						$ro_checked="checked";
						break;
					case ('Y'):
						$rw_flag='N';
						$ro_flag='Y';
						$rw_checked="";
						$ro_checked="checked";
						break;
					default:
						$rw_flag='N';
						$ro_flag='N';
						$rw_checked="";
						$ro_checked="";
				} // switch
                print("<tr>");
                print("    <td class=$row_class width=\"50%\">");
                print("<input type=\"hidden\" name=\"wk_resp_id[]\" value=\"");
                print($result[ROWS][$z][RESP_ID]);
                print("\">");
                print($result[ROWS][$z][DESCRIPTION]);
                print("    </td>");
                print("    <td class=$row_class align=\"center\" width=\"25%\">");
                print("<input type=\"checkbox\" name=\"select_roflag[]\" value=\"");
                print($result[ROWS][$z][RESP_ID]);
                print("\" $ro_checked></td>");
                print("    </td>");
                print("    <td class=$row_class align=\"center\" width=\"25%\">");
                print("<input type=\"checkbox\" name=\"select_rwflag[]\" value=\"");
                print($result[ROWS][$z][RESP_ID]);
                print("\" $rw_checked></td>");
                print("    </td>");
                print("</tr>");
                print("\n");
            }
        }
        print('</table>');
        print('</tr>');
    }
}

 ?>
    <tr>
      <td colspan="2" align="center"><?php $buttons_array=make_buttons($buttons); ?></td>
    </tr>
    </tbody>
</table>
      </td>
    </tr>
  </tbody>
</table>
</form>
<?

print('</td></tr>');
print ("\n");

print('</table>');
print('</div>'."\n");

include('pagefooter.inc');
?>