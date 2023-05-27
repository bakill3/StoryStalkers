<?php

include 'header.php';
if (!isset($_SESSION['login'])) {
	header('Location: index.php');
}

/////// CONFIG ///////
$username = $_SESSION['login'][0]; //VAI BUSCAR AO INDEX.PHP (LOGIN) 
$password = $_SESSION['login'][1]; //VAI BUSCAR AO INDEX.PHP (LOGIN)
$debug = false;
$truncatedDebug = false;
//////////////////////



$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
  $ig->login($username, $password); //LOGIN
} catch (\Exception $e) {
  echo 'Something went wrong: '.$e->getMessage()."\n";
  exit(0);
}


//echo "<br><br><br>--------------------------------------------------------------coninha--------------------------------------------------------------<br><br><br>";


try {

  $busca_archive_day = json_decode($ig->story->getArchivedStoriesFeed()); //VAI BUSCAR TODOS AS HISTORIAS DO ARQUIVO

  $archive_day = $busca_archive_day->items[0]->id; //VAI BUSCAR O ARCHIVE DAY AO OUTPUT DE JSON

  //$user_id = $ig->people->getUserIdForName($username); SERVE PARA IR BUSCAR O ID DO USER, NÃO NECESSÁRIO POR AGORA

  $feedList = [$archive_day];

  $mediaPk = $ig->story->getReelsMediaFeed($feedList)->getReels()->getData()[$archive_day]->getItems()[0]->getPk(); //PK DA HISTÓRIA


  $json = json_decode($ig->story->getStoryItemViewers($mediaPk));
  //echo "<pre>"; //ASSIM FICA BONITO EM VEZ DAQUELA CONFUSÃO TODA |||||| TIRA ESTE COMMENT SE METERES EM DEBUG MODE
  //print_r($json); //ASSIM FICA BONITO EM VEZ DAQUELA CONFUSÃO TODA |||||| TIRA ESTE COMMENT SE METERES EM DEBUG MODE

  $n_vistas = $json->total_viewer_count; //VAI BUSCAR O Nº DE VISTAS AO OUTPUT DA FUNÇÃO getStoryItemViewers() - PROCEDIMENTO IGUAL AO DO $busca_archive_day->items[0]->id; (LINHA 31)

  //echo "<br><br>-------------------------------------------------<br><br>";
  $id_user = $ig->people->getUserIdForName($username);
  $buscar_historia_foto = json_decode($ig->story->getUserReelMediaFeed($id_user));
  $story_foto = $buscar_historia_foto->items[0]->image_versions2->candidates[0]->url;

  echo "<div style='height: 95%;'>";
  echo "<div class='card' style='width: 50%; display: inline-block; height: 100%; border-color: #343a40; border-radius: 0px;'>
  <div class='card-header text-center' style='background: #343a40; border-radius: 0px;'>
    <h1 class='font-weight-bold Neue-bold text-white'>História</h1>
  </div>
  <div class='card-body text-center' style='/* background: linear-gradient(to right, #405DE6, #5851DB, #833AB4, #C13584, #E1306C); height: 91%; */ background: #343a40;''>
  <img src='$story_foto' style='width: 86%;'>
  </div>
  </div>";

  //echo "<pre><h1 class='font-weight-bold'>História<h1><br>";
  //echo "<img src='$story_foto' style='width: 432px; height: 768px;'><br>";
  //echo "<h1>Vistas na história:</h1><br>";



  echo '<div class="card" style="width: 50%; vertical-align: top; height: 100%; display: inline-block; height: 100%; border-color: #343a40; border-radius: 0px;">
  <div class="card-header text-center" style="background: #343a40; border-radius: 0px;">
    <h1 class="font-weight-bold Neue-bold text-white">Vistas na história</h1>
  </div>
  <div class="card-body" style="/* background: linear-gradient(to right, #FD1D1D, #F56040, #F77737, #FCAF45, #FFDC80);*/ background: #343a40; height: 91%; overflow-y: scroll;">
  <ul class="list-group">';
  for ($i=0; $i < $n_vistas; $i++) { //VAI BUSCAR TODOS OS USERNAMES DENTRO DA FUNÇÃO USERS (QUE VEM DO OUTRPUT EM JSON)

    //INFO DO USER
    $username_ig = $json->users[$i]->username; //USERNAME DO VIEWER
    $nome_ig = $json->users[$i]->full_name; //NOME DO VIEWER
    $foto_perfil = $json->users[$i]->profile_pic_url; //FOTO DE PERFIL DO VIEWER

    //echo "<img src='$foto_perfil' class='rounded-circle' style='width: 50px; height: 50px;'><br>";
    //echo "<p>".$nome_ig." (@".$username_ig.")</p><br>";

    echo '
    <a href="https://www.instagram.com/'.$username_ig.'" target="_blank" style=" color: inherit;  text-decoration: inherit;">
    <li style="cursor: pointer;" class="list-group-item visitar_perfil">
      <img src="'.$foto_perfil.'" style="width: 14%; border-radius: 50%; display: inline-block;">
      <span class="mb-1" style=" margin-left: 1.5%; font-size: 16px;"><b>'.$nome_ig.'</b> (@'.$username_ig.')</p><br></span></li></a>
  ';

  }
  echo '<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
				<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
				<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>';
  echo "</ul></div>
</div></div>";
  echo "<a href='logout.php' class='btn btn-danger btn-lg' style='width: 100%;'>Logout</a>";


} catch (\Exception $e) {
  echo 'Something went wrong: '.$e->getMessage()."\n";
}