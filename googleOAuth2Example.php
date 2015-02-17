<?php
require_once 'googleOAuth2Login.php';//class that handles interface to Google OAuth2 API
session_start();
if (empty($_SESSION['count'])) {//counter that shows how may times this session has been accessed
  $_SESSION['count'] = 1;
} else {
  $_SESSION['count']++;
}
$request = "";//build array of requests to show at bottom of page
if (isset($_REQUEST)) {
  $arr = $_REQUEST;
  foreach ($arr as $key => $value) {
    $request .= "$key : $value<br>";
  }
}
$googleOAuth2 = new Login();//instantiate new connection to google 
$googleOAuth2->openClient();//set api information for this program/connection
if (isset($_GET['code'])) {//upon successful login ?code is returned to this program
  $googleOAuth2->getCode();//use code to retrieve access token information
  header( 'Location: googleOAuth2Example.php');//return to original page, get rid of ?code...
}
$json = '';
$error = '';
$found = false;
if (isset($_REQUEST['command'])) {//all buttons on page send ?command=<command>
  $command = $_REQUEST['command'];
  switch ($command) {
    case 'getApiInfo':
      $json = $googleOAuth2->getApiInfo();
      $found = true;
      break;
    case 'getAuthUrl':
      $json = $googleOAuth2->getAuthUrl();
      $found = true;
      break;
    case 'getGClink':
      $json = '"'.$googleOAuth2->getGClink().'"';
      $found = true;
      break;
    case 'getAccessToken':
      if(isset($_SESSION['token'])) {
        $json = $googleOAuth2->getAccessToken();
      } else {
        $error = "Can't get token unless signed on"; 
      }
      $found = true;
      break;
    case 'getScopes':
      $json = $googleOAuth2->getScopes();
      $found = true;
      break;
    case 'getUser':
      $json = $googleOAuth2->getUser();
      $found = true;
      break;
    case 'logout':
      $json = $googleOAuth2->logout();
      $found = true;
      header( 'Location: googleOAuth2Example.php');//return to original page, get rid of ?command=logout
      break;
    default:
      $error = "Command '$command' not recognized";
  }
}
$refreshData = $googleOAuth2->refreshToken();//tests status of this connection. refreshData is shown in the Status area at top of page
$sessionArray = "";//build array of session variable to display at bottom of page
if (isset($_SESSION)) {
  $arr = $_SESSION;
  foreach ($arr as $key => $value) {
    if ($key != 'token') {
      $sessionArray = $sessionArray."$key : $value<br>";
    }
  }
}
$return = false;//build json data to return to client side
$string = '{"session":"'.$sessionArray.'"';
if ($request > '') {
  $string .= ', "request":"'.$request.'"';
}
if ($refreshData > '') {
  $string .= ', "message":"'.$refreshData.'"';
}
if ($error > '') {
  $string .= ', "error":"'.$error.'"';
  $return = true;
}
if ($json > '') {
  $string .= ', "payload":'.$json;
  $return = true;
}
$string .= '}';
if ($return) {
  echo $string;
  return;//if there is data, escape now, otherwise send the html (initial page load and page reload)
}
?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <title>Google OAuth2 Visualization</title>
    <meta charset="utf-8">
    <script src="googleOAuth2Connection.js" type="text/javascript"></script><!--handles XMLHttpRequest() for communcation withgoogleOAuth2Example.php--> 
    <script src="googleOAuth2.js" type="text/javascript"></script><!--handles client side functions-->
    <link rel="stylesheet" href="system.css" /><!-- contains lots of junk, not pertinent to this example --> 
  </head>
  <body>
    <h2>Register Application</h2>
    <h3>Status</h3>
    <div id="status" class="box">
      <?php
        print $refreshData;
      ?>
    </div>
    <h3>Google API Console</h3>
    <form id="register" method="GET" action="">  
      <input type="button" value="Get Registration Information" name="get" id="get" onmouseup="Login.getApiInfo()" class="fireballed" />  
    </form>
    <br />
    <div id="apiInfo"></div>
    <h2>Google_Client()</h2>
    <h3>https://accounts.google.com/o/oauth2/auth</h3>
    <h4>createAuthUrl()</h4>
    <form id="authUrl" method="POST" action="">  
      <input type="button" value="Get Authorization URL" onmouseup="Login.getAuthUrl()" class="fireballed" />
      <br /><br />
      <div id="authorization" class="box"></div>
      <br />
      <input type="button" value="Get Google_Client URL" onmouseup="Login.getGClink()" class="fireballed" />
      </div>
    </form>
    <br />
    <div id="link">
      <?php
        $url = $googleOAuth2->getGClink();
        print $url;
      ?>
    </div>
    <h3>Authorization Code</h3>
    <div id="code" class="box">
    <?php
      if(isset($_SESSION["code"])) {
        $code = $_SESSION['code'];
      	print $code;
      }
    ?>
    </div>
    <h3>https://accounts.google.com/o/oauth2/token</h3>
    <h4>authenticate(code)</h4>
    <h3>Receive Token</h3>
    <form id="accessTokenForm" method="POST" action="">  
      <input type="button" value="Get Access Token" onmouseup="Login.getAccessToken();" class="fireballed" />  
    </form>
    <br />
    <div id="accessToken"></div>
    <h2>Scope</h2>
    <form id="token" method="POST" action="">  
      <input type="button" value="Get Scope" onmouseup="Login.getScopes();" class="fireballed" />  
    </form>
    <br />
    <div id="scopes"></div>
    <h2>User Information</h2>
    <form id="token" method="POST" action="">  
      <input type="button" value="Get User Info" onmouseup="Login.getUser();" class="fireballed" />  
    </form>
    <br />
    <div id="user" class="box"></div>
    <h2>System</h2>
    <h3>$_SESSION</h3>
    <div id="session" class="box">
      <?php
        print $sessionArray;
      ?>
    </div>
    <h3>$_REQUEST</h3>
    <div id="request" class="box">
    <?php
      print $request;?>
    </div>
    <script type="text/javascript">
      Login = new Login() 
    </script>
  </body>
</html>
