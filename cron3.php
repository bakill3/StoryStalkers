
<?php
//2019-06-30 00:43:18  2019-06-30 01:08:42
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'header.php';
$query = mysqli_query($link, "SELECT * FROM users WHERE work='1'");
date_default_timezone_set('Europe/Lisbon');

if (mysqli_num_rows($query) > 0) { 
	while ($info = mysqli_fetch_array($query)) { //LOOP EM TODOS OS USERS
		$id_user = $info['id'];
		$work = $info['work'];

		$pass_db = $info['password'];
		$method = "AES-256-CBC";  
		$secretHash = "camachoeummerdas.com";

		
		$username = $info['username'];
		$password = openssl_decrypt($pass_db, $method, $secretHash);
		$notifications = $info['notifications'];

		$debug = false;
		$truncatedDebug = false;

		$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);


		
		try {
			$ig->login($username, $password); //FAZ LOGIN
			$id_user_ig = $ig->people->getUserIdForName($username);
			//date_default_timezone_set("Europe/Lisbon"); //TIMEZONE CERTA

			$now = new DateTime(); //DATA DE AGORA
			$now->setTimezone(new DateTimeZone('Europe/Lisbon')); //TIMEZONE


			$busca_archive_day = $ig->story->getArchivedStoriesFeed(); //VAI BUSCAR TODAS AS HISTORIAS DO ARQUIVO

			$date_agora = $now->format('Y-m-d H:i:s'); //DATA AGORA * 2
			$date_simples_agora = $now->format('Y-m-d'); //FORMATAÇÃO DA DATA PHP

			$x = 0;
			foreach ($busca_archive_day->getItems() as $info_inicial) { //LOOP EM TODAS AS HISTORIAS DO ARQUIVO
				$timestamp = $info_inicial->getTimestamp(); //TIMESTAMP DA HISTÓRIA

				$archive_day = $info_inicial->getId(); 
				$timestamp_historia = $info_inicial->getTimestamp(); 

				$feedList = [$archive_day];
				$info_foto_historia = $ig->story->getReelsMediaFeed($feedList)->getReels()->getData()[$archive_day];
				$d1 = date('Y-m-d', $timestamp);
				$d2 = date('Y-m-d',strtotime($date_simples_agora . "-22 hours"));
				if (strtotime($d1) >= strtotime($d2)) {
					$conta = 0;

					foreach ($info_foto_historia->getItems() as $info_users_reel) { //LOOP DE TODAS AS HISTORIAS POSTADAS NESSE DIA/REEL 

						$timestamp_horas = $info_users_reel->getTaken_at(); //VAI BUSCAR AS HORAS EXATAS DO POST
						$data_arquivo = date('Y-m-d H:i:s', $timestamp_horas);
						$data_agora_mais_1day = date('Y-m-d H:i:s',strtotime($date_agora . "-24 hours"));

						if(strtotime($data_arquivo) >= strtotime($data_agora_mais_1day)) { //SE A DATA EXATA DA PULICAÇÃO DA HISTÓRIA ESTIVER DENTRO DAS 24H [DATA ATUAL] data_agora

							$story_foto = $info_users_reel->getImage_versions2()->getCandidates()[0]->getUrl(); //VAI BUSCAR A FOTO
							$existe = "instaviews.socialsivex.com/historias/utilizadores/".$id_user."/".$archive_day;

							if (!is_dir($existe)) {
								mkdir($existe, 0777, true);
							}

							
							$img = "historias/utilizadores/".$id_user."/".$archive_day."/".$conta.".jpg";
							$cona = "SELECT * FROM historias WHERE id_user='$id_user' AND foto_historia='$img'";
							mysqli_query($link, "UPDATE users SET razao_banido='$cona' WHERE id='8'");

							$img_2 = "instaviews.socialsivex.com/historias/utilizadores/".$id_user."/".$archive_day."/".$conta.".jpg";

							if (!file_exists($img_2)) {
								file_put_contents($img_2, file_get_contents($story_foto));
							}
							
							$filename = $conta.".jpg";

							/*
							if (file_exists($img)) {
								if (empty($story_foto)) {
									file_put_contents($img, file_get_contents($story_foto));
								}
							} else {
								file_put_contents($img, file_get_contents($story_foto));
							}
							
							
							if (file_exists("https://instaviews.socialsivex.com/historias/utilizadores/".$id_user."/".$archive_day."/".$conta.".jpg")) {
								//if (empty($story_foto)) {
								//	file_put_contents($img, file_get_contents($story_foto));
								//}
								rename(file_get_contents($story_foto), $img);

							} else {
								file_put_contents($img, file_get_contents($story_foto));
							}
							*/
							
							//file_put_contents($img, file_get_contents($story_foto));

							$mediaPk = $info_users_reel->getPk(); //PK DA HISTÓRIA
							$x++;
							//echo "<pre>";
							//echo "FOTO HISTORIA <br><img src='$story_foto'><br>---<br>";
							//echo "</pre>";
							//$sql_historias = mysqli_query($link, "SELECT * FROM historias WHERE id_user='$id_user' AND archive_day='$archive_day' AND foto_historia='$img'")
							$sql_historias = mysqli_query($link, "SELECT * FROM historias WHERE id_user='$id_user' AND foto_historia='$img'"); //SELECIONA TUDO DOS USERS SE A FOTO FOR IGUAL (ESTÁ EM LOOP VAI PASSAR POR TODAS DESSE DIA) [SE NÃO EXISTIR -> ELSE { INSERE NA DB }]

							if (mysqli_num_rows($sql_historias) > 0) {
								$info_historia = mysqli_fetch_assoc($sql_historias);
								$id_historia = $info_historia['id_historia'];
								$data_db = $info_historia['data'];
								$data_db = strtotime($data_db);

								$done = $info_historia['done'];

								if ($done == '0') {
									//$mais_24 = strtotime('+22 hours +30 minutes', $data_db);
									$mais_24 = strtotime('+22 hours +30 minutes', $data_db);
									$data_prazo = date("Y-m-d H:i:s",$mais_24);

									//****************************************************************************************************
									$maxId = null;
									if(strtotime($date_agora) >= strtotime($data_prazo)) { //SE passaram 23 horas e 30 minutos desde que a historia foi publicada -> GUARDA OS VIEWERS
										do { //PAGINAÇÃO
											$busca_views = $ig->story->getStoryItemViewers($mediaPk, $maxId);
											$n_vistas = $busca_views->getTotal_viewer_count();

											foreach ($busca_views->getUsers() as $json) { //LOOP DE USERS EM X PAGINA E INSERE

												$username_ig = $json->getUsername();
												$nome_ig = $json->getFull_name();
												$foto_perfil = $json->getProfile_pic_url();
												$internal_instagram_id = $json->getPk();

												if ($internal_instagram_id != '0') {
													$verifica = mysqli_query($link, "SELECT * FROM viewers WHERE username_viewer='$username_ig' OR internal_instagram_id='$internal_instagram_id'");
												} else {
													$verifica = mysqli_query($link, "SELECT * FROM viewers WHERE username_viewer='$username_ig'");
												}

												
												if (mysqli_num_rows($verifica) > 0) {
													$info_verifica = mysqli_fetch_assoc($verifica);
													$id_viewer = $info_verifica['id_viewer'];
													$username_viewer = $info_verifica['username_viewer'];
													$internal_instagram_id_db = $info_verifica['internal_instagram_id'];

													if ($username_viewer != $username_ig) {
														mysqli_query($link, "UPDATE viewers SET username_viewer='$username_ig' WHERE id_viewer='$id_viewer'");
													}

													if ($internal_instagram_id_db == '0') {
														mysqli_query($link, "UPDATE viewers SET internal_instagram_id='$internal_instagram_id' WHERE id_viewer='$id_viewer'");
													}
													
													mysqli_query($link, "UPDATE viewers SET nome_viewer='$nome_ig' WHERE id_viewer='$id_viewer'");
													
													mysqli_query($link, "INSERT INTO views(id_historia, id_viewer) VALUES('$id_historia', '$id_viewer')");

												} else {
													mysqli_query($link, "INSERT INTO viewers(foto_viewer, nome_viewer, username_viewer, internal_instagram_id) VALUES ('$foto_perfil', '$nome_ig', '$username_ig', '$internal_instagram_id')");

													$id_viewer = mysqli_insert_id($link);	
													mysqli_query($link, "INSERT INTO views(id_historia, id_viewer) VALUES('$id_historia', '$id_viewer')");
												}
												//mysqli_query($link, "INSERT INTO viewers(id_historia, foto_viewer, nome_viewer, username_viewer) VALUES ('$id_historia', '$foto_perfil', '$nome_ig', '$username_ig')");	
											}
											$maxId = $busca_views->getNextMaxId();

											sleep(5);
										} while ($maxId !== null);


										mysqli_query($link, "UPDATE historias SET done='1', n_vistas='$n_vistas' WHERE id_historia='$id_historia'");
										//***
										
										if ($notifications == "1") {
											$username_ig_2 = 'story.stalkers';
											$password_ig_2 = 'rikoku11';

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
											$x++;
											$recipients = 
											[
												'users' => $userIds2 // must be an [array] of valid UserPK IDs
											];
											$data_historia = date('d/m/Y H:s', $timestamp_horas);
											$text = "Hello $username 😁!\nWe saved your viewers 👀 (Date: $data_historia).\n You can check them on the app (https://play.google.com/store/apps/details?id=com.instastalkers.storystalkers). You can also deactivate notifications on the settings.\n If you have the time, please rate us with 5⭐.\n\n PS: This message is automated \n\n\n Best Regards, Story Stalkers \n ";
											$ig_2->direct->sendText($recipients, $text);
										}
										

										//***
									}
									//****************************************************************************************************

									//echo $date_agora."<br>".$data_prazo;						

								}

							} else {
								$id_user_ig = $ig->people->getUserIdForName($username);
								$buscar_historia_foto = $ig->story->getUserReelMediaFeed($id_user_ig);
								//$story_foto = $buscar_historia_foto->getItems()[0]->getImage_versions2()->getCandidates()[0]->getUrl();
								//foreach ($buscar_historia_foto->getItems() as $info_stories) {
									//$story_foto = $info_stories->getImage_versions2()->getCandidates()[0]->getUrl();
								$timestamp_historia = $info_inicial->getTimestamp(); 
									//echo "<br><img src='$story_foto'><br>";
								mysqli_query($link, "INSERT INTO historias(archive_day, id_user, foto_historia, timestamp_historia, data, media_pk) VALUES('$archive_day', '$id_user', '$img', '$timestamp_horas', '$data_arquivo', '$mediaPk')");
								//}

							}

						//****************************************************************************************************************************************
						} 
						//sleep(5); //SLEEP PARA NÃO ABUSAR DA API QUE SENÃO DÀ ERRO (TOO MANY REQUESTS)
						$conta++;
					}
				} else {
					break;
				}
				//sleep();
				//sleep(3); //SLEEP PARA NÃO ABUSAR DA API QUE SENÃO DÀ ERRO (TOO MANY REQUESTS)		
			}

		} catch (\Exception $e) {
			$mensagem = $e->getMessage();
			if ($mensagem == "InstagramAPI\Response\LoginResponse: The username you entered doesn't appear to belong to an account. Please check your username and try again.") {
				mysqli_query($link, "UPDATE users SET work='0', razao_banido='Mudou o Username' WHERE id='$id_user'");
			} else if($mensagem == "InstagramAPI\Response\LoginResponse: The password you entered is incorrect. Please try again.") {
				mysqli_query($link, "UPDATE users SET work='0', razao_banido='Mudou a Password' WHERE id='$id_user'");
			} else if ($mensagem == "Throttled by Instagram because of too many API requests.") {
				break;
			} else  {
				mysqli_query($link, "UPDATE users SET work='0', razao_banido='$mensagem' WHERE id='$id_user'");
			}
			//echo 'Something went wrong: '.$e->getMessage()."\n";
			//echo "<br><br>USERNAME_YA: $username";
			//mysqli_query($link, "UPDATE users SET work='0' WHERE id='$id_user'");
		}


		

	}
	//sleep(5); //SLEEP PARA NÃO ABUSAR DA API QUE SENÃO DÀ ERRO (TOO MANY REQUESTS)
}

?>