<?php

/**
 *
 *
 * @version $Id: menu_y.inc,v 1.4 2011/03/01 16:17:29 cvsuser Exp $
 * @copyright 2003
 **/
/**
 *
 *
 **/

class menu {
	/**
	 * Constructor
	 * @access protected
	 */
	var $_menuStruct;

	public function __construct(){
	    $this->loadMenu($_SESSION['sess_uid']);
	}

	function GetmenuStruct() {
		return $this->_menuStruct;
	}

	function SetmenuStruct($value) {
		$this->_menuStruct = $value;
	}

	var $_PersonName;
	function GetPersonName() {
		return $this->_PersonName;
	}
	function SetPersonName($value) {
		$this->_PersonName = $value;
	}

	function getContent($value) {
		if ($value['MENU_FUNCTION'] > '') {
			$linkPage = preg_match("|\?|", $value['MENU_FUNCTION']) ? $value['MENU_FUNCTION'] . '&menu_id=' . $value['MENU_ID'] : $value['MENU_FUNCTION'] . '?menu_id=' . $value['MENU_ID'];
			return $linkPage;
		}
		elseif ($value['INITPAGE'] > '') {
			return '/main_page.php?template=' . $value['INITPAGE'] . '&menu_id=' . $value['MENU_ID'];
			//			return '/parsePopup.php?template='.$value['INITPAGE'].'&menu_id='.$value['MENU_ID'];
		}
		return FALSE;
	}
	function getDefaultLink($menuId) {
		if (is_null($menuName)) {
			return 'main_page.php';
		} else {
			$defaultMenuFunQuery = 'select sm.MENU_FUNCTION ' .
			'from sys_menu sm' .
			' left join sys_menu_names mn on (mn.menu_name=sm.menu_name) ' .
			' where sm.menu_name=\'' . $menuName . '\' ' .
			' order by sm.menu_sequence limit 1';
			if ($menuFuncResult = dbselect($defaultMenuFunQuery)) {
				return $menuFuncResult['ROWS'][0]['MENU_FUNCTION'];
			} else {
				return 'main_page.php';
			}
		}
	}

	function loadMenu($sess_uid, $menu = 'TOP') {

		$returnArray = array ();
		$menuQuery = "SELECT   DISTINCT " .
					"sys_menu.DESCRIPTION as MENU_TYPE, " .
					"sys_menu.*, " .
					"sys_menu_labels.*, " .
					"sys_users.USER_ID,
									CONCAT(trim(sys_users.FIRST_NAME),' ',TRIM(sys_users.LAST_NAME)) AS PERSON_NAME,
				                    sys_languages.W_MESSAGE
				           FROM (sys_menu, sys_users)
						   LEFT JOIN sys_user_resp_reference  ON ( sys_user_resp_reference.USER_ID = sys_users.USER_ID)
						   LEFT JOIN sys_responsabilities  ON ( sys_responsabilities.RESP_ID = sys_user_resp_reference.RESP_ID)
						   LEFT JOIN sys_menu_labels ON ((sys_menu.MENU_ID = sys_menu_labels.MENU_ID) AND (sys_menu_labels.LANGUAGE_ID = sys_users.LANGUAGE_ID))
						   LEFT JOIN sys_languages ON (sys_languages.LANGUAGE_ID = sys_users.LANGUAGE_ID)
						   LEFT JOIN sys_menu_resp_ref mrr ON ( mrr.RESP_ID = sys_user_resp_reference.RESP_ID )
						   WHERE ( (sys_menu.MENU_NAME = '$menu' )
						   		 AND (sys_users.USER_ID = " .$_SESSION['sess_uid']. ")
								 AND (sys_menu.MENU_ID = mrr.MENU_ID)
				                 )
				           ORDER BY sys_menu.MENU_SEQUENCE ASC";


		$regexp = "|<MENU>(.*)</MENU>|";
		$dirmeuexp = "|<DIRMENU>(.*)</DIRMENU>|";
		$menuLevel = dbselect($menuQuery, false);
		if (is_null($this->GetPersonName())) {
			$this->SetPersonName($menuLevel['ROWS'][0]['PERSON_NAME']);
		}
		$menuChoice = null;
		for ($i = 0; $i < $menuLevel['NROWS']; $i++) {
			if (preg_match($regexp, $menuLevel['ROWS'][$i]['SUBMENU'], $menuChoice)) {
				$returnArray[] = array (
					'menu_id' => $menuLevel['ROWS'][$i]['MENU_ID'],
					'label' => $menuLevel['ROWS'][$i]['DESCRIPTION'],
					'title' => $menuLevel['ROWS'][$i]['ALT_TAG'],
					'content' => $this->GetContent($menuLevel['ROWS'][$i]
				), 'submenu' => $this->loadMenu($_SESSION['sess_uid'], $menuChoice[1]));
			}
			elseif (preg_match($dirmeuexp, $menuLevel['ROWS'][$i]['SUBMENU'], $menuChoice)) {
				$returnArray[] = array (
					'menu_id' => $menuLevel['ROWS'][$i]['MENU_ID'],
					'label' => $menuLevel['ROWS'][$i]['DESCRIPTION'],
					'title' => $menuLevel['ROWS'][$i]['ALT_TAG'],
					'content' => $this->GetContent($menuLevel['ROWS'][$i]
				), 'submenu' => $this->loadDirMenu($_SESSION['sess_uid'], $menuChoice[1]));
			} else {
				$returnArray[] = array (
					'menu_id' => $menuLevel['ROWS'][$i]['MENU_ID'],
					'label' => $menuLevel['ROWS'][$i]['DESCRIPTION'],
					'title' => $menuLevel['ROWS'][$i]['ALT_TAG'],
					'content' => $this->GetContent($menuLevel['ROWS'][$i]
				));
			}
		} // for
		// r($returnArray,false);
		if ($menu == 'TOP') {
			$this->SetmenuStruct($returnArray);
		} else {
			return $returnArray;
		}
		return TRUE;
	}

