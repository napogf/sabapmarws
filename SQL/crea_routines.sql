DROP FUNCTION IF EXISTS getScadenza;
DELIMITER $$ 
CREATE FUNCTION getScadenza(praticaId INT) RETURNS DATE    
BEGIN 
	DECLARE Cinizio, cFine, Criavvio, dataPartenza,  Cdataarrivo  varchar(255);
	DECLARE dataPratica, DdataArrivo, praticaSospesa, scadenzaPratica, dataRiavvio DATE;
	DECLARE cGgpratica, cGgsospensione, ggCalcolati, done INT;

	set ggCalcolati=0;
	set cGgpratica=0;
	set cGgsospensione=0;

	set dataPratica = str_to_date('00-00-0000','%d-%m-%Y');
	set dataRiavvio = str_to_date('00-00-0000','%d-%m-%Y');
	set scadenzaPratica = str_to_date('00-00-0000','%d-%m-%Y');

	SELECT scadenza from arc_modelli where modello = (select modello from pratiche where pratica_id = praticaId) into cGgpratica;
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenza',now());
	select count(*) from arc_sospensioni where pratica_id = praticaId into cGgsospensione;
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenza',now());
	select MAX(fine) from arc_sospensioni where pratica_id = praticaId AND RIAVVIA = 'Y' into dataRiavvio;
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenza',now());
	select dataarrivo from pratiche where pratica_id = praticaId into dataPratica;
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenza',now());
	select min(inizio) from arc_sospensioni where pratica_id = praticaId and (fine is null or fine = str_to_date('00-00-0000','%d-%m-%Y')) group by pratica_id into praticaSospesa;
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenza',now());
	
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, 'scadenzaPratica', scadenzaPratica, 'getScadenza',now());
	IF (cGgpratica > 1) then
		IF (praticaSospesa is not null or praticaSospesa > str_to_date('00-00-0000','%d-%m-%Y')) THEN 
			RETURN scadenzaPratica;
		END IF;
	
		IF (dataPratica is null) then 
			RETURN scadenzaPratica;
		END IF;
		IF (cGgsospensione > 0) then  
			if (dataRiavvio is not null) then 
				select sum(datediff(fine,inizio)) from arc_sospensioni where pratica_id = praticaId and fine >= dataRiavvio into cGgsospensione;
				set  scadenzaPratica =date_add(dataRiavvio , interval (cGgpratica+cGgsospensione) DAY);
				RETURN scadenzaPratica;
			ELSE
				select sum(datediff(fine,inizio)) from arc_sospensioni where pratica_id = praticaId into cGgsospensione;
				set scadenzaPratica =date_add(dataPratica , interval (cGgpratica+cGgsospensione) DAY);
				RETURN scadenzaPratica;
			END IF;
		ELSE
			RETURN date_add(dataPratica , interval cGgpratica DAY);
		END IF;
	ELSE 
		RETURN NULL;	
	END IF;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS getScadenzaNoModello;
DELIMITER $$ 
CREATE FUNCTION getScadenzaNoModello(praticaId INT, newModello INT) RETURNS DATE    
BEGIN 
	DECLARE Cinizio, cFine, Criavvio, dataPartenza,  Cdataarrivo  varchar(255);
	DECLARE dataPratica, DdataArrivo, praticaSospesa, scadenzaPratica, dataRiavvio DATE;
	DECLARE cGgpratica, cGgsospensione, ggCalcolati, done INT;

	set ggCalcolati=0;
	set cGgpratica=0;
	set cGgsospensione=0;

	set dataPratica = str_to_date('00-00-0000','%d-%m-%Y');
	set dataRiavvio = str_to_date('00-00-0000','%d-%m-%Y');
	set scadenzaPratica = str_to_date('00-00-0000','%d-%m-%Y');

	SELECT scadenza from arc_modelli where modello = newModello into cGgpratica;
	/* insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenzaNoModello',now()); */
	select count(*) from arc_sospensioni where pratica_id = praticaId into cGgsospensione;
	/* insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenzaNoModello',now()); */
	select MAX(fine) from arc_sospensioni where pratica_id = praticaId AND RIAVVIA = 'Y' into dataRiavvio;
	/* insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenzaNoModello',now()); */
	select dataarrivo from pratiche where pratica_id = praticaId into dataPratica;
	/* insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenzaNoModello',now()); */
	select min(inizio) from arc_sospensioni where pratica_id = praticaId and (fine is null or fine = str_to_date('00-00-0000','%d-%m-%Y')) group by pratica_id into praticaSospesa;
	/*
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, '', '', 'getScadenzaNoModello',now());
	
	insert into sys_trigger_debug (pratica_id, field, value, procedura, created) values (praticaId, 'scadenzaPratica', scadenzaPratica, 'getScadenzaNoModello',now());
	*/
	IF (cGgpratica > 1) then
		IF (praticaSospesa is not null or praticaSospesa > str_to_date('00-00-0000','%d-%m-%Y')) THEN 
			RETURN scadenzaPratica;
		END IF;
	
		IF (dataPratica is null) then 
			RETURN scadenzaPratica;
		END IF;
		IF (cGgsospensione > 0) then  
			if (dataRiavvio is not null) then 
				select sum(datediff(fine,inizio)) from arc_sospensioni where pratica_id = praticaId and fine >= dataRiavvio into cGgsospensione;
				set  scadenzaPratica =date_add(dataRiavvio , interval (cGgpratica+cGgsospensione) DAY);
				RETURN scadenzaPratica;
			ELSE
				select sum(datediff(fine,inizio)) from arc_sospensioni where pratica_id = praticaId into cGgsospensione;
				set scadenzaPratica =date_add(dataPratica , interval (cGgpratica+cGgsospensione) DAY);
				RETURN scadenzaPratica;
			END IF;
		ELSE
			RETURN date_add(dataPratica , interval cGgpratica DAY);
		END IF;
	ELSE 
		RETURN NULL;	
	END IF;
END$$
DELIMITER ;



DROP FUNCTION IF EXISTS getGiorniPratica;
DELIMITER $$ 
CREATE FUNCTION getGiorniPratica(TipoPratica INT) RETURNS int    
BEGIN 
DECLARE cGgpratica, done INT;
DECLARE curModelli CURSOR FOR SELECT scadenza from arc_modelli where modello = TipoPratica;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
set cGgpratica = 0;
OPEN curModelli;
	myloop: LOOP
		FETCH curModelli INTO cGgpratica ;
			IF done=1 THEN 
				RETURN 0;
			ELSE
				RETURN cGgpratica ;
			END IF;			
	END LOOP myloop;
CLOSE curModelli;
END$$
DELIMITER ;


