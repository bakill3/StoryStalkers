<?php
include 'header.php';
$query = mysqli_query($link, "SELECT * FROM historias WHERE historias.data > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY data");
while ($info = mysqli_fetch_array($query)) {
	$id_user = $info['id_user'];
	$id_historia = $info['id_historia'];
	$foto_historia = $info['foto_historia'];
	$data = $info['data'];
	$done = $info['done'];
?>
<h3>USER ID: <?php echo $id_user; ?></h3>
<h3>ID HISTÓRIA: <?php echo $id_historia; ?></h3>
<h3>DATA: <?php echo $data; ?></h3>
<h3>DONE: <?php echo $done; ?></h3>
<img src="<?php echo $foto_historia; ?>" style="width: 150px; height: 130px;"><br>
<?php
}
?>