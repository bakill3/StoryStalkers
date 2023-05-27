<?php
include 'ligar_db.php';
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

if (!isset($_SESSION['login'])) {
	header('Location: index.php');
}

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
require_once "libs/Mobile_Detect.php";
$detect = new Mobile_Detect;


if (!isset($_SESSION['language'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$res = file_get_contents('https://www.iplocate.io/api/lookup/'.$ip.'');

	$res = json_decode($res);
	$country = $res->country;
	if ($country == "Portugal") {
		$_SESSION['language'] = "pt";
	} else {
		$_SESSION['language'] = "en";
	}
}
include 'translations.php';

$query = mysqli_query($link, "SELECT * FROM users WHERE id='".$_SESSION['login'][2]."'");
$info_user = mysqli_fetch_assoc($query);
$id_user = $info_user['id'];
$username = $info_user['username'];
$profile_pic = $info_user['profile_pic'];
$followers = $info_user['followers'];
$following = $info_user['following'];

$notifications = $info_user['notifications'];

?>
<!DOCTYPE html>
<html lang="en" style="width: 100% !important; height: 100% !important;">

<head>
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<meta charset="UTF-8">

	<title>Story Stalkers</title>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
	<script src="teste/js/index.js"></script>

	<link rel="stylesheet" href="teste/css/style.css?version=2">




	<link rel="stylesheet" type="text/css" href="bootstrap4/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap4/bootstrap-grid.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap4/bootstrap-reboot.min.css">
	<script src="jquery.min.js"></script>
	<script src="preloader.js"></script>
	<script src="list.min.js"></script>
	<script type="text/javascript" src="bootstrap4/popper.js"></script>
	<script src="bootstrap4/bootstrap.min.js"></script>

	<link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">


	<title>Story Stalkers</title>
	<meta name="description" content="Insta Stalkers é um website/aplicação que deixa o utilizador ver quem viu as suas histórias do Instagram há mais de 24 horas.">
	<meta name="keywords" content="Story viewer saver, Viewers Instagram, Save Instagram Story Viewers, Watch story viewers">
	<meta name="author" content="Gabriel Brandão">
	<link rel="stylesheet" type="text/css" href="main.css">

	<style type="text/css">
		fieldset {
			width: 100%;
			text-align: left;
			font-family: Lato, sans-serif;
		}

		form {
			border-top: 1px solid #c8c7cc;
			border-bottom: 1px solid #c8c7cc;
		}

		legend {
			margin: 0 0 5px;
			color: #6f6f74;
			font-size: 13px;
			line-height: 1.4;
		}

		.item {
			position: relative;
			box-sizing: border-box;
			background: #ffffff;
		}

		.item p {
			border-top: 1px solid #c8c7cc;
			margin: 0 0 0 8px;
			height: 44px;
			color: #000000;
			font-size: 15px;
			line-height: 44px;
		}

		.item:nth-child(1) p {
			border: 0;
		}


		input[type="checkbox"] {
			display: none;	
		}

		.toggle {
			position: absolute;
			top: 0;
			bottom: 0;
			right: 9px;
			margin: auto;
			width: 51px;
			height: 31px;
		}

		.toggle label,
		.toggle i {
			box-sizing: border-box;
			display: block;
			background: #ffffff;
		}

		.toggle label {
			width: 51px;
			height: 32px;
			border-radius: 32px; 
			border: 2px solid #e5e5e5;
			transition: all 0.30s ease;
		}

		.toggle i {
			position: absolute;
			top: 2px;
			left: 2px;
			width: 28px;
			height: 28px;
			border-radius: 28px;
			box-shadow: 0 0 1px 0 rgba(0,0,0, 0.25),
			0 3px 3px 0 rgba(0,0,0, 0.15);
			background: #ffffff;
			transition: all 0.3s cubic-bezier(0.275, -0.450, 0.725, 1.450);
		}

		input[type="checkbox"]:active + .toggle i {
			width: 35px;
		}

		input[type="checkbox"]:active + .toggle label,
		input[type="checkbox"]:checked + .toggle label {
			border: 16px solid #4cd964;
		}

		input[type="checkbox"]:checked + .toggle i {
			left: 21px;
		}

		input[type="checkbox"]:checked:active + .toggle label {
			border: 16px solid #e5e5e5;
		}

		input[type="checkbox"]:checked:active + .toggle i {
			left: 14px;
		}
	</style>




</head>

<body style="width: 100% !important; height: 100% !important;" id="body_g">
	<div id="change_g1" style="width: 100% !important; height: 100% !important; display: none;">

	</div>
	<div id="change_g2" style="width: 100% !important; height: 100% !important">
		<div class="preloader-wrapper">
			<div class="preloader">
				<img src="preloader.gif" alt="NILA">
			</div>
		</div>

		<div class="wrapper" style="width: 100% !important; height: 100% !important; margin: 0 auto !important;">
			<h1 class="h1_menu">Menu</h1>
			<a class="menu-btn" onclick="toggleMenu()"></a>
			<section class="one" onclick="goToPage(0)">
				<?php
      //STATS
				$viewers_query = mysqli_query($link, "SELECT DISTINCT username_viewer FROM viewers INNER JOIN views ON viewers.id_viewer = views.id_viewer INNER JOIN historias ON views.id_historia = historias.id_historia WHERE historias.id_user='$id_user'");
				$n_viewers_unicos = mysqli_num_rows($viewers_query);

				$historias = mysqli_query($link, "SELECT * FROM historias WHERE id_user='$id_user'");
				$n_historias = mysqli_num_rows($historias);
				$n_views = array();
				$x = 0;
				while ($info = mysqli_fetch_array($historias)) {
					$id_historia = $info['id_historia'];
					$views = mysqli_query($link, "SELECT * FROM views WHERE id_historia = '$id_historia'");
					$n_views_x_historia = mysqli_num_rows($views);
					$n_views[$x] = $n_views_x_historia;
					$x++;
				}
				$todas_views = array_sum($n_views);
				$n_views = array_filter($n_views);
				$average = array_sum($n_views)/count($n_views);
				$average_arredondado = round($average);
				?>
				<h1 class="h1_menu">Statistics</h1>
				<hr style="margin-top: 0;">
				<div class="text-center">
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $followers; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[5]; ?></span>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<img src="<?php echo $profile_pic; ?>" class="rounded-circle" style="width: 50%;">
						<h2 class="h1_menu Neue-bold" style="font-size: 100% !important; line-height: 50px !important;">@<?php echo $username; ?></h2>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $following; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[6]; ?></span>
					</div>
				</div>
				<hr style="margin-bottom: 0;">
				<div class="text-center" style="height: 100%; width: 100%;background: rgba(255, 255, 255, 0.8); padding-top: 1%;">
					<div class="card text-white bg-info mb-3" style="width: 47%; margin: 1%; display: inline-block;">
						<div class="card-header">Average Viewers</div>
						<div class="card-body">
							<h5 class="card-title"><?php echo $average_arredondado; ?></h5>
						</div>
					</div>
					<div class="card text-white bg-dark mb-3" style="width: 47%; margin: 1%; display: inline-block;">
						<div class="card-header">Total Views</div>
						<div class="card-body">
							<h5 class="card-title"><?php echo $todas_views; ?></h5>
						</div>
					</div>
					<br>
					<div class="card text-white bg-success mb-3" style="width: 47%; margin: 1%; display: inline-block;">
						<div class="card-header">Total Stories</div>
						<div class="card-body">
							<h5 class="card-title"><?php echo $n_historias; ?></h5>
						</div>
					</div>
					<div class="card text-white bg-danger mb-3" style="width: 47%; margin: 1%; display: inline-block;">
						<div class="card-header">Unique Viewers</div>
						<div class="card-body">
							<h5 class="card-title"><?php echo $n_viewers_unicos; ?></h5>
						</div>
					</div>
				</div>



			</section>
			<section class="two" onclick="goToPage(1)">
				<h1 class="h1_menu">Top Stalkers</h1>
				<hr style="margin-top: 0;">
				<div class="text-center">
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $followers; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[5]; ?></span>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<img src="<?php echo $profile_pic; ?>" class="rounded-circle" style="width: 50%;">
						<h2 class="h1_menu Neue-bold" style="font-size: 100% !important; line-height: 50px !important;">@<?php echo $username; ?></h2>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $following; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[6]; ?></span>
					</div>
				</div>
				<hr style="margin-bottom: 0;">
				<div class="text-center" style="height: 100%; width: 100%;background: rgba(255, 255, 255, 0.8); padding-top: 1%; overflow-y: scroll;">

					<fieldset>
						<legend style="text-align: center;">Settings:</legend>
						<form action="">
							<div class="item">
								<p>Notifications via DM</p>
								<input type="checkbox" id="toggle_today_summary" name="" value="" <?php if($notifications == "1") { echo "checked"; } ?>>
								<div class="toggle">
									<label for="toggle_today_summary"><i></i></label>
								</div>
							</div>
							<div class="item">
								<p>Follow Story Stalkers on Instagram</p>
								<input type="checkbox" id="toggle_calendar_day_view" name="" value="">
								<div class="toggle">
									<label for="toggle_calendar_day_view"><i></i></label>
								</div>
							</div>
							<div class="item">
								<p>Rate 5 stars on Play Store</p>
								<input type="checkbox" id="toggle_reminders" name="" value="">
								<div class="toggle">
									<label for="toggle_reminders"><i></i></label>
								</div>
							</div>
						</form>
					</fieldset>
					

				</div>
			</section>
			<script>
				$(document).ready(function() {
					$('#toggle_today_summary').change(function() {
						if(this.checked) {
							var status = 1;
						} else {
							var status = 0;
						}
						$.ajax({
							type: "POST",
							url: "update_notification.php",
							data: {
								status: status, 
							},
							success:function(data)
							{
								

							}

						});      
					});
				});
			</script>
			<section class="three" onclick="goToPage(2)">
				<h1 class="h1_menu">Top Viewers</h1>
				<hr style="margin-top: 0;">
				<div class="text-center">
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $followers; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[5]; ?></span>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<img src="<?php echo $profile_pic; ?>" class="rounded-circle" style="width: 50%;">
						<h2 class="h1_menu Neue-bold" style="font-size: 100% !important; line-height: 50px !important;">@<?php echo $username; ?></h2>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $following; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[6]; ?></span>
					</div>
				</div>
				<hr style="margin-bottom: 0;">
				<div class="" style="height: 100%; width: 100%;background: rgba(255, 255, 255, 0.8); padding-top: 1%; overflow-y: scroll;">
					<li class="list-group-item"><input type="text" id="myInput2" onkeyup="pesquisar_pessoas()" placeholder="Pesquisar Pessoas..." class="form-control"></li>

					<input id="btn_ads" class="btn btn-primary" type="button" value="Watch Ad to Unlock" onclick="startRewardVideo()" style="left: 50% !important;top: 50%;position: fixed;transform: translateX(-50%);">

					<ul class="list-group" id="ul" style="display: none;">
						<?php
						$query = "SELECT views.id_viewer, viewers.nome_viewer, viewers.foto_viewer, viewers.username_viewer,
						COUNT(views.id_viewer) AS `historias_vistas` 
						FROM views INNER JOIN historias ON views.id_historia=historias.id_historia INNER JOIN viewers ON views.id_viewer=viewers.id_viewer WHERE historias.id_user='".$_SESSION['login'][2]."'
						GROUP BY views.id_viewer
						ORDER BY historias_vistas DESC ;";

						$top_stalkers = mysqli_query($link, $query);
						while ($info = mysqli_fetch_array($top_stalkers)) {
							$username_ig = $info['username_viewer'];
							$nome_ig = $info['nome_viewer'];
							$foto_perfil = $info['foto_viewer'];
							$historias_vistas = $info['historias_vistas'];
							echo '
							<li style="cursor: pointer; padding: 3%; height: 74px; position: relative; border: 0px;" class="list-group-item visitar_perfil">
							<div style="display: inline-block; float: left; width: 12%;">
							<img src="'.$foto_perfil.'" style="width: 100%;border-radius: 50%; display: block;">
							</div>
							<div class="roboto" style="display: inline-block; width: 80%;">
							<span class="mb-1" style=" padding-left: 4%; font-size: 16px;">'.$username_ig.'</span><br>
							<span class="text-secondary" style="padding-left: 4%;">'.$nome_ig.' - '.$home[6].' '.$historias_vistas.' '.$home[7].'</span>

							</div>
							<div style="position: absolute;top: 23%;right: 2%;">
							<a href="https://www.instagram.com/'.$username_ig.'" class="text-right btn btn-primary" target="_blank" style=" color: inherit; text-decoration: inherit;"><i style="color: white;" class="fas fa-user"></i></a>
							</div>
							</li>

							';
						}
						?>


					</ul>
				</div>
			</section>
			<section class="four" onclick="goToPage(3)" style="overflow-y: scroll; background-color: #006ce0;">
				<h1 class="h1_menu">Home</h1>
				<hr style="margin-top: 0;">
				<div class="text-center">
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $followers; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[5]; ?></span>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<img src="<?php echo $profile_pic; ?>" class="rounded-circle" style="width: 50%;">
						<h2 class="h1_menu Neue-bold" style="font-size: 100% !important; line-height: 50px !important;">@<?php echo $username; ?></h2>
					</div>
					<div style="display: inline-block; vertical-align: top;">
						<span class="h1_menu" style="line-height: 50px !important"><?php echo $following; ?></span>
						<span class="text-white Neue-regular" style="display: block;"><?php echo $new_index[6]; ?></span>
					</div>
				</div>
				<hr style="margin-bottom: 0; margin-top: 0;">
				<div id="historias_home" style="height: 100%;">



					<div class="text-center" style="background-color: rgba(255, 255, 255, 0.85);">
						<h1 class="h1_menu" style="color: black;color: black !important;width: 100% !important;"><?php echo $new_index[4]; ?></h1>

						<p class="text-white roboto" style="display: inline-block; color: black;"><i style="color: black;" class="fas fa-sort-amount-up"></i></p>


						<div style="display: inline-block;">
							<button class="sort btn btn-primary roboto" data-sort="data" id="<i class='far fa-clock'></i> Data" value="default" style="display: inline-block;"><i class='far fa-clock'></i> <?php echo $home[4]; ?></button>
							<button class="sort btn btn-success roboto" data-sort="number" id="<i class='far fa-eye'></i> Vistas" value="default" style="display: inline-block;"><i class='far fa-eye'></i> <?php echo $home[5]; ?></button>
						</div>
					</div>
					<hr style="margin: 0;">
					<div class="row" style="margin: 0 -2% 0px -2%; background-color: rgba(255, 255, 255, 0.35); height: 100%;">

						<?php
						$query = mysqli_query($link, "SELECT * FROM historias WHERE id_user='".$_SESSION['login'][2]."' ORDER BY data DESC");
						echo "<div class='list'>";
						$conta = 0;
						if (mysqli_num_rows($query) > 0) {
							while ($info = mysqli_fetch_array($query)) {
								$foto_historia = $info['foto_historia'];
								$id_historia = $info['id_historia'];
								$archive_day = $info['archive_day'];
								$data = $info['data'];
								
								$date_db_show = date('d-m-Y H:m', strtotime($data));
								$timestamp = strtotime($date_db_show);

								$data_show = date('d/m', strtotime($data));

								$n_vistas = $info['n_vistas'];

								$link_href = "historia_pc.php?id=".$id_historia;

								if ( $detect->isMobile() ) {
									$link_href = "historia_mobile.php?id=".$id_historia;
								}
								if ($conta <= 2) {
									$text = "padding-top: 0;";
								} else {
									$test = "";
								}

								$old = $average_arredondado;
								$new = $n_vistas;
								$difference = (($new - $old) / $old) * 100;

								if ($difference > 0) {
									$difference = "<span style='color: #3fff3f;'>"."+".round($difference)."%</span>";
								} else {
									$difference = "<span style='color: #ff3f73;'>".round($difference)."%</span>";
								}

								if ($old == '0' || $new == '0') {
									$difference = '';
								}


								//<a href="'.$link_href.'" style="color: inherit; text-decoration: inherit;" id="link_id"></a>
								echo '

								<div class="column" style="cursor: pointer; '.$text.'" id="'.$id_historia.'">
								
								<div style="position: relative;">
								<div style="float: left; position: absolute; right: 0; bottom: 0; z-index: 1000; background-color: rgba(0, 0, 0, 0.6); padding: 1%; color: #FFFFFF; font-weight: bold;">
								'.$difference.'
								<span class="number" style="font-family: Lato;">'.$n_vistas.' <i class="far fa-eye"></i></span>
								</div>
								<div class="text-center" style="float: left;position: absolute;left: 5%;top: 3%;z-index: 1000;background-color: #ffffff;padding: 1%;color: black;padding: 5%;font-weight: bold;border-radius: 25%;">
								<span class="text-center" style="
								font-family: Lato;
								display: block;
								font-size: 80%;
								">'.$data_show.'</span>

								</div>

								<img src="'.$foto_historia.'" alt="'.$data.'" style="width:100%">
								</div>
								<!-- <p class="text-white text-center" style="background: black; font-family: Lato;">'.$data_show.'</p> -->
								<p style="display: none;" class="data">'.$timestamp.'</p>
								
								</div>

								';
								$conta++;
							} 
							echo "</div>";
						} else {
							echo "<h2 class='text-white' style='margin: 0 auto; padding: 5%;'>".$home[1]."</h2><p class='text-white' style='padding: 5%;'>".$home[2]."</p>";
						}
						?>

					</div>
				</div>
			</section> 


		</div>
		<a href="logout.php" class="btn btn-lg btn-danger" style="position: fixed; bottom: 1%; right: 1%; z-index: 1000; border-radius: 50%; "><i class="fas fa-sign-out-alt"></i></a>
	</div>

	<span style="position: fixed; top: 1%; left: 2.5%; padding: 2%; background: rgba(0, 0, 0, 0.4); display: none;" id="fechar_load"><i class="fas fa-arrow-left text-white"></i></span>


	<script>
		var options = {
			valueNames: [ 'number', 'data' ]
		};

		var userList = new List('historias_home', options);
	</script>

	<script type="text/javascript">
		function startRewardVideo(paramFromJS) {
			Android.startRewardVideoAndroidFunction(paramFromJS);
			$("#btn_ads").hide();
			$("#ul").show();
		}
	</script>

	<script>
		$(document).ready(function () {

			$(".sort").click(function(event) {
				var val = $(this).val();
				var id = $(this).id;
				if (val == "default" || val == "up") {
					$(this).html(this.id + " <i class='fas fa-sort-down'></i>");
					$(this).val("down");
				} else {
					$(this).html(this.id + " <i class='fas fa-sort-up'></i>");
					$(this).val("up");
				}
			});

			$(".column").click(function(event) {
				var id_historia = this.id;
				$("#change_g2").hide();
				$("#change_g1").show();
				$("#change_g1").load('historia_mobile2.php?id=' + id_historia + '.php');
				$("#fechar_load").show();
			});

			$("#fechar_load").click(function(event) {
				$("#change_g1").hide();
				$("#change_g1").empty();
				$("#fechar_load").hide();
				$("#change_g2").show();

			});

		});
	</script>
	<script>
		function pesquisar_pessoas() {
			var input, filter, ul, li, span, i, txtValue;
			input = document.getElementById("myInput2");
			filter = input.value.toUpperCase();
			ul = document.getElementById("ul");
			li = ul.getElementsByTagName("li");
    //span = li.getElementsByTagName("span");
    for (i = 0; i < li.length; i++) {
    	span = li[i].getElementsByTagName("span")[0];
    	if (span) {
    		txtValue = span.textContent || span.innerText;
    		if (txtValue.toUpperCase().indexOf(filter) > -1) {
    			li[i].style.display = "";
    		} else {
    			li[i].style.display = "none";
    		}
    	}       
    }
}
</script>

<?php
include 'footer.php';
?>
