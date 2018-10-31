<?php

/**
	@version  2011-02-01
	@abstract Profilatore di una singola query
 */

class Db_Profiler_Query
{
	/* L'SQL della query */
	protected $_query = '';

	/* Il tipo di query */
	protected $_queryType = 0;

	/* Inizio temporale della query */
	protected $_startedMicrotime = null;

	/* Fine temporale della query */
	protected $_endedMicrotime = null;

	/* Parametri, nel caso di una Prepared statement */
	protected $_boundParams = array();

	/* Costruttore: imposta la query da profilare e inizializza il calcolo del tempo di esecuzione */
	public function __construct($query, $queryType)
	{
		$this->_query = $query;
		$this->_queryType = $queryType;

		$this->start();
	}

	/* Nel clonare la query, pulisci e reinizializza */
	public function __clone()
	{
		$this->_boundParams = array();
		$this->_endedMicrotime = null;
		$this->start();
	}

	/* Ritorna il timestamp corrente */
	protected function getMicrotime()
	{
		// list($usec, $sec) = explode(' ', microtime());
		// return ((float)$usec + (float)$sec);

		return microtime(true);
	}

	/* Inizia il calcolo temporale della query */
	public function start()
	{
		$this->_startedMicrotime = $this->getMicrotime();
	}

	/* Conclude il calcolo temporale della query */
	public function end()
	{
		$this->_endedMicrotime = $this->getMicrotime();
	}

	/* Dice se la query e' conclusa */
	public function hasEnded()
	{
		return $this->_endedMicrotime !== null;
	}

	/* Restituisce l'SQL della query */
	public function getQuery()
	{
		return $this->_query;
	}

	/* Restituisce il tipo di query */
	public function getQueryType()
	{
		return $this->_queryType;
	}

	/* Imposta un parametro della query */
	public function bindParam($param, $variable)
	{
		$this->_boundParams[$param] = $variable;
	}

	/* Imposta i parametri della query */
	public function bindParams(array $params)
	{
		if (array_key_exists(0, $params)) {
			array_unshift($params, null);
			unset($params[0]);
		}
		foreach ($params as $param => $value) {
			$this->bindParam($param, $value);
		}
	}

	/* Restituisce i parametri della query */
	public function getQueryParams()
	{
		return $this->_boundParams;
	}

	/* Restituisce il tempo di esecuzione totale della query */
	public function getElapsedSecs()
	{
		if (null === $this->_endedMicrotime) {
			return false;
		}

		return $this->_endedMicrotime - $this->_startedMicrotime;
	}
}
