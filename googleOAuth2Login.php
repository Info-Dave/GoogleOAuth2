<?php
require_once '../google-api-php-client/src/Google_Client.php';
require_once '../google-api-php-client/src/contrib/Google_Oauth2Service.php';
class Login {
  // from Google API Console
  private $clientID = "44961071359-2qff2rk626sqtt6ic0ogs34v09fjhb7n.apps.googleusercontent.com";
  private $clientSecret = "DT1BYSV_m7o6S1MzZ7fQ_QiD";
  private $redirectURI = "http://info-tran.com/googleOAuth2Example.php";
  private $authUrl = "";
  public $client = "";
  public $oauth2 = ""; 
  function openClient() {
    $this->client = new Google_Client();//new client connection
    $this->client->setClientId($this->clientID);
    $this->client->setClientSecret($this->clientSecret);
    $this->client->setRedirectUri($this->redirectURI);
    $this->client->setApplicationName("Google OAuth2 Visualization");
    $this->oauth2 = new Google_Oauth2Service($this->client);//connection to user info
    $this->authUrl = $this->client->createAuthUrl();
    $this->client->setScopes(array('https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email'));
  }
  function getApiInfo() {
    $arr = array(
      "clientID" => $this->clientID,
      "clientSecret" => $this->clientSecret,
      "redirectURI" => rawurlencode($this->redirectURI)
    );
    $apiInfo = json_encode($arr);
    return $apiInfo;
  }
  function getAuthUrl() {
    $this->client->setApprovalPrompt("auto");
    $authUrl = $this->authUrl;
    $authUrl = rawurlencode($authUrl);
    $json = '{"authUrl":"'.$authUrl.'"}';
    return $json;
  }
  function getGClink() {
    if(isset($_SESSION["token"])) {
      $link =  "<a class='logout' href='?command=logout'>Logout</a>";
    } else {
      $url = $this->authUrl;
      $link =  "<a class='login' href='$url'>Sign In</a>";
    }
    return $link;
  }
  function getCode() {
    $_SESSION['code'] = $_GET['code'];
    $accessToken = $this->client->authenticate($_GET['code']);
    if ($accessToken != 1) {
      $this->client->setAccessToken($accessToken);
      $_SESSION['token'] = $accessToken;
      $json = json_decode($_SESSION['token']);
      $_SESSION['refresh_token'] = $json->refresh_token;
      $_SESSION['id_token'] = $json->id_token;
      $_SESSION['created'] = $json->created;
      $_SESSION['updated'] = $json->created;
      $_SESSION['now'] = time();
      $_SESSION['expires_in'] = $json->expires_in;
      $expires =  $json->created + $json->expires_in;
      $_SESSION['expires'] = $expires;
      $user = $this->oauth2->userinfo->get();
      $_SESSION['name'] = $user['name'];
      $_SESSION['img_url'] = $user['picture'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['user_id'] = $user['id'];
    }
  }
  function getScopes() {
    $x = json_encode($this->client->getScopes());
    $json = '{"scopes":'.$x.'}';
    return $json;
  }
  function getUser() {
    $user = array (
      'name' => $_SESSION['name'],
      'img_url' => rawurlencode($_SESSION['img_url']),
      'email' => $_SESSION['email'],
    );
    $json = json_encode($user);
    $name = $_SESSION['name'];
    $imgUrl = $_SESSION['img_url'];
    $email = $_SESSION['email'];
    return $json;
  }
  function getAccessToken() {
    $token = '';
    if(isset($_SESSION['token'])) {
      $token = $_SESSION['token'];
    }
    return $token;
  }
  function refreshToken() {
    $timeData = 'Not Signed In';
    if(isset($_SESSION["id_token"])) {//if there is no id_token, user is not signed on
      $timeData = 'Signed In';
      if($_SESSION['expires'] < time()) {//when token expires, we need to refresh
        $timeData = 'Google Token Refreshed';
        $rt = $_SESSION['refresh_token'];
        $this->client->refreshToken($rt);
        $token = $this->client->getAccessToken();//get updated token info
        $json = json_decode($token);
        $_SESSION['access_token'] = $json->access_token;
        $_SESSION['updated'] = $json->created;
        $_SESSION['expires_in'] = $json->expires_in;
        $expires =  $json->created + $json->expires_in;
        $_SESSION['expires'] = $expires;
      }
      $dateTimeZone = timezone_open( 'America/Detroit' );
      $now = time();
      $_SESSION['now'] = $now;
      if (isset($_SESSION['created'])) {
        $created = $_SESSION['created'];
        $timeCreated = new DateTime("@$created");
        date_timezone_set( $timeCreated, $dateTimeZone );
        $updated = $_SESSION['updated'];
        $timeUpdated = new DateTime("@$updated");
        date_timezone_set( $timeUpdated, $dateTimeZone );
        $expires_in = $_SESSION['expires_in'];
        $time = $created + $expires_in;
        $expires = $_SESSION['expires'];
        $timeNow = new DateTime("@$now");
        date_timezone_set( $timeNow, $dateTimeZone );
        $timeExpires = new DateTime("@$expires");
        date_timezone_set( $timeExpires, $dateTimeZone );
        $timeData .= '<br />Created: '.$timeCreated->format('Y-m-d H:i:s');
        $timeData .= '<br />Updated: '.$timeUpdated->format('Y-m-d H:i:s');
        $timeData .= '<br />Expires: '.$timeExpires->format('Y-m-d H:i:s');
        $timeData .= '<br />Now: '.$timeNow->format('Y-m-d H:i:s');
        $timeData .= '<br />[times are for timezone America/Detroit]';
      }
    }
    return $timeData;
  }
  function logout() {
    unset($_SESSION['token']);
    unset($_SESSION['code']);
    unset($_SESSION['id_token']);
    unset($_SESSION['created']);
    unset($_SESSION['now']);
    unset($_SESSION['expires_in']);
    unset($_SESSION['expires']);
    unset($_SESSION['updated']);
    unset($_SESSION['scopes']);
    unset($_SESSION['refresh_token']);
    unset($_SESSION['access_token']);
    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    unset($_SESSION['img_url']);
    $this->client->revokeToken();
  }
}
?>
