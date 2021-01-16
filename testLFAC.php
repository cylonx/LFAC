<?php
    include('lib.php');
    $file = null;
    $email = null;
    if (isset($_POST["admin"])) {
        $test = "";
        if (isset($_POST["start1"])) {
            $test = "1";
            $start = true;
        } else if (isset($_POST["end1"])) {
            $test = "1";
            $start = false;
        } else if (isset($_POST["start2"])) {
            $test = "2";
            $start = true;
        } else if (isset($_POST["end2"])) {
            $test = "2";
            $start = false;
        }
        if ($test != "") {
            startTest($start, $test);
            echo "started:[$start] test:[$test]"." <a href='index.php'>go back now </a>";
        }
        exit;
    }
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
        $gr = isset($_POST["grupa"]) ? $_POST["grupa"] : null;
        $info = findStudentInfo($email);
        $canParticipate = canParticipateTestSem($info);
        $canStart = canStart($gr);
        $file = getFileForStudent($email);
        if ($file) {
            $fileContent = file_get_contents($file);
            $dataB = base64_encode($fileContent);
            $data = "data:application/pdf;base64,$dataB";
        }
     } 
?>

<html>
    <head>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
     <link rel="stylesheet" href ="style.css"> 
     <script src ="test.js"></script>  
    </head>
    <body class="test">
    <?php if ($email != null) { ?>
    <div id = "data" style="display:none"><?php echo "$data"?></div>
    <div class="logout"><a href="logout.php">Logout <?php echo $email ?></a></div>
    <div class="wrapper">  
        <?php if (!$canParticipate) {?>
        <div class="msg"> Nu te-ai inscris pentru testul de seminar!</div>    
        <?php  } else if($file && $canStart) { ?>
        <embed id = "em" height="100%" style="display:none"></embed> 
        <?php } else { ?>
        <div class="msg"> Fisierul cu subiectele inca nu este disponibil. Testul nu a inceput inca pentru grupa ta!</div>    
        <?php } ?>
        <div class="end"></div>
    </div>
    <?php } else {?>
    <?php } ?> 
    </body>
</html> 
   



