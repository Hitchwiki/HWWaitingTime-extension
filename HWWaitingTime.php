<?php

/**
 * Supported algorithms for the calculation of average waiting times
 */

define('WAITING_TIME_AVG_ALGORITHM_MEAN', 1);
define('WAITING_TIME_AVG_ALGORITHM_MEDIAN', 2);

/**
 * Algorithm to be used for the calculation of average waiting times
 */

$wgWaitingTimeAvgAlgorithm = WAITING_TIME_AVG_ALGORITHM_MEDIAN;

/* ------------------------------------------------------------------------ */

$wgExtensionCredits['api'][] = array(
	'path' => __FILE__,
	'name' => 'HWWaitingTime',
	'version' => '0.0.1',
	"authors" => "http://hitchwiki.org"
);

$dir = __DIR__;

$wgAutoloadClasses['HWWaitingTimeHooks'] = "$dir/HWWaitingTimeHooks.php";
$wgHooks['LoadExtensionSchemaUpdates'][] = 'HWWaitingTimeHooks::onLoadExtensionSchemaUpdates';

//Deletion and undeletion hooks
$wgHooks['ArticleDeleteComplete'][] = 'HWWaitingTimeHooks::onArticleDeleteComplete';
$wgHooks['ArticleRevisionUndeleted'][] = 'HWWaitingTimeHooks::onArticleRevisionUndeleted';

$wgAutoloadClasses['HWWaitingTimeBaseApi'] = "$dir/api/HWWaitingTimeBaseApi.php";
$wgAutoloadClasses['HWAddWaitingTimeApi'] = "$dir/api/HWAddWaitingTimeApi.php";
$wgAutoloadClasses['HWDeleteWaitingTimeApi'] = "$dir/api/HWDeleteWaitingTimeApi.php";
$wgAutoloadClasses['HWAvgWaitingTimeApi'] = "$dir/api/HWAvgWaitingTimeApi.php";
$wgAutoloadClasses['HWGetWaitingTimesApi'] = "$dir/api/HWGetWaitingTimesApi.php";
$wgAPIModules['hwaddwaitingtime'] = 'HWAddWaitingTimeApi';
$wgAPIModules['hwdeletewaitingtime'] = 'HWDeleteWaitingTimeApi';
$wgAPIModules['hwavgwaitingtime'] = 'HWAvgWaitingTimeApi';
$wgAPIModules['hwgetwaitingtimes'] = 'HWGetWaitingTimesApi';

return true;
