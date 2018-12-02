SET FOREIGN_KEY_CHECKS=0;
set SQL_MODE="";
/*
 * Unità organizzative
 * 
 * 
 */

DROP VIEW languages;
CREATE VIEW languages AS
    SELECT *
    FROM sys_languages;

ALTER TABLE `pratiche`
    CHANGE `PRATICA_ID` `PRATICA_ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `pratiche` CHANGE `CLASSIFICA` `CLASSIFICA` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `pratiche` CHANGE `OGGETTO` `OGGETTO` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `pratiche` CHANGE `COGNOME` `COGNOME` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `arc_modelli` ADD `CLASSIFICAZIONE` VARCHAR(20) NULL AFTER `CATEGORIA`;

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

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `arc_pratiche_prj`
--


/*
* eliminare FK su arc_pratiche_prj
*
*/

ALTER TABLE `arc_pratiche_prj` CHANGE `PRATICA_ID` `PRATICA_ID` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `arc_pratiche_prj`
    ADD FOREIGN KEY (`PRATICA_ID`) REFERENCES `pratiche` (`PRATICA_ID`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

DROP TABLE IF EXISTS `pratiche_fascicoli`;
CREATE TABLE IF NOT EXISTS `pratiche_fascicoli` (
    `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fascicolo_id` INT(11) UNSIGNED NOT NULL,
    `pratica_id`   INT(11) UNSIGNED NOT NULL,
    `tipologia`    VARCHAR(60)      NOT NULL,
    `funzione`     VARCHAR(60)               DEFAULT NULL,
    `created`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `pratica_id` (`pratica_id`),
    KEY `fascicolo_id` (`fascicolo_id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = latin1
    COMMENT = 'Fascicoli'
    AUTO_INCREMENT = 1;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `pratiche_fascicoli`
--
ALTER TABLE `pratiche_fascicoli`
    ADD CONSTRAINT `pratiche_fascicoli_ibfk_1` FOREIGN KEY (`pratica_id`) REFERENCES `pratiche` (`PRATICA_ID`)
    ON DELETE CASCADE;


UPDATE `pratiche`
SET tipologia = 'I'
WHERE tipologia IS NULL;
UPDATE pratiche
SET project_id = NULL
WHERE project_id NOT IN (
    SELECT pratica_id
    FROM arc_pratiche_prj
);


ALTER TABLE `arc_sospensioni`
    CHANGE `PRATICA_ID` `PRATICA_ID` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `arc_sospensioni`
    CHANGE `PROTOENTRATA` `PROTOENTRATA` INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `arc_sospensioni`
    CHANGE `PROTOUSCITA` `PROTOUSCITA` INT(11) UNSIGNED NULL DEFAULT NULL;


DELETE FROM arc_sospensioni
WHERE protoentrata IS NOT NULL AND protoentrata NOT IN (SELECT pratica_id
                                                        FROM pratiche);
DELETE FROM arc_sospensioni
WHERE protouscita IS NOT NULL AND protouscita NOT IN (SELECT pratica_id
                                                      FROM pratiche);
DELETE FROM arc_sospensioni
WHERE pratica_id NOT IN (SELECT pratica_id
                         FROM pratiche);


ALTER TABLE `arc_sospensioni`
    ADD FOREIGN KEY (`PROTOENTRATA`) REFERENCES `pratiche` (
    `PRATICA_ID`
)
    ON DELETE SET NULL
    ON UPDATE RESTRICT;

ALTER TABLE `arc_sospensioni`
    ADD FOREIGN KEY (`PROTOUSCITA`) REFERENCES `pratiche` (
    `PRATICA_ID`
)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

ALTER TABLE `pratiche` ADD `ANNULLATO` ENUM( 'SI', 'NO' ) NOT NULL DEFAULT 'NO' AFTER `TIPOLOGIA` ;
ALTER TABLE `pratiche` ADD `FALDONE` VARCHAR(60) NULL AFTER `PROJECT_ID`;


ALTER TABLE `pratiche` CHANGE `DATASTMP` `DATASTMP` DATE NULL DEFAULT NULL;


# ALTER TABLE `pratiche` DROP `PRATICA_USCITA_ID`;

ALTER TABLE pratiche ADD COLUMN MAIL_SENT_ID varchar(255) DEFAULT NULL;
ALTER TABLE pratiche ADD COLUMN PRATICA_USCITA_ID int(11) unsigned DEFAULT NULL;

