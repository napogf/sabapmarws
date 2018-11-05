-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Set 04, 2017 alle 10:50
-- Versione del server: 10.0.20-MariaDB-1~utopic-log
-- PHP Version: 5.6.4-4ubuntu6.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sbapvrws`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `sys_config`
--

DROP TABLE IF EXISTS `sys_config`;
CREATE TABLE IF NOT EXISTS `sys_config` (
`id` int(11) NOT NULL,
  `chiave` varchar(255) NOT NULL,
  `valore` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `sys_config`
--

INSERT INTO `sys_config` (`id`, `chiave`, `valore`) VALUES
(1, 'PEC_HOSTNAME', 'in.telecompost.it'),
(2, 'PEC_HOSTPORT', '993'),
(3, 'PEC_USERNAME', 'mbac-sabap-vr@mailcert.beniculturali.it'),
(5, 'WS_UserID', 'PROTOCOLLO_WS'),
(6, 'WS_Password', '123456Aa'),
(7, 'WS_CodUtenteProtocollatore', 'PROTOCOLLO_WS'),
(8, 'WS_C_DES_AOO', 'TEST-MBAC-SBAP-VE'),
(9, 'WS_CodUfficioCompetente', 'SBAP-VE'),
(10, 'WS_debug', '1'),
(11, 'KEY_SOPRINTENDENTE', 'Fabrizio Magani'),
(12, 'PEC_SMTPHOST', 'smtp.telecompost.it'),
(13, 'PEC_SMTPPORT', '25'),
(14, 'KEY_NOME_MINISTERO', 'Ministero dei Beni e delle Attività Culturali e del Turismo'),
(15, 'KEY_NOME_SOPRINTENDENZA', 'SOPRINTENDENZA ARCHEOLOGIA, BELLE ARTI E PAESAGGIO PER LE PROVINCE DI VERONA, ROVIGO E VICENZA'),
(16, 'KEY_INTESTAZIONE_PIE_PAGINA_1', 'P.tta San Fermo, 3/a - 37121 Verona - C.F.:80022500237 - Codice IPA.: CER15H'),
(17, 'KEY_INTESTAZIONE_PIE_PAGINA_2', 'tel. 0458050111 - fax.: 045597504 - 0458050147; Ufficio Esportazione tel.: 045-8050198'),
(18, 'KEY_INTESTAZIONE_PIE_PAGINA_3', 'E-mail.: sabap-vr@beniculturali.it - PEC.: mbac-sabap-vr@mailcert.beniculturali.it'),
(19, 'KEY_INTESTAZIONE_PIE_PAGINA_4', 'Sito Web htpp://sbap-vr.beniculturali.it'),
(21, 'KEY_NOSTRO_PROTOCOLLO', 'Sabap'),
(22, 'PEC_PASSWORD', 'Egitto3322!');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sys_config`
--
ALTER TABLE `sys_config`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sys_config`
--
ALTER TABLE `sys_config`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
