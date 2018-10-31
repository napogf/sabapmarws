<?php
if(!function_exists('r')){
	function r($var,$exit=true) {
		$calledFrom = debug_backtrace();
		if (php_sapi_name() == 'cli') {
			echo $calledFrom[0]['file'] . ' (line ' . $calledFrom[0]['line'] . ')'.PHP_EOL;
			var_dump($var);
			echo PHP_EOL;
		} else {
			echo '<strong>' . $calledFrom[0]['file'] . '</strong> (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			echo '<pre>'.htmlspecialchars(var_dump($var,1)).'</pre>';
		}
		if ($exit) {
			exit;
		}
	}
}




//*/
function dbupdate($query, $test = false) {
	
	$db = Db_Pdo::getInstance();
	if ($test) {
		r($query,false);
	}


	return $db->query($query);
}

function dbLastId() {
	$db = Db_Pdo::getInstance();
	return $db->lastInsertId();
}

function ListTableFields($table) {
	global $fontedati, $linkDB;
	$campi = mysql_list_fields($fontedati, $table, $linkDB);
	

	return $campi;
}

function ListDbTables() {
	$result=dbselect('show tables');
	if (!$result) {
		print "Errorore database, Impossibile elencare le tabelle\n";
		print 'Errore MySQL: ' . mysql_error();
		exit;
	} else {
		return $result;
	}

}

function dbselect($query, $foundRows = false , $test = false) {
	$db = Db_Pdo::getInstance();

	if ($test) {
		r($query,false);
	}
	$rows_array = array ();
	$nrows = 0;
	$query = preg_replace('/\s\s+/', ' ', $query);

	if($foundRows){
		$pattern = '/(select distinct )(.*)/i';
		if (!preg_match_all($pattern, $query, $matches)){
			$pattern = '/(select )(.*)/i';
			if(preg_match_all($pattern, $query, $matches)){
				$query = $matches[1][0]. ' SQL_CALC_FOUND_ROWS ' . $matches[2][0];
			}
		}

	}

	$result = $db->query($query) or die("Query non valida: <BR>" . $query . '<br>' . var_dump(debug_backtrace()));
	/*
	 * Per implementazione tipo colonna in table
	 *
	 $colTypes = array();
	 for ($i = 0; $i < $result->columnCount(); $i++) {
		$col = $result->getColumnMeta($i);
		$colTypes[$col['name']] = $col['native_type'];
		}
	 *
	 */
//	$result->setFetchMode(PDO::FETCH_ASSOC);

	if (!$result) {
		print ($query . '<br>');
	}
	while ($riga = $result->fetch()) {
		$rows_array[] = $riga;
	}
	
	if($foundRows){
		$rs1 = $db->query('SELECT FOUND_ROWS()');
		$nrows = (int) $rs1->fetchColumn();
	}
	$nrows = ($foundRows ? $nrows : count($rows_array));

	if ($nrows > 0) {
		return (array (
			'NROWS' => $nrows,
			'ROWS' => $rows_array));
	} else {
		return false;
	}
}


