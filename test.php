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
      //echo ("open file");
      openPdf($email, $gr);
   } else {
      //echo ("download file");
      downloadPdf($email, $gr);
   }
   echo "<b>Start download for:[".$email."]</b><br>";
   //goToPdf($id,$gr);
 
   echo "<h1>Success!</h1>";
   //header("test.php");
   //exit;
} else {
   echo "<h1>Succes!</h1>";
}
?>

