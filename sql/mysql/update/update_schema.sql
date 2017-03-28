ALTER TABLE `ocmailalert` CHANGE `condition` `match_condition` varchar(10) NOT NULL;
ALTER TABLE `ocmailalert` CHANGE `condition_value` `match_condition_value` varchar(255) NOT NULL;