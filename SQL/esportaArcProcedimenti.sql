
DROP TABLE `procedimenti`;
CREATE TABLE IF NOT EXISTS `procedimenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `procedimento` varchar(255) DEFAULT NULL,
  `oggetto` varchar(255) DEFAULT NULL,
  `data_inizio` date DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_uscita` date DEFAULT NULL,
  `esito` varchar(255) DEFAULT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO procedimenti (oggetto, procedimento, data_inizio, status, data_uscita, esito, pdf)
                                    SELECT pr.oggetto, 
                                    substring(arc_modelli.description, locate('-',arc_modelli.description)+1),
                                    pr.dataarrivo, date_format(pr.scadenza,"%d/%m/%Y"), pr.uscita ,substr(es.description,6), 
                                    concat(upd.upload_id,"_",upd.filename) FROM uploads upd
                                    LEFT JOIN pratiche pr ON (pr.pratica_id = upd.pratica_id)
                                    LEFT JOIN arc_esiti es ON (es.esito_id = pr.esito_id)
                                    LEFT JOIN arc_modelli ON (arc_modelli.modello = pr.modello)
                                    WHERE upd.pubblica = "Y";
