<?php
/*
include 'header.php';

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';
\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

$query = mysqli_query($link, "SELECT * FROM users LIMIT 30");
while ($info = mysqli_fetch_array($query)) {
    $id_user = $info['id'];
    if ($id_user == "4" || $id_user == "7" || $id_user == "8" || $id_user == "9" || $id_user == "10" || $id_user == "42") {

    } else {
    /////// CONFIG ///////
        $username = $info['username'];
        $pass_db = $info['password'];
        $method = "AES-256-CBC";  
        $secretHash = "camachoeummerdas.com";
        $password = openssl_decrypt($pass_db, $method, $secretHash);

        $debug = true;
        $truncatedDebug = false;
    //////////////////////
        $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
        try {
           $ig->login($username, $password);
           $userId = $ig->people->getUserIdForName($username);
           $extraData = ['username' => $username, 'user_id' => $userId];
           $ig->media->like('2036494069204328046', 'photo_view_profile', $extraData);
       } catch (\Exception $e) {
           echo 'Something went wrong: '.$e->getMessage()."\n";
           exit(0);
       }
   }
}

include 'ligar_db.php';
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
date_default_timezone_set('Europe/Lisbon');
//$query2 = mysqli_query($link, "SELECT * FROM historias INNER JOIN users ON historias.id_user = users.id WHERE historias.data >= DATE_SUB(now(), INTERVAL 24 HOUR) AND historias.done='1' ORDER BY data DESC");

$query = mysqli_query($link, "SELECT * FROM users");
while ($info_users = mysqli_fetch_array($query)) {
  $id_user = $info_users['id'];
  $username_user = $info_users['username'];
  $username = 'story.stalkers';
  $password = 'gabriel124';


  $query2 = mysqli_query($link, "SELECT * FROM historias WHERE historias.data >= DATE_SUB(now(), INTERVAL 24 HOUR) AND historias.done='1' AND historias.id_user='$id_user' ORDER BY data DESC");
  if (mysqli_num_rows($query2) > 0) {
    $debug = false;
    $truncatedDebug = false;
    $n_historias = mysqli_num_rows($query2);
    
    try {
      $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
      $ig->login($username, $password);
      $userIds2 = array();
      $id_user_ig = $ig->people->getUserIdForName($username_user);
      $buscar_historia_foto = $ig->story->getUserReelMediaFeed($id_user_ig);
      $user_pk = $buscar_historia_foto->getUser()->getPk();
      $userIds2[0] = $user_pk;
      $x++;
      $recipients = 
      [
        'users' => $userIds2 // must be an [array] of valid UserPK IDs
      ];
      $text = "Olá $username_user 😁!\nJá guardamos os teus visualizadores 👀! Hoje guardamos ".$n_historias." historias tuas 🎉.\n Podes consulta-las na app ou no website ( https://instaviews.socialsivex.com/ ).\n\n PS: Esta mensagem é automatizada \n\n Cumprimentos, Story Stalkers \n ";
      $ig->direct->sendText($recipients, $text);
    } catch (\Exception $e) {
      echo 'Something went wrong: '.$e->getMessage()."\n";
    }


    
    while ($info = mysqli_fetch_array($query2)) {
      $data = $info['data'];

      $foto_historia = $info['foto_historia'];
      echo "<h1>$username</h1>";
      echo "<img src='$foto_historia' style='width: 250px; height: 150px;'>";
      echo "<h2>Data: $data</h2>";
    }
    
  }
  
}
*/



/*
include 'ligar_db.php';

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
date_default_timezone_set('Europe/Lisbon');

$username = 'gabriel.brandao2000';
$password = 'rikoku11';
$debug = false;
$truncatedDebug = false;
//////////////////////
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);


try {
  $ig->login($username, $password);
  $id_user = $ig->people->getUserIdForName($username);
  $response = $ig->people->getInfoById($id_user);
  $response2 = $ig->timeline->getUserFeed($id_user);
  echo "<pre>";
  print_r(json_decode($response2));
  echo "</pre>";
} catch (\Exception $e) {
  echo 'Something went wrong: '.$e->getMessage()."\n";
  exit(0);
}
*/




include 'ligar_db.php';

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
date_default_timezone_set('Europe/Lisbon');

$username = 'gabriel.brandao2000';
$password = 'rikoku11';
$debug = false;
$truncatedDebug = false;
//////////////////////
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

$ig->login($username, $password);

$query = mysqli_query($link, "SELECT * FROM viewers WHERE id_viewer<500");
while ($info = mysqli_fetch_array($query)) {
  $id_viewer = $info['id_viewer'];
  $foto_viewer = $info['foto_viewer'];
  $username_viewer = $info['username_viewer'];

  $handle = curl_init($foto_viewer);
  curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);


  $response = curl_exec($handle);


  $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
  if($httpCode == 404) {

    try {
      $id_user = $ig->people->getUserIdForName($username_viewer);
      $response = $ig->people->getInfoById($id_user);
      $foto = $response->getUser()->getProfile_pic_url();
      mysqli_query($link, "UPDATE viewers SET foto_viewer='$foto' WHERE id_viewer='$id_viewer'");
      echo "NOPE $foto <br>";
    } catch (\Exception $e) {
      echo 'Something went wrong: '.$e->getMessage()."\n";
    //exit(0);
    }

  }

  curl_close($handle);
}



/*

include 'ligar_db.php';

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
date_default_timezone_set('Europe/Lisbon');

$username = 'gabriel.brandao2000';
$password = 'rikoku11';
$debug = false;
$truncatedDebug = false;
//////////////////////
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

$ig->login($username, $password);

$query = mysqli_query($link, "SELECT * FROM viewers WHERE foto_viewer='https://instagram.flis3-1.fna.fbcdn.net/vp/378139aea3a8d1502b69eb86d4e26426/5D95182B/t51.2885-19/s150x150/52744863_847078605633215_6922247693460832256_n.jpg?_nc_ht=instagram.flis3-1.fna.fbcdn.net'");
while ($info = mysqli_fetch_array($query)) {
  $id_viewer = $info['id_viewer'];
  $foto_viewer = $info['foto_viewer'];
  $username_viewer = $info['username_viewer'];

  try {
    $id_user = $ig->people->getUserIdForName($username_viewer);
    $response = $ig->people->getInfoById($id_user);
    $foto = $response->getUser()->getProfile_pic_url();
    mysqli_query($link, "UPDATE viewers SET foto_viewer='$foto' WHERE id_viewer='$id_viewer'");
    echo "NOPE $foto <br>";
  } catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    //exit(0);
  }


}

*/

/*
try {
  $ig->login($username, $password);
  $id_user = $ig->people->getUserIdForName($username);
  $response = $ig->people->getInfoById($id_user);
  $foto = $response->getUser()->getProfile_pic_url();
  echo $foto;
} catch (\Exception $e) {
  echo 'Something went wrong: '.$e->getMessage()."\n";
  exit(0);
}

*/

/* Handle $response here. */

/*
set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/vendor/autoload.php';
\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
/////// CONFIG ///////
$username = 'landscapes.worldwidel';
$password = 'gabriel124';
$debug = false;
$truncatedDebug = false;
//////////////////////
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
  $ig->login($username, $password);
  $id_user = $ig->people->getUserIdForName($username);
  $response = $ig->people->getInfoById($id_user);
  echo "<pre>";
  print_r(json_decode($response));
  echo "</pre>";
} catch (\Exception $e) {
  echo 'Something went wrong: '.$e->getMessage()."\n";
  exit(0);
}
*/

