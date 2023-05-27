<script src="sweetalert2.min.js"></script>
<link rel="stylesheet" href="sweetalert2.min.css">
<script src="jquery.min.js"></script>
<?php
use InstagramAPI\Exception\ChallengeRequiredException;
use InstagramAPI\Instagram;
use InstagramAPI\Response\LoginResponse;

require __DIR__.'/vendor/autoload.php';
\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'ligar_db.php';
//set_time_limit(0);
//date_default_timezone_set('UTC');




if (!isset($_SESSION['language'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $res = file_get_contents('https://www.iplocate.io/api/lookup/'.$ip.'');

    $res = json_decode($res);
    $country = $res->country;
    if ($country == "Portugal" || $country == "Brasil") {
        $_SESSION['language'] = "pt";
    } elseif ($country == "Indonesia") {
        $_SESSION['language'] = "indonesia";
    } elseif ($country == "Poland") { 
        $_SESSION['language'] = "poland";
    } elseif ($country == "Spain") { 
        $_SESSION['language'] = "spain";
    } elseif ($country == "India") { 
        $_SESSION['language'] = "india";
    } else {
        $_SESSION['language'] = "en";
    }
}
include 'translations.php';

date_default_timezone_set('Europe/Lisbon');

if (isset($_SESSION['login'])) {
    //header('Location: home.php');
    echo "<script>window.location.href='home.php';</script>";
} else {
    /*
    if(isset($_COOKIE['member_id'])) {
        $username = $_COOKIE["member_login"];
    //$password = $_COOKIE["member_password"];
        $cookie = "cookie";
        $id_user = $_COOKIE["member_id"];

        session_destroy();
        $_SESSION['login'] = array($username, $cookie, $id_user);
        //header('Location: home.php');
        echo "<script>window.location.href='home.php';</script>";
    }
    */
}

if (isset($_POST['login'])) {

    $username = htmlspecialchars(mysqli_real_escape_string($link, $_POST['username'])); //PROTEGER INPUTS
    $password = htmlspecialchars(mysqli_real_escape_string($link, $_POST['password'])); //PROTEGER INPUTS
    $code = htmlspecialchars(mysqli_real_escape_string($link, $_POST['code']));

    $verification_method = 1;   //0 = SMS, 1 = Email

    //FUNCTIONS
    class ExtendedInstagram extends Instagram {
        public function changeUser( $username, $password ) {
            $this->_setUser( $username, $password );
        }
    }

    function readln( $prompt ) {
        if ( PHP_OS === 'WINNT' ) {
            echo "$prompt ";

            return trim( (string) stream_get_line( STDIN, 6, "\n" ) );
        }

        return trim( (string) readline( "$prompt " ) );
    }

    if (!empty($username) && !empty($password)) { //SE OS INPUTS NÃO TIVEREM VAZIOS
        $debug = false;
        $truncatedDebug = false;

        
        $ig = new ExtendedInstagram();
        //$ig->setProxy("http://68.183.100.171:80");

        try { //SE FIZER LOGIN NO INSTA COM SUCESSO 
            $loginResponse = $ig->login( $username, $password );
            $login = 1;
        } catch ( Exception $exception ) {

            /*
            $_SESSION['erro'] = "Username/Password Incorrect";
            echo "<script>console.log('Username/Password Incorrect #1');</script>";
            header('Location: index.php');
            exit(0);
            */
            $response = $exception->getResponse();
            //echo "<pre>";
            //print_r(json_decode($response));
            //echo "</pre>";
            //die();

            if ($exception instanceof ChallengeRequiredException) {

                if(empty($code)) {
                    echo "<script>
                    $(document).ready(function() {
                        $('#code').show();
                    });</script>";
                }

                sleep(3);
                die(print_r(json_decode($response->getChallenge()->getApiPath())));

                $checkApiPath = substr( $response->getChallenge()->getApiPath(), 1);
                //SAVE IN ARRAY
                $customResponse = $ig->request($checkApiPath)
                ->setNeedsAuth(false)
                ->addPost('choice', $verification_method)
                ->addPost('_uuid', $ig->uuid)
                ->addPost('guid', $ig->uuid)
                ->addPost('device_id', $ig->device_id)
                ->addPost('_uid', $ig->account_id)
                ->addPost('_csrftoken', $ig->client->getToken())
                ->getDecodedResponse();

                //$_SESSION['erro'] = "Email Verification code sent to you";
                /*
                echo "
                <script>
                Swal.fire(
                'Instagam Verification Code',
                'It was sent a verification code to your email so that you can login on the app.',
                'info'
                )
                </script>
                ";
                */
                $_SESSION['n_word'] = "Email Verification code sent to you";
                setcookie ("member_login",$username,time()+ (10 * 365 * 24 * 60 * 60));
                setcookie ("member_password",$password,time()+ (10 * 365 * 24 * 60 * 60));
                $login = 0;


            } else {
                $_SESSION['erro'] = "Username/Password Incorrect #1";
                $login = 0;
            }
            //CODE != 0
            if (!empty($code)) {
                try {

                    if ($customResponse['status'] === 'ok' && $customResponse['action'] === 'close') {
                        echo 'Checkpoint bypassed';
                        //exit();
                    }

                //$code = readln( 'Code that you received via ' . ( $verification_method ? 'email' : 'sms' ) . ':' );
                    $ig->changeUser( $username, $password );
                    $customResponse = $ig->request($checkApiPath)
                    ->setNeedsAuth(false)
                    ->addPost('security_code', $code)
                    ->addPost('_uuid', $ig->uuid)
                    ->addPost('guid', $ig->uuid)
                    ->addPost('device_id', $ig->device_id)
                    ->addPost('_uid', $ig->account_id)
                    ->addPost('_csrftoken', $ig->client->getToken())
                    ->getDecodedResponse();

                    if ($customResponse['status'] === 'ok' && (int) $customResponse['logged_in_user']['pk'] === (int) $user_id ) {
                        echo 'Finished, logged in successfully! Run this file again to validate that it works.';
                        //$_SESSION['erro'] = "Finished, logged in successfully! Run this file again to validate that it works.";
                        $login = 1;
                    } else {
                        echo "Probably finished...\n";
                        //$_SESSION['erro'] = "Probably finished...";
                        //var_dump( $customResponse );
                        $login = 1;
                    }

                } catch ( Exception $ex ) {
                    echo $ex->getMessage();
                }
            }
            //$login = 0;
        }

        if ($login == 1) {
            $id_user_ig = $ig->people->getUserIdForName($username);
            $info_user = $ig->people->getInfoById($id_user_ig);
            $profile_pic = $info_user->getUser()->getProfile_pic_url();
            $followers = $info_user->getUser()->getFollower_count();
            $following = $info_user->getUser()->getFollowing_count();

            $query = mysqli_query($link, "SELECT * FROM users WHERE username='$username'") or die(mysqli_error($link)); //VERIFICA SE ESSE USER JÁ ESTÁ NA NOSSA BASE DE DADOS

            if (mysqli_num_rows($query) == 0) { // SE SE ESSE USER NÃO TIVER NA NOSSA BASE DE DADOS

                $method = "AES-256-CBC";
                $secretHash = "camachoeummerdas.com";
                $pass_hash = openssl_encrypt($password, $method, $secretHash);
            //$pass_hash = password_hash($password, PASSWORD_DEFAULT); //ENCRIPTAR PASSWORD
            mysqli_query($link, "INSERT INTO users(username, password, profile_pic, followers, following) VALUES('$username', '$pass_hash', '$profile_pic', '$followers', '$following')") or die(mysqli_error($link)); //INSERE O USER
            $id_user = mysqli_insert_id($link);
            $_SESSION['login'] = array($username, $password, $id_user); //GUARDAR AS INFOS DO USER PARA SEREM USADAS NO SCRIPT (home.php)
            //echo "<script>window.location.href='home.php';</script>";
            header('Location: home.php');
            exit(0);
        } else {
            $info = mysqli_fetch_assoc($query);
            $id_user = $info['id'];
            $pass_db = $info['password'];
            $method = "AES-256-CBC";
            $secretHash = "camachoeummerdas.com";

            $pass_hash = openssl_decrypt($pass_db, $method, $secretHash);

            if ($password == $pass_hash) {

                $_SESSION['login'] = array($username, $password, $id_user);
                setcookie ("member_login",$username,time()+ (10 * 365 * 24 * 60 * 60));  
                setcookie ("member_id",$id_user,time()+ (10 * 365 * 24 * 60 * 60));
                setcookie ("member_password",$password,time()+ (10 * 365 * 24 * 60 * 60));

                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Lisbon'));
                $date_agora = $now->format('Y-m-d H:i:s');


                $ip_do_user = $_SERVER['REMOTE_ADDR'];
                mysqli_query($link, "UPDATE users SET followers='$followers', following='$following', profile_pic='$profile_pic' WHERE id='$id_user'");
                mysqli_query($link, "INSERT INTO data_logins (id_user, data, ip) VALUES('$id_user', '$date_agora', '$ip_do_user')");
                header('Location: home.php');
                exit(0);

            } else {
                $_SESSION['erro'] = "Username/Password Incorrect #2";
                echo "<script>console.log('Username/Password Incorrect #1');</script>";
                //header('Location: index.php');
                //exit(0);
            }
        }
    } else {
        $_SESSION['erro'] = "Username/Password Incorrect #2";
        echo "<script>console.log('Username/Password Incorrect #1');</script>";
    }
}
}



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






</head>

<body style="width: 100% !important; height: 100% !important;">
    <div class="preloader-wrapper">
        <div class="preloader">
            <img src="preloader.gif" alt="NILA">
        </div>
    </div>

    <div class="wrapper" style="width: 100% !important; height: 100% !important; margin: 0 auto !important;">
        <h1 class="h1_menu">Menu</h1>
        <a class="menu-btn" onclick="toggleMenu()"></a>
        <section class="one" onclick="goToPage(0)" style="overflow-y: scroll;">
            <h1 class="h1_menu"><?php echo $index[1]; ?></h1>
            <hr style="margin-top: 0;">

            <div class="text-center">
                <div class="card" style="width: 17rem; display: inline-block;">
                    <div class="card-body">
                        <div class="text-center"><i class="fab fa-instagram fa-5x"></i></div>
                        <h5 class="card-title text-center Neue-bold"><?php echo $index[1]; ?></h5>
                        <!-- <h6 class="card-subtitle mb-2 text-muted">Instagram Related</h6> -->
                        <p class="card-text Neue-regular text-center"><?php echo $index[2]; ?></p>
                    </div>
                </div>
                <div class="card" style="width: 17rem; display: inline-block;">
                    <div class="card-body">
                        <div class="text-center"><i class="fas fa-user-secret fa-5x"></i></div>
                        <h5 class="card-title text-center Neue-bold"><?php echo $index[3]; ?></h5>
                        <p class="card-text Neue-regular text-center"><?php echo $index[4]; ?></p>
                    </div>
                </div>
                <div class="card" style="width: 17rem; display: inline-block;">
                    <div class="card-body">
                        <div class="text-center"><i class="fas fa-euro-sign fa-5x"></i></div>
                        <h5 class="card-title text-center Neue-bold"><?php echo $index[5]; ?></h5>
                        <p class="card-text Neue-regular text-center"><?php echo $index[6]; ?></p>
                    </div>
                </div>
                <div class="card" style="width: 17rem; display: inline-block;">
                    <div class="card-body">
                        <div class="text-center"><i class="fas fa-shield-alt fa-5x"></i></div>
                        <h5 class="card-title text-center Neue-bold"><?php echo $index[7]; ?></h5>
                        <p class="card-text Neue-regular text-center"><?php echo $index[8]; ?></p>
                    </div>
                </div>

            </div>
        </section>

        <section class="two" onclick="goToPage(1)" style="overflow-y: scroll;">
            <h1 class="h1_menu"><?php echo $new_index[1]; ?></h1>
            <hr style="margin-top: 0;">
            <!--TERMOS-->
            <div class="text-white">
                <h2><strong>Terms and Conditions</strong></h2>

                <p>Welcome to Story Stalkers!</p>

                <p>These terms and conditions outline the rules and regulations for the use of Story Stalkers's Website, located at https://instaviews.socialsivex.com.</p>

                <p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Story Stalkers if you do not agree to take all of the terms and conditions stated on this page.</p>

                <p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: "Client", "You" and "Your" refers to you, the person log on this website and compliant to the Company’s terms and conditions. "The Company", "Ourselves", "We", "Our" and "Us", refers to our Company. "Party", "Parties", or "Us", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client’s needs in respect of provision of the Company’s stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>

                <h3><strong>Cookies</strong></h3>

                <p>We employ the use of cookies. By accessing Story Stalkers, you agreed to use cookies in agreement with the Story Stalkers's Privacy Policy.</p>

                <p>Most interactive websites use cookies to let us retrieve the user’s details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies.</p>

                <h3><strong>License</strong></h3>

                <p>Unless otherwise stated, Story Stalkers and/or its licensors own the intellectual property rights for all material on Story Stalkers. All intellectual property rights are reserved. You may access this from Story Stalkers for your own personal use subjected to restrictions set in these terms and conditions.</p>

                <p>You must not:</p>
                <ul>
                    <li>Republish material from Story Stalkers</li>
                    <li>Sell, rent or sub-license material from Story Stalkers</li>
                    <li>Reproduce, duplicate or copy material from Story Stalkers</li>
                    <li>Redistribute content from Story Stalkers</li>
                </ul>

                <p>This Agreement shall begin on the date hereof.</p>

                <p>Parts of this website offer an opportunity for users to post and exchange opinions and information in certain areas of the website. Story Stalkers does not filter, edit, publish or review Comments prior to their presence on the website. Comments do not reflect the views and opinions of Story Stalkers,its agents and/or affiliates. Comments reflect the views and opinions of the person who post their views and opinions. To the extent permitted by applicable laws, Story Stalkers shall not be liable for the Comments or for any liability, damages or expenses caused and/or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>

                <p>Story Stalkers reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive or causes breach of these Terms and Conditions.</p>

                <p>You warrant and represent that:</p>

                <ul>
                    <li>You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</li>
                    <li>The Comments do not invade any intellectual property right, including without limitation copyright, patent or trademark of any third party;</li>
                    <li>The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material which is an invasion of privacy</li>
                    <li>The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</li>
                </ul>

                <p>You hereby grant Story Stalkers a non-exclusive license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</p>

                <h3><strong>Hyperlinking to our Content</strong></h3>

                <p>The following organizations may link to our Website without prior written approval:</p>

                <ul>
                    <li>Government agencies;</li>
                    <li>Search engines;</li>
                    <li>News organizations;</li>
                    <li>Online directory distributors may link to our Website in the same manner as they hyperlink to the Websites of other listed businesses; and</li>
                    <li>System wide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</li>
                </ul>

                <p>These organizations may link to our home page, to publications or to other Website information so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products and/or services; and (c) fits within the context of the linking party’s site.</p>

                <p>We may consider and approve other link requests from the following types of organizations:</p>

                <ul>
                    <li>commonly-known consumer and/or business information sources;</li>
                    <li>dot.com community sites;</li>
                    <li>associations or other groups representing charities;</li>
                    <li>online directory distributors;</li>
                    <li>internet portals;</li>
                    <li>accounting, law and consulting firms; and</li>
                    <li>educational institutions and trade associations.</li>
                </ul>

                <p>We will approve link requests from these organizations if we decide that: (a) the link would not make us look unfavorably to ourselves or to our accredited businesses; (b) the organization does not have any negative records with us; (c) the benefit to us from the visibility of the hyperlink compensates the absence of Story Stalkers; and (d) the link is in the context of general resource information.</p>

                <p>These organizations may link to our home page so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and (c) fits within the context of the linking party’s site.</p>

                <p>If you are one of the organizations listed in paragraph 2 above and are interested in linking to our website, you must inform us by sending an e-mail to Story Stalkers. Please include your name, your organization name, contact information as well as the URL of your site, a list of any URLs from which you intend to link to our Website, and a list of the URLs on our site to which you would like to link. Wait 2-3 weeks for a response.</p>

                <p>Approved organizations may hyperlink to our Website as follows:</p>

                <ul>
                    <li>By use of our corporate name; or</li>
                    <li>By use of the uniform resource locator being linked to; or</li>
                    <li>By use of any other description of our Website being linked to that makes sense within the context and format of content on the linking party’s site.</li>
                </ul>

                <p>No use of Story Stalkers's logo or other artwork will be allowed for linking absent a trademark license agreement.</p>

                <h3><strong>iFrames</strong></h3>

                <p>Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>

                <h3><strong>Content Liability</strong></h3>

                <p>We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>

                <h3><strong>Your Privacy</strong></h3>

                <p>Please read Privacy Policy</p>

                <h3><strong>Reservation of Rights</strong></h3>

                <p>We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amen these terms and conditions and it’s linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>

                <h3><strong>Removal of links from our website</strong></h3>

                <p>If you find any link on our Website that is offensive for any reason, you are free to contact and inform us any moment. We will consider requests to remove links but we are not obligated to or so or to respond to you directly.</p>

                <p>We do not ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we promise to ensure that the website remains available or that the material on the website is kept up to date.</p>

                <h3><strong>Disclaimer</strong></h3>

                <p>To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>

                <ul>
                    <li>limit or exclude our or your liability for death or personal injury;</li>
                    <li>limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>
                    <li>limit any of our or your liabilities in any way that is not permitted under applicable law; or</li>
                    <li>exclude any of our or your liabilities that may not be excluded under applicable law.</li>
                </ul>

                <p>The limitations and prohibitions of liability set in this Section and elsewhere in this disclaimer: (a) are subject to the preceding paragraph; and (b) govern all liabilities arising under the disclaimer, including liabilities arising in contract, in tort and for breach of statutory duty.</p>

                <p>As long as the website and the information and services on the website are provided free of charge, we will not be liable for any loss or damage of any nature.</p>



                <h1>Terms of Service ("Terms")</h1>


                <p>Last updated: May 12, 2019</p>


                <p>Please read these Terms of Service ("Terms", "Terms of Service") carefully before using the https://instaviews.socialsivex.com/ website (the "Service") operated by Story Stalkers ("us", "we", or "our").</p>

                <p>Your access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users and others who access or use the Service.</p>

                <p>By accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you may not access the Service. The Terms of Service agreement  for Story Stalkers has been created with the help of <a href="https://www.termsfeed.com/">TermsFeed</a>.</p>




                <h2>Links To Other Web Sites</h2>

                <p>Our Service may contain links to third-party web sites or services that are not owned or controlled by Story Stalkers.</p>

                <p>Story Stalkers has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party web sites or services. You further acknowledge and agree that Story Stalkers shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such web sites or services.</p>

                <p>We strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or services that you visit.</p>


                <h2>Termination</h2>

                <p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>

                <p>All provisions of the Terms which by their nature should survive termination shall survive termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity and limitations of liability.</p>



                <h2>Governing Law</h2>

                <p>These Terms shall be governed and construed in accordance with the laws of Portugal, without regard to its conflict of law provisions.</p>

                <p>Our failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have between us regarding the Service.</p>


                <h2>Changes</h2>

                <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>

                <p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service.</p>


                <h2>Contact Us</h2>

                <p>If you have any questions about these Terms, please contact us.</p>





                <h1>Privacy Policy</h1>


                <p>Effective date: May 12, 2019</p>


                <p>Story Stalkers ("us", "we", or "our") operates the http://instaviews.socialsivex.com website and the Story Stalkers mobile application (the "Service").</p>

                <p>This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our Service and the choices you have associated with that data. Our Privacy Policy  for Story Stalkers is created with the help of the <a href="https://www.freeprivacypolicy.com/free-privacy-policy-generator.php">Free Privacy Policy Generator</a>.</p>

                <p>We use your data to provide and improve the Service. By using the Service, you agree to the collection and use of information in accordance with this policy. Unless otherwise defined in this Privacy Policy, terms used in this Privacy Policy have the same meanings as in our Terms and Conditions.</p>


                <h2>Information Collection And Use</h2>

                <p>We collect several different types of information for various purposes to provide and improve our Service to you.</p>

                <h3>Types of Data Collected</h3>

                <h4>Personal Data</h4>

                <p>While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you ("Personal Data"). Personally identifiable information may include, but is not limited to:</p>

                <ul>
                    <li>Email address</li><li>Phone number</li><li>Cookies and Usage Data</li>
                </ul>

                <h4>Usage Data</h4>

                <p>We may also collect information that your browser sends whenever you visit our Service or when you access the Service by or through a mobile device ("Usage Data").</p>
                <p>This Usage Data may include information such as your computer's Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>
                <p>When you access the Service by or through a mobile device, this Usage Data may include information such as the type of mobile device you use, your mobile device unique ID, the IP address of your mobile device, your mobile operating system, the type of mobile Internet browser you use, unique device identifiers and other diagnostic data.</p>

                <h4>Tracking & Cookies Data</h4>
                <p>We use cookies and similar tracking technologies to track the activity on our Service and hold certain information.</p>
                <p>Cookies are files with small amount of data which may include an anonymous unique identifier. Cookies are sent to your browser from a website and stored on your device. Tracking technologies also used are beacons, tags, and scripts to collect and track information and to improve and analyze our Service.</p>
                <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our Service.</p>
                <p>Examples of Cookies we use:</p>
                <ul>
                    <li><strong>Session Cookies.</strong> We use Session Cookies to operate our Service.</li>
                    <li><strong>Preference Cookies.</strong> We use Preference Cookies to remember your preferences and various settings.</li>
                    <li><strong>Security Cookies.</strong> We use Security Cookies for security purposes.</li>
                </ul>

                <h2>Use of Data</h2>

                <p>Story Stalkers uses the collected data for various purposes:</p>    
                <ul>
                    <li>To provide and maintain the Service</li>
                    <li>To notify you about changes to our Service</li>
                    <li>To allow you to participate in interactive features of our Service when you choose to do so</li>
                    <li>To provide customer care and support</li>
                    <li>To provide analysis or valuable information so that we can improve the Service</li>
                    <li>To monitor the usage of the Service</li>
                    <li>To detect, prevent and address technical issues</li>
                </ul>

                <h2>Transfer Of Data</h2>
                <p>Your information, including Personal Data, may be transferred to — and maintained on — computers located outside of your state, province, country or other governmental jurisdiction where the data protection laws may differ than those from your jurisdiction.</p>
                <p>If you are located outside Portugal and choose to provide information to us, please note that we transfer the data, including Personal Data, to Portugal and process it there.</p>
                <p>Your consent to this Privacy Policy followed by your submission of such information represents your agreement to that transfer.</p>
                <p>Story Stalkers will take all steps reasonably necessary to ensure that your data is treated securely and in accordance with this Privacy Policy and no transfer of your Personal Data will take place to an organization or a country unless there are adequate controls in place including the security of your data and other personal information.</p>

                <h2>Disclosure Of Data</h2>

                <h3>Legal Requirements</h3>
                <p>Story Stalkers may disclose your Personal Data in the good faith belief that such action is necessary to:</p>
                <ul>
                    <li>To comply with a legal obligation</li>
                    <li>To protect and defend the rights or property of Story Stalkers</li>
                    <li>To prevent or investigate possible wrongdoing in connection with the Service</li>
                    <li>To protect the personal safety of users of the Service or the public</li>
                    <li>To protect against legal liability</li>
                </ul>

                <h2>Security Of Data</h2>
                <p>The security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.</p>

                <h2>Service Providers</h2>
                <p>We may employ third party companies and individuals to facilitate our Service ("Service Providers"), to provide the Service on our behalf, to perform Service-related services or to assist us in analyzing how our Service is used.</p>
                <p>These third parties have access to your Personal Data only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>



                <h2>Links To Other Sites</h2>
                <p>Our Service may contain links to other sites that are not operated by us. If you click on a third party link, you will be directed to that third party's site. We strongly advise you to review the Privacy Policy of every site you visit.</p>
                <p>We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services.</p>


                <h2>Children's Privacy</h2>
                <p>Our Service does not address anyone under the age of 18 ("Children").</p>
                <p>We do not knowingly collect personally identifiable information from anyone under the age of 18. If you are a parent or guardian and you are aware that your Children has provided us with Personal Data, please contact us. If we become aware that we have collected Personal Data from children without verification of parental consent, we take steps to remove that information from our servers.</p>


                <h2>Changes To This Privacy Policy</h2>
                <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                <p>We will let you know via email and/or a prominent notice on our Service, prior to the change becoming effective and update the "effective date" at the top of this Privacy Policy.</p>
                <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>


                <h2>Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
                <ul>
                    <li>By email: deostulti2@gmail.com</li>

                </ul>

            </div>

            <!--/TERMOS-->

        </section>
        <section class="three" onclick="goToPage(2)" style="overflow-y: scroll;">
            <h1 class="h1_menu"><?php echo $new_index[2]; ?></h1>
            <hr style="margin-top: 0;">

            <h3 class="text-center text-white"><?php echo $new_index[3]; ?> <a target="_blank" href="https://www.instagram.com/story.stalkers">Instagram <i class="fab fa-instagram"></i></a></h3>

        </section>
        <section class="four" onclick="goToPage(3)" style="overflow-y: scroll; background-color: #007bff;">
            <div class="text-center" style="height: 93% !important;">
                <!-- <img src="logo.png" style="width: 14%; display: inline-block;"> -->

                <h1 class="h1_menu" style="font-family: 'Pacifico', cursive; display: inline-block; font-size: 115% !important;">Story Stalkers</h1>

                <div style="background: rgba(30, 40, 51, 0.15); box-shadow: 3px 3px 4px rgba(0,0,0,.3); border-radius: 2px; border: 1px solid rgba(0, 0, 0, 0.15); height: 100% !important;">
                    <div style="padding: 2%; background: rgba(0, 0, 0, 0.04); margin: 2%; ">
                        <div class="text-center">
                            <h1 class="text-white h1_menu Neue-regular"><?php echo $new_index[0]; ?></h1>
                            <img src="logo_new.png" class="img-fluid" style="margin-bottom: 3%; border-radius: 15%;" id="logo">
                            <!--<h1 class="Neue-bold" style="color: #ffe0e0e6;"><span style="font-family: 'Pacifico', cursive;">IG L</span>ogin</h1>-->
                        </div>
                        <span class="text-white"><?php if (isset($_SESSION['n_word'])) { echo $_SESSION['n_word']; unset($_SESSION['n_word']); } elseif (isset($_SESSION['erro'])) { echo $_SESSION['erro']; unset($_SESSION['erro']); } ?> </span>
                        <form method="POST">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">@</span>
                                </div>
                                <input type="text" name="username" class="form-control" placeholder='Instagram Username' <?php if(isset($_COOKIE['member_login'])) { echo "value='".$_COOKIE['member_login']."'"; } ?>>
                            </div>
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control" placeholder="Instagram Password" <?php if(isset($_COOKIE['member_password'])) { echo "value='".$_COOKIE['member_password']."'"; } ?>>
                            </div>
                            <div class="form-group input-group" id="code" style="display: none;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="number" name="code" class="form-control" placeholder="Instagram Email Code">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-lg btn-success" style="width: 100%; font-family: 'Neue-bold', cursive;" name="login" id="login">Login</button>

                            </div>
                        </form>
                        <hr>
                        <p class="text-white Neue-regular"><?php echo $new_index[7]; ?> <i class="fas fa-bars"></i> <?php echo $new_index[8]; ?></p>
                    </div>
                </div>


            </div>

        </section> 


    </div>
    <h5 class="text-white" style="position: fixed;bottom: 0;left: 50%; transform: translateX(-50%);"><i class="fas fa-shield-alt"></i> Secure Login</h5>

    <script>
        $(document).ready(function() {
            $("#login").click(function(event) {

                $("#login").html('Connecting <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            //$('#login').attr("disabled", true);
        });
        });
    </script>


    <?php
    include 'footer.php';
    ?>
