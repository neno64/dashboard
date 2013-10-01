<?php
  ini_set('display_errors',1);
  error_reporting(E_ALL);

include(dirname(__FILE__).'/config.php');
include(dirname(__FILE__).'/ytfunctions.php');
$channelId = ("UCV0c4SvILw3OZur3FEjE8RQ");
$cmsId = ("RPMNetworks");
$dashController = new dashClient($channelId);
if($dashController->getErrorCode()=="-1"){echo "problem -1";}
else{
  $start_date = date("Y-m-d", 0);
  $end_date = date("Y-m-d", time());
  $ytmetrics = "views,comments,favoritesAdded,favoritesRemoved,likes,dislikes,shares,estimatedMinutesWatched,averageViewDuration,averageViewPercentage,annotationClickThroughRate,annotationCloseRate,subscribersGained,subscribersLost,uniques";

  /*
  $alistOptArr['dimensions'] = 'day';
  $alistOptArr['mine'] = 'true';
  //$alistOptArr['filters'] = 'channel=='.$channelId;
  $alistOptArr['sort'] = 'day';
  $alistOptArr['access_token'] = $dashController->google->getAccessToken();

  $listOptArr['mine'] = 'true';
  $listOptArr['access_token'] = $dashController->google->getAccessToken();
  //$channelInfoArr = $dashController->youtube->channels->listChannels("id, snippet, contentDetails, statistics, topicDetails, invideoPromotion", $listOptArr);
  $analyticReport = $dashController->yanalytics->reports->query("channel==".$channelId, $start_date, $end_date, $ytmetrics, $alistOptArr);

  $db_audit = new mysqli($config->db_dash_host,$config->db_dash_username,$config->db_dash_password);
  if ($db_audit->connect_errno) { die("-7"); }
  foreach($analyticReport['rows'] as $reportDay){
    $dateRaw = explode('-', $reportDay[0]);
    $date->day = dateRaw[0];
    $date->month = dateRaw[1];
    $date->year = dateRaw[2];

    //    $result = $db_audit->query("CREATE TABLE IF NOT EXISTS `".$config->db_dash_database."`.`channel_analytics_".$update[0]."` (`cid` VARCHAR(60), `01` MEDIUMTEXT, `02` MEDIUMTEXT, `03` MEDIUMTEXT, `04` MEDIUMTEXT, `05` MEDIUMTEXT, `06` MEDIUMTEXT, `07` MEDIUMTEXT, `08` MEDIUMTEXT, `09` MEDIUMTEXT, `10` MEDIUMTEXT, `11` MEDIUMTEXT, `12` MEDIUMTEXT, INDEX (cid))");

    check ($result);

  }
  */
  echo '<pre>';
  print_r(date("Y-m-d", 0-172800));
}
$dashController->close();

function check ($r) {
  if (!$r) {
    $err = mysql_errno();
    if ($err == 1050 || $err == 1060) { return; }
    else { die ("Error: " . $err . ", " . mysql_error()); }
  }
}

?>