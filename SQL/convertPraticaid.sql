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
      set @statement = concat('ALTER TABLE ',dbName,'.',dbTable,' CHANGE ',dbColumn ,' `PRATICA_ID` INT(11) UNSIGNED NOT NULL;');
      prepare execStmt FROM @statement;
      EXECUTE execStmt;
    END LOOP dbLoop;
  END$$
DELIMITER ;
