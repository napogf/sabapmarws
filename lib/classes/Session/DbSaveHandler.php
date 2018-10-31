<?php

class Session_DbSaveHandler
{
	protected $_connection;

	protected $_table;

	protected $_columns;

	protected $_lifetime;

	protected $_sessionName;

	private $_isAlreadyRegistered = false;

	public function setConnection(PDO $connection)
	{
		$this->_connection = $connection;

		return $this;
	}

	public function getConnection()
	{
		if ($this->_connection === null) {
			throw new Session_Exception('Connessione a database per la sessione non impostata');
		}

		return $this->_connection;
	}

	public function setTable($table)
	{
		$this->_table = (string)$table;

		return $this;
	}

	public function getTable()
	{
		if ($this->_table === null) {
			throw new Session_Exception('Tabella per la sessione non impostata');
		}

		return $this->_table;
	}

	public function setColumns(array $columns)
	{
		$this->_columns = $columns;

		return $this;
	}

	public function getColumns()
	{
		$keys = array(
			'id',
			'creation',
			'lifetime',
			'data',
		);

		if ($this->_columns === null or array_keys($this->_columns) != $keys) {
			throw new Session_Exception('Colonne della tabella per la sessione non impostate');
		}

		return $this->_columns;
	}

	public function getColumn($column)
	{
		$columns = $this->getColumns();

		return $columns[$column];
	}

	public function setLifetime($lifetime)
	{
		$this->_lifetime = (int) $lifetime;

		return $this;
	}

	public function getLifetime()
	{
		if ($this->_lifetime === null) {
			$this->setLifetime( ini_get('session.gc_maxlifetime') );
		}

		return $this->_lifetime;
	}

	public function register()
	{
		if ($this->_isAlreadyRegistered) {
			throw new Session_Exception('La sessione è già stata registrata');
		}

        session_set_save_handler(
            array(&$this, 'open'),
            array(&$this, 'close'),
            array(&$this, 'read'),
            array(&$this, 'write'),
            array(&$this, 'destroy'),
            array(&$this, 'gc')
		);

		$this->_isAlreadyRegistered = true;

		return $this;
	}

	public function open($savePath, $name)
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($id)
	{
		$return = '';

		$row = $this->_find($id);

		if (!empty($row)) {
			$return = $row['data'];
		} else {
			$this->destroy($id);
		}

		return $return;
	}

	public function write($id, $data)
	{
		$return = false;

		$data = array(
			'creation'	=> time(),
			'data'		=> $data,
		);

		$row = $this->_find($id);

		if (!empty($row)) {
			$data['lifetime'] = $row['lifetime'];

			if ($this->getConnection()->query('
				UPDATE '.$this->getTable().'
				SET
					'.$this->getColumn('creation').' = :creation,
					'.$this->getColumn('lifetime').' = :lifetime,
					'.$this->getColumn('data').' = :data
				WHERE '.$this->getColumn('id').' = :id
			', array(
				':creation' => $data['creation'],
				':lifetime' => $data['lifetime'],
				':data' => $data['data'],
				':id' => $id,
			))) {
				$return = true;
			}
		} else {
			$data['lifetime'] = $this->getLifetime();

			if ($this->getConnection()->query('
				INSERT INTO '.$this->getTable().' ('.$this->getColumn('id').', '.$this->getColumn('creation').', '.$this->getColumn('lifetime').', '.$this->getColumn('data').')
				VALUES (:id, :creation, :lifetime, :data)
			', array(
				':id' => $id,
				':creation' => $data['creation'],
				':lifetime' => $data['lifetime'],
				':data' => $data['data'],
			))) {
				$return = true;
			}
		}

		return $return;
	}

	public function destroy($id)
	{
        $return = false;

        if ($this->getConnection()->query('
			DELETE
			FROM '.$this->getTable().'
			WHERE '.$this->getColumn('id').' = :id
		', array(
			':id' => $id,
		))) {
            $return = true;
        }

		$this->getConnection()->query('
			OPTIMIZE TABLE '.$this->getTable().'
		');

        return $return;
	}

	public function gc($maxlifetime)
	{
		$this->getConnection()->query('
			DELETE
			FROM '.$this->getTable().'
			WHERE ('.$this->getColumn('creation').' + '.$this->getColumn('lifetime').') <= :time
		', array(
			':time' => time(),
		));

		return true;
	}

	protected function _find($id)
	{
		return $this->getConnection()->query('
			SELECT
				'.$this->getColumn('lifetime').' AS lifetime,
				'.$this->getColumn('data').' AS data
			FROM '.$this->getTable().'
			WHERE (
					'.$this->getColumn('id').' = :id
				AND	('.$this->getColumn('creation').' + '.$this->getColumn('lifetime').') > :time
			)
			LIMIT 1
		', array(
			':id' => $id,
			':time' => time(),
		))->fetch();
	}
}