ALTER TABLE pratiche ADD INDEX MAIL_SENT_ID (MAIL_SENT_ID);
ALTER TABLE pratiche ADD INDEX FALDONE (FALDONE);



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



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `sbapveorws`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `sys_config`
--





ALTER TABLE sys_users
  ADD COLUMN WS_USER VARCHAR(255)
COLLATE utf8_bin DEFAULT '';
ALTER TABLE uploads
  ADD COLUMN NATURA_ATTO VARCHAR(120) DEFAULT NULL;
ALTER TABLE uploads
  ADD COLUMN TIPO_PROCEDIMENTO VARCHAR(120) DEFAULT NULL;
ALTER TABLE uploads
  ADD COLUMN SETTORE VARCHAR(120) DEFAULT NULL;
ALTER TABLE uploads
  ADD COLUMN A_B VARCHAR(120) DEFAULT NULL;
ALTER TABLE uploads
  ADD COLUMN PUBBLICATO_MIBACT ENUM ('Y', 'N') NOT NULL DEFAULT 'N';
ALTER TABLE uploads
  ADD COLUMN AMBITO VARCHAR(120) DEFAULT NULL;
ALTER TABLE uploads
  ADD COLUMN PUBBLICA ENUM ('Y', 'N') NOT NULL DEFAULT 'N';

