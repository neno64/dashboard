<?php
  
  ini_set('display_errors',1);
  error_reporting(E_ALL);
  /*
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
  */
include(dirname(__FILE__).'/config.php');
include(dirname(__FILE__).'/functions.php');
include(dirname(__FILE__).'/ytfunctions.php');
    echo('lol');  
//if(logged_in()=="0"){die('Log in');}
$channelId = ("UCV0c4SvILw3OZur3FEjE8RQ");
$cmsId = ("RPMNetworks");
$dashController = new dashClient($channelId);
if($dashController->getErrorCode()=="-1"){echo "problem -1";}
else{

  $db_audit = new mysqli($config->db_dash_host,$config->db_dash_username,$config->db_dash_password);
  if ($db_audit->connect_errno) { die("-7"); }

  $result= $db_audit->query("SELECT * FROM `".$config->db_dash_database."`.`channels` WHERE `cid`='".$channelId."'") or die($db_audit->error);
  if($result){$rowt=$result->fetch_array(MYSQLI_ASSOC);}else{die("-7");}
  $result->close();
  $db_audit->close();

  if( true ){ //$rowt['lastauditytd'] <= time()-8640){
    $dashController->setChannelData();
  }

  if( true ){ //($rowt['lastaudityta'] <= time()-172800) && ((intval(date("d", time()))+$rowt['userid'])%2==0)){
    $dashController->setChannelAnalytics();
  }
  
}
$dashController->close();
/*
function check ($r) {
  if (!$r) {
    $err = mysql_errno();
    if ($err == 1050 || $err == 1060) { return; }
    else { die ("Error: " . $err . ", " . mysql_error()); }
  }
}
*/
?>