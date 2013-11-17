<?php
ini_set('display_errors', 1);

$startup=false;

if ($_GET['data'] == "caca") {
  $startup=true;
} else {
  $height = base_convert("0x{$_GET['data']}", 16, 10);
  $height = round(-15.04*log($height)+84.54);
}
$signal = $_GET['avgSignal'];
$time = date("H:i:s", $_GET['time']+3600);

require('Pusher.php');

$app_id = '*****';
$app_key = '*******';
$app_secret = '*********';

if (!$startup) {
  $pusher = new Pusher( $app_key, $app_secret, $app_id );
  $pusher->trigger( 'handyquest', 'lift', array('height'=>$height, 'time'=>$time) );
}

require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "************",
    'oauth_access_token_secret' => "***********",
    'consumer_key' => "**********",
    'consumer_secret' => "************"
);

/** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ **/
$url = 'https://api.twitter.com/1.1/statuses/update.json';
$requestMethod = 'POST';

/** POST fields required by the URL above. See relevant docs as above **/
if ($startup) {
  $postfields = array(
    'status' => 'Cendrillon vient d\'allumer ses chaussures magiques... (#sigfox '.$signal.'dB / '.$time.') #hackcess'
  );
} else {
  $postfields = array(
    'status' => 'Oups : un obstacle de '.$height.'cm ! (#sigfox '.$signal.'dB / '.$time.') #hackcess'
  );
}


/** Perform a POST request and echo the response **/
$twitter = new TwitterAPIExchange($settings);
echo $twitter->buildOauth($url, $requestMethod)
             ->setPostfields($postfields)
             ->performRequest();