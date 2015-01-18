<?php

class HWGetWaitingTimesApi extends HWWaitingTimeBaseApi {
  public function execute() {
    $params = $this->extractRequestParams();
    $page_id = $params['pageid'];

    // Exit with an error if pageid is not valid (eg. non-existent or deleted)
    $this->getTitleOrPageId($params);

    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select(
      array(
        'hw_waiting_time',
        'user'
      ),
      array(
        'hw_user_id',
        'hw_page_id',
        'hw_waiting_time_id',
        'hw_waiting_time',
        'hw_timestamp',
        'user_name'
      ),
      array(
        'hw_page_id' => $page_id
      ),
      __METHOD__,
      array(),
      array( 'user' => array( 'JOIN', array(
        'hw_waiting_time.hw_user_id = user.user_id',
      ) ) )
    );

    $this->getResult()->addValue( array( 'query' ), 'waiting_times', array() );
    foreach( $res as $row ) {
      $vals = array(
        'pageid' => intval($row->hw_page_id),
        'waiting_time_id' => intval($row->hw_waiting_time_id),
        'waiting_time' => intval($row->hw_waiting_time),
        'timestamp' => $row->hw_timestamp,
        'user_id' => intval($row->hw_user_id),
        'user_name' => $row->user_name
      );
      $this->getResult()->addValue( array( 'query', 'waiting_times' ), null, $vals );
    }

    return true;
  }

  // Description
  public function getDescription() {
    return 'Get all the waiting times of a page';
  }

  // Parameters
  public function getAllowedParams() {
    return array(
      'pageid' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => true
      )
    );
  }

  // Describe the parameters
  public function getParamDescription() {
    return array_merge( parent::getParamDescription(), array(
      'pageid' => 'Page id',
    ) );
  }
}

?>
