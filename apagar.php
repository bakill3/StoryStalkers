<?php
include 'header.php';
if (isset($_POST['done'])) {
	$id_user = htmlspecialchars(mysqli_real_escape_string($link, $_POST['id']));
	$user = mysqli_query($link, "SELECT username FROM users WHERE id='$id_user'");
	$info_user = mysqli_fetch_assoc($user);
	$username = $info_user['username'];

	$historias = mysqli_query($link, "SELECT * FROM historias WHERE id_user='$id_user'");
	$n_historias = mysqli_num_rows($historias);
	$n_views = array();
	$x = 0;
	while ($info = mysqli_fetch_array($historias)) {
		$id_historia = $info['id_historia'];
		$views = mysqli_query($link, "SELECT * FROM views WHERE id_historia = '$id_historia'");
		$n_views_x_historia = mysqli_num_rows($views);
		$n_views[$x] = $n_views_x_historia;
		mysqli_query($link, "DELETE FROM views WHERE id_historia='$id_historia'");
		$x++;
	}
	mysqli_query($link, "DELETE FROM historias WHERE id_user='$id_user'");
	mysqli_query($link, "DELETE FROM data_logins WHERE id_user='$id_user'");
	mysqli_query($link, "DELETE FROM users WHERE id='$id_user'");
	//echo "N de historias: $n_historias <br> Array:";
	$todas_views = array_sum($n_views);
	$n_views = array_filter($n_views);
	$average = array_sum($n_views)/count($n_views);

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
	$x++;
	$recipients = 
	[
		'users' => $userIds2 // must be an [array] of valid UserPK IDs
	];

	$text = "Olá $username. Cancelamos a tua conta e os teus dados foram eliminados.\n As tuas estatísticas:\n Nº de historias guardadas: $n_historias \n Nº de todas as visualizações: $todas_views \n Média de visualizadores por história: $average \n Esperemos que tenhas gostado do nosso serviço. \n\n Cumprimentos, Story Stalkers";
	$ig_2->direct->sendText($recipients, $text);
}
?>
<form method="POST">
	<input type="text" name="id">
	<button type="submit" name="done">Done</button>
</form>