function get_directory_scheda($wk_dir_id) {
	$_SESSION['sess_lang'] = $_SESSION['sess_lang'];
	$sql = "select SCHEDA from dir_labels where dir_id='$wk_dir_id' and LANGUAGE_ID='".$_SESSION['sess_lang']."'";
	$result = dbselect($sql, false);
	return ($result['ROWS'][0]['SCHEDA']);
}
function get_directory_path($wk_dir_id = null, $path = '') {
	// $sql="select distinct dir_id, origin_id, description from directories_v where (user_id = '$_SESSION['sess_uid']') and (dir_id='$wk_dir_id') and (LANGUAGE_ID = '$_SESSION['sess_lang']') ";
	$sql = "SELECT DISTINCT USER_RESP_REFERENCE.USER_ID, DIRECTORIES.DIR_ID,
			                DIRECTORIES.ORIGIN_ID, DIRECTORIES.DIR_SEQUENCE,
			                DIR_LABELS.DESCRIPTION, DIR_LABELS.LANGUAGE_ID,
							DIRECTORIES.SKELETON_FLAG
			           FROM directories DIRECTORIES,
			                dir_labels DIR_LABELS,
			                dir_resp_reference DIR_RESP_REFERENCE,
			                languages LANGUAGES,
			                responsabilities RESPONSABILITIES,
			                resp_lang_descriptions RESP_LANG_DESCRIPTIONS,
			                users USERS,
			                user_resp_reference USER_RESP_REFERENCE
			          WHERE (    (DIRECTORIES.DIR_ID = DIR_LABELS.DIR_ID)
			                 AND (DIRECTORIES.DIR_ID = DIR_RESP_REFERENCE.DIR_ID)
			                 AND (LANGUAGES.LANGUAGE_ID = DIR_LABELS.LANGUAGE_ID)
			                 AND (RESPONSABILITIES.RESP_ID = DIR_RESP_REFERENCE.RESP_ID)
			                 AND (LANGUAGES.LANGUAGE_ID =
			                                          RESP_LANG_DESCRIPTIONS.LANGUAGE_ID
			                     )
			                 AND (RESPONSABILITIES.RESP_ID =
			                                                RESP_LANG_DESCRIPTIONS.RESP_ID
			                     )
			                 AND (LANGUAGES.LANGUAGE_ID = USERS.LANGUAGE_ID)
			                 AND (RESPONSABILITIES.RESP_ID = USER_RESP_REFERENCE.RESP_ID)
			                 AND (USERS.USER_ID = USER_RESP_REFERENCE.USER_ID)
							 and (USERS.USER_ID = '".$_SESSION['sess_uid']."')
							 and (DIRECTORIES.DIR_ID = '$wk_dir_id')
			                )
			       ORDER BY USER_RESP_REFERENCE.USER_ID ASC,
			                DIRECTORIES.DIR_ID ASC,
			                DIRECTORIES.ORIGIN_ID ASC";
	//print($sql.'<br>');
	$result = dbselect($sql, false);

	for ($x = 0; $x < $result['NROWS']; $x++) {
		$path = '/' . $result['ROWS'][$x]['DESCRIPTION'] . $path;
		$path .= get_directory_path($result['ROWS'][$x]['ORIGIN_ID']);
	}
	return ($path);
}

function printDirPath($wkDirId,$separator='->'){
	print('<p class="dirPath">');
	$template=get_directory_scheda($wkDirId);
	$path=reverseString(get_directory_path($wkDirId),'/');
	$path=str_replace('/',$separator ,$path );
	print(substr($path,2));
	print('</p>');
	print ("\n");
}





function reverseString($str, $token = '-') {
	$strArray = split($token, $str);
	for ($index = sizeof($strArray); $index > 0; $index--) {
		$string = $string . $token . $strArray[$index];
	}
	return ($string);
}

/**
 *
 * @access public
 * @return void
 **/
/**
 *
 * @access public
 * @return void
 **/
//function get_pathtest($dir_array, $path) {
//	global $_SESSION['sess_uid'], $_SESSION['sess_lang'];
//
//}

function getDirPermission($dirId) {
	$sql = "SELECT DISTINCT dir.DIR_ID, " .
	" drr.READ_ONLY_FLAG " .
	"FROM directories dir " .
	"right join dir_resp_reference drr on (drr.dir_id = dir.dir_id) " .
	"right join user_resp_reference urr on (urr.resp_id = drr.resp_id) " .
	"WHERE (dir.dir_id = '$dirId') and (urr.user_id = '".$_SESSION['sess_uid']."') " .
	"order by 2 asc";

	if (!$result=rowselect($sql)){
		return ('N');
	} else {
		if ($result[1]=='Y'){
			return 'R';
		} elseif ($result[1]=='N'){
			return 'W';
		} else {
			return ($result[1]);
		}
	}
	return false;
}

