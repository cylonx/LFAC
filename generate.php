<?php
require_once __DIR__ . '/vendor/autoload.php';
include('lib.php');
echo("all:");
print_r($_POST);
if (isset($_POST["assignExam"])) {
   $fileName = getStudentsCsv('all');
   echo("assign subjects to all:".$fileName);
   if (file_exists('TS1/PDFS')) {
      assignPdfs('TS1', $fileName, false);
   }
  
   if (file_exists('TS2/PDFS')) {
      assignPdfs('TS2', $fileName, false);
   }
   echo("here");
   exit;
}
if (isset($_POST["gen"])) {
   echo ("generateAssignCSV");
   generateFinalAssignCsv();
   exit;
}

if (isset($_POST["grupa_assign"])) {
   $groupDir = $_POST["grupa_assign"];
   $error = null;
   $fileName = null;
   if (!isset( $_FILES['file'])) {

      $fileName = getStudentsCsv($groupDir);
      if (!file_exists($fileName)) {
         $error = 'Trebuie un csv cu studenti';
      }
      
      // if (is_dir($groupDir)) {
      //    $fileName = getStudentsCsv($groupDir);
      // } else {
      //    $error = 'Trebuie un csv pt grupa';
      // } 
      
   } else {
      $fileName = $_FILES['file']['name'];
      $fileType = $_FILES['file']['type'];
      $fileContent = file_get_contents($_FILES['file']['tmp_name']);
    
      $error = $_FILES['file']['error'];
      file_put_contents($fileName, $fileContent);
      
   }
      
   if (!$fileName || $error) {
      $data = array( "result" => false, "created" => false, "error" => $error); 
      echo json_encode($data);
      exit;
   }
   if ($groupDir == "all") {
      assignPdfsToAll($fileName);
   } else {
      assignPdfs($groupDir, $fileName, true);
   }
  
   
   $data = array( "result" => true, "created" => true, "groupdir" => $groupDir, "fileName:" => $fileName); 
   echo json_encode($data);
   exit;

}

if (isset($_POST["grupa_gen"])) {
   $groupDir = $_POST["grupa_gen"];
   $nr = (int)$_POST["nr"];
   echo "nr:".$nr;
   $type = $_POST["type"];
   $randomOrder = ($type == "random");
   //generateSubjects($groupDir, $nr, $randomOrder);
   generateSubjectsV2($groupDir, $nr);
   $data = array( "result" => true, "created" => $nr); 
   echo json_encode($data);
   exit;
}

if (isset($_POST["grupa_send"])) {
   $groupDir = $_POST["grupa_send"];
   $emailType =  $_POST["email_type"];
   sendEmails($groupDir, $type);
   $data = array( "sentEmail" => true); 
   echo json_encode($data);
   exit;
   
}
echo "<h1>Succes !</h1>"
?>

