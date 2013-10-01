<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

include ('ytfunctions.php');

$ytclient = new ytClient;

if(isset($_GET['code'])){
  $ytclient->auth_code = $_GET['code'];
  $ytclient->exchangeToken();
}else{
  $ytclient->authorize();
}
/*
$ytclient->refreshToken();

$refresh_token = "1/Sa_hepDe1FEeDa1V-U0uPplXikc-Wc_ciAqozYiVQ2g";
$oauth2token_url = "https://accounts.google.com/o/oauth2/token";
$clienttoken_post = array(
			  "client_id" => '747090784-ih17uoruc6697mua8uoq9qhqip01bani.apps.googleusercontent.com',
			  "client_secret" => 'wGWj24931mNGwQP2roFeUAS9');

$clienttoken_post["refresh_token"] = $refresh_token;
$clienttoken_post["grant_type"] = "refresh_token";

$curl = curl_init($oauth2token_url);

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttoken_post);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$json_response = curl_exec($curl);
curl_close($curl);

$authObj = json_decode($json_response);

print_r($authObj);


exit();
  */
?>