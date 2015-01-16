<?php
class HWAddWaitingTimeApi extends ApiBase {
  public function execute() {
    // Get parameters
    $params = $this->extractRequestParams();
    global $wgUser;

    $page_id = $params['pageid'];
    $user_id = $wgUser->getId();
    $waiting_time = $params['waiting_time'];
    $timestamp = wfTimestampNow();

    $pageObj = $this->getTitleOrPageId($params);
    if($waiting_time >= 0) {
      $dbr = wfGetDB( DB_MASTER );

      $dbr->insert(
        'hw_waiting_time',
        array(            
          'hw_page_id' => $page_id,
          'hw_user_id' => $user_id,
          'hw_waiting_time' => $waiting_time,
          'hw_timestamp' => $timestamp
        )
      );

      $res = $dbr->query("SELECT hw_waiting_time FROM hw_waiting_time WHERE hw_page_id=".$dbr->addQuotes($page_id));

      /* We are making research to see how should look the average wainting time
      $i = 0;
      foreach( $res as $row ) {
        $waitings_array[$i] = $row->hw_waiting_time;
        $i++;
      }

      function calculate_median($arr) {
          sort($arr);
          $count = count($arr); //total numbers in array
          $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
          if($count % 2) { // odd number, middle is the median
              $median = $arr[$middleval];
          } else { // even number, calculate avg of 2 medians
              $low = $arr[$middleval];
              $high = $arr[$middleval+1];
              $median = (($low+$high)/2);
          }
          return $median;
      }
      $waiting_median = calculate_median($waitings_array);
      */


      $this->getResult()->addValue('query' , 'blabla', 'We dont know yet what to answer');
    }
    else {
      $this->getResult()->addValue('error' , 'info', 'waiting time should be 1 and 5.');
    }

    return true;
  }

  // Description
  public function getDescription() {
      return 'Add a waiting time to a spot.';
  }

  // Parameters.
  public function getAllowedParams() {
      return array(
          'waiting_time' => array (
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
          ),
          'pageid' => array (
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
          ),
          'token' => array (
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
          )
      );
  }

  // Describe the parameter
  public function getParamDescription() {
      return array_merge( parent::getParamDescription(), array(
          'waiting_time' => 'Waiting time to add to the spot (in minutes)',
          'pageid' => 'Id of the spot to rate',
          'token' => 'User edit token'
      ) );
  }

  public function needsToken() {
      return 'csrf';
  }

}
