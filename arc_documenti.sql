-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 08 giu, 2010 at 05:42 
-- Versione MySQL: 5.1.41
-- Versione PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sbapvr`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `arc_documenti`
--

DROP TABLE IF EXISTS `arc_documenti`;
CREATE TABLE IF NOT EXISTS `arc_documenti` (
  `DOC_ID` int(11) NOT NULL AUTO_INCREMENT,
  `MODELLO` int(11) DEFAULT NULL,
  `DESCRIPTION` varchar(60) NOT NULL,
  `FILE_OO` varchar(255) NOT NULL,
  PRIMARY KEY (`DOC_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=105 ;

--
-- Dump dei dati per la tabella `arc_documenti`
--

INSERT INTO `arc_documenti` (`DOC_ID`, `MODELLO`, `DESCRIPTION`, `FILE_OO`) VALUES
(69, 16, '031-Sgravi Fiscali - Certificazione L.512-82', 'ARC_DOCUMENTI-031-Sgravi  Fiscali - Certificazione L. 512-82.odt'),
(39, 2, '021-Autorizzazione con prescrizioni - art. 21', 'ARC_DOCUMENTI-021-Autorizzazione art. 21 con prescrizioni.odt'),
(8, NULL, 'Modello 302', 'ARC_DOCUMENTI-Modello 302-generico_test.odt'),
(10, 8, '146-Trasmissione elenco pratiche paesaggio - art. 146', 'ARC_DOCUMENTI-146-Elenco trasmissione pratiche paesaggio art. 146.odt'),
(11, 8, '146-Parere negativo - art. 146', 'ARC_DOCUMENTI-146-Parere negativo - art. 146.odt'),
(12, 8, '146-Parere favorevole - art. 146', 'ARC_DOCUMENTI-146-Parere favorevole - art. 146.odt'),
(13, 8, '146-Parere favorevole con prescrizioni - art. 146', 'ARC_DOCUMENTI-146-Parere favorevole - art. 146 con prescrizioni.odt'),
(14, 8, '146-Parere favorevole-Oo.Pp. - art. 146', 'ARC_DOCUMENTI-146-Parere favorevole - opere pubblche - art. 146.odt'),
(15, 8, '146-Parere prescrittivo - art. 146', 'ARC_DOCUMENTI-146-Parere prescrittivo - art. 146.odt'),
(16, 8, '146-Restituzione progetto beni paesaggistici - art. 146', 'ARC_DOCUMENTI-146-Restituzione progetto Beni Paesaggistici -2010.odt'),
(17, 9, '167-Parere favorevole - art. 167', 'ARC_DOCUMENTI-167-CompatibilitÃ  paesaggistica-parere favorevole - art. 167.odt'),
(18, 9, '167-Parere favorevole sine parere CEI - art. 167', 'ARC_DOCUMENTI-167-CompatibilitÃ  paesaggistica-parere favorevole sine parere CEI - art. 167.odt'),
(19, 9, '167-Parere negativo - art. 167', 'ARC_DOCUMENTI-167-CompatibilitÃ  paesaggistica-parere negativo - art. 167.odt'),
(20, 9, '167-Richiesta integrazioni - art. 167', 'ARC_DOCUMENTI-167-CompatibilitÃ  paesaggistica-sospensione pratica - art. 167.odt'),
(21, 8, '146-Richiesta integrazioni - art. 146', 'ARC_DOCUMENTI-021-Richiesta integrazioni art.21.odt'),
(25, 2, '021-Autorizzazione precedente verifica interesse - art. 21', 'ARC_DOCUMENTI-021-Autorizzazione art. 21 precedente verifica interesse.odt'),
(98, 2, '021-Autorizzazione - art. 21', 'ARC_DOCUMENTI-021-Autorizzazione art. 21.odt'),
(26, 2, '021-Autorizzazione semplificata - art. 21', 'ARC_DOCUMENTI-021-Autorizzazione art. 21 semplificata.odt'),
(99, 2, '021-Diniego autorizzazione - art. 21', 'ARC_DOCUMENTI-021-Diniego Autorizzazione art. 21.odt'),
(28, 2, '021-Motivi ostativi art. 10bis Legge 241.90 s.m.i.', 'ARC_DOCUMENTI-021-Motivi ostativi - art.10bis.odt'),
(29, 2, '021-Restituzione istanza per assenza firma architetto', 'ARC_DOCUMENTI-021-Restituzione istanza per assenza firma architetto.odt'),
(38, 2, '021-Restituzione istanza imm. non dich. interesse', 'ARC_DOCUMENTI-021-Restituzione istanza immobile non dichiarato d interesse.odt'),
(31, 2, '021-Restituzione istanza per imm.le non sottoposto a tutela', 'ARC_DOCUMENTI-021-Restituzione istanza per immobile non sottoposto a tutela.odt'),
(33, 2, '021-Restituzione pratica per trasm. Ordinario Diocesano', 'ARC_DOCUMENTI-021-Restituzione per trasmissione Ordinario Diocesano - art. 21.odt'),
(34, 2, '021-Richiesta integrazioni - art. 21', 'ARC_DOCUMENTI-021-Richiesta integrazioni art.21.odt'),
(35, 2, '027-Provvedimento art. 27', 'ARC_DOCUMENTI-027-Provvedimento art. 27.odt'),
(36, 2, '045-Autorizzazione art. 45', 'ARC_DOCUMENTI-045-Autorizzazione art. 45.odt'),
(100, 2, '021-Autorizzazione - art. 21-31', 'ARC_DOCUMENTI-021-Autorizzazione art. 21-31.odt'),
(41, 20, 'AA01-Atto di proprietÃ ', 'ARC_DOCUMENTI-AA01-Atto di proprieta .odt'),
(42, 20, 'AA02-Modello bolli', 'ARC_DOCUMENTI-AA02-Modello bolli.odt'),
(43, 20, 'AA03-Modello DPR184 controinteressati', 'ARC_DOCUMENTI-AA03-Modello DPR 184 controinteressati.odt'),
(44, 20, 'AA04-Modello invito nuovo', 'ARC_DOCUMENTI-AA04-Modello invito nuovo.odt'),
(45, 20, 'AA05-Modello motivazione 2', 'ARC_DOCUMENTI-AA05-Modello motivazione 2.odt'),
(46, 20, 'AA06-Modello riscontro istanza accesso', 'ARC_DOCUMENTI-AA06-Modello riscontro istanza accesso.odt'),
(47, 20, 'AA07-Modello struttura esterna', 'ARC_DOCUMENTI-AA07-Modello struttura esterna.odt'),
(48, 20, 'AA08-Modello studiosi 2', 'ARC_DOCUMENTI-AA08-Modello studiosi 2.odt'),
(49, 20, 'AA09-Rischiesta accesso agli atti art. 25', 'ARC_DOCUMENTI-AA09-Richiesta accesso agli atti art. 25.odt'),
(50, 20, 'AA10-Rilascio copie bolli', 'ARC_DOCUMENTI-AA10-Rilascio copie bolli.odt'),
(51, 12, 'CS01-Richiesta trasmissione progetto', 'ARC_DOCUMENTI-CS01-Richiesta trasmissione documentazione di progetto.odt'),
(52, 12, 'CS02-Trasmissione parere favorevole', 'ARC_DOCUMENTI-CS02-Trasmissione parere favorevole.odt'),
(53, 12, 'CS03-Trasmissione parere direzione regionale', 'ARC_DOCUMENTI-CS03-Trasmissione parere alla direzione regionale.odt'),
(54, 12, 'CS04-Richiesta integrazioni', 'ARC_DOCUMENTI-CS04-Conferenza servizi - richiesta integrazioni.odt'),
(55, 12, 'CS05-Rinvio data conferenza', 'ARC_DOCUMENTI-CS05-Conferenza servizi - richiesta rinvio data conferenza.odt'),
(56, 12, 'CS06-Parere favorevole con prescrizioni', 'ARC_DOCUMENTI-CS06-Conferenza servizi - parere favorevole con prescrizioni.odt'),
(57, 12, 'CS07-Delega funzionario ', 'ARC_DOCUMENTI-CS07-Conferenza servizi delega funzionario.odt'),
(58, 13, '010-Verifica interesse avvio procedimento-art. 10', 'ARC_DOCUMENTI-010-Verifica-interesse-avviodelprocedimento-art10.odt'),
(59, 13, '012-Dichiarazione immobile non sottoposto a tutela-art. 12', 'ARC_DOCUMENTI-012-Dichiarazione di immobile non sottoposto a tutela-art. 12.odt'),
(61, 13, '012-Dichiarazione di vincolo L. 364-1909-art. 12', 'ARC_DOCUMENTI-012-Dichiarazione di vincolo L.364-1909-art. 12.odt'),
(62, 13, '012-Elenco immobili sottoposti a tutela', 'ARC_DOCUMENTI-012-Elenco immobili sottoposti a tutela-art. 12.odt'),
(63, 13, '012-Verifica interesse accertamento negativo - art. 12', 'ARC_DOCUMENTI-012-Verifica interesse accertamento negativo-art. 12.odt'),
(64, 13, '012-Verifica interesse accertamento positivo-art. 12', 'ARC_DOCUMENTI-012-Verifica interesse accertamento positivo-art. 12.odt'),
(65, 13, '012-Verifica interesse comuni province e regioni-art. 12', 'ARC_DOCUMENTI-012-Verifica interesse comuni province e regione-art. 12.odt'),
(66, 13, '012-Verifica interesse enti pubblici-art. 12', 'ARC_DOCUMENTI-012-Verifica interesse enti pubblici-art. 12.odt'),
(67, 13, '012-Ver. int. persone private  senza scopo di lucro-art. 12', 'ARC_DOCUMENTI-012-Verifica interesse persone private senza scopo di lucro-art. 12.odt'),
(68, 13, '012-Verifica interesse proprietÃ  ecclesiastiche-art. 12', 'ARC_DOCUMENTI-012-Verifica interesse proprietÃ  ecclesiastiche-art. 12.odt'),
(97, 6, '035-Contributi-Istruttoria ammissibilitÃ ', 'ARC_DOCUMENTI-035-Istruttoria ammissibilitÃ  contributo - art. 35.odt'),
(71, 6, '035-Contributi-AmmissibilitÃ ', 'ARC_DOCUMENTI-035-AmmissibilitÃ  a contributo-art. 35.odt'),
(72, 6, '035-Contributi- Non ammissibilitÃ ', 'ARC_DOCUMENTI-035-Non ammissibilitÃ  a contributo-art. 35.odt'),
(73, 6, '036-Contributi-Allegato B', 'ARC_DOCUMENTI-036-Allegato B-art. 36.odt'),
(74, 6, '036-Contributi-Allegato II-Dichiarazione assenza contributi', 'ARC_DOCUMENTI-036-Allegato II-Dichiarazione di assenza contributi - art. 36.odt'),
(75, 6, '036-Contributi-Allegato I-Proposta convenzione', 'ARC_DOCUMENTI-036-Allegato I-Proposta convenzione-art. 36.odt'),
(76, 6, '036-Contributi-Certificato di N.O. al pagamento', 'ARC_DOCUMENTI-036-Certificato di  N.O. al pagamento-art. 36.odt'),
(94, 6, '036-Contributi-Certificato di collaudo', 'ARC_DOCUMENTI-036-Certificato di collaudo-art. 36.odt'),
(78, 6, '036-Contributi-Elenco documentazione a consuntivo', 'ARC_DOCUMENTI-036-Elenco documentazione a consuntivo-art. 36.odt'),
(79, 6, '036-Contributi-Parere conclusivo', 'ARC_DOCUMENTI-036-Parere conclusivo-art. 36.odt'),
(80, 6, '036-Contributi-Proposta applicazione percentuale', 'ARC_DOCUMENTI-036-Proposta della percentuale-art. 36.odt'),
(81, 6, '037-Contributi in conto interessi-Allegato IV', 'ARC_DOCUMENTI-037-Allegato IV-conto interessi-art. 37.odt'),
(82, 6, '037-Contributi in conto interessi- Allegato III-mod. pagam.', 'ARC_DOCUMENTI-037-Allegato III-modalitÃ  di pagamento - art. 37.odt'),
(83, 17, '055-Richiesta autorizzazione alienazione', 'ARC_DOCUMENTI-055-Richiesta autorizzazione alienazione.odt'),
(84, 17, '056-Richiesta autorizzazione alienazione art. 56 c.1 lett. b', 'ARC_DOCUMENTI-056-Richiesta autorizzazione alienazione c1b.odt'),
(104, 13, '012-Dichiarazione immobile sottoposto a tutela-art. 12', 'ARC_DOCUMENTI-012-Dichiarazione di immobile sottoposto a tutela-art. 12.odt'),
(101, 22, '062-Comunicazione agli enti per prelazione', 'ARC_DOCUMENTI-062-Comunicazione agli enti per prelazione.odt'),
(102, 22, '062-Comunicazione alla D.R. rinuncia prelazione', 'ARC_DOCUMENTI-062-Comunicazione DR rinuncia prelazione.odt'),
(103, 22, '062-Parere esercizio prelazione', 'ARC_DOCUMENTI-062-Parere esercizio prelazione.odt');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
