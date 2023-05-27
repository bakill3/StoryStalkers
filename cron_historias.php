<?php
include 'header.php';
$query = mysqli_query($link, "SELECT * FROM users");
date_default_timezone_set('Europe/Lisbon');

if (mysqli_num_rows($query) > 0) { 
	while ($info = mysqli_fetch_array($query)) { //LOOP EM TODOS OS USERS

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
			$id_user_ig = $ig->people->getUserIdForName($username);
			$now = new DateTime();
			$now->setTimezone(new DateTimeZone('Europe/Lisbon'));


			$busca_archive_day = $ig->story->getArchivedStoriesFeed(); //VAI BUSCAR TODAS AS HISTORIAS DO ARQUIVO
			$date_agora = $now->format('Y-m-d H:i:s');
			$date_simples_agora = $now->format('Y-m-d');

			$x = 0;
			foreach ($busca_archive_day->getItems() as $info_inicial) { //LOOP EM TODAS AS HISTORIAS DO ARQUIVO
				$timestamp = $info_inicial->getTimestamp();

				$archive_day = $info_inicial->getId(); 
				$timestamp_historia = $info_inicial->getTimestamp(); 

				$feedList = [$archive_day];
				$info_foto_historia = $ig->story->getReelsMediaFeed($feedList)->getReels()->getData()[$archive_day];
				$d1 = date('Y-m-d', $timestamp);
				$d2 = date('Y-m-d',strtotime($date_simples_agora . '-23 hours'));
				if (strtotime($d1) >= strtotime($d2)) {
					$conta = 0;
					foreach ($info_foto_historia->getItems() as $info_users_reel) { //LOOP DE TODAS AS HISTORIAS POSTADAS NESSE DIA/REEL 

						$timestamp_horas = $info_users_reel->getTaken_at(); //VAI BUSCAR AS HORAS EXATAS DO POST
						$data_arquivo = date('Y-m-d H:i:s', $timestamp_horas);
						$data_agora_mais_1day = date('Y-m-d H:i:s',strtotime($date_agora . '-24 hours'));

						if(strtotime($data_arquivo) >= strtotime($data_agora_mais_1day)) { //SE A DATA EXATA DA PULICAÇÃO DA HISTÓRIA ESTIVER DENTRO DAS 24H [DATA ATUAL]

							$story_foto = $info_users_reel->getImage_versions2()->getCandidates()[0]->getUrl(); //VAI BUSCAR A FOTO
							$existe = 'historias/utilizadores/'.$id_user.'/'.$archive_day;
							if (!is_dir($existe)) {
								mkdir($existe,0777,true);
							}
							$img = 'historias/utilizadores/'.$id_user.'/'.$archive_day.'/'.$conta.'.jpg';
							$filename = $conta.'.jpg';

							
							if (file_exists($img)) {
								if (empty($story_foto)) {
									file_put_contents($img, file_get_contents($story_foto));
								}
							} else {
								file_put_contents($img, file_get_contents($story_foto));
							}

								$mediaPk = $info_users_reel->getPk(); //PK DA HISTÓRIA
								$x++;
								$sql_historias = mysqli_query($link, "SELECT * FROM historias WHERE id_user='$id_user' AND foto_historia='$img'"); //SELECIONA TUDO DOS USERS SE A FOTO FOR IGUAL (ESTÁ EM LOOP VAI PASSAR POR TODAS DESSE DIA) [SE NÃO EXISTIR -> ELSE { INSERE NA DB }]


								if (mysqli_num_rows($sql_historias) == 0) {
									$id_user_ig = $ig->people->getUserIdForName($username);
									$buscar_historia_foto = $ig->story->getUserReelMediaFeed($id_user_ig);
									$timestamp_historia = $info_inicial->getTimestamp(); 
									mysqli_query($link, "INSERT INTO historias(archive_day, id_user, foto_historia, timestamp_historia, data, media_pk) VALUES('$archive_day', '$id_user', '$img', '$timestamp_horas', '$data_arquivo', '$mediaPk')");

								} 
						//sleep(5); //SLEEP PARA NÃO ABUSAR DA API QUE SENÃO DÀ ERRO (TOO MANY REQUESTS)
						$conta++;
					}
					}
				} else {
					break;
				}
				//sleep(5); //SLEEP PARA NÃO ABUSAR DA API QUE SENÃO DÀ ERRO (TOO MANY REQUESTS)		
			}

		} catch (\Exception $e) {
			echo 'Something went wrong: '.$e->getMessage()."\n";
			echo "<br><br>USERNAME_YA: $username";
		}


		

	}
	//sleep(5); //SLEEP PARA NÃO ABUSAR DA API QUE SENÃO DÀ ERRO (TOO MANY REQUESTS)
}

?>


