-- SQL schema for HWWaitingTime

CREATE TABLE `hw_waiting_time` (
  hw_user_id int unsigned NOT NULL,
  hw_page_id int unsigned NOT NULL,
  hw_waiting_time int(3) NOT NULL,
  hw_timestamp CHAR(14) NOT NULL,
  hw_deleted BOOL DEFAULT false
);

CREATE INDEX hw_page_primary ON hw_waiting_time ( hw_page_id );
