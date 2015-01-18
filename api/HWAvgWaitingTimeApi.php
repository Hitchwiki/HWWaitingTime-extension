<?php

class HWAvgWaitingTimeApi extends HWWaitingTimeBaseApi {
  public function execute() {
    $params = $this->extractRequestParams();
    $page_ids = $params['pageid'];

    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select(
      'hw_waiting_time_avg',
      array(
        'hw_average_waiting_time',
        'hw_count_waiting_time',
        'hw_page_id'
      ),
      array(
        'hw_page_id' => $page_ids
      )
    );

    $this->getResult()->addValue( array( 'query' ), 'waiting_times', array() );
    foreach( $res as $row ) {
      $vals = array(
        'pageid' => intval($row->hw_page_id),
        'waiting_time_average' => intval(round($row->hw_average_waiting_time)),
        'waiting_time_count' => intval($row->hw_count_waiting_time)
      );
      $this->getResult()->addValue( array( 'query', 'waiting_times' ), null, $vals );
    }

    return true;
  }

  // Description
  public function getDescription() {
    return 'Get waiting time count and average waiting time of one or more pages';
  }

  // Parameters
  public function getAllowedParams() {
    return array(
      'pageid' => array (
        ApiBase::PARAM_TYPE => 'integer',
        ApiBase::PARAM_REQUIRED => true,
        ApiBase::PARAM_ISMULTI => true
      )
    );
  }

  // Describe the parameters
  public function getParamDescription() {
    return array_merge( parent::getParamDescription(), array(
      'pageid' => 'Page ids, delimited by | (vertical bar)'
    ) );
  }
}

?>