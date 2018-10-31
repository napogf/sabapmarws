<?php

/**
 * @version $Id: manageDbtable.inc,v 1.17 2011/01/20 14:06:51 cvsuser Exp $
 * @copyright 2003
 **/

$xlsBar = IsSet ($xlsBar) ? $xlsBar : 'Y';

// valorizzo
if (isSet ($b_clear) and ($b_clear > '')) {
	// reset sessions variables filter
	foreach ($_SESSION as $key => $value) {
		if (preg_match('|filter_|', $key)) {
			session_unregister($key);
			//			var_dump($key);
		}
	}
	foreach ($GLOBALS as $key => $value) {
		if (preg_match('|filter_|', $key)) {
			unset ($GLOBALS[$key]);
			unset ($_POST[$key]);
		}
	}
}
elseif (isSet ($b_search) and ($b_search > '')) {
	foreach ($_SESSION as $key => $value) {
		if (preg_match('|filter_|', $key)) {
			session_unregister($key);
		}
	}
	session_register('filter_' . $dbTable);
	$_SESSION['filter_' . $dbTable] = array ();
	foreach ($_POST as $key => $value) {
		if (preg_match('|filter_|', $key)) {
			$_SESSION['filter_' . $dbTable][$key] = $value;
			$GLOBALS[$key] = $value;
		}
	}
} else {

	if (session_is_registered('filter_' . $dbTable)) {
		foreach ($_SESSION['filter_' . $dbTable] as $key => $value) {
			$GLOBALS[$key] = $value;
		}

	}
}

$ManagedTable = new MyDbForm($dbTable, $_SESSION['sess_lang']);

//
if (isSet($recursiveFields))
	$ManagedTable->setRecursiveFields($recursiveFields);
if (isSet ($backAfterInsert))
	$ManagedTable->setAfterInsertLocation($backAfterInsert);
if (isSet ($backAfterUpdate))
	$ManagedTable->setAfterUpdateLocation($backAfterUpdate);

if (isSet ($formTitleAdd))
	$ManagedTable->SetFormTitle($ManagedTable->GetFormTitle() . ' - ' . $formTitleAdd);

$ManagedTable->SetFormMode($mode, stripslashes($dbKey));

if (isSet ($wk_page))
	$ManagedTable->AddFormActionParameter('&wk_page=' . $wk_page);
if (isSet ($dbFilter)) {
	$ManagedTable->AddFormActionParameter('&dbFilter=' . $dbFilter);
}

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
'left join sys_forms_fields_labels ON ( (sys_forms_fields_labels.field_id=sys_forms_fields.field_id) and (sys_forms_fields_labels.language_id=' . $sess_lang . ')) ' .
'where  (sys_forms.form_name=\'' . $dbTable . '\')  ' .
'order by IS_KEY DESC, ' . $linkOrder . ' vseq, hseq';
// var_dump($tableFieldsQuery);

