<?php

$xlsBar = IsSet ($xlsBar) ? $xlsBar : 'Y';
// valorizzo

$ManagedTable = new MyDbForm($dbTable, $_SESSION['sess_lang']);
$sess_lang = $_SESSION['sess_lang'];
//
if (isSet($recursiveFields))
	$ManagedTable->setRecursiveFields($recursiveFields);
if (isSet ($backAfterInsert))
	$ManagedTable->setAfterInsertLocation($backAfterInsert);
if (isSet ($backAfterUpdate))
	$ManagedTable->setAfterUpdateLocation($backAfterUpdate);

if (isSet ($formTitleAdd))
	$ManagedTable->SetFormTitle($ManagedTable->GetFormTitle() . ' - ' . $formTitleAdd);



if((isSet($_GET[$ManagedTable->GetFormKey()]) and !empty($_GET[ $ManagedTable->GetFormKey()]))
	or (isSet($_POST[ $ManagedTable->GetFormKey()]) and !empty($_POST[ $ManagedTable->GetFormKey()]))){
		$ManagedTable->SetFormMode((isset($_GET['mode']) ? $_GET['mode'] : 'modify'), ' WHERE ' . $ManagedTable->GetFormKey() . ' = ' .
			(empty($_GET[$ManagedTable->GetFormKey()]) ? $_POST[$ManagedTable->GetFormKey()] : $_GET[$ManagedTable->GetFormKey()]) );
} else {
	$ManagedTable->SetFormMode('insert',null);
}

if (isSet ($_GET['wk_page']))
$ManagedTable->AddFormActionParameter('&wk_page=' . $_GET['wk_page']);

if (isSet ($recallPage)) {
	$_action = '&' . substr($recallPage, 1);
	$ManagedTable->AddFormActionParameter($_action);
	$recallPage = $recallPage . '&';
} else {
	$recallPage = '';
}

if ($filterField > '') {
	$ManagedTable->HideFormField($filterField);
	$ManagedTable->SetFormFieldValue($filterField, $$filterField);
}

$linkOrder = $hideForm ? '' : 'IN_LINK DESC, ';
$del_message = get_label('del_message');
$tableFieldsQuery = 'select TABLE_NAME, ' .
		'PAGED, ' .
		'FIELD_NAME, ' .
		'DATA_TYPE, ' .
		'TYPE as FORMAT, ' .
		'IS_KEY, ' .
		'LISTED, ' .
		'IN_LINK, ' .
		'VALIDATION, ' .
		'LOOKUP, ' .
		'SHORTDESC , ' .
		'DESCRIPTION, ' .
		'TABLE_ORDER, ' .
		'TABLE_ORDER_TYPE ' .
		'from sys_forms ' .
		'left join sys_forms_fields ON (sys_forms_fields.form_id=sys_forms.form_id) ' .
		'left join sys_forms_fields_labels ON ( (sys_forms_fields_labels.field_id=sys_forms_fields.field_id) and (sys_forms_fields_labels.language_id=' . $_SESSION['sess_lang'] . ')) ' .
		'where  (sys_forms.form_name=\'' . $dbTable . '\')  ' .
		'order by IS_KEY DESC, ' . $linkOrder . ' vseq, hseq';
// var_dump($tableFieldsQuery);

if (!($tableFields = dbselect($tableFieldsQuery))){
	print('<h2>'.$dbTable.' Form not defined!</h2>' . PHP_EOL);
	exit;
}

$selectTableQuery = "";
$firstField = TRUE;
$leftJoin = "";

$table = strtolower($tableFields['ROWS'][0]['TABLE_NAME']);

$linkKey = '';
$imgLink = '';
for ($i = 0; $i < $tableFields['NROWS']; $i++) {
	$fieldName = $tableFields['ROWS'][$i]['FIELD_NAME'];
	$fieldLabel = $tableFields['ROWS'][$i]['SHORTDESC'];
	if ($tableFields['ROWS'][$i]['IS_KEY'] == 'Y') {
		$fieldKey = $tableFields['ROWS'][$i]['FIELD_NAME'];
		$linkKey .= $linkKey == '' ? 'where ' . $fieldKey . '=#' . $fieldKey . '# ' : ' and ' . $fieldKey . '=#' . $fieldKey . '# ';
		$imgKey .= $imgKey == '' ? "where $fieldKey=',$table.$fieldKey" : ",' and $fieldKey=',$table.$fieldKey";
		$formKey .= '&' . $fieldKey . '=#' . $fieldKey . '#';
	}
} // for

