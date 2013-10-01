<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();
//set_include_path('.:/var/www/vhosts/n4gtv.com/dashboard/lib/google/src');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include(dirname(__FILE__).'/google/src/Google_Client.php');
include(dirname(__FILE__).'/google/src/contrib/Google_YouTubeService.php');
include(dirname(__FILE__).'/config.php');
include(dirname(__FILE__).'/functions.php');

/** Xenforo Information **/ 

if(logged_in()=="0"){die('Log in');}
$userInfo = getInfo($_SESSION['n4guser'],$_SESSION['n4gxhash']);

$client = new Google_Client();
$client->setApprovalPrompt('force');
$client->setAccessType('offline');
$client->setScopes('https://www.googleapis.com/auth/yt-analytics-monetary.readonly https://www.googleapis.com/auth/yt-analytics.readonly https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.readonly https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtubepartner');
$youtubeController = new Google_YoutubeService($client);

$auth = $client->authenticate();
$token = json_decode($client->getAccessToken());
//print_r ($token);

$client->refreshToken($token->refresh_token);
$token = json_decode($client->getAccessToken());
//print_r ($token);
if(isset($token->access_token)){
  $listOptArr['mine'] = 'true';
  $listOptArr['access_token'] = $token->access_token;
  $channelInfoArr = $youtubeController->channels->listChannels("id, snippet, contentDetails, statistics, topicDetails, invideoPromotion", $listOptArr);

  $db_dash = new mysqli($config->db_dash_host,$config->db_dash_username,$config->db_dash_password);
  if ($db_dash->connect_errno) { echo "-7";  }
  $resultAC = $db_dash->query("SELECT * FROM `".$config->db_dash_database."`.`accesscodes` WHERE `cid`='".$channelInfoArr['items'][0]['id']."'");
  if($resultAC){$rowac=$resultAC->fetch_array(MYSQLI_ASSOC);if($channelInfoArr['items'][0]['id']==$rowac['cid']){$channelExists=true;}else{$channelExists=false;}}
  $resultAC->close();

  if($channelExists==true){  
    $db_dash->query("UPDATE `".$config->db_dash_database."`.`accesscodes` 
                   SET accesscode='".$client->getAccessToken()."'
                   WHERE cid='".$channelInfoArr['items'][0]['id']."'");
    $db_dash->query("UPDATE `".$config->db_dash_database."`.`channels` 
                   SET userid='".$userInfo->user_id."', 
                       email='".$db_dash->real_escape_string($userInfo->email)."', 
                       title='".$db_dash->real_escape_string($channelInfoArr['items'][0]['snippet']['title'])."',
                       description='".$db_dash->real_escape_string($channelInfoArr['items'][0]['snippet']['description'])."',
                       thumbnail='".urlencode($channelInfoArr['items'][0]['snippet']['thumbnails']['high']['url'])."',
                       statistics='".$db_dash->real_escape_string(json_encode($channelInfoArr['items'][0]['statistics']))."',
                       relatedplaylists='".$db_dash->real_escape_string(json_encode($channelInfoArr['items'][0]['contentDetails']['relatedPlaylists']))."'
                   WHERE cid='".$channelInfoArr['items'][0]['id']."'");

  } else {
    $db_dash->query("INSERT INTO `".$config->db_dash_database."`.`accesscodes` (cid, accesscode) 
                   VALUES ('".$channelInfoArr['items'][0]['id']."', '".$client->getAccessToken()."')");
    $db_dash->query("INSERT INTO `".$config->db_dash_database."`.`channels` (userid, cid, email, title, description, thumbnail, statistics, relatedplaylists)
                        VALUES (
                       '".$userInfo->user_id."',
                       '".$channelInfoArr['items'][0]['id']."',
                       '".$db_dash->real_escape_string($userInfo->email)."', 
                       '".$db_dash->real_escape_string($channelInfoArr['items'][0]['snippet']['title'])."',
                       '".$db_dash->real_escape_string($channelInfoArr['items'][0]['snippet']['description'])."',
                       '".urlencode($channelInfoArr['items'][0]['snippet']['thumbnails']['high']['url'])."',
                       '".$db_dash->real_escape_string(json_encode($channelInfoArr['items'][0]['statistics']))."',
                       '".$db_dash->real_escape_string(json_encode($channelInfoArr['items'][0]['contentDetails']['relatedPlaylists']))."'
                   )");

  }
}
$db_dash->close();
echo(json_encode($channelInfoArr['items'][0]['statistics']));
//echo(json_encode($channelInfoObj['items'][0]['statistics']));
?>
