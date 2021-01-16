<?php
include('lib.php');


if (isset($_POST["email"])) {
   $email = $_POST["email"];
   $gr = isset($_POST["grupa"]) ? $_POST["grupa"] : null;
  
   $info = findStudentInfo($email);
   $canParticipate = canParticipateTestSem($info);
   $canStart = canStart($gr);
   $file = getFileForStudent($email);  
   if (!$file || !$canStart || !$canParticipate) {
      echo("Nu poti participa la test:{$email}");
      exit;
   }

   if (isset($_POST["open"])) {
      openPdf($email, $gr);
   } else {
      downloadPdf($email, $gr);
   }
   echo "<b>Start download:".$email."</b><br>";
   //goToPdf($id,$gr);
 
   echo "<h1>Success!</h1>";
   //header("test.php");
   //exit;
} else {
   //echo "id:". $_GET["id"];
   //echo "grupa:". $_GET["grupa"];
   //print_r($_GET);
   //print_r($_REQUEST);
   print_r($_POST);
   echo "<h1>Succes!</h1>";
}
?>

