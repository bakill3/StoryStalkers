<?php
include 'ligar_db.php';
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;


if (!isset($_SESSION['language'])) {
  $ip = $_SERVER['REMOTE_ADDR'];
  $res = file_get_contents('https://www.iplocate.io/api/lookup/'.$ip.'');

  $res = json_decode($res);
  $country = $res->country;
  if ($country == "Portugal" || $country == "Brasil") {
    $_SESSION['language'] = "pt";
  } else {
    $_SESSION['language'] = "en";
  }
}
include 'translations.php';
?>
<!DOCTYPE html>
<html>
<head>
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta charset="utf-8">

  <link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
  <link rel="manifest" href="favicon/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">

  <link rel="stylesheet" type="text/css" href="bootstrap4/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="bootstrap4/bootstrap-grid.min.css">
  <link rel="stylesheet" type="text/css" href="bootstrap4/bootstrap-reboot.min.css">
  <script src="jquery.min.js"></script>
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
  body {
    font-family: 'IBM Plex Sans', sans-serif;
    background: url(background.png);
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center;
    background-size: cover;
  }
</style>
</head>
<body style="font-family: Neue-regular; !important" id="body">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="nav_menu">
    <?php 
    if (isset($_SESSION['login'])) {
      echo '<a class="navbar-brand" href="home.php" style="font-family: \'Pacifico\', cursive;">Story Stalkers</a>'; 
    } else {
      echo '<a class="navbar-brand" href="index.php" style="font-family: \'Pacifico\', cursive;">Story Stalkers</a>'; 
    }
    ?>
    
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <?php 
          if (isset($_SESSION['login'])) {
            echo '<a class="nav-link" href="home.php">'.$nav[0].' <span class="sr-only">(current)</span></a>'; 
            if ($_SESSION['login'][2] == '7') {
              echo '<a class="nav-link" href="admin.php">Admin Panel</a>'; 
            }
          } else {
            echo '<a class="nav-link" href="index.php">'.$nav[0].' <span class="sr-only">(current)</span></a>'; 
          }
          ?>
        </li>
      </ul>
      <?php 
      if (isset($_SESSION['login'])) {
        ?>
        <form class="form-inline my-2 my-lg-0">
          <a href="logout.php" class="btn btn-outline-danger my-2 my-sm-0"><?php echo $nav[1]; ?></a>
        </form>
        <?php
      }
      ?>
    </div>
  </nav>

  <div class="container" style="height: 100%;" id="container">

    