$tableOrderArray = array ();
// Resetto le variabili per i filtri
for ($i = 0; $i < $tableFields['NROWS']; $i++) {
	if ($tableFields['ROWS'][$i]['TABLE_ORDER'] > 0) {
		$tableOrderArray[$tableFields['ROWS'][$i]['TABLE_ORDER']] = array (
		$table.'.'.$tableFields['ROWS'][$i]['FIELD_NAME'],
		$tableFields['ROWS'][$i]['TABLE_ORDER_TYPE'],
		$tableFields['ROWS'][$i]['SHORTDESC'],
		$tableFields['ROWS'][$i]['DATA_TYPE']
		);
	}
	$fieldName = $tableFields['ROWS'][$i]['FIELD_NAME'];
	$fieldLabel = $tableFields['ROWS'][$i]['SHORTDESC'];
	if (($tableFields['ROWS'][$i]['LISTED'] == 'Y' or $tableFields['ROWS'][$i]['LISTED'] == 'F' or (isset($_GET['xlsExport']) and $_GET['xlsExport'] == 'Y')) and ($tableFields['ROWS'][$i]['FIELD_NAME'] <> $filterField)) {
		$LangFilter = '';
		$LookFilter = '';
		// Se la colonna  stata definita come filtro, deve essere visualizzata e gestita come campo di selezione
		if ($tableFields['ROWS'][$i]['LISTED'] == 'F') {
			if ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'AJAX' || $tableFields['ROWS'][$i]['DATA_TYPE']=='DJSELECT') {
				$splarray = explode(';', $tableFields['ROWS'][$i]['LOOKUP']);
				for ($n = 0; $n < sizeof($splarray); $n++) {
					if (preg_match('[TABLE=(.+)]', $splarray[$n], $searched_value)) {
						$joinTable = strtolower($searched_value[1]);
						$aliasSearchJoin = $joinTable . '_' . $tableFields['ROWS'][$i]['FIELD_NAME'];
					}
					if (preg_match('[DESCRIPTION=(.*)]', $splarray[$n], $searched_value)) {
						$descSearchField = $searched_value[1];
					}
				}
				$filterFieldsArray[] = array (
				$tableFields['ROWS'][$i]['DATA_TYPE'],
				$tableFields['ROWS'][$i]['FIELD_NAME'],
				$tableFields['ROWS'][$i]['DESCRIPTION'],
				$tableFields['ROWS'][$i]['SHORTDESC'],
				$aliasSearchJoin . '.' . $descSearchField
				);
			} else {
				$filterFieldsArray[] = array (
				$tableFields['ROWS'][$i]['DATA_TYPE'],
				$tableFields['ROWS'][$i]['FIELD_NAME'],
				$tableFields['ROWS'][$i]['DESCRIPTION'],
				$tableFields['ROWS'][$i]['SHORTDESC'],
				$table
				);
				$setFilter = TRUE;
			}
		}
		if ($tableFields['ROWS'][$i]['LOOKUP'] > '') {
			if (($tableFields['ROWS'][$i]['DATA_TYPE'] == 'GRPSELECT' or $tableFields['ROWS'][$i]['DATA_TYPE'] == 'DESCFIELD') and (preg_match('[QUERY=(.+)]', $tableFields['ROWS'][$i]['LOOKUP'], $searched_value))) {
				$fieldQuery = $searched_value[1];
				$fieldQuery = preg_replace('|<SESS_LANG/>|', $_SESSION['sess_lang'] , $fieldQuery);
				$selectTableQuery .= $firstField ? " ($fieldQuery) as \"$fieldLabel\" " : ", ($fieldQuery) as \"$fieldLabel\" ";
			}
			elseif ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'AJAX' or $tableFields['ROWS'][$i]['DATA_TYPE'] == 'DJSELECT') {
				if (preg_match_all('[<FIELD>(.+)</FIELD><JOIN>(.+)</JOIN>]', $tableFields['ROWS'][$i]['LOOKUP'], $lookResult)) {
					if (preg_match_all('[<(.+)/>]U', $lookResult[2][0], $sess_var)) {
						for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
							$lookResult[2][0] = preg_replace('[' . $sess_var[0][$z] . ']', $_SESSION[$sess_var[1][$z]], $lookResult[2][0]);
						} // for
					}
					$selectTableQuery .= $firstField ? " " . $lookResult[1][0] . " as \"$fieldLabel\"" : ", " . $lookResult[1][0] . " as \"$fieldLabel\"";
					$leftJoin .= $lookResult[2][0];
				}
			} else {
				$splarray = explode(';', $tableFields['ROWS'][$i]['LOOKUP']);
				for ($n = 0; $n < sizeof($splarray); $n++) {
					if (preg_match('[TABLE=(.+)]', $splarray[$n], $searched_value)) {
						$joinTable = strtolower($searched_value[1]);
						$aliasJoin = $joinTable . '_' . $tableFields['ROWS'][$i]['FIELD_NAME'];
					}
					if (preg_match('[KEY-F=(.*)]', $splarray[$n], $searched_value)) {
						$fkeyField = $searched_value[1];
					}
					elseif (preg_match('[KEY-P=(.*)]', $splarray[$n], $searched_value)) {
						$pkeyField = $searched_value[1];
					}
					elseif (preg_match('[KEY=(.*)]', $splarray[$n], $searched_value)) {
						$pkeyField = $searched_value[1];
						$fkeyField = $searched_value[1];
					}
					if (preg_match('[DESCRIPTION=(.*)]', $splarray[$n], $searched_value)) {
						$descField = $searched_value[1];
					}
					if (preg_match('[FILTER=(.*)]', $splarray[$n], $searched_value)) {
						if (preg_match_all('[<(.+)/>]U', $searched_value[1], $sess_var)) {
							for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
								$searched_value[1] = preg_replace('[' . $sess_var[0][$z] . ']', $GLOBALS[$sess_var[1][$z]], $searched_value[1]);
							} // for
						}
						$LookFilter = $searched_value[1];
					}
					if (preg_match('[LANGUAGE=(.*)]', $splarray[$n], $searched_value)) {
						$LangFilter = $searched_value[1] == 'Y' ? " and ($aliasJoin.language_id = " . $_SESSION['sess_lang'] . ") " : "";
					}
				} // for
				$LookFilter = $LookFilter > '' ? " and ( $aliasJoin.$LookFilter ) " : '';
				$selectTableQuery .= $firstField ? " $aliasJoin.$descField as \"$fieldLabel\"" : ", $aliasJoin.$descField as \"$fieldLabel\"";
				$leftJoin .= " left join $joinTable $aliasJoin ON (($aliasJoin.$pkeyField = $table.$fkeyField) $LookFilter $LangFilter ) ";
			}
		} else {
			if ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'DATE' and $tableFields['ROWS'][$i]['FORMAT'] > '') {
				$dateFormat = explode(',', $tableFields['ROWS'][$i]['FORMAT']);
				$linkField = "date_format($table.$fieldName,'" . $dateFormat[1] . "')";
			} elseif ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'FILE'){


				$linkField = '(case when trim('.$table.'.'.$fieldName.') > \'\' then ' .
						'concat(\'<img src="graphics/data.gif" style="padding-left:5px;padding-right:5px;" ' .
						'id="\','.$fieldKey.',\'" ' .
						'onclick="' .
						'window.open(\',"\'",\'get_file.php?wk_inline=Y&f=\','.$fieldKey.',\'-\',\''.$fieldName.'-\','.$fieldName.',"\'",\');' .
						'" ><span dojoType="dijit.Tooltip" connectId="\','.$fieldKey.',\'" style="display:none;" > ' .
						'<img src="thumbnailNew.php?src=\','.$fieldKey.',\'-'.$fieldName.'-\', '.$fieldName.',\'&maxw=300"></span>' .
						'\') ' .
						' else \'\' end ) ';


			} else {
				$linkField = "$table.$fieldName";
			}
			$selectTableQuery .= $firstField ? " $linkField as $fieldLabel " : ", $linkField as \"$fieldLabel\" ";
		}
		$firstField = FALSE;
	} elseif ($tableFields['ROWS'][$i]['IS_KEY'] == 'Y') {
		$selectTableQuery .= $firstField ? ' ' . $tableFields['ROWS'][$i]['FIELD_NAME'] . ' ' : ", " . $tableFields['ROWS'][$i]['FIELD_NAME'] . " ";
		$firstField = FALSE;
	}
} // for
// imposto la sezione di ordinamento della query

