<?php   
//---------------------------------
// Function get_origin_id
// Parameters:
// $wk_dir_id -> directories.dir_id
// $linkDB = Connection to database
// ---------------------------------
function get_origin_id($wk_dir_id,$test=false) {

    global $sess_uid;	
    $sql="SELECT DISTINCT directories.ORIGIN_ID
           FROM directories, dir_resp_reference, users, user_resp_reference
          WHERE (    (directories.dir_id = dir_resp_reference.dir_id)
                 AND (users.user_id = user_resp_reference.user_id)
                 AND (dir_resp_reference.resp_id = user_resp_reference.resp_id)
                 AND (user_resp_reference.user_id = $sess_uid)
                 AND (dir_resp_reference.dir_id = $wk_dir_id)
                )";

 	if(!($row=rowselect($sql,$test))) {
        return null;
	} else {
        return $row[0];
    }
}


//---------------------------------
// Funzione is_root
// Parameters:
// $wk_dir_id -> directories.dir_id
// $linkDB = Connection to database
//
// Return 1 if is root
//---------------------------------
function is_root($wk_dir_id) {

		if(get_origin_id($wk_dir_id) == null)
		{
			return 1;
		}
		else
		{
			return 0;
		}
}

//---------------------------------
// Function get_id_from_root
// Parameters:
// $wk_dir_id -> directories.dir_id
// $linkDB = Connection to database
//
// Nota: Determina la lista dei Codici delle Categorie presenti nella gerarchia del menu
//       dalla radice alla Categoria selezionata
// Ritorna nulla
//---------------------------------
function get_id_from_root($wk_dir_id,$test=false) {
        $Lista = array($wk_dir_id);
		if(!is_root($wk_dir_id))			
             $Lista=array_merge ($Lista, get_id_from_root(get_origin_id($wk_dir_id)));
        return $Lista;
}

//---------------------------------
// Function get_directories_from_origin
// Parameters:
// $wk_origin_id -> directories.origin_id
// $linkDB = Connection to database
//
// Nota: Seleziona l'Elenco di categorie Figle di una Categoria
// Ritorna Record di Categorie
//---------------------------------
function get_directories_from_origin($wk_origin_id,$test=false){

    global $sess_uid;
    // $sel_origin_id=($wk_origin_id==null)?"= '0' ":"=".$wk_origin_id;
    $sel_origin_id=($wk_origin_id==null)?"IS NULL ":"=".$wk_origin_id;

    $sql="SELECT DISTINCT USER_RESP_REFERENCE.USER_ID, DIRECTORIES.DIR_ID,
                DIRECTORIES.ORIGIN_ID, DIRECTORIES.DIR_SEQUENCE,
                DIR_LABELS.DESCRIPTION, DIRECTORIES.DIR_TARGET, DIRECTORIES.PARAMS,
				DIR_LABELS.SCHEDA, DIRECTORIES.FRAME
           FROM directories DIRECTORIES,
                dir_labels DIR_LABELS,
                dir_resp_reference DIR_RESP_REFERENCE,
                languages LANGUAGES,
                users USERS,
                user_resp_reference USER_RESP_REFERENCE
          where (    (DIRECTORIES.DIR_ID = DIR_LABELS.DIR_ID)
                 and (DIRECTORIES.DIR_ID = DIR_RESP_REFERENCE.DIR_ID)
                 and (LANGUAGES.LANGUAGE_ID = DIR_LABELS.LANGUAGE_ID)
                 and (LANGUAGES.LANGUAGE_ID = USERS.LANGUAGE_ID)
                 and (USERS.USER_ID = USER_RESP_REFERENCE.USER_ID)
                 and (DIR_RESP_REFERENCE.RESP_ID = USER_RESP_REFERENCE.RESP_ID)
                 and (USER_RESP_REFERENCE.USER_ID = $sess_uid)
                 and (DIRECTORIES.ORIGIN_ID ".$sel_origin_id.") ";
    $sql.=is_administrator($sess_uid)?"":"AND (DIRECTORIES.SKELETON_FLAG = 'N')";
    $sql.=       ")
          ORDER BY DIRECTORIES.DIR_SEQUENCE ASC";
	// print($sql.'<br>');

 	if(!$dir_array=dbselect($sql,false)) {
		return (false);
	}
	return($dir_array);
}

//---------------------------------
// Funzione print_Html_Item
// Parametri Ingresso:
// $CodCat = Codice Categoria, $RecordSet = Lista campi della Categoria ,$Livello = Livello nella gerarchia
// Nota: Visualizza in codice HTML una categoria in modo Gerarchico
// Ritorna nulla
//---------------------------------
function print_Html_Item($CodCat,$RecordSet,$x,$Livello){
       for ($i=0;$i<$Livello;$i++)
              print "&nbsp;&nbsp;";
       if ($Livello)
              print ('<img src="immagini/bar.gif">');
       print ('<A class="dir_tree" HREF="');
       print $PHP_SELF;
       print ('?wk_dir_id=');
       print $RecordSet[$x]['DIR_ID'];
       print ('&Livello=');
       print $Livello+1;
       print ('" onclick="javascript:apri_categoria(');
       print $RecordSet[$x]['DIR_ID'];
	   print(',');
	   print('\''.$RecordSet[$x]['DIR_TARGET'].'\'');
	   print(',');
	   print('\'&template='.$RecordSet[$x]['SCHEDA'].$RecordSet[$x]['PARAMS'].'\'');	   
	   print(',');
	   print('\''.$RecordSet[$x]['FRAME'].'\'');	   
	   // print (');');
       print (');"><IMG ALT="');
       print $RecordSet[$x]['DESCRIPTION'];
       if ($CodCat==$RecordSet[$x]['DIR_ID'])
              print ('" BORDER=0 SRC="immagini/categoria_aperta.gif');
       else
              print ('" BORDER=0 SRC="immagini/categoria_chiusa.gif');
       // print ('"><font size="-2" face="Verdana, Arial, Helvetica, sans-serif">');
	   print ('">');
       print $RecordSet[$x]['DESCRIPTION'];
       print ('</A><BR>'."\n");
}

//---------------------------------
// Funzione Crea_Menu
// Parametri Ingresso:
// $CodCat = Codice Categoria Selezionata,
// $CurrCat = Codice Categoria scandita,
// $db = Connessione verso un database MySql ,
// $Livello = Livello nella gerarchia
// Nota: Visualizza in codice HTML una categoria in modo Gerarchico
// Ritorna nulla
//---------------------------------
function Create_Menu($wk_dir_id,$Curr_dir_id,$Livello){
    $dir_array=get_directories_from_origin($Curr_dir_id,$linkDB);
	// print_r($dir_array);
    $nrows=$dir_array['NROWS'];
    $dir_rows=$dir_array['ROWS'];
    for ($x=0; $x < $nrows; $x++){
        print_Html_Item($wk_dir_id,$dir_rows,$x,$Livello);
        if (in_array($dir_rows[$x]['DIR_ID'],get_id_from_root($wk_dir_id))) {
            Create_Menu($wk_dir_id,$dir_rows[$x]['DIR_ID'],$Livello+1);
        }

    }
}



?>
