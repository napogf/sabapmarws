DROP PROCEDURE IF EXISTS convertPraticaid;
DELIMITER $$
CREATE PROCEDURE convertPraticaid( db varchar(255))
  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE dbColumn, dbTable, dbName varchar(255);
    DECLARE cur1 CURSOR FOR select col.COLUMN_NAME , col.TABLE_NAME, col.TABLE_SCHEMA from information_schema.COLUMNS col join information_schema.TABLES tab on (tab.table_schema = col.table_schema AND tab.table_name = col.table_name) where col.COLUMN_NAME = 'PRATICA_ID' and tab.TABLE_TYPE='BASE TABLE' and col.table_schema = db;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
    OPEN cur1;
    dbLoop: LOOP
      FETCH cur1 INTO dbColumn, dbTable, dbName;
      IF done=1 THEN LEAVE dbLoop; END IF;
      set @statement = concat('ALTER TABLE ',dbName,'.',dbTable,' CHANGE ',dbColumn ,' `PRATICA_ID` INT(11) UNSIGNED;');
      select @statement ;
      prepare execStmt FROM @statement;
      EXECUTE execStmt;
    END LOOP dbLoop;
  END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS addPraticaidIdx;
DELIMITER $$
CREATE PROCEDURE addPraticaidIdx( db varchar(255))
  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE dbColumn, dbTable, dbName varchar(255);
    DECLARE cur1 CURSOR FOR select col.COLUMN_NAME , col.TABLE_NAME, col.TABLE_SCHEMA from information_schema.COLUMNS col join information_schema.TABLES tab on (tab.table_schema = col.table_schema AND tab.table_name = col.table_name)
                where col.COLUMN_NAME = 'PRATICA_ID' and tab.TABLE_TYPE='BASE TABLE' and col.table_schema = db and col.TABLE_NAME <> 'pratiche' ;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
    OPEN cur1;
    dbLoop: LOOP
      FETCH cur1 INTO dbColumn, dbTable, dbName;
      IF done=1 THEN LEAVE dbLoop; END IF;
      set @statement = concat('ALTER TABLE ',dbName,'.',dbTable,' ADD INDEX(`PRATICA_ID`);');
      select @statement ;
      prepare execStmt FROM @statement;
      EXECUTE execStmt;
    END LOOP dbLoop;
  END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS addPraticaidCost;
DELIMITER $$
CREATE PROCEDURE addPraticaidCost( db varchar(255))
  BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE dbColumn, dbTable, dbName varchar(255);
    DECLARE cur1 CURSOR FOR select col.COLUMN_NAME , col.TABLE_NAME, col.TABLE_SCHEMA from information_schema.COLUMNS col join information_schema.TABLES tab on (tab.table_schema = col.table_schema AND tab.table_name = col.table_name)
    where col.COLUMN_NAME = 'PRATICA_ID' and tab.TABLE_TYPE='BASE TABLE' and col.table_schema = db and col.TABLE_NAME <> 'pratiche' ;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;
    OPEN cur1;
    dbLoop: LOOP
      FETCH cur1 INTO dbColumn, dbTable, dbName;
      IF done=1 THEN LEAVE dbLoop; END IF;
      set @statement = concat('ALTER TABLE ',dbName,'.',dbTable,' ADD FOREIGN KEY (`PRATICA_ID`) REFERENCES `pratiche` (`PRATICA_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;');
      select @statement ;
      prepare execStmt FROM @statement;
      EXECUTE execStmt;
    END LOOP dbLoop;
  END$$
DELIMITER ;



SET FOREIGN_KEY_CHECKS=0;
set SQL_MODE="";
ALTER TABLE arc_pratiche_pec DROP FOREIGN KEY arc_pratiche_pec_ibfk_1;
ALTER TABLE arc_pratiche_pec DROP INDEX PRATICA_ID;

ALTER TABLE arc_pratiche_prj DROP FOREIGN KEY arc_pratiche_prj_ibfk_1;
ALTER TABLE arc_pratiche_prj DROP INDEX PRATICA_ID;

ALTER TABLE arc_pratiche_uo DROP FOREIGN KEY arc_pratiche_uo_ibfk_1;
ALTER TABLE arc_pratiche_uo DROP INDEX PRATICA_ID;

