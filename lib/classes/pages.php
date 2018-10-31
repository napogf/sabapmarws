<?php
/**
 *
 *
 * @version $Id: pages.inc,v 1.3 2010/07/27 14:55:58 cvsuser Exp $
 * @copyright 2003
 **/
/**
 *
 *
 **/
class pages{
	/**
     * Constructor
     * @access protected
     */
	    var $_pages = -1;

	    function Getpages(){
			return $this->_pages;
		}

		function Setpages($newValue){
			$this->_pages = $newValue;
		}
	    var $_max_lines = 10;

	    function GetmaxLines(){
			return $this->_max_lines;
		}

		function SetmaxLines($newValue){
			$this->_max_lines = $newValue;
		}
	    var $_nrecord = -1;

	    function Getnrecord(){
			return $this->_nrecord;
		}

		function Setnrecord($newValue){
			$this->_nrecord = $newValue;
		}
	    var $_max_pages = 10;

	    function GetmaxPages(){
			return $this->_max_pages;
		}

		function SetmaxPages($newValue){
			$this->_max_pages = $newValue;
		}

	    var $_actual_page = 1;

	    function GetactualPage(){
			return $this->_actual_page;
		}

		function SetactualPage($newValue){
			$this->_actual_page = $newValue;
		}


	var $_number_pages = -1;

	function getNumberPages(){
		return ceil($this->_nrecord/$this->_max_lines);
	}


	function pages($nrecords){
		$this->Setnrecord($nrecords);
	}

	function ShowPages(){

		$totale = $this->Getnrecord();

		if ($totale <= $this->GetmaxLines()) {
		    return null;
		}

		$pagina = $this->GetactualPage();

		$baseref='?';
		reset($_GET);

		while (!is_null($key = key($_GET) ) ) {
			if ($key<>'wk_page') {
				$baseref .= $baseref=='?'?$key.'='.$_GET[$key]:'&'.$key.'='.$_GET[$key];
			}
			next($_GET);
		}
		while (!is_null($key = key($_GET) ) ) {
			if ($key<>'wk_page') {
				$baseref .= $baseref=='?'?$key.'='.$_GET[$key]:'&'.$key.'='.$_GET[$key];
			}
			next($_GET);
		}



			if (!is_array($_SESSION['filter_'.$dbTable]) and sizeof($_SESSION['filter_'.$dbTable])>0)	{
				foreach ($SESSION['filter_'.$dbTable] as $fKey => $fValue) {
					if (!empty($value[$fKey])) {
						$baseref .= $baseref=='?'?$fKey.'='.$fValue:'&'.$fKey.'='.$fValue;
					}
				}
			}

		$baseref .= $baseref=='?'? 'wk_page=': '&wk_page=';

		$quantiPerPag = $this->_max_lines;
		// quanti records per pagina

		$maxPagine = $this->_max_pages;
		// quante pagine mostrare in fondo a ogni documento

		$ultimaPag = $pagina+$maxPagine;
		// var usata per il ciclo sulle pagine
		//  l'ultima pagina di quelle visualizzate in fondo

		$pagineTot = ceil($this->_nrecord/$this->_max_lines);



		print("\n");
        print('<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" >');
        print('<tr>');
		print('<td class="page_counter" nowrap width="20%" >');
		print(get_label('page_counter').' '.$pagineTot);
		print('</td><td class="page_counter" width="100%"><table cellspacing="0" cellpadding="0" width="100%"><tr><td width="50%"></td>');
        print('<td class="page_counter" align="right" nowrap >');
		if ($pagina==1) {
		    print('<img src="graphics/webapp/firstrecd.gif" >');
			print('<img src="graphics/webapp/prevrecd.gif" >');
		} else {
			// Primo record
			print('<img src="graphics/webapp/firstrec.gif" STYLE="cursor: pointer, float:right" onclick="javascript:location.href=\''.$baseref.'1'.'\'">');
			// Pagina precedente
			print('<img src="graphics/webapp/prevrec.gif" STYLE="cursor: pointer, float:right" onclick="javascript:location.href=\''.$baseref.($pagina-1).'\'">');
		}

		print('</td><td nowrap align="center" class="page_counter">&nbsp;&nbsp;&nbsp;');
		// totale di pagine
		if ($pagineTot > 10) {
		    $prima_pagina = intval($pagina/10)<1?1:intval(($pagina)/10)*10;
			$ultimaPag = $prima_pagina + 9;
		} else {
			$prima_pagina = 1;
		}
		if ($ultimaPag > $pagineTot) {
			$ultimaPag = $pagineTot;
		}

		for ($i=$prima_pagina;$i<=$ultimaPag;$i++) {

			if ($i != $pagina) {
				print("<a href='". $baseref . $i . "' class=\"page_counter\">");
			}
			if ($pagineTot != 1) {
				print($i . " ");
			}
			if ($i != $pagina) {
				print("</a> ");
			}

		}


		print('&nbsp;&nbsp;&nbsp;</td><td class="page_counter" align="left" nowrap >');
		if ($pagina < $pagineTot) {
			print('<img src="graphics/webapp/nextrec.gif" STYLE="cursor: pointer" onclick="javascript:location.href=\''.$baseref.($pagina+1).'\'">');
			print('<img src="graphics/webapp/lastrec.gif" STYLE="cursor: pointer" onclick="javascript:location.href=\''.$baseref.$pagineTot.'\'">');

//			print("<a href='". $baseref . ($pagina+1) . "' class=\"page_counter\">--&gt;&gt;</a>");
		} else {
			print('<img src="graphics/webapp/nextrecd.gif">');
		    print('<img src="graphics/webapp/lastrecd.gif">');
		}
        print('</td><td width="50%"></td>');
		print('</tr></table></td>');
        print('</tr>');
        print('</table>');
		print("\n");

	}

}


?>