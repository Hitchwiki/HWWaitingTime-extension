<?php

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
