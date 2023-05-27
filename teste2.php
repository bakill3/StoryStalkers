<?php
use InstagramAPI\Utils;
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';
/////// CONFIG ///////
$username = 'landscapes.worldwidel';
$password = 'gabriel124';
$debug = true;
$truncatedDebug = false;
//////////////////////
/////// MEDIA ID ////////
$mediaId = '2035603012168578099'; // Only Media PK for this scenario.
//////////////////////
\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}
// NOTE: This code will make the credits of the media area 'clickable', but YOU need to
// manually draw the credit to the user or a sticker-image on top of your image yourself
// before uploading, if you want the credit to actually be visible on-screen!
// If we want to attach a media, we must find a valid media_id first.
try {
    $mediaInfo = $ig->media->getInfo($mediaId)->getItems()[0];
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}
// Now create the metadata array:
$metadata = [
    'attached_media' => [
        [
            'media_id'         => $mediaId,
            'x'                => 0.5, // Range: 0.0 - 1.0. Note that x = 0.5 and y = 0.5 is center of screen.
            'y'                => 0.5, // Also note that X/Y is setting the position of the CENTER of the clickable area.
            'width'            => 0.1, // Clickable area size, as percentage of image size: 0.0 - 1.0
            'height'           => 0.01, // ...
            'rotation'         => 0.0,
            'is_sticker'       => true, // Don't change this value.
        ],
    ],
];
$client = new GuzzleHttp\Client();
$outputFile = Utils::createTempFile(sys_get_temp_dir(), 'IMG');
try {
    $response = $client->request('GET', $mediaInfo->getImageVersions2()->getCandidates()[0]->getUrl(), ['sink' => $outputFile]);
    // This example will upload the image via our automatic photo processing
    // class. It will ensure that the story file matches the ~9:16 (portrait)
    // aspect ratio needed by Instagram stories.
    $photo = new \InstagramAPI\Media\Photo\InstagramPhoto($outputFile, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);
    $ig->story->uploadPhoto($photo->getFile(), $metadata);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
} finally {
    @unlink($outputFile);
}



/*
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

  echo "<pre><br><br>-------------------------------------------------<br><br>";
  echo "<h1>Vistas na história:<br></h1>";



  for ($i=0; $i < $n_vistas; $i++) { //VAI BUSCAR TODOS OS USERNAMES DENTRO DA FUNÇÃO USERS (QUE VEM DO OUTRPUT EM JSON)

    //INFO DO USER
    $username_ig = $json->users[$i]->username;
    $nome_ig = $json->users[$i]->full_name;
    $foto_perfil = $json->users[$i]->profile_pic_url;

    echo "<img src='$foto_perfil' style='width: 200px; height: 200px;'><br>";
    echo "<h2>".$nome_ig." (@".$username_ig.")</h2><br>";

  }


  $id_user = $ig->people->getUserIdForName($username);
  $buscar_historia_foto = json_decode($ig->story->getUserReelMediaFeed($id_user));
  $story_foto = $buscar_historia_foto->items[0]->image_versions2->candidates[0]->url;
  echo "<pre>";
  echo "<img src='$story_foto' style='width: 300px; height: 300px;'><br>";

} catch (\Exception $e) {
  echo 'Something went wrong: '.$e->getMessage()."\n";
}
*/
?>