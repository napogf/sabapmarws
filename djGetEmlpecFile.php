<?php
/*
 * Created on 28/set/2012
 *
 * djGetEmlpecFile.inc
*/
include "login/autentication.php";
require_once("inc/dbfunctions.php");
$db = Db_Pdo::getInstance();

class myhtmlETable extends htmlETable {

		function GetColValue($column,$i){
			if (is_null($this->GetColumnHref($column,$i))) {
				if (($this->getColSubstring($column)>0) and (strlen($this->_tableData[$column]->GetValue($i))>$this->getColSubstring($column))){
					$content ='<span  id="'.$column.'_'.$i.'" >'.substr($this->_tableData[$column]->GetValue($i),0,$this->getColSubstring($column)).'</span>';
					$content.='<span dojoType="dijit.Tooltip" connectId="'.$column.'_'.$i.'" style="display:none;"><div style="max-width:250px; display:block;">'.$this->_tableData[$column]->GetValue($i).'</div></span>';
					return $content;
				} else {
                    switch ($column) {
                        case 'FILENAME':
                            return 	'<span>
    						<i class="fa fa-download cursor"  style="padding-left:5px;padding-right:5px;" ' .
                                'id="file_'.$this->_tableData['FILENAME']->GetValue($i).'" ' .
                                'onclick="' .
                                'window.open(\'get_file.php?wk_inline=Y&f='.$this->_tableData[$column]->GetValue($i).'\');' .
                                '" > </i></span> ';
                            break;
                        case 'ID':
                            return '<span  id="'.$column.'_'.$i.'" onClick="loadPec('.$this->_tableData[$column]->GetValue($i).')" >
						    <i class="fa fa-envelope-o mouseOn"> </i></span>';
                            break;
                    }
			    	return $this->_tableData[$column]->GetValue($i);
				}

			} else {
				$value=$this->GetColumnHref($column,$i);
				$pattern='|<([a-zA-Z]{1,3}).*>|';
			    preg_match_all($pattern, $value, $match);
				$closeTag='</'.$match[1][0].'>';
				return $value.$this->_tableData[$column]->GetValue($i).$closeTag;

			}
		}


}
$dispEmlFilesQuery = null;

$fascicolo = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id LIMIT 1', [
    ':pratica_id' => $_GET['PRATICA_ID'],
])->fetchColumn();

if($fascicolo){
    $dispEmlFilesQuery = 'select PEC_ID as ID, MITTENTE, SUBJECT, concat(pec_id,\'_\',TYPE,\'_\',mail_hash,\'.eml\') as FILENAME FROM arc_pratiche_pec ' .
        'where pratica_id IN (select pratica_id FROM pratiche_fascicoli where fascicolo_id = '.$fascicolo . ')' ;
} else {
    $dispEmlFilesQuery = 'select PEC_ID as ID, MITTENTE, SUBJECT, concat(pec_id,\'_\',TYPE,\'_\',mail_hash,\'.eml\') as FILENAME From arc_pratiche_pec ' .
        'where pratica_id = '.$_GET['PRATICA_ID'] ;
}

if($dispEmlFilesQuery !== null){
    $destTable = new myHtmlEtable($dispEmlFilesQuery);
    if($destTable->getTableRows()>0){
        $destTable->show();
        print('<div dojoType="dijit.layout.ContentPane" id="dispPec" style="margin-top: 20px;" href="djDisplayPec.php" >');

        print ('</div>');
    }

}



?>