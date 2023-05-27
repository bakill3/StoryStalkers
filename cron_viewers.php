<?php
include 'header.php';

//users>historias = id_user
//$query = mysqli_query($link, "SELECT * FROM historias INNER JOIN users ON historias.id_user = users.id WHERE historias.done='0'");
$query = mysqli_query($link, "SELECT * FROM users");
date_default_timezone_set('Europe/Lisbon');

while ($info_users = mysqli_fetch_array($query)) {

	//MÉTODOS DE DESINCRIPTAÇÃO DA PASSWORD
	$username = $info_users['username'];
	$id_user = $info_users['id'];
	$pass_db = $info_users['password'];
	$method = "AES-256-CBC";  
	$secretHash = "camachoeummerdas.com";
	$historias = mysqli_query($link, "SELECT * FROM historias WHERE done='0' AND id_user='".$id_user."'");
	if (mysqli_num_rows($historias) > 0) {
		while ($info = mysqli_fetch_array($historias)) {



			$data_db = $info['data'];
	$data_db = strtotime($data_db);

	//AGORA DATE
	$now = new DateTime();
	$now->setTimezone(new DateTimeZone('Europe/Lisbon'));
	$date_agora = $now->format('Y-m-d H:i:s');
	//

	$mais_24 = strtotime('+23 hours +25 minutes', $data_db);
	$data_prazo = date("Y-m-d H:i:s",$mais_24);
	//
	echo $data_db." ".$date_agora." ".$data_prazo;

	if(strtotime($date_agora) >= strtotime($data_prazo)) {

		//CONFIG
		
		$password = openssl_decrypt($pass_db, $method, $secretHash); //DECRYPT PASSWORD openssl
		$debug = false;
		$truncatedDebug = false;
		//***
		$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
		try {
			$ig->login($username, $password);
			$id_historia = $info['id_historia'];
			$mediaPk = $info['media_pk'];
			echo $mediaPk."<br>";
			/*
			if (empty($info['media_pk'])) {

				$mediaPk = $info['media_pk'];
				$archive_day = $info['archive_day'];
				$feedList = [$archive_day];
				$info_foto_historia = $ig->story->getReelsMediaFeed($feedList)->getReels()->getData()[$archive_day];
				$d1 = date('Y-m-d', $timestamp);
				$d2 = date('Y-m-d',strtotime($date_simples_agora . "-23 hours"));
				if (strtotime($d1) >= strtotime($d2)) {
					$conta = 0;
					foreach ($info_foto_historia->getItems() as $info_users_reel) { //LOOP DE TODAS AS HISTORIAS POSTADAS NESSE DIA/REEL 
						$timestamp_horas = $info_users_reel->getTaken_at(); //VAI BUSCAR AS HORAS EXATAS DO POST
						$data_arquivo = date('Y-m-d H:i:s', $timestamp_horas);
						$data_agora_mais_1day = date('Y-m-d H:i:s',strtotime($date_agora . "-24 hours"));

						if(strtotime($data_arquivo) >= strtotime($data_agora_mais_1day)) { //SE A DATA EXATA DA PULICAÇÃO DA HISTÓRIA ESTIVER DENTRO DAS 24H [DATA ATUAL]
							$mediaPk = $info_users_reel->getPk();
							
							do { //PAGINAÇÃO
								$busca_views = $ig->story->getStoryItemViewers($mediaPk, $maxId);
								$n_vistas = $busca_views->getTotal_viewer_count();

								foreach ($busca_views->getUsers() as $json) { 
									$username_ig = $json->getUsername();
									$nome_ig = $json->getFull_name();
									$foto_perfil = $json->getProfile_pic_url();
									$verifica = mysqli_query($link, "SELECT * FROM viewers WHERE username_viewer='$username_ig'");
									if (mysqli_num_rows($verifica) > 0) {
										$info_verifica = mysqli_fetch_assoc($verifica);
										$id_viewer = $info_verifica['id_viewer'];
										mysqli_query($link, "INSERT INTO views(id_historia, id_viewer) VALUES('$id_historia', '$id_viewer')");
									} else {
										mysqli_query($link, "INSERT INTO viewers(foto_viewer, nome_viewer, username_viewer) VALUES ('$foto_perfil', '$nome_ig', '$username_ig')");
										$id_viewer = mysqli_insert_id($link);	
										mysqli_query($link, "INSERT INTO views(id_historia, id_viewer) VALUES('$id_historia', '$id_viewer')");
									}
								}
								$maxId = $busca_views->getNextMaxId();

								sleep(5);
							} while ($maxId !== null);
							mysqli_query($link, "UPDATE historias SET done='1', n_vistas='$n_vistas' WHERE id_historia='$id_historia'");
							$username_ig_2 = 'story.stalkers';
							$password_ig_2 = 'gabriel124';

							$debug = false;
							$truncatedDebug = false;

							$ig_2 = new \InstagramAPI\Instagram($debug, $truncatedDebug);
							//LOGIN NO INSTA
							$ig_2->login($username_ig_2, $password_ig_2);


							$userIds2 = array();
							$id_user_ig = $ig_2->people->getUserIdForName($username);
							$buscar_historia_foto = $ig_2->story->getUserReelMediaFeed($id_user_ig);
							$user_pk = $buscar_historia_foto->getUser()->getPk();
							$userIds2[0] = $user_pk;
							$recipients = 
							[
								'users' => $userIds2 // must be an [array] of valid UserPK IDs
							];
							$text = "Olá $username 😁!\nJá guardamos os teus visualizadores (Data da história: $data_d).\n Podes consulta-los na app ou no website (https://instaviews.socialsivex.com/).\n\n PS: Esta mensagem é automatizada \n\n Cumprimentos, Story Stalkers... \n ";
							$ig_2->direct->sendText($recipients, $text);

						}
					}
				}

			}
			*/
			
			if (!empty($mediaPk)) {
				//$mediaPk = $info['media_pk'];
				$maxId = null;
				do { //PAGINAÇÃO
					$busca_views = $ig->story->getStoryItemViewers($mediaPk, $maxId);
					$n_vistas = $busca_views->getTotal_viewer_count();

					foreach ($busca_views->getUsers() as $json) { 
						$username_ig = $json->getUsername();
						$nome_ig = $json->getFull_name();
						$foto_perfil = $json->getProfile_pic_url();
						$verifica = mysqli_query($link, "SELECT * FROM viewers WHERE username_viewer='$username_ig'");
						if (mysqli_num_rows($verifica) > 0) {
							$info_verifica = mysqli_fetch_assoc($verifica);
							$id_viewer = $info_verifica['id_viewer'];
							mysqli_query($link, "INSERT INTO views(id_historia, id_viewer) VALUES('$id_historia', '$id_viewer')");
						} else {
							mysqli_query($link, "INSERT INTO viewers(foto_viewer, nome_viewer, username_viewer) VALUES ('$foto_perfil', '$nome_ig', '$username_ig')");
							$id_viewer = mysqli_insert_id($link);	
							mysqli_query($link, "INSERT INTO views(id_historia, id_viewer) VALUES('$id_historia', '$id_viewer')");
						}
					}
					$maxId = $busca_views->getNextMaxId();

					sleep(5);
				} while ($maxId !== null);
				mysqli_query($link, "UPDATE historias SET done='1', n_vistas='$n_vistas' WHERE id_historia='$id_historia'");
				$username_ig_2 = 'story.stalkers';
				$password_ig_2 = 'gabriel124';

				$debug = false;
				$truncatedDebug = false;

				$ig_2 = new \InstagramAPI\Instagram($debug, $truncatedDebug);
					//LOGIN NO INSTA
				$ig_2->login($username_ig_2, $password_ig_2);


				$userIds2 = array();
				$id_user_ig = $ig_2->people->getUserIdForName($username);
				$buscar_historia_foto = $ig_2->story->getUserReelMediaFeed($id_user_ig);
				$user_pk = $buscar_historia_foto->getUser()->getPk();
				$userIds2[0] = $user_pk;
				$recipients = 
				[
						'users' => $userIds2 // must be an [array] of valid UserPK IDs
					];
					$text = "Olá $username 😁!\nJá guardamos os teus $n_vistas visualizadores ( Data da história: $data_db ).\n Podes consulta-los na app ou no website ( https://instaviews.socialsivex.com/ ).\n\n PS: Esta mensagem é automatizada \n\n Cumprimentos, Story Stalkers. \n ";
					$ig_2->direct->sendText($recipients, $text);

				}

			} catch (\Exception $e) {
				echo 'Something went wrong: '.$e->getMessage()."\n";
				echo "<br><br>USERNAME_YA: $username";
			}
	}









		}
	}


	
}
	?>