function get_directory_rights($wk_dir_id) {


	// $sql="select dir_id, origin_id, description, read_only_flag from directories_v where (user_id = '$_SESSION['sess_uid']') and (dir_id='$wk_dir_id') and (LANGUAGE_ID = '$_SESSION['sess_lang']') ";

	$sql = "SELECT DISTINCT directories.DIR_ID, directories.ORIGIN_ID, directories.DIR_SEQUENCE, dir_labels.DESCRIPTION, dir_resp_reference.READ_ONLY_FLAG, dir_labels.LANGUAGE_ID, directories.SKELETON_FLAG, directories.DIR_TARGET
					FROM directories, dir_labels, dir_resp_reference, user_resp_reference
					WHERE (
					directories.dir_id = '$wk_dir_id'
					) AND (
					user_resp_reference.user_id = '".$_SESSION['sess_uid']."'
					) AND (
					dir_labels.dir_id = directories.dir_id
					) AND (
					dir_labels.language_id = '".$_SESSION['sess_lang']."'
					) AND (
					(
					dir_resp_reference.dir_id = directories.dir_id
					) AND (
					dir_resp_reference.resp_id = user_resp_reference.resp_id
					)
					) ";
	$result = dbselect($sql, false);
	for ($i = 0; $i < count($result['ROWS']); $i++) {
		if ($result['ROWS'][$i]['READ_ONLY_FLAG'] == 'N') {
			return (array (
				'N',
			$result['ROWS'][$i]['ORIGIN_ID'],
			$result['ROWS'][$i]['DIR_TARGET']
			));
		} else {
			$wk_origin_id = $result['ROWS'][$i]['ORIGIN_ID'];
			$wk_dir_target = $result['ROWS'][$i]['DIR_TARGET'];
		}
	} // for
	return (array (
		'Y',
	$wk_origin_id,
	$wk_dir_target
	));

}

function is_administrator($sess_uid) {
	if (!dbselect("select * from user_resp_reference where user_id=$sess_uid and resp_id=1")) {
		return false;
	} else {
		return true;
	}

}

