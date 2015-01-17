<?php

class HWAddWaitingTimeApi extends ApiBase {
  public function execute() {
    global $wgUser;
    if (!$wgUser->isAllowed('edit')) {
      $this->dieUsage("You don't have permission to add waiting time", "permissiondenied");
    }

    $params = $this->extractRequestParams();
    $page_id = $params['pageid'];
    $user_id = $wgUser->getId();
    $waiting_time = $params['waiting_time'];
    $timestamp = wfTimestampNow();

    // Exit with an error if pageid is not valid (eg. non-existent or deleted)
    $this->getTitleOrPageId($params);

    $dbw = wfGetDB( DB_MASTER );
    $dbw->insert(
      'hw_waiting_time',
      array(
        'hw_user_id' => $user_id,
        'hw_page_id' => $page_id,
        'hw_waiting_time' => $waiting_time,
        'hw_timestamp' => $timestamp
      )
    );
    $waiting_time_id = $dbw->insertId();

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
    $this->getResult()->addValue('query' , 'waiting_time_id', $waiting_time_id);
    $this->getResult()->addValue('query' , 'timestamp', $timestamp);

    return true;
  }

  // Description
  public function getDescription() {
    return 'Add waiting time for page';
  }

  // Parameters
  public function getAllowedParams() {
    return array(
      'waiting_time' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => true,
        ApiBase::PARAM_MIN => 0,
        ApiBase::PARAM_RANGE_ENFORCE => true
      ),
      'pageid' => array (
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
          'waiting_time' => 'Waiting time (in minutes)',
          'pageid' => 'Page id',
          'token' => 'csrf token'
      ) );
  }

  public function needsToken() {
      return 'csrf';
  }
}

?>
