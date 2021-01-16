<?php
include('lib.php');
if (isset($_POST["email"]) && (isset($_POST["TS1"]) || isset($_POST["TS2"]))) {
   $email = $_POST["email"];
   $gr = isset($_POST["grupa"]) ? $_POST["grupa"] : null;
   $test = isset($_POST["TS1"]) ? "TS1" : "TS2";
   $info = findStudentInfo($email);
   $canParticipate = canParticipateExam($info, $test); 
   //echo("canParticipate:".$canParticipate);
   $canStart = canStartEx($gr,$test); 
   //echo("canStart:".$canStart);
   $file = getExamFileForStudent($email,$test); 
   if (!$file || !$canStart) {
      echo("Testul {$test} nu a inceput inca");
      exit;
   }
   if (isset($_POST["open"])) {
      //echo ("open file");
      //openPdf($email, $gr);
      openExamPdf($file);
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

