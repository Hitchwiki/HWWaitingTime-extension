<?php

class HWWaitingTimeHooks {
  public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
    $updater->addExtensionTable( 'hw_waiting_time', dirname( __FILE__ ) . '/sql/db-hw_waiting_time.sql' );
    $updater->addExtensionTable( 'hw_waiting_time_avg', dirname( __FILE__ ) . '/sql/db-hw_waiting_time_avg.sql' );

    return true;
  }
}



