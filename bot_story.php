<?php
use InstagramAPI\Utils;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'header.php';

$username = 'landscapes.worldwidel';
$password = 'gabriel124';
$debug = false;
$truncatedDebug = false;


//mediaId = '1772128724164452834'; // Only Media PK for this scenario.


$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}


// Now create the metadata array:

try {
    $imagesDir = 'stories_bot/';

    $images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    $photoFilename = $images[array_rand($images)];
    //$photoFilename = "stories_bot/9k_.jpeg";


    $photo = new \InstagramAPI\Media\Photo\InstagramPhoto($photoFilename, ['targetFeed' => \InstagramAPI\Constants::FEED_STORY]);

    $ig->story->uploadPhoto($photo->getFile());

} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}



?>