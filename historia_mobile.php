<?php
include 'header.php';
if (isset($_GET['id'])) {
	$id_historia = htmlspecialchars(mysqli_real_escape_string($link, $_GET['id']));

	$query = mysqli_query($link, "SELECT * FROM historias WHERE id_historia='$id_historia'");
	$info = mysqli_fetch_assoc($query);
	$story_foto = $info['foto_historia'];
	$n_vistas = $info['n_vistas'];
	?>


	<div style="height: 100%; width: 100%;" id="div">
		<div style="position: absolute; left: 0; width: 100%; height: 100%;" id="myElement">
			<div style="width: 100%; height: 100%;">
				<img src="<?php echo $story_foto; ?>" style="width: 100%; height: 100%;" value="default">
				<p class="roboto" style="position: absolute; bottom: 1%; right: 2%; color: white; z-index: 100;" id="n_vistas_p"><i class="far fa-eye"></i> <?php echo $n_vistas; ?></p>
			</div>

		</div>

		<div class="card" id="card" style="border: 0;">

			<div class="card-header" style="padding: 0; border: 0;">

				<li class="list-group-item" style="padding-left: 2%; padding-right: 1%;">
					<div style="width: 19%; display: inline-block;">
						<span class="roboto"><i class="far fa-eye"></i> <?php echo $n_vistas; ?></span>
					</div>
					<div style="width: 79%; display: inline-block;">
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
						echo '

						<li style="cursor: pointer; padding: 3%; height: 74px; position: relative; border: 0px;" class="list-group-item visitar_perfil">
						<div style="display: inline-block; float: left; width: 12%;">
						<img src="'.$foto_perfil.'" style="width: 100%;border-radius: 50%; display: block;">
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
			<a href="home.php" style="position: fixed; top: 1%; left: 2.5%; padding: 2%; background: rgba(0, 0, 0, 0.4);">
				<i class="fas fa-arrow-left text-white"></i>
			</a>
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