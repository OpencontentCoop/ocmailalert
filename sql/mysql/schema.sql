CREATE TABLE `ocmailalert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `frequency` varchar(30) NOT NULL,
  `query` longtext,
  `match_condition` varchar(10) NOT NULL,
  `match_condition_value` varchar(255) NOT NULL,
  `recipients` longtext,
  `subject` longtext,
  `body` longtext,
  `last_call` int(11) DEFAULT NULL,
  `last_log` longtext,
  `last_mail` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
