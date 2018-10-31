<?php
/*
 * toolBar.inc Created on 04/ago/06
 * Copyright 1999-2006 Giacomo Fonderico <giacomo@opensourcesolutions.it>
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 */
class toolBar {
  function toolBar() {
	$this->_htmlOpen='<tr><td align="center" width="100%" STYLE="padding: 0; margin:0; border: 0;" ><table width="100%"><tr>';
	$this->_htmlClose='</tr></table></td></tr>'."\n";  
  }
	var $_htmlOpen;
	var $_htmlClose;


	
	function display($mode=null){
		print($this->_htmlOpen);
		print('<td width="30%" align="left" class="lista2" >'."\n");
		$this->getLeftLinks($mode);
		print('</td>'."\n");

		print('<td width="40%" align="center" class="lista2" >'."\n");
		$this->getCenterLinks($mode);
		print('</td>'."\n");

		print('<td width="30%" align="right" class="lista2" >'."\n");
		$this->getRightLinks($mode);
		print('</td>'."\n");



		print($this->_htmlClose);		
	}	

	var $_LeftLinks;
	var $_LeftLinksMode;
			function setLeftLinks($value,$mode=null) {
					if (is_null($mode)){
						$this->_LeftLinks[]  = $value;						
					} else {
						$this->_LeftLinksMode[$mode][] = $value;
					}
			}
			function getLeftLinks($mode=null){
					if (is_null($mode)){
						for ($index = 0; $index < sizeof($this->_LeftLinks); $index++) {
							print($this->_LeftLinks[$index]);					
						}					
					} else {
						for ($index = 0; $index < sizeof($this->_LeftLinks); $index++) {
							print($this->_LeftLinks[$index]);					
						}					
						for ($index = 0; $index < sizeof($this->_LeftLinksMode[$mode]); $index++) {
							print($this->_LeftLinksMode[$mode][$index]);					
						}					

					}
			}

	var $_CenterLinks;
	var $_CenterLinksMode;
			function setCenterLinks($value,$mode=null) {
					if (is_null($mode)){
						$this->_CenterLinks[]  = $value;						
					} else {
						$this->_CenterLinksMode[$mode][] = $value;
					}
			}
			function getCenterLinks($mode=null){
					if (is_null($mode)){
						for ($index = 0; $index < sizeof($this->_CenterLinks); $index++) {
							print($this->_CenterLinks[$index]);					
						}					
					} else {
						for ($index = 0; $index < sizeof($this->_CenterLinks); $index++) {
							print($this->_CenterLinks[$index]);					
						}					
						for ($index = 0; $index < sizeof($this->_CenterLinksMode[$mode]); $index++) {
							print($this->_CenterLinksMode[$mode][$index]);					
						}					

					}
			}
	
	var $_RightLinks;
	var $_RightLinksMode;
			function setRightLinks($value,$mode=null) {
					if (is_null($mode)){
						$this->_RightLinks[]  = $value;						
					} else {
						$this->_RightLinksMode[$mode][] = $value;
					}
			}
			function getRightLinks($mode=null){
					if (is_null($mode)){
						for ($index = 0; $index < sizeof($this->_RightLinks); $index++) {
							print($this->_RightLinks[$index]);					
						}					
					} else {
						for ($index = 0; $index < sizeof($this->_RightLinks); $index++) {
							print($this->_RightLinks[$index]);					
						}					
						for ($index = 0; $index < sizeof($this->_RightLinksMode[$mode]); $index++) {
							print($this->_RightLinksMode[$mode][$index]);					
						}					
					}
			}

}




?>
