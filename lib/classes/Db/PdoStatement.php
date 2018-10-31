<?php

/**
	@version  2011-02-01
	@abstract
		Estensione della PDOStatement classica con i seguenti scopi
		 1) Poter profilare le query
		[2) Impostare automaticamente il metodo di fetch dei risultati]
 */

class Db_PdoStatement extends PDOStatement
{
	/* Istanza della connessione madre */
	protected $_adapter = null;

	/* ID della query per il profilatore */
	protected $_queryId = null;

	/**
	 * Costruttore:
	 *		- Salva quale connessione lo ha istanziato (per poter accedere al suo profilatore)
	 *		- Imposta il metodo di Fetch
	 */
	protected function __construct($adapter, $fetchMode = '')
	{
		$this->_adapter = $adapter;

		if (!empty($fetchMode)) {
			$this->setFetchMode($fetchMode);
		}

		$this->_queryId = $this->_adapter->getProfiler()->queryStart($this->queryString);
	}

	/**
	 * Wrapper, con profilatore, del metodo PDOStatement::execute
	 */
	public function execute($params = null)
	{
	    try {
    		/* Nel caso il profilatore sia disabilitato, eseguo la PDOStatement::execute di default */
    		if ($this->_queryId === null) {
    			return parent::execute($params);
    		}

    		$prof = $this->_adapter->getProfiler();
    		$qp = $prof->getQueryProfile($this->_queryId);

    		/**
    		 * Nel caso in cui stia facendo una Prepared Statement multipla,
    		 * con un solo PDO::prepare e successivamente molti PDOStatement::execute,
    		 * nel profilatore clono la query corrente
    		 */
    		if ($qp->hasEnded()) {
    			$this->_queryId = $prof->queryClone($qp);
    			$qp = $prof->getQueryProfile($this->_queryId);
    		}

    		if (!empty($params)) {
    			$qp->bindParams($params);
    		}

    		$qp->start($this->_queryId);

			$retval = parent::execute($params);
		} catch (PDOException $e) {
            r($e->getTrace(),false);
		    r($e->getCode(),false);
		    r($e->getMessage(),false);
		    r($this->queryString,false);
		    r($params);

		}

		$prof->queryEnd($this->_queryId);

		return $retval;
	}
}