$initQuery = 'select ';
// se non ho chiavi di ordinamento ordino su fieldKey decrescente
$orderToken=' ORDER BY ';

if (sizeof($tableOrderArray) > 0) {
	ksort($tableOrderArray);
	if (isSet ($wk_ORDER)) {
		for ($index = 0; $index < sizeof($tableOrderArray); $index++) {
			if ($tableOrderArray[$index][2] == $wk_ORDER) {
				$type = $orderType == 'DESC' ? $orderType : 'ASC';
				$element = array_splice($tableOrderArray, $index, 1);
				$element[0][1] = $type;
				break;
			}
		}
		array_unshift($tableOrderArray, $element[0]);
	} else {
		$tableOrderArray[0] = array (
		$fieldKey,
			'DESC',
		$fieldKey,
			'number'
			);
	}
	$i=0;
	foreach ($tableOrderArray as $key => $orderVal) {
		// Problema : se l'ordinamento  su una colonna select non lo faccio sul field_name (che  un id) ma sulla shortdesc (che  la decodifica dell'id come da lookup)
		//            se  su un campo data lo faccio sul field_name altrimenti me lo ordina come una stringa
		if(!is_null($orderVal[0]) ){
			if ($orderVal[3] == 'DATE') {
				$tableOrder .= $orderToken . $orderVal[0] . ' ' . $orderVal[1];
				$orderToken = ', ';
			} else {
				$tableOrder .= $orderToken . $orderVal[0] . ' ' . $orderVal[1];
				$orderToken = ', ';
			}

		}
	}
}

