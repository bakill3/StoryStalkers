<?php
include 'ligar_db.php';
if (isset($_POST['status'])) {
	$status = htmlspecialchars(mysqli_real_escape_string($link, $_POST['status']));

	mysqli_query($link, "UPDATE users SET notifications='".$status."' WHERE id='".$_SESSION['login'][2]."'");
}
?>