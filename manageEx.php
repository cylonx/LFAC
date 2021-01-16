<?php
    include('lib.php');
    $file = null;
    $email = null;
    if (isset($_POST["admin"])) {
        $test = "";
        if (isset($_POST["start1_1"])) {
            $test = "TS1"; 
            $start = true;
            $part = 1;
        } else if (isset($_POST["end1_1"])) {
            $test = "TS1"; 
            $start = false;
            $part = 1;
        } else if (isset($_POST["start2_1"])) {
            $test = "TS2"; 
            $start = true;
            $part = 1;
        } else if (isset($_POST["end2_1"])) {
            $test = "TS2"; 
            $start = false;
            $part = 1;
        } else  if (isset($_POST["start1_2"])) {
            $test = "TS1"; 
            $start = true;
            $part = 2;
        } else if (isset($_POST["end1_2"])) {
            $test = "TS1"; 
            $start = false;
            $part = 2;
        } else if (isset($_POST["start2_2"])) {
            $test = "TS2"; 
            $start = true;
            $part = 2;
        } else if (isset($_POST["end2_2"])) {
            $test = "TS2"; 
            $start = true;
            $part = 2;
        } 
        if ($test != "") {
            startEx($start, $test, $part);
            echo "started:[$start] test:[$test] part:[$part]"." <a href='index.php'>go back now </a>";
        }
        exit;
    }
   else {
       echo ("<h1>Start exam </h1>");
   }
?>

   