if (!($tableFields = dbselect($tableFieldsQuery))){
	print('<h2>'.$dbTable.' Form not defined!</h2>');
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
$setFilter = FALSE;
$filterFieldsArray = array ();
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
	if (($tableFields['ROWS'][$i]['LISTED'] == 'Y' or $tableFields['ROWS'][$i]['LISTED'] == 'F') and ($tableFields['ROWS'][$i]['FIELD_NAME'] <> $filterField)) {
		$LangFilter = '';
		$LookFilter = '';
		// Se la colonna  stata definita come filtro, deve essere visualizzata e gestita come campo di selezione
		if ($tableFields['ROWS'][$i]['LISTED'] == 'F') {
			if ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'AJAX') {
				$splarray = split(';', $tableFields['ROWS'][$i]['LOOKUP']);
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

		if ($tableFields['ROWS'][$i]['IN_LINK'] == 'Y') {
			if ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'DATE' and $tableFields['ROWS'][$i]['FORMAT'] > '') {
				$dateFormat = split(',', $tableFields['ROWS'][$i]['FORMAT']);
				$linkField = "date_format($table.$fieldName,'" . $dateFormat[1] . "')";
			} else {
				$linkField = "$table.$fieldName";
			}
			$fieldsKey[$fieldLabel] = $linkKey;

		}

		if ($tableFields['ROWS'][$i]['LOOKUP'] > '') {
			if (($tableFields['ROWS'][$i]['DATA_TYPE'] == 'GRPSELECT' or $tableFields['ROWS'][$i]['DATA_TYPE'] == 'DESCFIELD') and (preg_match('[QUERY=(.+)]', $tableFields['ROWS'][$i]['LOOKUP'], $searched_value))) {
				$fieldQuery = $searched_value[1];
				$fieldQuery = preg_replace('|<SESS_LANG/>|', $sess_lang, $fieldQuery);
				$selectTableQuery .= $firstField ? " ($fieldQuery) as \"$fieldLabel\" " : ", ($fieldQuery) as \"$fieldLabel\" ";
			}
			elseif ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'AJAX' or $tableFields['ROWS'][$i]['DATA_TYPE'] == 'DJSELECT') {
				if (preg_match_all('[<FIELD>(.+)</FIELD><JOIN>(.+)</JOIN>]', $tableFields['ROWS'][$i]['LOOKUP'], $lookResult)) {
					if (preg_match_all('[<(.+)/>]U', $lookResult[2][0], $sess_var)) {
						for ($z = 0; $z < sizeof($sess_var[1]); $z++) {
							$lookResult[2][0] = preg_replace('[' . $sess_var[0][$z] . ']', $GLOBALS[$sess_var[1][$z]], $lookResult[2][0]);
						} // for
					}
					$selectTableQuery .= $firstField ? " " . $lookResult[1][0] . " as \"$fieldLabel\"" : ", " . $lookResult[1][0] . " as \"$fieldLabel\"";
					$leftJoin .= $lookResult[2][0];
				}
			} else {
				$splarray = split(';', $tableFields['ROWS'][$i]['LOOKUP']);
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
						$LangFilter = $searched_value[1] == 'Y' ? " and ($aliasJoin.language_id = $sess_lang) " : "";
					}
				} // for
				$LookFilter = $LookFilter > '' ? " and ( $aliasJoin.$LookFilter ) " : '';
				$selectTableQuery .= $firstField ? " $aliasJoin.$descField as \"$fieldLabel\"" : ", $aliasJoin.$descField as \"$fieldLabel\"";
				$leftJoin .= " left join $joinTable $aliasJoin ON (($aliasJoin.$pkeyField = $table.$fkeyField) $LookFilter $LangFilter ) ";
			}
		} else {
			if ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'DATE' and $tableFields['ROWS'][$i]['FORMAT'] > '') {
				$dateFormat = split(',', $tableFields['ROWS'][$i]['FORMAT']);
				$linkField = "date_format($table.$fieldName,'" . $dateFormat[1] . "')";
			} elseif ($tableFields['ROWS'][$i]['DATA_TYPE'] == 'FILE'){


				$linkField = '(case when trim('.$table.'.'.$fieldName.') > \'\' then ' .
						'concat(\'<img src="graphics/data.gif" style="padding-left:5px;padding-right:5px;" ' .
						'id="\','.$fieldKey.',\'" ' .
						'onclick="' .
						'window.open(\',"\'",\'get_file.php?dir=img&wk_inline=Y&f=\','.$fieldKey.',\'-\',\''.$fieldName.'-\','.$fieldName.',"\'",\');' .
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
				if (preg_match('[\40]',$orderVal[2])){
					$tableOrder .= $orderToken. ' \'' . $orderVal[2] . '\' ' . $orderVal[1];
				} else {
					$tableOrder .= $orderToken . $orderVal[0] . ' ' . $orderVal[1];
				}
				$orderToken = ', ';
			}

		}
	}
}

