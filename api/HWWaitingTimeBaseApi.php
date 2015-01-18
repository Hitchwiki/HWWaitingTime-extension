<?php

/**
 * Base functionality shared by API calls
 */
abstract class HWWaitingTimeBaseApi extends ApiBase {
  public function updateWaitingTimeAverages($page_id) {
    $page_id = intval($page_id);
    global $wgWaitingTimeAvgAlgorithm;
    $dbw = wfGetDB( DB_MASTER );

    $columns = array(
      'COUNT(*) AS count_waiting_time'
    );

    if ($wgWaitingTimeAvgAlgorithm != WAITING_TIME_AVG_ALGORITHM_MEDIAN) { // use mean algorithm
      $columns[] = 'COALESCE(AVG(hw_waiting_time), -1) AS average_waiting_time'; // we decided to stay away from NULLs
    }

    // Get fresh waiting time count (and, in case of mean value algorithm, mean waiting time)
    $res = $dbw->select(
      'hw_waiting_time',
      $columns,
      array(
        'hw_page_id' => $page_id
      )
    );
    $row = $res->fetchRow();
    $count = intval($row['count_waiting_time']);

    if ($count != 0) {
      if ($wgWaitingTimeAvgAlgorithm != WAITING_TIME_AVG_ALGORITHM_MEDIAN) { // use mean algorithm
        $average = intval($row['average_waiting_time']);
      } else { // use median algorithm
          if ($count & 1) { // odd number of waiting times; fetch one middle number
            $offset = ($count - 1) / 2;
            $limit = 1;
          } else { // even number of waiting times; fetch two middle numbers
            $offset = $count / 2 - 1;
            $limit = 2;
          }

          $median_res = $dbw->query(
            'SELECT hw_waiting_time' .
              ' FROM hw_waiting_time' .
              ' WHERE hw_page_id = ' . $page_id .
              ' ORDER BY hw_waiting_time' .
              ' LIMIT ' . $limit .
              ' OFFSET ' . $offset
          );

          if ($count & 1) { // odd number of waiting times; median is the middle number
            $median_row = $median_res->fetchRow();
            $average = intval($median_row['hw_waiting_time']);
          } else { // even number of waiting times; median is the mean value of the two middle numbers
            $median_row1 = $median_res->fetchRow();
            $median_row2 = $median_res->fetchRow();
            $average = intval(round(
              (doubleval($median_row1['hw_waiting_time']) + doubleval($median_row2['hw_waiting_time'])) / 2
            ));
          }
      }

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
    } else { // $count == 0
      $average = -1; // we decided to stay away from NULLs because of JSON limitations

      // Delete waiting time count and average waiting time for the page, if the page doesn't have any waiting times
      $dbw->delete(
        'hw_waiting_time_avg',
        array(
          'hw_page_id' => $page_id,
        )
      );
    }

    return array(
      'average' => $average,
      'count' => $count
    );
  }
}

?>
