<?php
require_once 'vendor/autoload.php';
 
// init configuration
$clientID = '575445888811-kb6u8ugtu2s9a4115me8hpl4cg95o3k2.apps.googleusercontent.com';
$clientSecret = 'gkbVBfWHHxm_aKfkMlNjrJyt';
$redirectUri = 'http://localhost/TestGen/index.php';
  
//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId($clientID);

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret($clientSecret);

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri($redirectUri);

//
$google_client->addScope('email');

//$google_client->addScope('profile');

//start session on web page
session_start();
?>