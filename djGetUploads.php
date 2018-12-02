<?php
include "login/autentication.php";


class myEtable extends htmlETable {
	function getColValue($column,$i) {
		switch ($column) {
			case '#':
				return 	'<center><img src="graphics/page_delete.png" STYLE="cursor: pointer;" onClick="delUploads(\''.$this->_tableData[$column]->GetValue($i).'\')" title="Cancella File" ></center>';
				break;
			case 'File':
				return 	'<span id="file_'.$this->_tableData['UPLOAD_ID']->GetValue($i).'">
						<img src="graphics/data.gif" style="padding-left:5px;padding-right:5px;" ' .
						'id="file_'.$this->_tableData['UPLOAD_ID']->GetValue($i).'" ' .
						'onclick="' .
						'window.open(\'get_file.php?wk_inline=Y&dir='.FILES_PATH.'/'.'&f='.$this->_tableData[$column]->GetValue($i).'\');' .
						'" ></span>
						<span dojoType="dijit.Tooltip" connectId="file_'.$this->_tableData['UPLOAD_ID']->GetValue($i).'" style="display:none;" > ' .
						'<img src="thumbnailNew.php?src='.$this->_tableData[$column]->GetValue($i).'&maxw=300&dir='.FILES_PATH.'"></span>';				
				break;			
			default:
				if (is_null($this->GetColumnHref($column,$i))) {
					if (($this->getColSubstring($column)>0) and (strlen($this->_tableData[$column]->GetValue($i))>$this->getColSubstring($column))){
						$content ='<span  id="'.$column.'_'.$i.'" >'.substr($this->_tableData[$column]->GetValue($i),0,$this->getColSubstring($column)).'</span>';
						$content.='<span dojoType="dijit.Tooltip" connectId="'.$column.'_'.$i.'" style="display:none;"><div style="max-width:250px; display:block;">'.
								$this->_tableData[$column]->GetValue($i).'</div></span>';
						return $content;
					} else {
				    	return $this->_tableData[$column]->GetValue($i);
					}
	
				} else {
					$value=$this->GetColumnHref($column,$i);
					$pattern='|<([a-zA-Z]{1,3}).*>|';
				    preg_match_all($pattern, $value, $match);
					$closeTag='</'.$match[1][0].'>';
					return $value.$this->_tableData[$column]->GetValue($i).$closeTag;
	
				}
				
				break;
		}
	}
}

$db = Db_Pdo::getInstance();

$fascicolo = $db->query('SELECT fascicolo_id FROM pratiche_fascicoli WHERE pratica_id = :pratica_id LIMIT 1', [
    ':pratica_id' => $_GET['PRATICA_ID'],
])->fetchColumn();

if($fascicolo){
    $sql = 'select UPLOAD_ID, DESCRIPTION as Descrizione, PUBBLICA , concat(UPLOAD_ID,\'_\',FILENAME) as Scarica , UPLOAD_ID as \'#\' from uploads ' .
        'where pratica_id IN (select pratica_id FROM pratiche_fascicoli where fascicolo_id = '.$fascicolo . ')' ;
} else {
    $sql = 'select UPLOAD_ID, DESCRIPTION as Descrizione, PUBBLICA , concat(UPLOAD_ID,\'_\',FILENAME) as Scarica , UPLOAD_ID as \'#\'
			from uploads where PRATICA_ID='.$_GET['PRATICA_ID'];
}


$imgList = new myEtable($sql);
if ($imgList->getTableRows()>0) {
	$imgList->HideCol('UPLOAD_ID');
	$imgList->SetColumnHeader('PUBBLICA', 'Web');
	$imgList->SetColumnAttribute('Descrizione',  'width="100%"');
	$imgList->SetColumnAttribute('#',  'align="center"');
	$imgList->SetColumnAttribute('PUBBLICA',  'align="center"');
	$imgList->SetColumnAttribute('Scarica',  'align="center"');
	print('<div style="width:90%;margin: 10px">');
	$imgList->show();
    print('</div>');
}
