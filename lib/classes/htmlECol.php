<?php

/**
 *
 *
 * @version $Id: Column_c.inc,v 1.4 2010/07/27 14:55:58 cvsuser Exp $
 * @copyright 2003
 **/
/**
 *
 *
 **/
class htmlECol{
	/**
     * Constructor
     * @access protected
	 *
	 *
	 *
     */

	var $_ColumnType;
	function GetColumnType(){
		return $this->_ColumnType;
	}
	function SetColumnType($type,$format=null,$total=false){
		$this->setColTotal($total);
		switch ($type) {
			case 'number':
				$this->SetColDecimals(2);
				$this->SetColAlign('right');
				break;
			case 'currency':
				$this->SetColAlign('right');
				$this->SetColDecimals($format);
				break;
			case 'percent':
				$this->SetColAlign('right');
				$this->SetColDecimals(2);
				break;
			default:
				break;
		}
		$this->_ColumnType = $type;
	}
	private $_colOnClick;
	function setColOnClick($value){
		$this->_colOnClick=$value;
	}
	function getColOnClick(){
		return $this->_colOnClick;
	}
	var $_ColDecimals;
	function GetColDecimals(){
		return $this->_ColDecimals;
	}
	function SetColDecimals($value=0){
		$this->_ColDecimals = $value;
	}
	var $_ColTotal=false;
	function getColTotal(){
		return $this->_ColTotal;
	}
	function SetColTotal($value=false){
		$this->_ColTotal = $value;
	}

	var $_ColName;
	function GetColName(){
		return $this->_ColName;
	}
	function SetColName($value){
		$this->_ColName = $value;
	}


	var $_ColHeader;
	function GetColHeader(){
		return $this->_ColHeader;
	}
	function SetColHeader($value){
		$this->_ColHeader = $value;
	}


	var $_Value=array();
	function GetValue($i){
		$patterns=array('|\.|','|,|');
		$replacements=array(',','.');
		switch ($this->GetColumnType()) {
			case 'number':
				return number_format($this->_Value[$i], $this->GetColDecimals(), ',', '.');
			case 'currency':
				return number_format($this->_Value[$i], $this->GetColDecimals(), ',', '.').' ';
			case 'percent':
				return number_format($this->_Value[$i], $this->GetColDecimals(), ',', '.').' %';
			default:
				return $this->_Value[$i];
		}
	}
	function SetValue($value){
		$this->_Value[] = $value;
	}

	var $_ColHref=null;
	function GetColHref(){
		return $this->_ColHref;
	}
	function SetColHref($value){
		$this->_ColHref = $value;
	}

	var $_ColWrap=FALSE;
	function GetColWrap(){
		return $this->_ColWrap=$this->_ColWrap?' nowrap':'';
	}
	function SetColWrap($value){
		$this->_ColWrap = $value;
	}

	var $_ColAlign;
	function GetColAlign(){
		return $this->_ColAlign;
	}
	function SetColAlign($value){
		$this->_ColAlign = ' align="'.$value.'" ';
	}

	var $_ColAttribute;
	function GetColAttribute(){
		return $this->_ColAttribute;
	}
	function SetColAttribute($value){
		$this->_ColAttribute .= ' '.$value;
	}

    function SetOrder($type=null){
		$linkUrl=$_SERVER['REQUEST_URI'];
		$linkUrl=strpos($linkUrl,'wk_ORDER')>0?substr($linkUrl,0,strpos($linkUrl,'wk_ORDER')-1):$linkUrl;
    	$linkUrl=strpos($linkUrl,'?')>0?$linkUrl.'&':$linkUrl.'?';
    	if (is_null($type)){
    		$colHeader='<A class="topbar_bianco" href="'.$linkUrl.'wk_ORDER='.$this->GetColName().'">'.$this->GetColHeader().'</A>';
    	} else {
			$orderIcon=$type=='ASC'?'graphics/up.gif':'graphics/down.gif';
			$colHeader = '<div class="colsort">'.$this->GetColHeader();
			$colHeader.= '&nbsp;&nbsp;<img src="'.$orderIcon;
			$colHeader.= '" onClick="location.href=\''.$linkUrl;
			$colHeader.= 'wk_ORDER='.$this->GetColName().'&orderType=';
			$colHeader.=$type=='ASC'?'DESC':'ASC';
			$colHeader.= '\'"></div>';
    	}
    	$this->SetColHeader($colHeader);
    }



	var $_ColClass='';
	function GetColClass(){
		return $this->_ColClass.$this->addClass();
	}
	function SetColClass($value){
		$this->_ColClass = $value;
	}

	var $_ColRowClass='';
	function GetColRowClass(){
		return $this->_ColRowClass;
	}
	function SetColRowClass($value){
		$this->_ColRowClass = $value;
	}
	private $_addClass;
	function addClass($value=null){
		if (is_null($value)) {
			return $this->_addClass;
		} else {
			$this->_addClass .= ' '.$value;
		}
	}


	var $_Showed=TRUE;
	function IsShowed(){
		return $this->_Showed;
	}
	function Disable(){
		$this->_Showed = FALSE;
	}
	function Enable(){
		$this->_Showed = TRUE;
	}

	    var $_FilterContent = '';

	    function GetFilterContent(){
			return $this->_FilterContent;
		}

		function SetFilterContent($type,$value,$size){
			global $GLOBALS;
			if($size>15) $size=15;
			$size=$size>''?' size="'.$size.'" ':'';
			switch($type){
				case 'TEXT':
					$this->_FilterContent='<input type="TEXT" name="'.$value.'" value="'.$GLOBALS[$value].'"  class="TableFilter" '.$size.$maxsize.'>'."\n" ;
					break;
				case 'DATE':
					$inputField.='<input type="TEXT" name="'.$value.'" value="'.$GLOBALS[$value].'"  class="TableFilter" '.$size.'>'."\n";
					$inputField.='<input type="button" name="data" value="..." onMouseDown="cal('.$value.', event, 1)">'."\n";
					$this->_FilterContent=$inputField;
					break;
				default:
					$this->_FilterContent=$value;
			} // switch
		}

	function __construct($name){
		$this->SetColHeader($name);
		$this->SetColName($name);
	}



}


