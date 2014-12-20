-- SQL schema for HWWaitingTime averages

CREATE TABLE `hw_waiting_time_avg` (
  hw_page_id int unsigned PRIMARY KEY NOT NULL,
  hw_average_waiting_time int(3) NOT NULL
);

CREATE INDEX hw_page_primary ON hw_waiting_time_avg ( hw_page_id );
