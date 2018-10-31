<?php       //require_once('/inc/dbfunctions.php');


function get_directory_path($wk_dir_id,$dir_array=null){
       $dir_array=($dir_array==null)?array(1 => '/'):$dir_array;
       $sess_lang=$_SESSION[sess_lang];
       $sql="SELECT directories.dir_id, directories.orgigin_id, dir_labels.description,
                    dir_labels.language_id, languages.language_code
             FROM directories, dir_labels, languages
             WHERE (    (directories.dir_id = dir_labels.dir_id)
                   AND (languages.language_id = dir_labels.language_id)
                   AND (languages.language_code = '$sess_lang')
                   AND (directories.dir_id = '$wk_dir_id')
                   )";
       $path_result=dbselect($sql,false);

       
       
                      


}

function get_label($program_name,$sess_lang,$wk_label){
         $sql="select description from program_language_labels where program='$program_name' and language_code='$sess_lang'
                                                                     and label='$wk_label'";
         $result=dbselect($sql,false);
         if ($result==null){
             return ("Label $wk_label not defined");
         } else {
             return ($result[ROWS][DESCRIPTION][0]);
         }
}


 ?>
