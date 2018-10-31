<?php

/**
	@version  2011-02-01
	@abstract Profilatore di query
 */

class Db_Profiler
{
	/* Tipo di query */
	const CONNECT = 1;
	const QUERY = 2;
	const INSERT = 4;
	const UPDATE = 8;
	const DELETE = 16;
	const SELECT = 32;
	const TRANSACTION = 64;
	const STORED = 'stored';
	const IGNORED = 'ignored';

	/* Array con le query profilate */
	protected $_queryProfiles = array();

	/* Identifica lo stato di attivo o disattivo */
	protected $_enabled = false;

	/**
	 * Restituisce lo stato di attivo/disattivo corrente
	 */
	public function getEnabled()
	{
		return $this->_enabled;
	}

	/**
	 * Imposto lo stato di attivo/disattivo
	 *
	 * Di default e' attivo, posso disabilitarlo per esempio
	 * in produzione o per una connessione secondaria
	 */
	public function setEnabled($enabled)
	{
		$this->_enabled = (bool) $enabled;

		return $this;
	}

	/**
	 * Pulisce il set di query profilate fin'ora
	 */
	public function clear()
	{
		$this->_queryProfiles = array();

		return $this;
	}

	/**
	 * Clona una query da profilare
	 *
	 * Viene usato nel caso facessi un solo PDO::prepare
	 * e successivamente PDOStatement::execute multipli
	 */
	public function queryClone(Db_Profiler_Query $query)
	{
		$this->_queryProfiles[] = clone $query;

		end($this->_queryProfiles);

		return key($this->_queryProfiles);
	}

	/**
	 * Inizializza il profilatore di una singola query
	 */
	public function queryStart($queryText, $queryType = null)
	{
		// Se non e' attivo, salto
		if (!$this->_enabled) {
			return null;
		}

		if (null === $queryType) {
			switch (strtolower(substr(ltrim($queryText), 0, 6))) {
				case 'insert':
					$queryType = self::INSERT;
					break;
				case 'update':
					$queryType = self::UPDATE;
					break;
				case 'delete':
					$queryType = self::DELETE;
					break;
				case 'select':
					$queryType = self::SELECT;
					break;
				default:
					$queryType = self::QUERY;
					break;
			}
		}

		$this->_queryProfiles[] = new Db_Profiler_Query($queryText, $queryType);

		end($this->_queryProfiles);

		return key($this->_queryProfiles);
	}

	/**
	 * Conclude il profilo di una query
	 */
	public function queryEnd($queryId)
	{
		// Se non e' attivo, salto
		if (!$this->_enabled) {
			return self::IGNORED;
		}

		if (!isset($this->_queryProfiles[$queryId])) {
			throw new Db_Profiler_Exception("Profiler has no query with handle '$queryId'.");
		}

		$qp = $this->_queryProfiles[$queryId];

		if ($qp->hasEnded()) {
			throw new Db_Profiler_Exception("Query with profiler handle '$queryId' has already ended.");
		}

		$qp->end();

		return self::STORED;
	}

	/**
	 * Restituisce il profilo di una specifica query
	 */
	public function getQueryProfile($queryId)
	{
		if (!array_key_exists($queryId, $this->_queryProfiles)) {
			throw new Db_Profiler_Exception("Query handle '$queryId' not found in profiler log.");
		}

		return $this->_queryProfiles[$queryId];
	}

	/**
	 * Restituisce tutte le query profilate fin'ora
	 *
	 * Posso richiamare anche solo le query di un certo tipo
	 */
	public function getQueryProfiles($queryType = null, $showUnfinished = false)
	{
		$queryProfiles = array();
		foreach ($this->_queryProfiles as $key => $qp) {
			if ($queryType === null) {
				$condition = true;
			} else {
				$condition = ($qp->getQueryType() & $queryType);
			}

			// Non restituisco query non completate (PDO::prepare SENZA PDOStatement::execute)
			if (($qp->hasEnded() || $showUnfinished) && $condition) {
				$queryProfiles[$key] = $qp;
			}
		}

		if (empty($queryProfiles)) {
			$queryProfiles = false;
		}

		return $queryProfiles;
	}

	/**
	 * Restituisce il tempo totale delle query (anche solo di un certo tipo)
	 */
	public function getTotalElapsedSecs($queryType = null)
	{
		$elapsedSecs = 0;
		foreach ($this->_queryProfiles as $key => $qp) {
			if (null === $queryType) {
				$condition = true;
			} else {
				$condition = ($qp->getQueryType() & $queryType);
			}
			if (($qp->hasEnded()) && $condition) {
				$elapsedSecs += $qp->getElapsedSecs();
			}
		}
		return $elapsedSecs;
	}

	/**
	 * Restituisce il numero totale di query (anche solo di un certo tipo)
	 */
	public function getTotalNumQueries($queryType = null)
	{
		if (null === $queryType) {
			return count($this->_queryProfiles);
		}

		$numQueries = 0;
		foreach ($this->_queryProfiles as $qp) {
			if ($qp->hasEnded() && ($qp->getQueryType() & $queryType)) {
				$numQueries++;
			}
		}

		return $numQueries;
	}

	/**
	 * Restituisce il profilo dell'ultima query
	 */
	public function getLastQueryProfile()
	{
		if (empty($this->_queryProfiles)) {
			return false;
		}

		end($this->_queryProfiles);

		return current($this->_queryProfiles);
	}
}
