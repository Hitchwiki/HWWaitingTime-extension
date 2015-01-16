-- SQL schema for HWWaitingTime

DROP TABLE IF EXISTS `hw_waiting_time`;

CREATE TABLE `hw_waiting_time` (
  hw_waiting_time_id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  hw_user_id int unsigned NOT NULL,
  hw_page_id int unsigned NOT NULL,
  hw_waiting_time int(3) NOT NULL,
  hw_timestamp CHAR(14) NOT NULL,
  hw_deleted BOOL DEFAULT false
);

CREATE INDEX hw_page_primary ON hw_waiting_time ( hw_page_id );
