CREATE TABLE IF NOT EXISTS `inbox` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` datetime NOT NULL COMMENT 'дата получения sms',
  `msg` varchar(255) NOT NULL COMMENT 'сообщение абонента',
  `msg_trans` varchar(255) NOT NULL COMMENT 'транслитерированное сообщение',
  `operator_id` int(10) unsigned NOT NULL COMMENT 'id оператора',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'номер абонента',
  `smsid` int(10) unsigned NOT NULL COMMENT 'id смс',
  `cost_rur` float(15,4) unsigned NOT NULL COMMENT 'сумма в рублях',
  `cost` float(15,4) unsigned NOT NULL COMMENT 'информационный параметр (по курсу последней выплаты)',
  `test` tinyint(1) unsigned NOT NULL COMMENT '1 - тестовая смс',
  `num` varchar(20) NOT NULL COMMENT 'короткий номер',
  `skey` char(32) NOT NULL COMMENT 'секретный ключ md5',
  `sign` char(32) NOT NULL COMMENT 'md5 от параметров',
  `ran` tinyint(1) unsigned NOT NULL,
  `answer` text NOT NULL COMMENT 'ответ сервиса без заголовка',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `smsid` (`smsid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `passwords` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `password` int(10) unsigned NOT NULL COMMENT 'пароль',
  `date` datetime NOT NULL COMMENT 'дата создания пароля',
  `end_date` datetime NOT NULL COMMENT 'дата окончания действия пароля',
  PRIMARY KEY  (`id`),
  KEY `password` (`password`),
  KEY `end_date` (`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;