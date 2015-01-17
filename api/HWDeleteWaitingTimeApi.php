<?php

class HWDeleteWaitingTimeApi extends ApiBase {
  public function execute() {
    global $wgUser;
    if (!$wgUser->isAllowed('edit')) {
      $this->dieUsage("You don't have permission to delete waiting time", "permissiondenied");
    }

    $params = $this->extractRequestParams();
    $waiting_time_id = $params['waiting_time_id'];

    $dbw = wfGetDB( DB_MASTER );
    $res = $dbw->select(
      'hw_waiting_time',
      array(
        'hw_user_id',
        'hw_page_id'
      ),
      array(
        'hw_waiting_time_id' => $waiting_time_id
      )
    );

    $row = $res->fetchObject();
    if (!$row) {
      $this->dieUsage("There is no waiting time with specified id", "nosuchwaitingtimeid");
    }

    if ($row->hw_user_id != $wgUser->getId()) {
      $this->dieUsage("You don't have permission to delete waiting time that was authored by another user", "permissiondenied");
    }

    $dbw->delete(
      'hw_waiting_time',
      array(
        'hw_waiting_time_id' => $waiting_time_id
      )
    );

    $page_id = $row->hw_page_id;

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

    // Update waiting time count and average waiting time cache
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

    $this->getResult()->addValue('query' , 'average', round($average, 2));
    $this->getResult()->addValue('query' , 'count', intval($count));
    $this->getResult()->addValue('query' , 'pageid', intval($page_id));

    return true;
  }

  // Description
  public function getDescription() {
      return 'Delete waiting time of page';
  }

  // Parameters
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

  // Describe the parameters
  public function getParamDescription() {
      return array_merge( parent::getParamDescription(), array(
          'waiting_time_id' => 'Waiting time id',
          'token' => 'csrf token'
      ) );
  }

  public function needsToken() {
      return 'csrf';
  }

}

?>
