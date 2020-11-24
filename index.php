<?php

//index.php

//Include Configuration File
include('config.php');
include ('lib.php');
$mobile = isMobile();
$login_button = '';
$info = null;
$email = null;
function getStyle() {
   if (isMobile()) {
      return "display:none";
   }
   return "";
}

//This $_GET["code"] variable value received after user has login into their Google Account redirct to PHP script then this variable value has been received
if(isset($_GET["code"]))
{
 //It will Attempt to exchange a code for an valid authentication token.
 $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

 //This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
 if(!isset($token['error']))
 {
  //Set the access token used for requests
  $google_client->setAccessToken($token['access_token']);

  //Store "access_token" value in $_SESSION variable for future use.
  $_SESSION['access_token'] = $token['access_token'];

  //Create Object of Google Service OAuth 2 class
  $google_service = new Google_Service_Oauth2($google_client);

  //Get user profile data from google
  $data = $google_service->userinfo->get();

  //Below you can find Get profile data and store into $_SESSION variable
  
  if(!empty($data['email']))
  {
   $_SESSION['user_email_address'] = $data['email'];
  }
 }
}

//This is for check user has login into system by using Google account, if User not login into system then it will execute if block of code and make code for display Login link for Login using Google account.
if(!isset($_SESSION['access_token']))
{
 //Create a URL to obtain user authorization
 $login_button = '<a href="'.$google_client->createAuthUrl().'"><img src="sign-in-with-google.png" /></a>';
}

?>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Test LFAC</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
 <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" /> -->
 <link rel="stylesheet" href ="style.css">
 </head>
 <body>
  <div class="container">
   <br />
   <div class="title" align="center">Limbaje Formale, Automate si Compilatoare - test seminar</div>
   <br />
   <div class="panel">
   <?php if($login_button == '') { $email = $_SESSION['user_email_address']; $info = findStudentInfo($email); if ($info) {$grupa = $info[2]; $nume = $info[0]; $comps = $info[3];} if($info){?>
   <div class = "table">
      <div class="panel-heading"> <?php echo($nume) ?></div><div class="panel-body">
      <div><b>Email:</b> <?php echo $_SESSION['user_email_address'] ?></div>
      <div><b>Grupa:</b> <?php echo $grupa ?></div>
      <div><b>Pariticpi la:</b> <?php echo $comps ?></div>
    <!--<form action="test.php" method="post">
       <input type = "hidden" name="email" id="email" value="<?php echo $_SESSION['user_email_address'] ?>" >
       <input type = "hidden" name="grupa" id = "grupa" value="A1">
       <input type="submit" value="Download test now">
    </form> -->
        <?php if (isAdmin($email)) {?>
        <form action="testLFAC.php" method="post">
         <input type = "hidden" name="admin" id="admin" value="<?php echo $_SESSION['user_email_address'] ?>" >
         <input type="submit" name="start1" value="Start test 1" />
         <input type="submit" name="end1" value="End test 1" />
         <input type="submit" name="start2" value="Start test 2" />
         <input type="submit" name="end2" value="End test 2" />
         </form>
        <?php } ?>    
      <?php if(isMobile()){ ?>
      <form action="testLFAC.php" method="post">
         <input type = "hidden" name="email" id="email" value="<?php echo $_SESSION['user_email_address'] ?>" >
         <input type = "hidden" name="open" id="open" value="open" >
         <input type = "hidden" name="grupa" id = "grupa" value="<?php echo $grupa ?>">
         <input type="submit" value="Get test now ">
      </form>
      <?php } else { ?>
      <form action="testLFAC.php" method="post" style="<?php echo getStyle() ?>">
         <input type = "hidden" name="email" id="email" value="<?php echo $_SESSION['user_email_address'] ?>" >
         <input type = "hidden" name="grupa" id = "grupa" value="<?php echo $grupa ?>">
         <?php if (canStart($grupa)) { ?>
         <input type="submit" value="Go to test now">
         <?php }?>
      </form>
      <?php } ?> 
   <?php }else { ?>
      <h2>Nu sunteti inregistrat cu acest email! <?php echo ($email)?></h2>
    <?php } ?>
    <div><a href="logout.php" class="logout">Logout</a></div>
    </div>
   </div>
   <?php } else {?>
    <div align="center" class="loginContainer"> <?php echo $login_button ?></div>
   <?php } ?>
   <?php if($info) { ?>
   <div class = "table">
     <div class="panel-heading">Instructiuni test</div>
     <div class="panel-body">
       <ul>
          <li>Vei intra in meetingul afisat pentru grupa: <?php echo $grupa ?></li>
          <li>Informatii suplimentare privind desfasurarea testului se gasesc: <a href="https://docs.google.com/document/d/1i2EOxT6liUf9QLUw2Ef2odE8tA5iACrID5KOgmYVAIc/edit?usp=sharing">aici</a></li>
       </ul>
     </div>
   </div>
   <?php }?>
   </div> <!-- panel -->
  </div> <!--container -->
 </body>
</html>