if (isSet ($_POST['clearButton']) and ($_POST['clearButton'] > '')) {
	// reset sessions variables filter
	unset($_SESSION['filter_'.$dbTable]);
} elseif (isSet ($_POST['filterButton']) and ($_POST['filterButton'] > '')) {
	unset($_SESSION['filter_'.$dbTable]);
	$_SESSION['filter_' . $dbTable] = array ();
	foreach ($_POST as $key => $value) {
	    if(preg_match('/filter_/', $key)){
            $_SESSION['filter_' . $dbTable][$key] = $value;
        }
	}
}


$whereFilter = ' WHERE 1 ';

if(isset($_SESSION['filter_' . $dbTable])){
	foreach ($_SESSION['filter_' . $dbTable] as $fieldFilter => $filterValue) {
		if(!empty($filterValue)){
		    preg_match('/filter_(.*)/', $fieldFilter,$matches);

			switch ($ManagedTable->getFieldDataType($matches[1])) {
				case 'NONE' :
					break;
				case 'TEXT' :
				case 'TEXTAREA' :
					$whereFilter .= " AND ($table." . $matches[1] . ' REGEXP "' . $filterValue .'") ';
					break;
				case 'DATE' :
					break;
				default :
					$whereFilter .= " AND ($table." . $matches[1] . ' = "' . $filterValue .'") ';
					break;
			}
		}
	}
}

if (isSet ($dbFilter)) {
	$hrefDbFilter .= '&dbFilter=' . $dbFilter;
	$whereFilter .= $whereFilter == '' ? " where $dbFilter " : " and $dbFilter ";
}

$copyLabel = get_label('copyLabel');
$delLabel = get_label('delLabel');

