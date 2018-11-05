

UPDATE pratiche
SET tipologia = 'I'
WHERE tipologia IS NULL;
UPDATE pratiche
SET project_id = NULL
WHERE project_id NOT IN (
    SELECT pratica_id
    FROM arc_pratiche_prj
);


DELETE FROM arc_sospensioni
WHERE protoentrata IS NOT NULL AND protoentrata NOT IN (SELECT pratica_id
                                                        FROM pratiche);
DELETE FROM arc_sospensioni
WHERE protouscita IS NOT NULL AND protouscita NOT IN (SELECT pratica_id
                                                      FROM pratiche);
DELETE FROM arc_sospensioni
WHERE pratica_id NOT IN (SELECT pratica_id
                         FROM pratiche);
# update arc_sospensioni set PROTOUSCITA = CAST(PROTOUSCITA) AS INT;
# update arc_sospensioni set PROTOENTRATA = CAST(PROTOENTRATA) AS INT;
COMMIT;

ALTER TABLE arc_sospensioni
    CHANGE PRATICA_ID PRATICA_ID INT(11) UNSIGNED NOT NULL;
ALTER TABLE arc_sospensioni
    CHANGE PROTOENTRATA PROTOENTRATA INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE arc_sospensioni
    CHANGE PROTOUSCITA PROTOUSCITA INT(11) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE arc_sospensioni
    ADD INDEX (PROTOENTRATA);
ALTER TABLE arc_sospensioni
    ADD INDEX (PROTOUSCITA);
COMMIT;


ALTER TABLE arc_sospensioni
    ADD FOREIGN KEY (PROTOENTRATA) REFERENCES pratiche (
    PRATICA_ID
)
    ON DELETE SET NULL
    ON UPDATE RESTRICT;

ALTER TABLE arc_sospensioni
    ADD FOREIGN KEY (PROTOUSCITA) REFERENCES pratiche (
    PRATICA_ID
)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;

ALTER TABLE sys_espiws
    CHANGE documento_id documento_id INT(11) NULL;
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

ALTER TABLE arc_pratiche_pec
    ADD suapente VARCHAR(255) NULL
    AFTER STATUS;

CREATE TABLE sys_trigger_debug (
    pratica_id INT(11)      DEFAULT NULL,
    field      VARCHAR(255) DEFAULT NULL,
    value      VARCHAR(255) DEFAULT NULL,
    procedura  VARCHAR(255) DEFAULT NULL,
    created    DATETIME     DEFAULT NULL
)
    ENGINE = InnoDB
    DEFAULT CHARSET = latin1;


DROP VIEW IF EXISTS arc_mittenti_v;
CREATE VIEW arc_mittenti AS
    SELECT DISTINCT
        sbapvrws.pratiche.TITOLO        AS titolo,
        sbapvrws.pratiche.NOME          AS nome,
        sbapvrws.pratiche.COGNOME       AS cognome,
        sbapvrws.pratiche.TOPONIMO      AS toponimo,
        sbapvrws.pratiche.CAP           AS cap,
        sbapvrws.pratiche.COMUNE        AS comune,
        sbapvrws.pratiche.PROVINCIA     AS provincia,
        sbapvrws.pratiche.LOCALITA      AS localita,
        sbapvrws.pratiche.TELEFONO      AS telefono,
        sbapvrws.pratiche.FAX           AS fax,
        sbapvrws.pratiche.CODICEFISCALE AS codicefiscale,
        sbapvrws.pratiche.EMAIL         AS email
    FROM sbapvrws.pratiche
    WHERE ((sbapvrws.pratiche.COGNOME IS NOT NULL)
           AND (sbapvrws.pratiche.TITOLO IS NOT NULL)
           AND (sbapvrws.pratiche.TITOLO > '')
    )
    ORDER BY sbapvrws.pratiche.NOME, sbapvrws.pratiche.COGNOME


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
/*
Aggiungere il campo PEC in fondo
e l'id primery key auto increment
indici su titolo, nome , cognome
 */