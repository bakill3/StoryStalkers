<?php
include 'header.php';
$query = mysqli_query($link, "SELECT * FROM users");
date_default_timezone_set('Europe/Lisbon');

if (mysqli_num_rows($query) > 0) {
	while ($info = mysqli_fetch_array($query)) {

		$pass_db = $info['password'];
		$method = "AES-256-CBC";  
		$secretHash = "camachoeummerdas.com";

		$id_user = $info['id'];
		$username = $info['username'];
		$password = openssl_decrypt($pass_db, $method, $secretHash);

		$debug = false;
		$truncatedDebug = false;

		$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
		try {
			$ig->login($username, $password);
		} catch (\Exception $e) {
			echo 'Something went wrong: '.$e->getMessage()."\n";
			exit(0);
		}
		
		try {

			date_default_timezone_set("Europe/Lisbon");
			$now = new DateTime();
			$now->setTimezone(new DateTimeZone('Europe/Lisbon'));


			 //VAI BUSCAR TODOS AS HISTORIAS DO ARQUIVO
			
			//if (isset($busca_archive_day->getItems()[0]->getId())) {

					$busca_archive_day = $ig->story->getArchivedStoriesFeed();
					//$date_agora = date("Y-m-d H:m:s");
					$date_agora = $now->format('Y-m-d H:i:s');

					$archive_day = $busca_archive_day->getItems()[0]->getId(); //VAI BUSCAR O ARCHIVE DAY AO OUTPUT DE JSON
					$feedList = [$archive_day];
  					$mediaPk = $ig->story->getReelsMediaFeed($feedList)->getReels()->getData()[$archive_day]->getItems()[0]->getPk(); //PK DA HISTÓRIA

  					//$query = mysqli_query($link, "SELECT * FROM viewers INNER JOIN historias ON viewers.id_historia = historias.id_historia WHERE historias.id_user = '$id_user'");
  					$sql_historias = mysqli_query($link, "SELECT * FROM historias WHERE id_user='$id_user' AND archive_day='$archive_day'");

  					if (mysqli_num_rows($sql_historias) > 0) {
  						$info_historia = mysqli_fetch_assoc($sql_historias);
  						$id_historia = $info_historia['id_historia'];
  						$data_db = $info_historia['data'];
  						$data_db = strtotime($data_db);

  						$done = $info_historia['done'];

  						if ($done == 0) {
  							$data_prazo = date('Y-m-d H:i:s',strtotime('+23 hours +20 minutes',strtotime($data_db)));
  							$data_prazo = strtotime('+23 hours +20 minutes',strtotime($data_db));
  							$mais_24 = strtotime('+23 hours +20 minutes', $data_db);
  							$data_prazo = date("Y-m-d H:i:s",$mais_24);
  							//$json = $ig->story->getStoryItemViewers($mediaPk);

  							//$n_vistas = $json->total_viewer_count;

  							//****************************************************************************************************
  							$maxId = null;
  							if(strtotime($date_agora) >= strtotime($data_prazo)) {
	  							do {
	  								$busca_views = $ig->story->getStoryItemViewers($mediaPk, $maxId);
	  								$n_vistas = $busca_views->getTotal_viewer_count();
	  								foreach ($busca_views->getUsers() as $json) {
	  									$username_ig = $json->getUsername();
	  									$nome_ig = $json->getFull_name();
	  									$foto_perfil = $json->getProfile_pic_url();
	  									mysqli_query($link, "INSERT INTO viewers(id_historia, foto_viewer, nome_viewer, username_viewer) VALUES ('$id_historia', '$foto_perfil', '$nome_ig', '$username_ig')");	
	  								}
	  								$maxId = $busca_views->getNextMaxId();

	  								sleep(5);
	  							} while ($maxId !== null);
	  							mysqli_query($link, "UPDATE historias SET done='1' WHERE id_historia='$id_historia'");
	  						}
  							//****************************************************************************************************

  							echo $date_agora."<br>".$data_prazo;						
  							
  						}

  					} else {
  						$id_user_ig = $ig->people->getUserIdForName($username);
  						$buscar_historia_foto = $ig->story->getUserReelMediaFeed($id_user_ig);
						$story_foto = $buscar_historia_foto->getItems()[0]->getImage_versions2()->getCandidates()[0]->getUrl();
  						mysqli_query($link, "INSERT INTO historias(archive_day, id_user, foto_historia, data) VALUES('$archive_day', '$id_user', '$story_foto', '$date_agora')");
  					}

  			//}

  		} catch (\Exception $e) {
  			echo 'Something went wrong: '.$e->getMessage()."\n";
  		}




  	}
  }
  ?>