<?php
//set_time_limit(0);
//date_default_timezone_set('UTC');
//require __DIR__.'/vendor/autoload.php';
include 'header.php';
/////// CONFIG ///////
$username = $_SESSION['login'][0];
$password = $_SESSION['login'][1];
$debug = false;
$truncatedDebug = false;
//////////////////////
\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}
try {
	$userId = $ig->people->getUserIdForName('gabriel.brandao2000');
	echo $userId;
  $id_user = $ig->people->getUserIdForName($username);
  $buscar_historia_foto = $ig->story->getUserReelMediaFeed($id_user);
  $story_foto = $buscar_historia_foto->getItems()[0]->getImage_versions2()->getCandidates()[0]->getUrl();

  $busca_archive_day = $ig->story->getArchivedStoriesFeed(); //VAI BUSCAR TODOS AS HISTORIAS DO ARQUIVO
  $archive_day = $busca_archive_day->getItems()[0]->getId(); //VAI BUSCAR O ARCHIVE DAY AO OUTPUT DE JSON
  $feedList = [$archive_day];
  $mediaPk = $ig->story->getReelsMediaFeed($feedList)->getReels()->getData()[$archive_day]->getItems()[0]->getPk(); //PK DA HISTÓRIA

  echo "<div style='height: 95%;'>";
  echo "<div class='card' style='width: 50%; display: inline-block; height: 100%; border-color: #343a40; border-radius: 0px;'>
  <div class='card-header text-center' style='background: #343a40; border-radius: 0px;'>
  <h1 class='font-weight-bold Neue-bold text-white'>História</h1>
  </div>
  <div class='card-body text-center' style='/* background: linear-gradient(to right, #405DE6, #5851DB, #833AB4, #C13584, #E1306C); height: 91%; */ background: #343a40;''>
  <img src='$story_foto' style='width: 86%;'>
  </div>
  </div>";

  echo '<div class="card" style="width: 50%; vertical-align: top; height: 100%; display: inline-block; height: 100%; border-color: #343a40; border-radius: 0px;">
  <div class="card-header text-center" style="background: #343a40; border-radius: 0px;">
  <h1 class="font-weight-bold Neue-bold text-white">Vistas na história</h1>
  </div>
  <div class="card-body" style="/* background: linear-gradient(to right, #FD1D1D, #F56040, #F77737, #FCAF45, #FFDC80);*/ background: #343a40; height: 91%; overflow-y: scroll;">
  <ul class="list-group">';
  
  $maxId = null;
  do {
    $busca_views = $ig->story->getStoryItemViewers($mediaPk, $maxId);
    $n_vistas = $busca_views->getTotal_viewer_count();
    echo '
          <li style="cursor: pointer;" class="list-group-item visitar_perfil text-right"><h5><i class="far fa-eye"></i> '.$n_vistas.'</h5></li>
    ';
        // Request the page corresponding to maxId.
        //$response = $ig->timeline->getUserFeed($userId, $maxId);
        // In this example we're simply printing the IDs of this page's items.
    	echo "<pre>";
        	print_r(json_decode($busca_views));
        	echo "</pre>";
        foreach ($busca_views->getUsers() as $json) {
        	echo "<pre>";
        	print_r(json_decode($json));
        	echo "</pre>";
        	echo $json->getPk();
            //printf("[%s] https://instagram.com/p/%s/\n", $item->getId(), $item->getCode());
            $username_ig = $json->getUsername();
        $nome_ig = $json->getFull_name();
        $foto_perfil = $json->getProfile_pic_url();

        echo '
          <a href="https://www.instagram.com/'.$username_ig.'" target="_blank" style=" color: inherit;  text-decoration: inherit;">
          <li style="cursor: pointer;" class="list-group-item visitar_perfil">
            <img src="'.$foto_perfil.'" style="width: 14%; border-radius: 50%; display: inline-block;">
            <span class="mb-1" style=" margin-left: 1.5%; font-size: 16px;"><b>'.$nome_ig.'</b> (@'.$username_ig.')</p><br></span></li></a>
        ';
        }
        $maxId = $busca_views->getNextMaxId();

        sleep(5);
    } while ($maxId !== null);
  echo '<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
  <li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
  <li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>';
  echo "</ul></div>
  </div></div>";

} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}