//---------------------------------
// Funzione errore
// Parametri Ingresso:
// $msg = Messaggio generico da visualizza,
// Nota: Scrive un generico Messaggio
// Ritorna nulla
//---------------------------------
function errore($msg, $terminate = true) {
	global $linkDB;
	print ("<br>
			<TABLE height=\"100%\" BORDER=\"0\" CELLPADDING=\"1\" CELLSPACING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"80%\">
			<TR width=\"100%\" valign=\"top\"><TD BGCOLOR=\"white\">
				<TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\">
				<TR BGCOLOR=\"white\" ALIGN=\"LEFT\">
					<TD>
						<font face=\"Verdana\" size=\"2\"><ul><p align=\"center\">$msg</p></ul></font>
					</TD>
				</TR>
				</TABLE>
			</TD></TR>
		 	</TABLE>
		 <br>");
	if (IsSet ($rollback) and ($rollback))
	ocirollback($linkDB);
	if ($terminate) {
		var_dump(debug_backtrace());
		exit;
	}
}

function getmicrotime() {
	list ($usec, $sec) = explode(' ', microtime());
	return ((float) $usec + (float) $sec);
}

/**
 function get_directories($wk_dir_id=null,$skeleton=false){
 global $_SESSION['sess_uid'], $_SESSION['sess_lang'];
 $skel_filter = ($skeleton)?"where (skeleton_flag='Y') ":"where ((skeleton_flag='Y') or (skeleton_flag='N')) ";
 $dir_filter = ($wk_dir_id==null)?"":"and (dir_id='$wk_dir_id') ";
 $sql="select dir_id, origin_id from directories ".$skel_filter.$dir_filter." start with dir_id= 0 connect by origin_id = PRIOR dir_id";
 //  $sql="select distinct dir.dir_id, dir.origin_id, lpad('>', 2*(Level), '- ') || lbl.description DIR_PATH from directories dir, dir_labels lbl ".$skel_filter.$dir_filter." and (dir.DIR_ID in (select dus.dir_id from directories_v dus where dus.USER_ID = '$_SESSION['sess_uid']' and dus.LANGUAGE_ID = '$_SESSION['sess_lang']'))"." AND ((lbl.dir_id = dir.dir_id) and (lbl.LANGUAGE_ID = '$_SESSION['sess_lang']'))"." start with dir.dir_id= 0 connect by dir.origin_id = PRIOR dir.dir_id";

 print("$sql<br>");
 $local_dir_array=array();
 $result=dbselect($sql,false);
 // $local_dir_array=$result[ROWS];
 for ($x=0; $x<$result[NROWS]; $x++){
 $local_dir_array[DIR_ID][]=$result[ROWS][DIR_ID][$x];
 $local_dir_array[DIR_PATH][]=get_directory_path($result[ROWS][DIR_ID][$x]);
 $local_dir_array[ORIGIN_ID][]=$result[ROWS][ORIGIN_ID][$x];
 }
 return $local_dir_array;
 }
 //*/

function get_directories($wk_dir_id = null, $local_dir_array = null, $initial_path = '/', $exclude_dir = null) {
	
	if ($local_dir_array == null) {
		$local_dir_array = array ();
	}
	$sql = "select dir.DIR_ID, dir.ORIGIN_ID, dirlbl.DESCRIPTION
					FROM directories dir, dir_labels dirlbl
					WHERE (
							((dirlbl.dir_id = dir.dir_id) and (dirlbl.language_id='".$_SESSION['sess_lang']."'))";
	$sql .= $wk_dir_id == null ? " AND (dir.origin_id IS NULL) " : " AND (dir.origin_id = '$wk_dir_id') ";
	$sql .= $exclude_dir == null ? "" : " AND (dir.dir_id <> '$exclude_dir') ";
	$sql .= "		)
					ORDER BY dir.dir_sequence";
	$result = dbselect($sql, false);
	//print_r($result);
	for ($x = 0; $x < $result['NROWS']; $x++) {
		$local_dir_array['DIR_ID'][] = $result['ROWS'][$x]['DIR_ID'];
		$local_dir_array['DIR_PATH'][] = $initial_path . $result['ROWS'][$x]['DESCRIPTION'];
		$local_dir_array['ORIGIN_ID'][] = $result['ROWS'][$x]['ORIGIN_ID'];
		if (dbselect("select dir_id from directories where origin_id='" . $result['ROWS'][$x]['DIR_ID'] . "'")) {
			$local_dir_array = get_directories($result['ROWS'][$x]['DIR_ID'], $local_dir_array, $initial_path . $result['ROWS'][$x]['DESCRIPTION'] . '/', $exclude_dir);
		}
	}
	return $local_dir_array;

}
/**
 *
 * @access public
 * @return void
 **/
function emulate_lang_trigger($wk_resp_id, $wk_description) {
	$wk_languages = dbselect("select LANGUAGE_ID from languages");
	for ($i = 0; $i < $wk_languages[NROWS]; $i++) {
		$wk_LANGUAGE_ID = $wk_languages[ROWS][$i][LANGUAGE_ID];
		dbupdate("insert into resp_lang_descriptions (resp_id, LANGUAGE_ID, description) values ('$wk_resp_id', '$wk_LANGUAGE_ID', '$wk_description')");
	} // for
}

/**
 *
 * @access public
 * @return void
 **/
function update_responsabilities($wk_resp_id, $wk_dir_id, $wk_flag) {

	if (dbselect("select resp_id, dir_id from dir_resp_reference where (resp_id='$wk_resp_id') and (dir_id='$wk_dir_id')", false) <> false) {
		if ($wk_flag == null) {
			dbupdate("delete from dir_resp_reference where (resp_id='$wk_resp_id') and (dir_id='$wk_dir_id')");
		}
		elseif ($wk_flag == 'ro') {
			dbupdate("update dir_resp_reference set read_only_flag='Y' where (resp_id='$wk_resp_id') and (dir_id='$wk_dir_id')", false);
		}
		elseif ($wk_flag == 'rw') {
			dbupdate("update dir_resp_reference set read_only_flag='N' where (resp_id='$wk_resp_id') and (dir_id='$wk_dir_id')", false);
		}
	} else {
		if ($wk_flag == 'ro') {
			dbupdate("insert into dir_resp_reference (resp_id, dir_id, read_only_flag) values ($wk_resp_id, $wk_dir_id, 'Y')", false);
		}
		elseif ($wk_flag == 'rw') {
			dbupdate("insert into dir_resp_reference (resp_id, dir_id, read_only_flag) values ($wk_resp_id, $wk_dir_id, 'N')", false);
		}
	}
}


function sysCounter($sysCounter = 'COMMON') {
	if ($Counter = dbselect('select (COUNTER+1) as COUNTER from sys_counters where counter_name =\'' . $sysCounter . '\'')) {
		dbupdate("update sys_counters set counter = ".$Counter['ROWS'][0]['COUNTER']." where  counter_name = '$sysCounter'");
		return $Counter['ROWS'][0]['COUNTER'];
	} else {
		dbupdate("insert into sys_counters (counter_name, counter) values ('$sysCounter', 1)");
		return 1;
	}
}

function getCounter($type, $counter = null) {
	if (intval($counter) > 0) {
		return ($counter);
	} else {
		if ($countResult = dbselect('select COUNTER from arc_counters where type=\'' . $type . '\'')) {
			dbupdate('update arc_counters set counter = counter+1 where type=\'' . $type . '\'');
			return ($countResult['ROWS'][0]['COUNTER'] + 1);
		} else {
			dbupdate('insert into arc_counters (counter, type) values (1,\'' . $type . '\')');
			return (1);
		}
	}
}

function ajaxCallBack($matches) {
	return ("'+$('" . $matches[1] . "').value+'");
}


function getFormsLabels($formName){
	$labelArray=array();
	// Ricavo le labels per la scheda da sys_forms_fields
	$labelQuery='select FIELD_NAME, DESCRIPTION from sys_forms_fields_labels sffl ' .
									' right join sys_forms_fields sff on (sff.field_id = sffl.field_id) ' .
									' right join sys_forms sf on (sf.form_id = sff.form_id) ' .
									'where sf.form_name = \''.$formName.'\' and sffl.language_id = '.$_SESSION['sess_lang'];
	if(!$labelResult=dbselect($labelQuery)){
		return FALSE;
	} else {
		for ($index = 0; $index < $labelResult['NROWS']; $index++) {
			$labelArray[$labelResult['ROWS'][$index]['FIELD_NAME']]=$labelResult['ROWS'][$index]['DESCRIPTION'];
		}
		return $labelArray;
	}

}
function get_label($label,$program=null){
	$db = Db_Pdo::getInstance();

	if(is_null($program)){
		$label = $db->query("select DESCRIPTION FROM sys_prg_language_labels  where language_id = :language_id
                                                                     and label = :label and ((program is null) or (program = '')) ", array( 
				':label' => $label,
				':language_id' => $_SESSION['sess_lang'], ))->fetchColumn();
	} else {
		$label = $db->query("select DESCRIPTION FROM sys_prg_language_labels  where language_id = :language_id
                                                                     and label = :label and program = :program ", array( 
				':label' => $label,
				':language_id' => $_SESSION['sess_lang'], 
				':program' => $program,  ))->fetchColumn();			
	}
	if (empty($label)){
		return ("Label $label for program $program not defined");
	} else {
		return $label;
	}
}

