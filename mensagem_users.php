<?php
include 'ligar_db.php';
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
date_default_timezone_set('Europe/Lisbon');


$query = mysqli_query($link, "SELECT * FROM users");
while ($info_users = mysqli_fetch_array($query)) {
  $id_user = $info_users['id'];
  $username_user = $info_users['username'];
  $username = 'story.stalkers';
  $password = 'gabriel124';


  $query2 = mysqli_query($link, "SELECT * FROM historias WHERE historias.data >= DATE_SUB(now(), INTERVAL 48 HOUR) AND historias.done='1' AND historias.id_user='$id_user' AND historias.sent='0' ORDER BY data DESC");
  if (mysqli_num_rows($query2) > 0) {
    $debug = false;
    $truncatedDebug = false;
    $n_historias = mysqli_num_rows($query2);
    if ($n_historias > 0) {
      while ($info = mysqli_fetch_array($query2)) {
        $id_historia = $info['id_historia'];
        mysqli_query($link, "UPDATE historias SET sent='1'");
      }
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


    }
  }
  
}

