<?php

class Session_Namespace implements IteratorAggregate
{
	protected $_namespace;

	public function __construct($name)
	{
		if (empty($name)) {
			throw new Session_Exception('Il nome della sessione deve essere specificato');
		}

		$this->_namespace = $name;
	}

	public function lock()
	{
		$_SESSION['__LOCKED'][$this->_namespace] = true;
	}

	public function unlock()
	{
		$_SESSION['__LOCKED'][$this->_namespace] = false;
	}

	public function isLocked()
	{
		return (bool)$_SESSION['__LOCKED'][$this->_namespace];
	}

	public function delete()
	{
		if ($this->isLocked()) {
			throw new Session_Exception('Questo nome di sessione è bloccato');
		}

		unset($_SESSION[$this->_namespace]);
		unset($_SESSION['__LOCKED'][$this->_namespace]);
	}

	public function deleteIfEmpty()
	{
		if (empty($_SESSION[$this->_namespace])) {
			$this->delete();
			return true;
		}

		return false;
	}

	public function __set($name, $value)
	{
		if ($this->isLocked()) {
			throw new Session_Exception('Questo nome di sessione è bloccato');
		}

		$name = (string) $name;

		$_SESSION[$this->_namespace][$name] = $value;
	}

	public function & __get($name)
	{
		return $_SESSION[$this->_namespace][$name];
	}

	public function __isset($name)
	{
		return isset($_SESSION[$this->_namespace][$name]);
	}

	public function __unset($name)
	{
		unset($_SESSION[$this->_namespace][$name]);
	}

	public function getIterator() {
		return new ArrayIterator($_SESSION[$this->_namespace]);
	}
}