function GetMessage($message_name){
	if (!empty($message_name)) {
		$db = Db_Pdo::getInstance();
	    $msg_result = $db->query('select MESSAGE from sys_messages where language_id = :language_id and message_name = :message_name',array(
	    	':language_id' => $_SESSION['sess_lang'],
	    	':message_name' => $message_name,
	    ))->fetchColumn();
	    return (empty($msg_result) ? 'Message not defined!' : $msg_result);
	}
}
function MakeButtons($program=null){
    
    $query=($program==null)?"select NAME, VALUE, type as BUTTON_TYPE, ONCLICK from program_language_buttons where (trim(program)= '' or program is null) and (language_id = '". $_SESSION['sess_lang'] ."') order by type DESC, button_seq asc":
                            "select NAME, VALUE, type as BUTTON_TYPE, ONCLICK from program_language_buttons where (program = '$program') and (language_id = '". $_SESSION['sess_lang'] ."') order by type DESC, button_seq asc";

    $result=dbselect($query,false);
    if ($result <> null){
			        print('<!-- Start bottoniera -->');
			        print("\n");
			        print('<table width="100%" border="0" >');
			        print('  <tr>');
			        $change_column=null;
			        for ($x=0; $x<$result['NROWS']; $x++){
			            $buttons_array['BUTTON'][]=$result['ROWS'][$x]['NAME'];
			            $buttons_array['BUTVALUE'][]=$result['ROWS'][$x]['VALUE'];
			            $alignment=(strtoupper(trim($result['ROWS'][$x]['BUTTON_TYPE']))=='SUBMIT')?"left":"right";
			            if (($change_column == null ) or ($change_column <> strtoupper(trim($result['ROWS'][$x]['BUTTON_TYPE'])))){
			                if ($change_column <> null) {
			                    print('</td>');
			                    print("\n");
			                }
			                $change_column=strtoupper($result['ROWS'][$x]['BUTTON_TYPE']);
			                print('<td width="50%" align="');
			                print($alignment);
			                print('" class="button_bar" >');
			            }
			            print('  <input class="button_bar" name="');
			            print($result['ROWS'][$x]['NAME']);
			            print('" type="');
			            print($result['ROWS'][$x]['BUTTON_TYPE']);
			            print('" value="');
			            print($result['ROWS'][$x]['VALUE']);
			            print('" >');
			        }
			        print('</td>');
			        print('  </tr>');
			        print("\n");
			        print('</table>');
			        print('<!-- End bottoniera -->');
			        print("\n");
    }
}
