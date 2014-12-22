<?php
class HWGetWaitingTimesApi extends ApiBase {
  public function execute() {
    // Get parameters
    $params = $this->extractRequestParams();

    $page_id = $params['pageid'];
    $pageObj = $this->getTitleOrPageId($params);

    $dbr = wfGetDB( DB_SLAVE );
    $res = $dbr->select(
      'hw_waiting_time',
      array(
        'hw_user_id',
        'hw_page_id',
        'hw_waiting_time',
        'hw_timestamp'
      ),
      'hw_page_id ='.$page_id
    );    

    foreach( $res as $row ) {
      $vals = array(
        'pageid' => $row->hw_page_id,
        'user_id' => $row->hw_user_id,
        'wainting_time' => $row->hw_waiting_time,
        'timestamp' => $row->hw_timestamp
      );
      $this->getResult()->addValue( array( 'query', 'ratings' ), null, $vals );
    }
    if($vals == null) {
        $this->getResult()->addValue( array( 'query', 'ratings' ), null, null);
    }

    return true;
  }

  // Description
  public function getDescription() {
      return 'Get all the waiting times of a page.';
  }

  // Parameters.
  public function getAllowedParams() {
      return array(
          'pageid' => array (
              ApiBase::PARAM_TYPE => 'string',
              ApiBase::PARAM_REQUIRED => true
          )
      );
  }

  // Describe the parameter
  public function getParamDescription() {
      return array_merge( parent::getParamDescription(), array(
          'pageid' => 'Id of the page',
      ) );
  }
}