// Imposto la sezione di filtraggio della query ##FILTRI
for ($index = 0; $index < sizeof($filterFieldsArray); $index++) {
	$filterString = 'filter_' . $filterFieldsArray[$index][1];
	$filterValue = isSet ($_POST[$filterString]) ? $_POST[$filterString] : $GLOBALS[$filterString];
	if ($filterValue > '') {
		switch ($filterFieldsArray[$index][0]) {
			case 'TEXT' :
				//						TODO verificare un possibile utilizzo di REGEXP in mysql
				$whereFilter .= $whereFilter == '' ? " where (upper($table." . $filterFieldsArray[$index][1] . ") like upper('%" . $filterValue . "%')) " : " and ($table." . $filterFieldsArray[$index][1] . " like '%" . $filterValue . "%') ";
				break;
			case 'TEXTAREA' :
				//						TODO verificare un possibile utilizzo di REGEXP in mysql
				$whereFilter .= $whereFilter == '' ? " where (upper($table." . $filterFieldsArray[$index][1] . ") like upper('%" . $filterValue . "%')) " : " and ($table." . $filterFieldsArray[$index][1] . " like '%" . $filterValue . "%') ";
				break;
			case 'DATE' :
				$whereFilter .= $whereFilter == '' ? " where ($table." . $filterFieldsArray[$index][1] . ">=date_format('" . $filterValue . "','%d-%m-%Y')) " : " and ($table." . $filterFieldsArray[$index][1] . "='" . $filterValue . "') ";
				break;
			case 'AJAX' :
				$whereFilter .= $whereFilter == '' ? " where (upper(" . $filterFieldsArray[$index][4] . ") like upper('%" . $filterValue . "%')) " : " and (" . $filterFieldsArray[$index][4] . " like '%" . $filterValue . "%') ";
				break;
			default :
				$whereFilter .= $whereFilter == '' ? " where ($table." . $filterFieldsArray[$index][1] . "='" . $filterValue . "') " : " and ($table." . $filterFieldsArray[$index][1] . "='" . $filterValue . "') ";
				break;
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
	if (!isSet ($xlsExport) and ($xlsExport <> 'Y')) {
		$selectTableQuery .= ", concat('<center><img src=\"graphics/folders.gif\" STYLE=\"cursor: pointer;\" onClick=\"location.href=\'" . $PHP_SELF . $recallPage . "mode=duplicate&dbKey=$imgKey,'\'\" title=\"" . $copyLabel . "\"></center>') AS \"@\"";
		$selectTableQuery .= ", concat('<center><img src=\"graphics/webapp/deleted.gif\" STYLE=\"cursor: pointer;\" onClick=\"submit_del(\'" . $PHP_SELF . $recallPage . "mode=delete&dbKey=$imgKey,'\',\'$del_message\')\" title=\"" . $delLabel . "\" ></center>') AS \"#\"";
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

// $tableOrder = $tableOrder > ' '?$tableOrder:' ORDER BY '.$fieldKey.' DESC ';

$selectTableQuery .= $tableOrder;

$findOrderKeysExp = '|(ORDER[0-9])|';
preg_match_all($findOrderKeysExp, $selectTableQuery, $arrayFound);

if(isSet($dbg) and $dbg=='Y') var_dump($selectTableQuery);

$selectTable = new htmlETable($selectTableQuery);

// print_query($selectTableQuery);

// Se la $xlsExport  definito non esporto il tutto in formato Excel
if (!isSet ($xlsExport) or ($xlsExport <> 'Y')) {
	print ('<table width="100%"><tr><td width="100%">' . "\n");

	if (!IsSet ($hideForm) or ($hideForm == 'N')) {
		$ManagedTable->ShowForm();
	}

	if ($selectTable->getTableRows() > 0) {
		if(!isSet($hideForm) or $hideForm<>'Y'){
			if(is_array($fieldsKey)){
				foreach ($fieldsKey as $key => $value) {
					$selectTable->SetColumnHref($key, '<a href="' . $PHP_SELF . $recallPage . 'mode=modify' . $hrefDbFilter . '&wk_page=' . $wk_page . '&dbKey=' . $linkKey . $formKey . '">');
				}
				$tableTitle = get_label('selectkey');
			} else {
				$tableTitle = 'key not defined in data dictionary for this table! call system administrator';
			}
		}
		$selectTable->HideCol($fieldKey);
		for ($index = 0; $index < sizeof($tableFields['ROWS']); $index++) {
			if ($tableFields['ROWS'][$index]['FORMAT'] > '') {
				if (is_object($selectTable->_tableData[$tableFields['ROWS'][$index]['SHORTDESC']])) {
					$colType = explode(',', $tableFields['ROWS'][$index]['FORMAT']);
					$selectTable->_tableData[$tableFields['ROWS'][$index]['SHORTDESC']]->SetColumnType($colType[0], $colType[1]);
				}
			}
		}
	}

	print ('</td></tr>');
	print ('<tr><td width="100%">' . "\n");
	print ('<hr>' . "\n");
	print ('</td></tr>');

	if ($xlsBar == 'Y') {
		// Tool Bar per le lingue go back etcetera
		if (!IsSet ($midToolBar))
			$midToolBar = new toolBar();
		$midToolBar->SetRightLinks('<img src="graphics/mime/msexcel.gif" ' .
		'STYLE="cursor: pointer; padding-left: 20px; padding-right: 5px;" ' .
		'onclick="submitDownload()" title="' .
		get_label('exportXls') . '" >&nbsp;' . get_label('exportXls') . '&nbsp;');
		$midToolBar->SetCenterLinks($tableTitle);
	}

	if (IsSet ($midToolBar))
		$midToolBar->display($ManagedTable->GetFormMode());

	print ('<tr><td width="100%">' . "\n");
	print ('<TABLE width="100%">');

	//	if ($tableTitle>'') {
	//		print('<TR><TD class="TableTitle" >');
	//		print($tableTitle);
	//		print('</TD></TR>'."\n");
	//	}

	if ($setFilter) {
		print ('<TR><TD align="center">');
		print ('<form name="filterTable" action="' . $_SERVER['REQUEST_URI'] . '" method="post">');
		print ('<table width="100%" cellpadding="0" cellspacing="0" >');
		print ('<tr><td><table width="100%">');
		print ('<tr class="ButtonBar" >');
		print ('<td align="left">');
		print ('<input name="b_search" type="submit" value="' . get_label('wrkfilter') . '">');
		print ('</td>' . "\n");
		print ('<td align="right">');
		print ('<input name="b_clear" type="submit" value="' . get_label('delfilter') . '">');
		print ('</td>' . "\n");
		print ('</tr></table></td></tr>' . "\n");
	}

	print ('<tr><td align="center" >');
	if ($selectTable->_TableRows == 0) {
		print ('<hr>');
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
		if ($setFilter) {
			$selectTable->SetTableFilter();
			for ($index = 0; $index < sizeof($filterFieldsArray); $index++) {
				switch ($filterFieldsArray[$index][0]) {
					case 'AJAX' :
						$selectTable->_tableData[$filterFieldsArray[$index][3]]->SetFiltercontent('TEXT', 'filter_' . $filterFieldsArray[$index][1], 20);
						break;
					case 'SELECT' :
						$selectTable->_tableData[$filterFieldsArray[$index][3]]->SetFiltercontent($filterFieldsArray[$index][0], $ManagedTable->_FormFields[$filterFieldsArray[$index][1]]->GetInputField('filter_'), strlen($filterFieldsArray[$index][2]));
						break;
					case 'GRPSELECT' :
						$selectTable->_tableData[$filterFieldsArray[$index][3]]->SetFiltercontent($filterFieldsArray[$index][0], $ManagedTable->_FormFields[$filterFieldsArray[$index][1]]->GetInputField('filter_'), strlen($filterFieldsArray[$index][2]));
						break;
					case 'TEXTAREA' :
						$selectTable->_tableData[$filterFieldsArray[$index][3]]->SetFiltercontent('TEXT', 'filter_' . $filterFieldsArray[$index][1], 40);
//						$selectTable->_tableData[$filterFieldsArray[$index][3]]->SetFiltercontent($filterFieldsArray[$index][0], 'filter_' . $filterFieldsArray[$index][1], $ManagedTable->_FormFields[$filterFieldsArray[$index][1]]->GetLength());
						break;
					default :
						$selectTable->_tableData[$filterFieldsArray[$index][3]]->SetFiltercontent($filterFieldsArray[$index][0], 'filter_' . $filterFieldsArray[$index][1], $ManagedTable->_FormFields[$filterFieldsArray[$index][1]]->GetLength());
						break;
				}

			}
		}

		$selectTable->SetWidth('95%');
		for ($index = 0; $index < sizeof($arrayFound[0]) / 2; $index++) {
			$selectTable->HideCol($arrayFound[0][$index]);
		}
		if ($tableFields['ROWS'][0]['PAGED'] == 'Y') {
			$wk_page = (isSet ($wk_page) and $wk_page > 1) ? $wk_page : 1;
			$selectTable->SetPageDivision(true);
		}
		if (!isSet ($showTable) or ($showTable == 'Y')) {
			$selectTable->show($wk_page);
		}
		if ($setFilter) {
			print ('</form>');
		}
	}

	print ('</td></tr></table>' . "\n");
	print ('</td></tr></table>' . "\n");
} else {
	if ($selectTable->getTableRows() == 0) {
		exit;
	}
	$selectTable->HideCol($fieldKey);
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
	$selectTable->saveAsXls();
	exit;
}
?>