$selectTableQuery = $initQuery . $selectTableQuery;

if (!IsSet ($hideForm) or ($hideForm == 'N')) {
	if (!isSet ($_GET['xlsExport']) and  ($_GET['xlsExport'] <> 'Y')) {
		$selectTableQuery .= ", concat('<center><img src=\"graphics/folders.gif\" STYLE=\"cursor: pointer;\" onClick=\"location.href=\'" . $_SERVER['PHP_SELF'] . $recallPage . "mode=duplicate&dbKey=$imgKey,'\'\" title=\"" . $copyLabel . "\"></center>') AS \"@\"";
		$selectTableQuery .= ", concat('<center><img src=\"graphics/webapp/deleted.gif\" STYLE=\"cursor: pointer;\" onClick=\"submit_del(\'" . $_SERVER['PHP_SELF'] . $recallPage . "mode=delete&".$ManagedTable->getFormKey()."=',".$ManagedTable->getFormKey().",'\',\'$del_message\')\" title=\"" . $delLabel . "\" ></center>') AS \"#\"";
	}
}
if ($viewTable > '') {
	$selectTableQuery .= " from " . $viewTable . ' ' . $table;
} else {
	$selectTableQuery .= " from " . $table;
}

$selectTableQuery .= $leftJoin;

if ($filterField > '') {
	$whereFilter .= $whereFilter > '' ? " and (" . $ManagedTable->GetTableName() . ".$filterField = '" . $$filterField . "') " : " where (" . $ManagedTable->GetTableName() . ".$filterField = '" . $$filterField . "') ";
}

$selectTableQuery .= $whereFilter;

 $tableOrder = $tableOrder > ' '?$tableOrder:' ORDER BY '.$fieldKey.' DESC ';



$selectTableQuery .= $tableOrder;

$findOrderKeysExp = '|(ORDER[0-9])|';
preg_match_all($findOrderKeysExp, $selectTableQuery, $arrayFound);

if(isSet($_GET['dbg']) and $_GET['dbg']=='Y') r($selectTableQuery);

$selectTable = new dbTable($selectTableQuery);

