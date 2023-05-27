<?php
include 'ligar_db.php';
include 'translations.php';
if (isset($_GET['id'])) {
	$id_historia = htmlspecialchars(mysqli_real_escape_string($link, $_GET['id']));

	$query = mysqli_query($link, "SELECT * FROM historias WHERE id_historia='$id_historia'");
	$info = mysqli_fetch_assoc($query);
	$story_foto = $info['foto_historia'];
	$n_vistas = $info['n_vistas'];

	$avg_query = mysqli_query($link, "SELECT AVG(n_vistas) AS n_vistas_avg FROM historias WHERE id_user='".$_SESSION['login'][2]."'");
	$info_avg = mysqli_fetch_assoc($avg_query);
	$avg_views = $info_avg['n_vistas_avg'];

	$old = $avg_views;
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
	?>


	<div style="height: 100%; width: 100%;" id="div">
		<div style="position: absolute; left: 0; width: 100%; height: 100%;" id="myElement">
			<div style="width: 100%; height: 100%;">
				<img src="<?php echo $story_foto; ?>" style="width: 100%; height: 100%;" value="default">
				<div id="n_vistas_p" style="color: white; position: fixed;bottom: 0;width: 100% !important;z-index: 100;background: rgba(0, 0, 0, 0.4);padding: 2%;
				">
				<span class="text-center Neue-bold" style="margin-left: 40%;transform: translateX(-40%);">Swipe Up <i class="fas fa-level-up-alt"></i></span>
				
				<?php echo $difference; ?>
				<span class="roboto" style="position: fixed;/* position: absolute; *//* bottom: 1%; *//* right: 2%; *//* color: white; *//* z-index: 100; *//* position: absolute; *//* margin: 0; *//* margin-left: 83%; */left: 80%;/* transform: translateX(-80%); */"><i class="far fa-eye"></i> <?php echo $n_vistas; ?></span>
			</div>
		</div>

	</div>

	<div class="card" id="card" style="border: 0;">

		<div class="card-header" style="padding: 0; border: 0;">

			<li class="list-group-item" style="padding-left: 2%; padding-right: 1%;">
				<div style="width: 16%; display: inline-block;">
					<span class="roboto" style="font-family: Lato, sans-serif;font-weight: bold;"><i class="far fa-eye"></i> <?php echo $n_vistas; ?></span>
				</div>
				<div style="width: 82%; display: inline-block;">
					<input type="text" id="myInput2" onkeyup="pesquisar_pessoas()" placeholder="<?php echo $search; ?>" class="form-control roboto" style="width: 100%">
				</div>

			</li>

		</div>

		<div class="card-body" style="overflow-y: scroll; width: 100%; padding: 0; height: 100%;">

			<ul class="list-group" id="ul" style="height: 100%;">

				<?php
				$query = mysqli_query($link, "SELECT * FROM views INNER JOIN viewers ON views.id_viewer = viewers.id_viewer WHERE views.id_historia='$id_historia'");
				$n_vistas = mysqli_num_rows($query);
		  //echo '
		  //<li style="cursor: pointer;" class="list-group-item visitar_perfil text-right"><h5><i class="far fa-eye"></i> '.$n_vistas.'</h5></li>
		  //';
				while ($info_views = mysqli_fetch_array($query)) {
					$username_ig = $info_views['username_viewer'];
					$nome_ig = $info_views['nome_viewer'];
					$foto_perfil = $info_views['foto_viewer'];

					/*

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $foto_perfil);
    				// don't download content
					curl_setopt($ch, CURLOPT_NOBODY, 1);
					curl_setopt($ch, CURLOPT_FAILONERROR, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

					$result = curl_exec($ch);
					curl_close($ch);
					if($result !== FALSE)
					{
						//return true;
					}
					else
					{
						$foto_perfil = 'avatar.png';
					}
					curl_close($ch);
					*/

					echo '

					<li style="cursor: pointer; padding: 3%; height: 74px; position: relative; border: 0px;" class="list-group-item visitar_perfil">
					<div style="display: inline-block; float: left; width: 12%;">
					<img src="'.$foto_perfil.'" style="width: 100%;border-radius: 50%; display: block;" onerror="imgError(this);">
					</div>
					<div class="roboto" style="display: inline-block; width: 80%;">
					<span class="mb-1" style=" padding-left: 4%; font-size: 16px;">'.$username_ig.'</span><br>
					<span class="text-secondary" style="padding-left: 4%;">'.$nome_ig.'</span>

					</div>
					<div style="position: absolute;top: 23%;right: 2%;">
					<a href="https://www.instagram.com/'.$username_ig.'" class="text-right btn btn-primary" target="_blank" style=" color: inherit; text-decoration: inherit;"><i style="color: white;" class="fas fa-user"></i></a>
					</div>
					</li>
					';
				}
				?>


				<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
				<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
				<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li></ul>
			</div>
		</div>

	</div>

	<script src="hammer.min.js"></script>
	<script>
		$(document).ready(function(){
			$("#nav_menu").hide();
			var myElement = document.getElementById('myElement');
			var modo = $("#myElement").val();
// create a simple instance
// by default, it only adds horizontal recognizers
var mc = new Hammer(myElement);

// let the pan gesture support all directions.
// this will block the vertical scrolling on a touch-device while on the element
mc.get('pan').set({ direction: Hammer.DIRECTION_ALL });

// listen to events...
mc.on("panup pandown", function(ev) {
    //myElement.textContent = ev.type +" gesture detected.";
    if (ev.type == "panup") {
    	console.log("UP");
    	$("#body").css('background', 'white');
    	$("#card").show();
    	$("#n_vistas_p").hide();
    	$("#myElement").css('transform', 'translate(-50%)');
    	/*
    	$("#myElement").css({
    		width: '90px',
    		height: '140px',
    		left: '53%',
    		top: '3%'
    	});
    	*/
    	$("#myElement").css({
    		left: "53%", 
    		top: "3%"
    	});
    	$("#myElement").animate({width: "90px", height: "140px"}, 250);


    	//left: 50%;transform: translate(-50%);
    	
    	//$("#myElement").animate({});
    } else if (ev.type == "pandown") {
    	console.log("DOWN");
    	$("#card").hide();
    	$("#n_vistas_p").show();
    	/*
    	$("#myElement").css({
    		width: '100%',
    		height: '100%',
    		left: '0px',
    		top: '0'
    	});
    	*/
    	
    	$("#myElement").animate({width: "100%", height: "100%"}, 200);
    	$("#myElement").css({
    		left: '0',
    		top: '0',
    		transform: 'none'
    	});
    	//$("#myElement").css('transform', 'none');
    	
    }
});


});

		function imgError(image) {
			image.onerror = "";
			image.src = "avatar.png";
			return true;
		}
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
}
include 'footer.php';
?>