ALTER TABLE arc_sospensioni DROP INDEX PRATICA_ID;


ALTER TABLE pratiche_storia DROP FOREIGN KEY pratiche_storia_ibfk_1;


#eliminare tutte le costraint e gl'indici su pratica_id
truncate table arc_password;


CALL sbapvrws.convertPraticaid('sbapvrws');
CALL sbapvrws.addPraticaidIdx('sbapvrws');
CALL sbapvrws.addPraticaidCost('sbapvrws');




DROP TRIGGER IF EXISTS `on_delete_projects`;
DELIMITER //
CREATE TRIGGER `on_delete_projects` AFTER DELETE ON `arc_pratiche_prj`
FOR EACH ROW BEGIN
    UPDATE pratiche
    SET project_id = NULL
    WHERE project_id = old.project_id;
END
//
DELIMITER ;

ALTER TABLE `pratiche` CHANGE `DATASTMP` `DATASTMP` DATE NULL DEFAULT NULL;
# ALTER TABLE `pratiche` DROP `PRATICA_USCITA_ID`;
ALTER TABLE `pratiche` ADD `RESPONSABILE_ID` INT(11) UNSIGNED NULL AFTER `RESPONSABILE`, ADD INDEX (`RESPONSABILE_ID`);
ALTER TABLE `pratiche` ADD `FASCICOLO` VARCHAR(120) NULL AFTER `ESITO_ID`;
ALTER TABLE `pratiche` ADD `MAIL_SENT_ID` VARCHAR(255) NULL AFTER `SER_VALUTAZIONI`, ADD INDEX (`MAIL_SENT_ID`) ;


ALTER TABLE `arc_sospensioni` CHANGE `PROTOENTRATA` `PROTOENTRATA` INT( 11 ) UNSIGNED  NULL DEFAULT NULL ,
  CHANGE `PROTOUSCITA` `PROTOUSCITA` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

/* verificare indici */
ALTER TABLE `arc_sospensioni` ADD INDEX ( `PROTOENTRATA` ) ;
ALTER TABLE `arc_sospensioni` ADD INDEX ( `PROTOUSCITA` ) ;
ALTER TABLE `sys_responsabilities` CHANGE `CREATION` `CREATION` DATETIME NULL DEFAULT null, CHANGE `UPDATED` `UPDATED` DATETIME NULL DEFAULT null;
ALTER TABLE `sys_pages_functions` CHANGE `CREATION` `CREATION` DATETIME NULL DEFAULT null, CHANGE `UPDATED` `UPDATED` DATETIME NULL DEFAULT null;

