<?php
function logged_in(){
  if(isset($_SESSION['n4guser'],$_SESSION['n4gxhash'])){
    if($_SESSION['n4glogmode'] == 1){
      $expireTime = time()+86400;
      setcookie('n4guser',$_SESSION['n4guser'],$expireTime);
      setcookie('n4gxhash',$_SESSION['n4gxhash'],$expireTime);
    }
    return "1";
  }
  elseif(isset($_COOKIE['n4guser'],$_COOKIE['n4gxhash'])){
    $_SESSION['n4guser'] = $_COOKIE['n4guser'];
    $_SESSION['n4gxhash'] = $_COOKIE['n4gxhash'];
    $_SESSION['n4glogmode'] = 1;
    return "1";
  }
  else{ return "0"; }
}
function login($username, $password, $mode){
  include(dirname(__FILE__).'/config.php');
  $resultraw = file_get_contents_curl($config->xendomain."/api.php?action=authenticate&username=".$username."&password=".$password,true);
  $expireTime = time()+86400;
  $result = explode('"', $resultraw);
  if($result[1]!="error"){
    $_SESSION['n4guser'] = $username;
    $_SESSION['n4gxhash'] = $result[3];
    $_SESSION['n4glogmode'] = 0;
    if($mode==1){
      $_SESSION['n4glogmode'] = 1;
      setcookie('n4guser',$username,$expireTime);
      setcookie('n4gxhash',$result[3],$expireTime);
    }
    return "1";
  } else { return "0"; }
}
function getAvatar($username, $hash){
  include(dirname(__FILE__).'/config.php');
  $resultraw = file_get_contents_curl($config->xendomain."/api.php?action=getAvatar&size=M&hash=".$username.":".$hash,true);
  $result = explode('"', $resultraw);
  if($result[1]!="error"){ return $result[3]; } else { return "0"; }
}
function getInfo($username, $hash){
  include(dirname(__FILE__).'/config.php');
  $resultraw = file_get_contents_curl($config->xendomain."/api.php?action=getUser&hash=".$username.":".$hash,true);
  $userInfo = json_decode($resultraw);
  return $userInfo;
}
function setPassword($username, $password, $newpassword){
  include(dirname(__FILE__).'/config.php');
  if($password==$newpassword){ return "-2"; }
  $resultraw = file_get_contents_curl($config->xendomain."/api.php?action=authenticate&username=".$username."&password=".$password,true);
  $result = explode('"', $resultraw);
  if($result[1]!="error"){
    $presultraw = file_get_contents_curl($config->xendomain."/api.php?action=editUser&user=".$username."&password=".$newpassword."&hash=nicklovesdonkeycock",true);
    $presult = explode('"', $presultraw);
    if($result[1]!="error"){ return "1"; } else { return "0"; }
  } else { return "-1"; }
}
function file_get_contents_curl($url,$json=false){
  $ch = curl_init();
  $headers = array();
  if($json) {
    $headers[] = 'Content-type: application/json';
    $headers[] = 'X-HTTP-Method-Override: GET';
  }
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)');

  $response = curl_exec($ch);
  curl_close($ch);
  if($response === false) {
    return false;
  } else {
    return $response;
  }
}
?>