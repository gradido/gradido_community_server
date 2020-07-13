CREATE TABLE `transaction_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) COLLATE utf8_bin NOT NULL,
  `text` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `transaction_types` (`id`, `name`, `text`) VALUES (1, 'creation', 'Aktives oder Bedingungsloses Grundeinkommen.'), (2, 'transfer', 'Einfache Überweisung');

