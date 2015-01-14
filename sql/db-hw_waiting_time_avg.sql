-- SQL schema for HWWaitingTime averages

CREATE TABLE `hw_waiting_time_avg` (
  hw_page_id int unsigned PRIMARY KEY NOT NULL,
  hw_average_waiting_time int(3) NOT NULL,
  hw_deleted BOOL DEFAULT false
);