CREATE TABLE sys_trigger_debug (
  pratica_id INT(11)      DEFAULT NULL,
  field      VARCHAR(255) DEFAULT NULL,
  value      VARCHAR(255) DEFAULT NULL,
  procedura  VARCHAR(255) DEFAULT NULL,
  created    DATETIME     DEFAULT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;


DROP VIEW arc_mittenti;

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

ALTER TABLE `arc_destinazioni` ADD `CODICEFISCALE` VARCHAR(60) NULL AFTER `PROVINCIA`,
  ADD `FAX` VARCHAR(30) NULL AFTER `CODICEFISCALE`,
  ADD `TELEFONO` VARCHAR(30) NULL AFTER `FAX`,
  ADD `EMAIL` VARCHAR(255) NULL AFTER `TELEFONO`,
  ADD `PEC` VARCHAR(255) NULL AFTER `EMAIL`;

UPDATE arc_destinazioni SET COGNOME = NOME_COGNOME, TOPONIMO = VIA;

/*
Aggiungere il campo PEC in fondo
e l'id primery key auto increment
indici su titolo, nome , cognome
 */


ALTER TABLE `log_audit` CHANGE `LOG_DATE` `LOG_DATE` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;





CREATE TABLE `arc_messaggi` (
  `MESSAGGIO_ID` int(10) UNSIGNED NOT NULL,
  `TITOLO` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `MESSAGGIO` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `DATA_MESSAGGIO` date NOT NULL,
  `PUBBLICA` char(1) COLLATE latin1_bin DEFAULT NULL,
  `RESP_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin COMMENT='Tabella messaggi utenti';

create view user_resp_reference as SELECT * FROM `sys_user_resp_reference` WHERE 1;



--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `arc_pratiche_pec`
--
ALTER TABLE arc_pratiche_pec ADD suapente varchar(255) NULL;
ALTER TABLE arc_pratiche_pec ADD TYPE enum('pec', 'mail') DEFAULT 'pec' NOT NULL;
ALTER TABLE arc_pratiche_pec ADD ARCHIVIATA enum('Y', 'N', 'E', 'A') DEFAULT 'N' NULL;
ALTER TABLE arc_pratiche_pec MODIFY NUMEROREGISTRAZIONE varchar(25) DEFAULT NULL ;
ALTER TABLE arc_pratiche_pec MODIFY DATAREGISTRAZIONE date DEFAULT NULL ;
ALTER TABLE `arc_pratiche_pec` CHANGE `SUBJECT` `SUBJECT` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE arc_pratiche_pec MODIFY STATUS enum('U', 'R', 'A', 'P') NOT NULL DEFAULT 'U';


ALTER TABLE `arc_pratiche_pec`
  ADD PRIMARY KEY (`PEC_ID`),
  ADD KEY `pec_mail_hash_idx` (`MAIL_HASH`),
  ADD KEY `PRATICA_ID` (`PRATICA_ID`),
  ADD KEY `MITTENTE` (`MITTENTE`),
  ADD KEY `STATUS` (`STATUS`),
  ADD KEY `DATAARRIVO` (`DATAARRIVO`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `arc_pratiche_pec`
--
ALTER TABLE `arc_pratiche_pec`
  MODIFY `PEC_ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `arc_pratiche_pec`
--
ALTER TABLE `arc_pratiche_pec`
  ADD CONSTRAINT `arc_pratiche_pec_ibfk_1` FOREIGN KEY (`PRATICA_ID`) REFERENCES `pratiche` (`PRATICA_ID`);

ALTER TABLE `arc_organizzazione` ADD `VALID` ENUM('Y','N') NOT NULL DEFAULT 'Y' AFTER `EMAIL`;
alter table arc_organizzazione modify CODE varchar(60) null;

-- Organizzazioni da mettere a posto
-- Eseguire create_routines.sql e create_trigger.sql


-- Altri destinatari tabella srac_destinazioni anagrafice collegate con la pratica e
-- arc_mittenti archivio mittenti/destinatari



ALTER TABLE `arc_destinazioni` CHANGE `NOME_COGNOME` `COGNOME` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `arc_destinazioni` ADD `NOME` VARCHAR( 120 ) NULL AFTER `PRATICA_ID` ;
ALTER TABLE `arc_destinazioni` ADD `TITOLO` VARCHAR( 120 ) NULL AFTER `COGNOME` ;
ALTER TABLE `arc_destinazioni` CHANGE `VIA` `TOPONIMO` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `arc_destinazioni` ADD `LOCALITA` VARCHAR( 120 ) NULL ,
  ADD `TELEFONO` VARCHAR( 50 ) NULL ,
  ADD `FAX` VARCHAR( 50 ) NULL ,
  ADD `CODICEFISCALE` VARCHAR( 20 ) NULL ,
  ADD `EMAIL` VARCHAR( 255 ) NULL,
  ADD `PEC` VARCHAR( 255 ) NULL ;



DROP TABLE IF EXISTS `arc_mittenti`;
CREATE TABLE `arc_mittenti` (
  `id` int(11) NOT NULL,
  `titolo` varchar(150) DEFAULT NULL,
  `nome` varchar(150) DEFAULT NULL,
  `cognome` varchar(150) DEFAULT NULL,
  `toponimo` varchar(150) DEFAULT NULL,
  `cap` varchar(15) DEFAULT NULL,
  `comune` varchar(150) DEFAULT NULL,
  `provincia` varchar(150) DEFAULT NULL,
  `localita` varchar(150) DEFAULT NULL,
  `telefono` varchar(150) DEFAULT NULL,
  `fax` varchar(150) DEFAULT NULL,
  `codicefiscale` varchar(16) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `pec` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `arc_mittenti`
--
ALTER TABLE `arc_mittenti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `arc_mittenti`
--
ALTER TABLE `arc_mittenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Caricamento Mittenti/Destinatari in arc_mittenti


insert into arc_mittenti (
titolo,
nome,
cognome,
toponimo,
cap,
comune,
provincia,
localita,
telefono,
fax,
codicefiscale,
email
) select distinct TITOLO, NOME, COGNOME, TOPONIMO, CAP, COMUNE, PROVINCIA, LOCALITA, TELEFONO, FAX, CODICEFISCALE ,EMAIL
  from pratiche where DATAREGISTRAZIONE >= '2016-01-01'

-- Tipo anagrafica (Titolo) da inserire in sys_fields_validations
delete from sys_fields_validations where FIELD_NAME = 'tipo_anagrafica';
insert into sys_fields_validations (LANGUAGE_ID, FIELD_NAME, VALUE, CODE) SELECT distinct 1, 'tipo_anagrafica' , TITOLO , 1 FROM pratiche where DATAREGISTRAZIONE >= '2016-01-01' and titolo is not null ;

set @titoloval := 0;

update sys_fields_validations set code = lpad(@titoloval := @titoloval+1,3,'0') where FIELD_NAME = 'tipo_anagrafica';

ALTER TABLE `arc_modelli` CHANGE `CLASSIFICAZIONE` `CLASSIFICAZIONE` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

--





SET FOREIGN_KEY_CHECKS=1;

