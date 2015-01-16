<?php
class HWDeleteWaitingTimeApi extends ApiBase {
  public function execute() {
    // Get parameters
    $params = $this->extractRequestParams();
    global $wgUser;

    $waiting_time_id = $params['waiting_time_id'];
    $dbr = wfGetDB( DB_MASTER );
    $res = $dbr->select(
      'hw_waiting_time',
      array(
        'hw_user_id',
        'hw_page_id'
      ),
      'hw_waiting_time_id='.$waiting_time_id
    );

    $row = $res->fetchObject();
    if (!$row) {
      $this->getResult()->addValue('error' , 'info', 'waiting time does not exist');
      return true;
    }

    if ($row->hw_user_id != $wgUser->getId()) {
      $this->getResult()->addValue('error' , 'message', 'waiting time is authored by another user');
      return true;
    }

    $page_id = $row->hw_page_id;
    $dbr->delete(
      'hw_waiting_time',
      array(
        'hw_waiting_time_id' => $waiting_time_id
      )
    );

    $res = $dbr->query(
      "SELECT COUNT(*) AS count_waiting_time, AVG(hw_waiting_time) AS average_waiting_time" .
        " FROM hw_waiting_time" .
        " WHERE hw_page_id=".$dbr->addQuotes($page_id)
    );
    $row = $res->fetchObject();
    $count = $row->count_waiting_time;
    $avg = $row->average_waiting_time ? $row->average_waiting_time : 0;

    $dbr->upsert(
      'hw_waiting_time_avg',
      array(
        'hw_page_id' => $page_id,
        'hw_count_waiting_time' => $count,
        'hw_average_waiting_time' => $avg
      ),
      array('hw_page_id'),
      array(
        'hw_page_id' => $page_id,
        'hw_count_waiting_time' => $count,
        'hw_average_waiting_time' => $avg
      )
    );

    $this->getResult()->addValue('info' , 'message', 'waiting time was deleted');
    $this->getResult()->addValue('info' , 'pageid', $page_id);
    $this->getResult()->addValue('info' , 'waiting_time_count', $count);
    $this->getResult()->addValue('info' , 'waiting_time_average', $avg);

    return true;
  }

  // Description
  public function getDescription() {
      return 'Delete waiting time from a spot.';
  }

  // Parameters.
  public function getAllowedParams() {
      return array(
          'waiting_time_id' => array (
              ApiBase::PARAM_TYPE => 'integer',
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
          'waiting_time_id' => 'Waiting time id',
          'token' => 'User edit token'
      ) );
  }

  public function needsToken() {
      return 'csrf';
  }

}

?>