	function loadDirMenu($sess_uid, $dirId) {
		$returnArray = array ();
		$dirQuery = "select distinct dirs.DIR_ID, dirs.DIR_TARGET, dl.DESCRIPTION, dl.SCHEDA " .
		" from users " .
		" left join user_resp_reference urr on (urr.user_id = users.user_id) " .
		" left join dir_resp_reference drr on (drr.resp_id = urr.resp_id) " .
		" left join directories dirs on (dirs.dir_id=drr.dir_id)" .
		" left join dir_labels dl on ((dl.dir_id = dirs.dir_id) and (dl.language_id = users.language_id)) " .
		" where (users.user_id=".$_SESSION['sess_uid'].") " .
		"	and (dirs.origin_id=$dirId) " .
		"order by dirs.dir_sequence ";

		$menuChoice = null;
		if ($menuLevel = dbselect($dirQuery, false)) {
			for ($i = 0; $i < $menuLevel['NROWS']; $i++) {
				$returnArray[] = array (
					'dirMenu' => 'Y',
					'menu_id' => $menuLevel['ROWS'][$i]['DIR_ID'],
					'label' => $menuLevel['ROWS'][$i]['DESCRIPTION'],
					'title' => $menuLevel['ROWS'][$i]['ALT_TAG'],
					'content' => 'listContents.php?dirMenu=TRUE&wk_dir_id=' . $menuLevel['ROWS'][$i]['DIR_ID'] . '&template=' . $menuLevel['ROWS'][$i]['SCHEDA']
				);
			} // for
			return $returnArray;
		}
		return TRUE;
	}

	function menu_c($sess_uid) {
		$this->loadMenu($_SESSION['sess_uid']);
	}

