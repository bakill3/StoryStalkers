<?php
include 'header.php';
if (isset($_GET['id'])) {
  $id_historia = htmlspecialchars(mysqli_real_escape_string($link, $_GET['id']));

  $query = mysqli_query($link, "SELECT * FROM historias WHERE id_historia='$id_historia'");
  $info = mysqli_fetch_assoc($query);
  $story_foto = $info['foto_historia'];
  //$id_historia = $info['id_historia'];

  echo "<div style='height: 95%;'>";
  echo "<div class='card' style='width: 50%; display: inline-block; height: 100%; border-color: #343a40; border-radius: 0px;'>
  <div class='card-header text-center' style='background: #343a40; border-radius: 0px;'>
  <h1 class='font-weight-bold Neue-bold text-white'>História</h1>
  </div>
  <div class='card-body text-center' style='/* background: linear-gradient(to right, #405DE6, #5851DB, #833AB4, #C13584, #E1306C); height: 91%; */ background: #343a40; height: 91%;''>
  <img src='$story_foto' style='width: 86%;'>
  </div>
  </div>";

  echo '<div class="card" style="width: 50%; vertical-align: top; height: 100%; display: inline-block; height: 100%; border-color: #343a40; border-radius: 0px;">
  <div class="card-header text-center" style="background: #343a40; border-radius: 0px;">
  <h1 class="font-weight-bold Neue-bold text-white">Vistas na história</h1>
  </div>
  <div class="card-body" style="/* background: linear-gradient(to right, #FD1D1D, #F56040, #F77737, #FCAF45, #FFDC80);*/ background: #343a40; height: 91%; overflow-y: scroll;">
  <li class="list-group-item"><input type="text" id="myInput2" onkeyup="pesquisar_pessoas()" placeholder="Pesquisar Pessoas..." class="form-control"></li>
  <ul class="list-group" id="ul">
  
  ';

  $maxId = null;

  $query = mysqli_query($link, "SELECT * FROM views INNER JOIN viewers ON views.id_viewer = viewers.id_viewer WHERE views.id_historia='$id_historia'");
  $n_vistas = mysqli_num_rows($query);
  echo '
  <li style="cursor: pointer;" class="list-group-item visitar_perfil text-right"><h5><i class="far fa-eye"></i> '.$n_vistas.'</h5></li>
  ';
  while ($info_views = mysqli_fetch_array($query)) {
    $username_ig = $info_views['username_viewer'];
    $nome_ig = $info_views['nome_viewer'];
    $foto_perfil = $info_views['foto_viewer'];
    echo '
    <a href="https://www.instagram.com/'.$username_ig.'" target="_blank" style=" color: inherit;  text-decoration: inherit;">
    <li style="cursor: pointer;" class="list-group-item visitar_perfil">
    <img src="'.$foto_perfil.'" style="width: 14%; border-radius: 50%; display: inline-block;">
    <span class="mb-1" style=" margin-left: 1.5%; font-size: 16px;"><b>'.$nome_ig.'</b> (@'.$username_ig.')</p><br></span></li></a>
    ';
  }
  echo '<li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
  <li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>
  <li class="list-group-item">&nbsp;&nbsp;<p>&nbsp;&nbsp;&nbsp;&nbsp;</p></li>';
  echo "</ul></div>
  </div></div>";
  echo '
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
  ';

}
include 'footer.php';