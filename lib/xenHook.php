<?php
  
  //ini_set('display_errors',1); 
  //error_reporting(E_ALL);
  
  // Specify domains from which requests are allowed
header('Access-Control-Allow-Origin: *');

// Specify which request methods are allowed
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

// Additional headers which may be sent along with the CORS request
// The X-Requested-With header allows jQuery requests to go through
header('Access-Control-Allow-Headers: X-Requested-With');

// Set the age to 1 day to improve speed/caching.
header('Access-Control-Max-Age: 86400');

//error_reporting(E_ERROR | E_PARSE);
include('functions.php');
session_start();
switch($_SERVER['REQUEST_METHOD'])
  {
  case 'GET': $_IO = &$_GET; break;
  case 'POST': $_IO = &$_POST; break;
  }

if($_IO['action']=='login'){
  $loginResult = login($_IO['username'],$_IO['password'],$_IO['m']);
  //if($loginResult==true){echo 'Success';} else { echo 'error(1)'; }
  echo $loginResult;
}

?>