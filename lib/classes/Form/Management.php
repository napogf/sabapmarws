<?php
class Form_Management {
	
	private $_form;
	private $_id;
	private $_mode = 'insert';
	protected static $_instance;
	protected $_session;
	
	protected $_form;
	
	protected $_formObj;
	
	protected function getSession()
	{
		if ($this->_session === null) {
			$this->_session = new Session_Namespace(__CLASS__);
		}

		return $this->_session;
	}	
	
	protected function __construct($form=null) {
		if(is_null($form)){
			throw new Form_Exception('Nome Form non passato!'); 
		}
		$this->_form = $form;
		$this->_formObj = new formExtended($this->_form , $_SESSION['sess_lang']);
		r($)
	}

	protected function __clone()
	{}

	public static function getInstance($form)
	{
		if (self::$_instance === null) {
			self::$_instance = new self($form);
		}

		return self::$_instance;
	}

	protected function getSession()
	{
		if ($this->_session === null) {
			$this->_session = new Session_Namespace(__CLASS__);
		}

		return $this->_session;
	}	
	
	
	public function setId($id) {
		$this->_id;
	}
	public function hasId(){
		if (isSet($this->_id)) {
			return $this->_id;
		}
		
		return false;
	}
	
	public function showForm() {
		
	}
	
	
	
}