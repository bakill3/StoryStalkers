<?php
include 'header.php';
$users_query = mysqli_query($link, "SELECT * FROM users");
$n_users = mysqli_num_rows($users_query); //NUMERO DE USERS

$historias_query = mysqli_query($link, "SELECT * FROM historias");
$n_historias = mysqli_num_rows($historias_query); //NUMERO DE HISTORIAS GUARDADAS

$views_query = mysqli_query($link, "SELECT * FROM views");
$n_views = mysqli_num_rows($views_query); //NUMERO DE VIEWS GUARDADOS

$viewers_query = mysqli_query($link, "SELECT * FROM viewers");
$n_viewers = mysqli_num_rows($viewers_query); //NUMERO DE VIEWERS ÚNICOS GUARDADOS

$data_logins_query = mysqli_query($link, "SELECT DISTINCT id_user FROM data_logins WHERE data_logins.data > DATE_SUB(NOW(), INTERVAL 1 DAY)");
$n_logins = mysqli_num_rows($data_logins_query); //NUMBERO DE LOGINS NAS ULTIMAS 24H

$historias24_query = mysqli_query($link, "SELECT * FROM historias WHERE historias.data > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY data");
$n_historias24 = mysqli_num_rows($historias24_query); //NUMERO DE HISTORIAS NAS ULTIMAS 24H

$total_items  = count( glob("vendor/mgp25/instagram-php/sessions/*", GLOB_ONLYDIR) );



//STATISTICS LONG-TERM
$n_users_arr = [];
$n_logins_24_arr = [];
$n_login_attempts_arr = [];
$n_historias_arr = [];
$n_historias_24_arr = [];
$n_vistas_arr = [];
$n_viewers_arr = [];
$data_arr = [];

$stats_query = mysqli_query($link, "SELECT * FROM stats ORDER BY data");
while ($info_stats = mysqli_fetch_array($stats_query)) {
	$n_users_2 = $info_stats['n_users'];
	$n_logins_24_2 = $info_stats['n_logins_24'];
	$n_login_attempts_2 = $info_stats['n_login_attempts'];
	$n_historias_2 = $info_stats['n_historias'];
	$n_historias_24_2 = $info_stats['n_historias_24'];
	$n_vistas_2 = $info_stats['n_vistas'];
	$n_viewers_2 = $info_stats['n_viewers'];
	$data_2 = date("d/m/Y", strtotime($info_stats['data']));

	$n_users_arr[] = (int)$n_users_2;
	$n_logins_24_arr[] = $n_logins_24_2;
	$n_login_attempts_arr[] = $n_login_attempts_2;
	$n_historias_arr[] = $n_historias_2;
	$n_historias_24_arr[] = $n_historias_24_2;
	$n_vistas_arr[] = $n_vistas_2;
	$n_viewers_arr[] = $n_viewers_2;
	$data_arr[] = $data_2;
}

$me = mysqli_query($link, "SELECT * FROM historias WHERE n_vistas>0 AND id_user='7'");
while ($me_info = mysqli_fetch_array($me)) {
	$n_vistas_me[] = $me_info['n_vistas'];
	$data_me = date("d/m", strtotime($me_info['data']));
	$datas_me[] = $data_me;
}
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js" integrity="sha256-xKeoJ50pzbUGkpQxDYHD7o7hxe0LaOGeguUidbq6vis=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>

