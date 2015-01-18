<?php

/**
 * Base functionality shared by API calls
 */
abstract class HWWaitingTimeBaseApi extends ApiBase {
  public function updateWaitingTimeAverages($page_id) {
    $dbw = wfGetDB( DB_MASTER );

    // Get fresh waiting time count and average waiting time
    $res = $dbw->select(
      'hw_waiting_time',
      array(
        'COALESCE(AVG(hw_waiting_time), 0) AS average_waiting_time', // we decided to stay away from NULLs
        'COUNT(*) AS count_waiting_time'
      ),
      array(
        'hw_page_id' => $page_id
      )
    );
    $row = $res->fetchRow();
    $average = $row['average_waiting_time'];
    $count = $row['count_waiting_time'];

    // Update waiting time count and average waiting time
    $dbw->upsert(
      'hw_waiting_time_avg',
      array(
        'hw_page_id' => $page_id,
        'hw_count_waiting_time' => $count,
        'hw_average_waiting_time' => $average
      ),
      array('hw_page_id'),
      array(
        'hw_count_waiting_time' => $count,
        'hw_average_waiting_time' => $average
      )
    );

    return array(
      'average' => $average,
      'count' => $count
    );
  }
}

?>
