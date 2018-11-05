CREATE TABLE IF NOT EXISTS `sys_trigger_debug` (
  `pratica_id` int(11) DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `procedura` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `pratiche` CHANGE `UPDATED` `UPDATED` DATETIME NULL ;
ALTER TABLE `pratiche` CHANGE `CREATED` `CREATED` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ;

DROP TRIGGER IF EXISTS ON_UPDATE_PRATICHE;
DELIMITER $$ 
CREATE TRIGGER ON_UPDATE_PRATICHE BEFORE UPDATE ON pratiche FOR EACH ROW
BEGIN
	IF (new.modello > '' ) THEN 
			set new.scadenza = getScadenzaNoModello(new.pratica_id,new.modello); 
		ELSE 
			set new.scadenza =  null;
	END IF;
	IF (new.SCADENZA <> old.SCADENZA) THEN
		IF (new.email > '' and old.email_flag = 'N' ) THEN set new.email_flag='Y'; END IF;
	END IF;
	set new.updated = now();
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS ON_INSERT_PRATICHE;
DELIMITER $$ 
CREATE TRIGGER ON_INSERT_PRATICHE BEFORE INSERT ON pratiche FOR EACH ROW
BEGIN
IF (new.modello > '' and new.dataarrivo is not null and new.scadenza is null and new.tipologia <> 'U') THEN 
		set new.scadenza =  getScadenzaNoModello(new.pratica_id);
	ELSE 
		set new.scadenza = null;
END IF;
END$$
DELIMITER ;





DROP TRIGGER IF EXISTS on_delete_pratiche;
DELIMITER $$ 
CREATE TRIGGER on_delete_pratiche AFTER DELETE ON pratiche FOR EACH ROW 	
BEGIN
delete from pratiche_storia where pratica_id = old.pratica_id;
END$$
DELIMITER ;


DROP TRIGGER IF EXISTS on_delete_projects;
DELIMITER $$ 
CREATE TRIGGER on_delete_projects AFTER DELETE ON arc_pratiche_prj FOR EACH ROW 	
BEGIN
update pratiche set project_id = null where project_id = old.project_id;
END$$
DELIMITER ;



DROP TRIGGER IF EXISTS on_update_sospensioni;
DELIMITER $$ 
CREATE TRIGGER on_update_sospensioni AFTER UPDATE ON arc_sospensioni FOR EACH ROW 
BEGIN
update pratiche set scadenza = getScadenza(new.pratica_id) where pratica_id = new.pratica_id;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS on_insert_sospensioni;
DELIMITER $$ 
CREATE TRIGGER on_insert_sospensioni AFTER INSERT ON arc_sospensioni FOR EACH ROW 
BEGIN
UPDATE pratiche set scadenza = getScadenza(new.pratica_id) where pratica_id = new.pratica_id;
END$$
DELIMITER ;

