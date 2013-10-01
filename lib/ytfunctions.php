<?php
  ini_set('display_errors',1);
  error_reporting(E_ALL);


  require_once(dirname(__FILE__).'/google/src/Google_Client.php');
  require_once(dirname(__FILE__).'/google/src/contrib/Google_YouTubeService.php');
  require_once(dirname(__FILE__).'/google/src/contrib/Google_YouTubeAnalyticsService.php');

  class dashClient{
    private $accessCode, $channelId, $errorCode, $db_dash;
    public $google;
    public $youtube;
    public $yanalytics;
    
    public function __construct($cid) {
      include(dirname(__FILE__).'/config.php');
      $this->google = new Google_Client();
      $this->youtube = new Google_YoutubeService($this->google);
      $this->yanalytics = new Google_YoutubeAnalyticsService($this->google);
      $this->channelId = $cid;

      $this->db_dash = new mysqli($config->db_dash_host,$config->db_dash_username,$config->db_dash_password);

      $resultAC = $this->db_dash->query("SELECT * FROM `".$config->db_dash_database."`.`accesscodes` WHERE `cid`='".$this->channelId."'");
      if($resultAC){$rowac=$resultAC->fetch_array(MYSQLI_ASSOC);if($this->channelId==$rowac['cid']){$this->accessCode=json_decode($rowac['accesscode']); $this->errorCode="0";}else{$this->errorCode="-1";}}else{$this->errorCode="-1";}
      $resultAC->close();
      if($this->errorCode!="-1"){ $this->google->setAccessToken($rowac['accesscode']); }


    }
    public function setChannelData(){

      include(dirname(__FILE__).'/config.php');
      $ytlistOptArr['mine'] = 'true';
      $ytlistOptArr['access_token'] = $this->google->getAccessToken();
      $channelInfoArr = $this->youtube->channels->listChannels("id, snippet, contentDetails, statistics, topicDetails, invideoPromotion", $ytlistOptArr);
      $this->db_dash->query("UPDATE `".$config->db_dash_database."`.`channels`
                             SET
                              title='".$this->db_dash->real_escape_string($channelInfoArr['items'][0]['snippet']['title'])."',
                              description='".$this->db_dash->real_escape_string($channelInfoArr['items'][0]['snippet']['description'])."',
                              thumbnail='".urlencode($channelInfoArr['items'][0]['snippet']['thumbnails']['high']['url'])."',
                              statistics='".$this->db_dash->real_escape_string(json_encode($channelInfoArr['items'][0]['statistics']))."',
                              relatedplaylists='".$this->db_dash->real_escape_string(json_encode($channelInfoArr['items'][0]['contentDetails']['relatedPlaylists']))."',
                              lastauditytd='".time()."'
                             WHERE cid='".$this->channelId."'") or die('error');
      return true;
    }
   
    public function setChannelAnalytics($start_date = null, $end_date = null, $ametrics = null, $alistOptArr = null){
      include(dirname(__FILE__).'/config.php');
      $result= $this->db_dash->query("SELECT * FROM `".$config->db_dash_database."`.`channels` WHERE `cid`='".$this->channelId."'") or die($this->db_dash->error);
      if($result){$rowt=$result->fetch_array(MYSQLI_ASSOC);}else{return false;}
      $result->close();
      if($start_date==null){ $start_date = date("Y-m-d", $rowt['lastaudityta']-345600); }
      if($end_date==null){ $end_date = date("Y-m-d", time()); } 
      if($alistOptArr==null){
	$alistOptArr['dimensions'] = 'day';
	$alistOptArr['mine'] = 'true';
	$alistOptArr['sort'] = 'day';
	$alistOptArr['access_token'] = $this->google->getAccessToken();
      }
      if($ametrics==null){ $ametrics = "views,comments,favoritesAdded,favoritesRemoved,likes,dislikes,shares,estimatedMinutesWatched,averageViewDuration,averageViewPercentage,annotationClickThroughRate,annotationCloseRate,subscribersGained,subscribersLost,uniques"; }

      $analyticsArray = array();
      $analyticReport = $this->yanalytics->reports->query("channel==".$this->channelId, $start_date, $end_date, $ametrics, $alistOptArr);
      $yearChange ="";
    
      if($rowt['lastaudityta'] > 0){
	$currYear = intval(date("Y", time())); 
	$auditYear = intval(date("Y", $rowt['lastaudityta']));
	for($i = $currYear; $i >= $auditYear && $i >= 2004; $i--){
	  $result = $this->db_dash->query("SELECT * FROM `".$config->db_dash_database."`.`channel_analytics_".$i."` WHERE `cid`='".$this->channelId."'") or die($this->db_dash->error);
	  if($result !== FALSE){
	    $rowan = $result->fetch_array(MYSQLI_ASSOC);
	    if($rowan['cid']==$this->channelId){
	      for($z=1; $z<=12; $z++){
		$zS = str_pad($z, 2, "0", STR_PAD_LEFT);
		$analyticsArray[$i][$zS] = json_decode($rowan[$zS], TRUE);
	      }
	    }
	    $result->close();
	  }
	}
      }
      foreach($analyticReport['rows'] as $reportDay){
	$dateRaw = explode('-', $reportDay[0]);
	$reportDate->day = $dateRaw[2];
	$reportDate->month = $dateRaw[1];
	$reportDate->year = $dateRaw[0];
	if($yearChange!=$reportDate->year){
	  $yearChange=$reportDate->year;
	  $result = $this->db_dash->query("CREATE TABLE IF NOT EXISTS `".$config->db_dash_database."`.`channel_analytics_".$reportDate->year."` (`cid` VARCHAR(60), `01` MEDIUMTEXT, `02` MEDIUMTEXT, `03` MEDIUMTEXT, `04` MEDIUMTEXT, `05` MEDIUMTEXT, `06` MEDIUMTEXT, `07` MEDIUMTEXT, `08` MEDIUMTEXT, `09` MEDIUMTEXT, `10` MEDIUMTEXT, `11` MEDIUMTEXT, `12` MEDIUMTEXT, INDEX (cid))") or die($this->db_dash->error);
	  //check ($result);
	}
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['views'] = $reportDay[1]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['comments'] = $reportDay[2]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['favoritesAdded'] = $reportDay[3]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['favoritesRemoved'] = $reportDay[4]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['likes'] = $reportDay[5]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['dislikes'] = $reportDay[6]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['shares'] = $reportDay[7]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['estimatedMinutesWatched'] = $reportDay[8]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['averageViewDuration'] = $reportDay[9]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['averageViewPercentage'] =  $reportDay[10]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['annotationClickThroughRate'] = $reportDay[11]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['annotationCloseRate'] = $reportDay[12]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['subscribersGained'] = $reportDay[13]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['subscribersLost'] = $reportDay[14]; 
	$analyticsArray[$reportDate->year][$reportDate->month][$reportDate->day]['uniques'] = $reportDay[15]; 
      }
      
      foreach($analyticsArray as $yearKey=>$monthsArray){
	foreach($monthsArray as $monthKey=>$daysArray){
	  $result = $this->db_dash->query("SELECT * FROM `".$config->db_dash_database."`.`channel_analytics_".$yearKey."` WHERE `cid`='".$this->channelId."'") or die($this->db_dash->error);
	  if($result){
	    $rowan = $result->fetch_array(MYSQLI_ASSOC);
	    if($rowan['cid']==$this->channelId){
	      $this->db_dash->query("UPDATE `".$config->db_dash_database."`.`channel_analytics_".$yearKey."`
                                     SET `".$monthKey."`='".json_encode($daysArray)."' WHERE cid='".$this->channelId."'") or die($this->db_dash->error);
	    }
	    else{
	      echo ('lol5');
	      $this->db_dash->query("INSERT INTO `".$config->db_dash_database."`.`channel_analytics_".$yearKey."` (`cid`,`".$monthKey."`)
                                     VALUES ('".$this->channelId."', '".json_encode($daysArray)."')") or die($this->db_dash->error);
	    }
	  }
	  else{
	    echo ('lol');
	    $this->db_dash->query("INSERT INTO `".$config->db_dash_database."`.`channel_analytics_".$yearKey."` (`cid`,`".$monthKey."`)
                                   VALUES ('".$this->channelId."', '".json_encode($daysArray)."')") or die($this->db_dash->error);
	  }
	}
      }
      echo '<pre>';
      echo(json_encode($analyticsArray));
      return true;
    }
    public function setChannelId($cid){
       $this->channelId = $cid;
    }
    public function getErrorCode(){
      return $this->errorCode;
    }
    public function close(){
      include(dirname(__FILE__).'/config.php');
      if($this->errorCode!="-1"){

	$this->db_dash->query("UPDATE `".$config->db_dash_database."`.`accesscodes` 
                         SET accesscode='".$this->google->getAccessToken()."'
                         WHERE cid='".$this->channelId."'");
	$this->db_dash->close();
      }
    }
  }
?>