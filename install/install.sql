CREATE TABLE IF NOT EXISTS `PREFIX_winbankredirect_transaction` (
  `id_winbankredirect_transaction` int(11) NOT NULL AUTO_INCREMENT,
  `id_cart` int(11) NOT NULL,
  `merchant_reference` text NOT NULL,
  `installments` tinyint(1) NOT NULL,
  `ticket` char(64) DEFAULT '0',
  `successful` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_winbankredirect_transaction`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;