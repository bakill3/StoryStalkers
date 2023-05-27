<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'header.php';

$username = 'landscapes.worldwidel';
$password = 'gabriel124';
$debug = false;
$truncatedDebug = false;
//////////////////////
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
  $ig->login($username, $password);

  $captions = array("Whatever is good for your soul, do that", "Even the stars were jealous of the sparkle in her eyes", "Stress less and enjoy the best", "Get out there and live a little", "I’m not high maintenance, you’re just low effort", "I’m not gonna sugar coat the truth, I’m not Willy Wonka", "Life is better when you’re laughing", "Look for the magic in every moment", "Vodka may not be the answer but it’s worth a shot", "A sass a day keeps the basics away", "You can regret a lot of things but you’ll never regret being kind", "Do whatever makes you happiest", "Having the dream is easy, making it come true is hard – Serena Williams", "Decluttering my life like Marie Kondo", "If I were rich, I’d pull a Netflix and spend $100 million on my Friends", "In 2019, I want to be as Insta famous as an egg and as ageless as Paul Rudd", "Can’t hear, can’t speak, can’t see.", "Be heroes of your own stories – Constance Wu", "Hella fine and it works every time – Ariana Grande", "My life is as crooked as Rami Malek’s bowtie", "We did it!", "Work hard then work harder", "Hustlin’", "When daydreams become reality", "Say yes, take risks, and live life on your own terms", "The impossible is now possible", "Perseverance pays… a lot!", "It wasn’t always easy but it’s worth it", "Pursue your passion and you’ll never work a day in your life", "Entrepreneur life", "Yes or No?", "How can I help you?", "What do you think of this?", "How gorgeous is this?", "If you could be doing anything right now, what would it be?", "Which [fill in the blank] are you?", "Trivia Time: How many people have tried [fill in the blank]?", "How are you guys doing?", "Miss me?", "What time is it?", "Don’t trust everything you see, even salt can look like sugar", "Everyone has a chapter they don’t read out loud", "Too lit to quit", "Shoutout to myself because I’m lit", "I don’t tell you I love you out of habit but as a reminder of how much you mean to me", "When I started counting my blessings, I realized I have everything I could ever need", "A smile can change the world", "The biggest mistake you can ever make is to be afraid to make mistakes", "Life is tough but so am I", "My coach said I run like a girl. So I said if he ran a little faster he could too. – Mia Hamm", "Oops is always better than what if", "If you want opportunity to knock, it’s time to build a door", "The little things in life matter", "If you don’t believe in yourself, who will?", "I may not be there yet, but I’m closer than I was yesterday", "It always seems impossible until it’s done", "It may be stormy now but it never rains forever", "There’s a dream that I’ve been chasing want so badly for it to be reality – Justin Bieber", "Dreams don’t have expiration dates, keep going.", "Set goals you don’t tell anyone about. Achieve them. Then give yourself the highest of fives!", "Me", "Us", "Love", "YES!", "Ugh…", "Heaven", "Speechless.", "OMG!", "Surprise!", "Awkward…", "Feelin’ hot, hot, hot", "Sea, Sun and Smiles 🙂", "Summer lovin’ happened so fast", "Chasin’ the sun", "Blue skies, high tides and good vibes", "Hello sunshine!", "Life’s a beach", "I never want summer to end", "Eat, tan, sleep, repeat", "This is my resting beach face", "Act like you own the world because you do", "As long as my bank account keeps growing, I couldn’t care less about anything else", "I keep it real because I’m not afraid of having enemies", "…um ok", "You were my cup of tea but I drink wine now", "Ambition on fleek", "I wouldn’t chase you because I’m the catch", "Know your worth but don’t forget to add tax", "I’m the reason why I smile everyday", "I don’t care if you approve of me, I approve of myself", "I’m not a businessman, I’m a business, man", "You only get one shot, do not miss your chance to blow. This opportunity comes once in a lifetime.", "California love", "We gonna party like it’s your birthday", "I got 99 problems, but [fill in the blank] ain’t one", "The more money we come across, the more problems we see", "Big poppa", "Drop it like it’s hot", "Only God Can Judge Me", "I’m feelin’ myself", "To live doesn’t mean you’re alive", "I’ve loved and I’ve lost but that’s not what I see – Ariana Grande", "I need somebody who can take control – Sam Smith", "Keep switchin’ your alibi, or stutterin’ when you reply. You can’t even look me in the eye. Oh, I can tell, I know you’re lyin’ – The Chainsmokers", "Farewell tequila, so long margarita. And lady sativa, I hate to leave ya – Bebe Rexha", "You know what you’re doin’ to me – Ariana Grande", "It’s a hard time finding your freedom – Slushii", "I would sell my soul for a bit more time – Halsey", "Always wanted to be one of those people in the room that says something and everyone puts their hand up – Julia Michaels feat. Selena Gomez", "I’m a babe, I’m a boss and I’m makin’ this money – Avril Lavigne feat. Nicki Minaj");



  $hashtags = array("#ig_exquisite ","#fantastic_earth ","#nature_wizards ","#landscape ","#landscape_capture ","#landscape_captures ","#landscape_hunter ","#landscape_lover ","#landscape_lovers ","#landscape_photography ","#landscapehunter","#landscapelover","#landscapelovers ","#landscapephotography","#landscapephotomag","#landscapephotos ","#landscapes ","#landscapeshot","#landscapeslovers","#landscape_specialist","#natgeo","#nationalgeographic","#naturephotography","#nature_prefection ","#ourplanetdaily","#landscape_lovers","#sky_captures","#landscapephotography","#fantastic_earth","#landscape_captures","#ic_landscapes","#ig_exquisite ","#nature_wizards ","#nature_shooters ","#landscapestyles_gf ","#ourplanetdaily ","#landscapehunter ","#special_shots ","#naturediversity ","#landscapelovers","#earth_deluxe ","#instanaturelover ","#nature_prefection ","#nature_brilliance ","#gottalove_a_ ","#allnatureshots ","#EarthVisuals ","#welivetoexplore    ","#canonphotos","#canoneos","#canonrebel","#canonphotographer","#canonphotography","#kodak ","#kodakfilm","#kodak_photo ","#filmisnotdead ","#expofilm","#nikonphotography","#nikontop","#nikon_photography_","#nikon_photography","#nikkor","#androidography","#androidnesia","#androidinstagram ","#instaandroid ","#teamdroid  ","#iphoneography","#iphoneonly ","#iphonesia ","#iphoneography ","#iphonephotography");
  //shuffle($hashtags);

  //echo sub_str(implode('',$hashtags),0,6);
  $rand_hash = array_rand($hashtags, 30);
  $rand_captions = array_rand($captions, 1);


  $landscapes = "";
  for ($i=0; $i < 30; $i++) { 
    $landscapes .= $hashtags[$rand_hash[$i]]." ";
  }
  //echo $landscapes;

  //$photoFilename = 'foto.jpg';
  //print_r($captions[0]);


  $captionText = "".$captions[$rand_captions]." \n.\n.\n.\n.\n.\n.\n.\n.\n\n".$landscapes."";
  echo $captionText;

  $imagesDir = 'instaviews.socialsivex.com/fotos_bot/';

  $images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

  $photoFilename = $images[array_rand($images)]; // See comments
  echo $photoFilename;

  

  $photo = new \InstagramAPI\Media\Photo\InstagramPhoto($photoFilename);
  $ig->timeline->uploadPhoto($photo->getFile(), ['caption' => $captionText]);

  $metadata = ['caption' => 'Test \n\n#lol'];

  $ig->timeline->uploadPhoto($photoFilename, $metadata);

  

} catch (\Exception $e) {
  echo 'Something went wrong: '.$e->getMessage()."\n";
    //exit(0);
}
unlink($photoFilename);