// Se la $xlsExport  definito non esporto il tutto in formato Excel
if (!isSet($_GET['xlsExport']) or $_GET['xlsExport'] <> 'Y') {
	print ('<div class="appLayout" dojoType="dijit.layout.TabContainer" id="container_' . $ManagedTable->GetFormName() . '"
									region="center" >' . PHP_EOL);

	if (!IsSet ($hideForm) or ($hideForm == 'N')) {
		print ('<div dojoType="dijit.layout.ContentPane" title="Edit" id="Edit_' . $ManagedTable->GetFormName() . '">' . PHP_EOL);
		if (IsSet ($modifyToolBar)){
			$modifyToolBar->display($ManagedTable->GetFormMode());
		}
		$ManagedTable->ShowForm();
		print('</div>' . PHP_EOL);
	}
	$selected = isSet($selected) ? $selected : '';
	if(isset($_GET['wk_page']) or (isset($_POST['filter']) and $_POST['filter'] == 'apply') or $_GET['mode'] == 'delete' or $_GET['mode'] == 'duplicate'){
		$selected = ' selected="true" ';
	}
	print ('<div dojoType="dijit.layout.ContentPane" title="Lista Record" ' . $selected . ' id="Lista_' . $ManagedTable->GetFormName() . '">' . PHP_EOL);
	if ($selectTable->getTableRows() > 0) {
		$tableTitle = get_label('selectkey');
		$selectTable->setFormKey($fieldKey);
		$selectTable->SetColumnHeader($fieldKey,'<img src="graphics/page_edit.png" >');
		for ($index = 0; $index < sizeof($tableFields['ROWS']); $index++) {
			if ($tableFields['ROWS'][$index]['FORMAT'] > '') {
				if (is_object($selectTable->_tableData[$tableFields['ROWS'][$index]['SHORTDESC']])) {
					$colType = explode(',', $tableFields['ROWS'][$index]['FORMAT']);
					$selectTable->_tableData[$tableFields['ROWS'][$index]['SHORTDESC']]->SetColumnType($colType[0], $colType[1]);
				}
			}
		}
	}

	print ('<TABLE width="100%">' . PHP_EOL);
	if ($xlsBar == 'Y') {
		// Tool Bar per le lingue go back etcetera
		if (!IsSet ($midToolBar)){
			$midToolBar = new toolBar();
			$midToolBar->SetRightLinks('<img src="graphics/mime/msexcel.gif" ' .
				'STYLE="cursor: pointer; padding-left: 20px; padding-right: 5px;" ' .
				'onclick="location.href=\'' . $_SERVER['REQUEST_URI'].  '&xlsExport=Y\'" title="' .
			get_label('exportXls') . '" >&nbsp;' . get_label('exportXls') . '&nbsp;');
			$midToolBar->SetCenterLinks($tableTitle);
		}

	}
	if (IsSet ($midToolBar)){
			$midToolBar->display($ManagedTable->GetFormMode());
	}

	$tokenAction = '?';
	foreach ($_GET as $field => $param) {
		if($field <> 'wk_page'){
			$formAction .= $tokenAction.$field.'='.$param;
			$tokenAction = '&';
		}
	}
	if(isset($msg) and !empty($msg)){
		print ('<tr><td align="center" class="AlertMessages" >'.$msg.'</td></tr>');
	}
	print ('<tr><td align="center" >' . PHP_EOL);
	if ($selectTable->getTableRows() == 0) {
		print ('<hr>' . PHP_EOL);
	} else {
		for ($index = 1; $index < sizeof($tableOrderArray); $index++) {
			if (isSet ($wk_ORDER) and ($wk_ORDER == $tableOrderArray[$index][2])) {
				$orderType = isSet ($orderType) ? $orderType : $tableOrderArray[$index][1];
				$selectTable->SetColumnOrder($tableOrderArray[$index][2], $orderType);
			}
			elseif (!isSet ($orderType) and ($index == 0)) {
				$orderType = $tableOrderArray[$index][1] == 'DESC' ? 'DESC' : 'ASC';
				$selectTable->SetColumnOrder($tableOrderArray[$index][2], $orderType);
			} else {
				$selectTable->SetColumnOrder($tableOrderArray[$index][2], null);
			}
		}


		$selectTable->SetWidth('95%');
		for ($index = 0; $index < sizeof($arrayFound[0]) / 2; $index++) {
			$selectTable->HideCol($arrayFound[0][$index]);
		}
		if ($tableFields['ROWS'][0]['PAGED'] == 'Y') {
			$_GET['wk_page'] = (isSet ($_GET['wk_page']) and $_GET['wk_page'] > 1) ? $_GET['wk_page'] : 1;
			$selectTable->SetPageDivision(true);
		}
		if (!isSet ($showTable) or ($showTable == 'Y')) {
			$selectTable->show($_GET['wk_page']);
		}

	}

	print ('</td></tr></table>' . PHP_EOL);
	print('</div>' . PHP_EOL);
	$ManagedTable->showFilterForm();
	print('</div>' . PHP_EOL);
} else {
	if ($selectTable->getTableRows() == 0) {
		exit;
	}
	$selectTable->HideCol($fieldKey);
	$selectTable->HideCol('File');
	$selectTable->HideCol('#');
	$selectTable->HideCol('@');
	for ($index = 0; $index < sizeof($tableFields['ROWS']); $index++) {
		if ($tableFields['ROWS'][$index]['FORMAT'] > '') {
			if (is_object($selectTable->_tableData[$tableFields['ROWS'][$index]['SHORTDESC']])) {
				$colType = explode(',', $tableFields['ROWS'][$index]['FORMAT']);
				$selectTable->_tableData[$tableFields['ROWS'][$index]['SHORTDESC']]->SetColumnType($colType[0], $colType[1]);
			}
		}
	}

	for ($index = 0; $index < sizeof($arrayFound[0]) / 2; $index++) {
		$selectTable->HideCol($arrayFound[0][$index]);
	}
	$selectTable->saveAsXls($_SESSION['FORM']);
	exit;
}
?>