	function showMenu($menu_id=NULL, $dirMenu = FALSE) {
		if (is_null($menu_id)) {
			$menu_id = isSet($_SESSION['sess_menu']) ? $_SESSION['sess_menu'] : 1;
		} else {
			$_SESSION['sess_menu'] = $menu_id;
		}
		$menuSelect = null;
		if ($dirMenu) {
			$originId = dbselect('select ORIGIN_ID from directories where dir_id=' . $menu_id);
			$dirMenuQuery = 'select sm.MENU_ID from sys_menu sm where sm.submenu = \'<DIRMENU>' . $originId['ROWS'][0]['ORIGIN_ID'] . '</DIRMENU>\'';
			$menuSelectedArray = dbselect($dirMenuQuery);
			$menuSelect = $menuSelectedArray['ROWS'][0]['MENU_ID'];
		} else {
			$originMenu = dbselect('select MENU_NAME from sys_menu where menu_id=\'' . $menu_id . '\'');
			if (is_array($originMenu)) {
				$menuQuery = dbselect('select sm.MENU_ID from sys_menu sm where sm.SUBMENU=\'<MENU>' . $originMenu['ROWS'][0]['MENU_NAME'] . '</MENU>\'');
				$menuSelect = $menuQuery['ROWS'][0]['MENU_ID'];
			}
		}


		$menuStruct = $this->GetmenuStruct();

		print ('<div id="Hnav"  dojoType="dijit.layout.ContentPane" region="bottom">' . "\n");
		print ('<div id="Htabs">' . "\n");
		print ('<ul>' . "\n");
		$subMenuStruct = null;
		for ($index = 0; $index < sizeof($menuStruct); $index++) {
			if ($menuStruct[$index]['menu_id'] == $menu_id) {
				$subMenuStruct = $menuStruct[$index]['submenu'];
				$menuTitle = ' -> '.$menuStruct[$index]['label'];
			} elseif (isSet($menuStruct[$index]['submenu']) and is_array($menuStruct[$index]['submenu'])) {
				for ($z = 0; $z < sizeof($menuStruct[$index]['submenu']); $z++) {
					if ($menuStruct[$index]['submenu'][$z]['menu_id']==$menu_id){
						$subMenuStruct = $menuStruct[$index]['submenu'];
						$menuTitle = ' -> '.$menuStruct[$index]['label'].' -> '.$menuStruct[$index]['submenu'][$z]['label'];
					}
				}
			}
			$selected = ($menuStruct[$index]['menu_id'] == $menu_id or $menuStruct[$index]['menu_id'] == $menuSelect) ? ' class="selezionata" ' : '';
			print ('<li' . $selected . '>');
			if (!$menuStruct[$index]['content']) {
				$menuName = $selected > '' ? $originMenu['ROWS'][0]['MENU_NAME'] : null;
				$linkPage = $this->getDefaultLink($menuName);
			} else {
				$linkPage = $menuStruct[$index]['content'];
			}

			// $linkPage = preg_match("|\?|", $linkPage) ? $linkPage . '&menu_id=' . $menuStruct[$index]['menu_id'] : $linkPage . '?menu_id=' . $menuStruct[$index]['menu_id'];

			if (preg_match('|<(\w+)/>|',$linkPage,$test)){
				$linkPage = preg_replace('|<(\w+)/>|',$_SESSION[$test[1]],$linkPage);
			}
			print ('<a href="' . $linkPage . '" title="'.$menuStruct[$index]['title'].'" >' . $menuStruct[$index]['label'] . '</a>');
			print ('</li>' . "\n");
		}
		print ('</ul>' . "\n");
		print ('</div>' . "\n");
		print ('<div style="clear:both"></div>' . "\n");
//		print ('</div>' . "\n");
		print ('<div id="Hmenu" >' . "\n");

			if (is_array($subMenuStruct)){
				print ('<ul>' . "\n");
				for ($i = 0; $i < sizeof($subMenuStruct); $i++) {
					if ($subMenuStruct[$i]['dirMenu'] == 'Y') {
						$selected = ($subMenuStruct[$i]['menu_id'] == $menu_id) ? ' class="selezionata" ' : '';
					} else {
						$selected = ($subMenuStruct[$i]['menu_id'] == $menu_id or $subMenuStruct[$i]['menu_id'] == $menuSelect) ? ' class="selezionata" ' : '';
					}
					print ('<li' . $selected . '>');
					if (preg_match('|<(\w+)/>|',$subMenuStruct[$i]['content'],$test)){
						$subMenuLink = preg_replace('|<(\w+)/>|',$_SESSION[$test[1]],$subMenuStruct[$i]['content']);
					} else {
						$subMenuLink = $subMenuStruct[$i]['content'];
					}
					print ('<a href="' . $subMenuLink . '" title="'.$subMenuStruct[$i]['title'].'" >' . $subMenuStruct[$i]['label'] . '</a>');
					print ('</li>' . "\n");
				}
				print ('</ul>' . "\n");
			}
		print('<p style="float:right; display:inline; margin:0; padding-right:10px;">'.$menuTitle.'</p>');

		print ('</div>' . "\n");
		print ('</div>' . "\n");
	}
}