DROP TABLE IF EXISTS pratiche_fascicoli;
CREATE TABLE IF NOT EXISTS pratiche_fascicoli (
  id           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  fascicolo_id INT(11) UNSIGNED NOT NULL,
  pratica_id   INT(11) UNSIGNED NOT NULL,
  tipologia    VARCHAR(60)      NOT NULL,
  funzione     VARCHAR(60)               DEFAULT NULL,
  created      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY pratica_id (pratica_id),
  KEY fascicolo_id (fascicolo_id)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  COMMENT = 'Fascicoli'
  AUTO_INCREMENT = 1;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella pratiche_fascicoli
--
ALTER TABLE pratiche_fascicoli
  ADD CONSTRAINT pratiche_fascicoli_ibfk_1 FOREIGN KEY (pratica_id) REFERENCES pratiche (PRATICA_ID)
  ON DELETE CASCADE;




DROP TABLE IF EXISTS `sys_espiws`;
CREATE TABLE `sys_espiws` (
  `id` int(10) UNSIGNED NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `documento_id` int(11) DEFAULT NULL,
  `tipo` enum('pratica','pec') NOT NULL DEFAULT 'pratica',
  `ws_request_header` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `ws_result` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `ws_request` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `ws_response_header` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `ws_response` text CHARACTER SET latin1 COLLATE latin1_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `sys_espiws`
--
ALTER TABLE `sys_espiws`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pratica_id` (`documento_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `sys_espiws`
--
ALTER TABLE `sys_espiws`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

/* protocollo uscita normalizzazione */
ALTER TABLE `pratiche` ADD `PRATICA_USCITA_ID` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `PRATICA_ID`;

/* Per archiviazione anche delle mail istituzionali */

ALTER TABLE `arc_pratiche_pec` ADD `TYPE` ENUM('pec','mail') NOT NULL DEFAULT 'pec' AFTER `PRATICA_ID`;
ALTER TABLE `arc_pratiche_pec` ADD `FOLDER` ENUM('INBOX','SENT') NOT NULL DEFAULT 'INBOX' AFTER `CREATION_DATETIME`;
ALTER TABLE `arc_pratiche_pec` ADD `ARCHIVIATA` ENUM('Y','N','E','A') NOT NULL DEFAULT 'N' AFTER `PRATICA_ID`;
ALTER TABLE `arc_pratiche_pec` CHANGE `STATUS` `STATUS` ENUM('U','R','A','P','S') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'U';
ALTER TABLE `arc_pratiche_pec` ADD `suapente` VARCHAR(255) NULL AFTER `FOLDER`;

ALTER TABLE `arc_organizzazione` ADD `VALID` ENUM('Y','N') NOT NULL DEFAULT 'Y' AFTER `EMAIL`;
/* Aggiungere classifica ai tipi pratica */
ALTER TABLE `arc_modelli` ADD `CLASSIFICAZIONE` VARCHAR(200) NOT NULL AFTER `MODELLO`;

UPDATE pratiche SET ANNULLATO = 'NO';
DELETE from arc_sospensioni WHERE PRATICA_ID = 0;

update pratiche set modello = null where modello not in (select modello from arc_modelli);
ALTER TABLE `pratiche` CHANGE `PRATICA_ID` `PRATICA_ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;



CREATE TABLE `arc_mittenti` AS SELECT DISTINCT
                                 `pratiche`.`TITOLO`        AS `titolo`,
                                 `pratiche`.`NOME`          AS `nome`,
                                 `pratiche`.`COGNOME`       AS `cognome`,
                                 `pratiche`.`TOPONIMO`      AS `toponimo`,
                                 `pratiche`.`CAP`           AS `cap`,
                                 `pratiche`.`COMUNE`        AS `comune`,
                                 `pratiche`.`PROVINCIA`     AS `provincia`,
                                 `pratiche`.`LOCALITA`      AS `localita`,
                                 `pratiche`.`TELEFONO`      AS `telefono`,
                                 `pratiche`.`FAX`           AS `fax`,
                                 `pratiche`.`CODICEFISCALE` AS `codicefiscale`,
                                 `pratiche`.`EMAIL`         AS `email`
                               FROM `pratiche`
                               WHERE ((`pratiche`.`COGNOME` IS NOT NULL) AND (`pratiche`.`TITOLO` IS NOT NULL))
                               ORDER BY `pratiche`.`NOME`, `pratiche`.`COGNOME`;


ALTER TABLE `arc_mittenti` ADD `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE `arc_mittenti` ADD `PEC` VARCHAR(255) NULL AFTER `email`;



-- Solo per chi ha arc_destinazioni diverso da arc_mittenti
ALTER TABLE `arc_destinazioni` ADD `TITOLO` VARCHAR(120) NULL AFTER `PRATICA_ID`;
ALTER TABLE `arc_destinazioni` ADD `NOME` VARCHAR(120) NULL AFTER `TITOLO`;

ALTER TABLE `arc_destinazioni` ADD `COGNOME` VARCHAR(255) NULL AFTER `NOME`;
ALTER TABLE `arc_destinazioni` ADD `TOPONIMO` VARCHAR(255) NULL AFTER `COGNOME`;
ALTER TABLE `arc_destinazioni` ADD `LOCALITA` VARCHAR(120) NULL AFTER `TOPONIMO`;

ALTER TABLE `arc_destinazioni` ADD `CODICEFISCALE` VARCHAR(60) NULL AFTER `PROVINCIA`,
  ADD `FAX` VARCHAR(30) NULL AFTER `CODICEFISCALE`,
  ADD `TELEFONO` VARCHAR(30) NULL AFTER `FAX`,
  ADD `EMAIL` VARCHAR(255) NULL AFTER `TELEFONO`,
  ADD `PEC` VARCHAR(255) NULL AFTER `EMAIL`;

UPDATE arc_destinazioni SET COGNOME = NOME_COGNOME, TOPONIMO = VIA;



SET FOREIGN_KEY_CHECKS=1;