<div>
	<div class="card text-center" style="border-bottom: 1px solid rgba(0, 0, 0, 0.3);">
		<div class="card-body">
			<h5 class="card-title">Painel de administração</h5>
			<h4 class="Neue-bold">Estatisticas</h4>
			<?php //<h6 class="Neue-bold">Nº de logins (24h): <span style="color: #004b9c;"><?php echo $n_logins; '></span></h6> ?>
		</div>
	</div>
	<div class="card text-center">
		<div class="card-body">
			<h5 class="card-title">Utilizadores</h5>
			<h6 class="Neue-bold">Nº de utilizadores (IG): <span style="color: #004b9c;"><?php echo $n_users; ?></span></h6>
			<h6 class="Neue-bold">Nº de logins nas últimas 24H: <span style="color: #004b9c;"><?php echo $n_logins; ?></span></h6> 
			<h6 class="Neue-bold">Nº de tentativas de login: <span style="color: #004b9c;"><?php echo $total_items; ?></span></h6> 

			<canvas id="users"></canvas>
		</div>
	</div>
	<div class="card text-center">
		<div class="card-body">
			<h5 class="card-title">Histórias</h5>
			<h6 class="Neue-bold">Nº de histórias guardadas: <span style="color: #004b9c;"><?php echo number_format($n_historias , 0, ',', '.'); ?></span></h6>
			<h6 class="Neue-bold">Nº de histórias guardadas nas últimas 24H: <span style="color: #004b9c;"><?php echo $n_historias24; ?></span></h6>

			<canvas id="historias"</canvas>
			</div>
		</div>
		<div class="card text-center">
			<div class="card-body">
				<h5 class="card-title">Views</h5>
				<h6 class="Neue-bold">Nº de vistas em todas as histórias: <span style="color: #004b9c;"><?php echo number_format($n_views , 0, ',', '.'); ?></span></h6>
				<h6 class="Neue-bold">Nº de visualizadores únicos: <span style="color: #004b9c;"><?php echo number_format($n_viewers , 0, ',', '.'); ?></span></h6>

				

				
			</div>
			<canvas id="views"></canvas>

			<canvas id="viewers"></canvas>
			<canvas id="me"></canvas>
		</div>











		


		<script>
				//USERS
				new Chart(document.getElementById("users"), {
					type: 'line',
					data: {
						labels: <?php echo json_encode($data_arr); ?>, //DATAS
						datasets: [{ 
							data: <?php echo json_encode($n_users_arr); ?>, //VALORES
							label: "Nº de Utilizadores",
							borderColor: "#ff6384",
							backgroundColorHover: "#ff6384",
							fill: false
						}, { 
							data: <?php echo json_encode($n_logins_24_arr); ?>,
							label: "Nº de Logins 24H",
							borderColor: "#36a2eb",
							backgroundColorHover: "#36a2eb",
							fill: false
						}
						]
					},
					options: {
						responsive: true,
						title: {
							display: true,
							text: 'Utilizadores'
						},
						tooltips: {
							mode: 'index',
							intersect: false,
						},
						hover: {
							mode: 'nearest',
							intersect: true
						},
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Datas'
								}
							}],
							yAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Números'
								}
							}]
						}
					}
				});
				//HISTORIAS
				new Chart(document.getElementById("historias"), {
					type: 'line',
					data: {
						labels: <?php echo json_encode($data_arr); ?>, //DATAS
						datasets: [{ 
							data: <?php echo json_encode($n_historias_arr); ?>, //VALORES
							label: "Nº de Histórias",
							borderColor: "#ff6384",
							backgroundColorHover: "#ff6384",
							fill: false
						}, { 
							data: <?php echo json_encode($n_historias_24_arr); ?>,
							label: "Nº de Histórias 24H",
							borderColor: "#36a2eb",
							backgroundColorHover: "#36a2eb",
							fill: false
						}
						]
					},
					options: {
						responsive: true,
						title: {
							display: true,
							text: 'Utilizadores'
						},
						tooltips: {
							mode: 'index',
							intersect: false,
						},
						hover: {
							mode: 'nearest',
							intersect: true
						},
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Datas'
								}
							}],
							yAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Números'
								}
							}]
						}
					}
				});
				//VIEWS
				new Chart(document.getElementById("views"), {
					type: 'line',
					data: {
						labels: <?php echo json_encode($data_arr); ?>, //DATAS
						datasets: [{ 
							data: <?php echo json_encode($n_vistas_arr); ?>, //VALORES
							label: "Nº de Visualizações",
							borderColor: "#ff6384",
							backgroundColorHover: "#ff6384",
							fill: false
						}
						]
					},
					options: {
						responsive: true,
						title: {
							display: true,
							text: 'Utilizadores'
						},
						tooltips: {
							mode: 'index',
							intersect: false,
						},
						hover: {
							mode: 'nearest',
							intersect: true
						},
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Datas'
								}
							}],
							yAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Números'
								}
							}]
						}
					}
				});
				//VISUALIZADORES
				new Chart(document.getElementById("viewers"), {
					type: 'line',
					data: {
						labels: <?php echo json_encode($data_arr); ?>, //DATAS
						datasets: [{ 
							data: <?php echo json_encode($n_viewers_arr); ?>, //VALORES
							label: "Nº de Visualizadores Únicos",
							borderColor: "#36a2eb",
							backgroundColorHover: "#36a2eb",
							fill: false
						}
						]
					},
					options: {
						responsive: true,
						title: {
							display: true,
							text: 'Utilizadores'
						},
						tooltips: {
							mode: 'index',
							intersect: false,
						},
						hover: {
							mode: 'nearest',
							intersect: true
						},
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Datas'
								}
							}],
							yAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Números'
								}
							}]
						}
					}
				});


				//ME
				new Chart(document.getElementById("me"), {
					type: 'line',
					data: {
						labels: <?php echo json_encode($datas_me); ?>, //DATAS
						datasets: [{ 
							data: <?php echo json_encode($n_vistas_me); ?>, //VALORES
							label: "Nº de Vistas",
							borderColor: "#36a2eb",
							backgroundColorHover: "#36a2eb",
							fill: false
						}
						]
					},
					options: {
						responsive: true,
						title: {
							display: true,
							text: 'Utilizadores'
						},
						tooltips: {
							mode: 'index',
							intersect: false,
						},
						hover: {
							mode: 'nearest',
							intersect: true
						},
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Datas'
								}
							}],
							yAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Números'
								}
							}]
						}
					}
				});
			</script>

		</div>
		<?php
		include 'footer.php';
		?>