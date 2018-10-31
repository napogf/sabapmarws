<?php

/**
	@version  2011-02-01
	@abstract 
		Estensione della PDO classica con i seguenti scopi
		 1) Poter profilare tutte le query
		 2) Poter decidere di avere una connessione globale, richiamabile senza 'global'
		[3) Impostare automaticamente il metodo di fetch dei risultati]
 */

class Db_Pdo extends PDO
{
	/* STATIC: Istanza della connessione globale corrente */
	private static $_currentSharedInstance = null;

	/* Profilatore per questa connessione */
	protected $_profiler = null;

	/* Classe del profilatore */
	protected $_profilerClass = 'Db_Profiler';

	/* Classe degli Statement */
	protected $_statementClass = 'Db_PdoStatement';

	/* Parametri della connessione corrente */
	protected $_dbParams = array();

	/**
	 * Costruttore:
	 *		- Imposto la classe per gli statament
	 *		- Imposto la modalita' di gestione degli errori
	 *		- Salvo i parametri attuali
	 */
	public function __construct($dsn, $username = '', $password = '', $driver_options = array())
	{
		$driver_options[PDO::ATTR_STATEMENT_CLASS] = array(
			$this->_statementClass,
			array(
				'adapter'	=> $this,
				'fetchMode'	=> PDO::FETCH_ASSOC,
			),
		);

		$driver_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

		if (!empty($driver_options['connection_charset'])) {
			$driver_options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $driver_options['connection_charset'];
			unset($driver_options['connection_charset']);
		}

		$this->_dbParams = array(
			'dsn'		=> $dsn,
			'username'	=> $username,
			'password'	=> $password,
		);

		return parent::__construct($dsn, $username, $password, $driver_options);
	}

	/**
	 * STATIC: Richiedo la connessione globale corrente
	 *
	 * Se non esiste, NON la creo: questa deve essere impostata
	 * in fase di configurazione del sistema
	 */
	public static function getInstance()
	{
		if (self::$_currentSharedInstance === null) {
			throw new Db_Exception('Nessuna connessione globale attivata');
		}

		return self::$_currentSharedInstance;
	}

	/**
	 * STATIC: Imposto la connessione globale
	 */
	public static function setInstance(Db_Pdo $instance)
	{
		self::$_currentSharedInstance = $instance;

		return self::$_currentSharedInstance;
	}

	/**
	 * Fornisce gli attuali parametri di connessione
	 *
	 * Viene usata dalla DB_Sql
	 */
	public function getDbParams()
	{
		return $this->_dbParams;
	}

	/**
	 * Restituisce l'attuale profilatore
	 *
	 * Il profilatore e' uno per ogni diversa connessione, cosi' nel caso di una connessione
	 * di servizio secondaria (come quella per il log degli errori), quella globale rimane indipendente
	 */
	public function getProfiler()
	{
		if ($this->_profiler === null) {
			$class = $this->_profilerClass;
			$this->_profiler = new $class();
		}

		return $this->_profiler;
	}

	/**
	 * Wrapper, con profilatore, del metodo PDO::exec
	 */
	public function exec($statement)
	{
		$q = $this->getProfiler()->queryStart($statement);
		$int = parent::exec($statement);
		$this->getProfiler()->queryEnd($q);

		return $int;
	}

	/**
	 * Wrapper, con profilatore, del metodo PDO::query
	 */
	public function query($statement, $binds = null)
	{
		// Necessario il prepare e poi l'execute per dare la query anche al profiler
		$stmt = $this->prepare($statement);
		$stmt->execute($binds);

		return $stmt;
	}

	/**
	 * Wrapper, con profilatore, del metodo PDO::beginTransaction
	 */
	public function beginTransaction()
	{
		$q = $this->getProfiler()->queryStart('begin', Db_Profiler::TRANSACTION);
		parent::beginTransaction();
		$this->getProfiler()->queryEnd($q);

		return $this;
	}

	/**
	 * Wrapper, con profilatore, del metodo PDO::commit
	 */
	public function commit()
	{
		$q = $this->getProfiler()->queryStart('commit', Db_Profiler::TRANSACTION);
		parent::commit();
		$this->getProfiler()->queryEnd($q);

		return $this;
	}

	/**
	 * Wrapper, con profilatore, del metodo PDO::rollBack
	 */
	public function rollBack()
	{
		$q = $this->getProfiler()->queryStart('rollback', Db_Profiler::TRANSACTION);
		parent::rollBack();
		$this->getProfiler()->queryEnd($q);

		return $this;
	}
}
