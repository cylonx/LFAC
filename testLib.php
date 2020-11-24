<?php
include('lib.php');
//to be safe copy students.csv in groups where it does not exist...
//generateNamedPdfs("A1", "A1/students.csv");
//generateNamedPdfs("A2", "students.csv");
/*$dirs = getGroupDirs();
print_r($dirs); 
echo "<br>";*/

/*$csvs = getAllAssignCsvs();
print_r($csvs);
echo "<br>"; */

// $info = getAllAssignsInfo ();
// print_r($info);

echo "<br>";
echo "<br>";
//$email = 'otto.captarencu@gmail.com';
//$nume = findStudent($email, true);
//echo "found user $nume for email $email";

// $vals = array();
// $output = array();
// for ($index = 0; $index < 3; $index++) {
//     $vals[] = $index;
// }

// $output = fillAll(3, $vals);

// echo ('<br><b>done!</b>');
// print_r($output);

//generateSubjectsV2("A1", -1); 

$email = 'alexanoosefu@gmail.com';
$result = findStudentInfo($email);
if ($result) {
    echo "<h2>Nume:".$result[0]."</h2>";
    echo "<h2>Grupa:".$result[2]."</h2>";
    echo "<h2>Particip la:".$result[3]."</h2>";
    echo "<h2>Cod:".$result[4]."</h2>";
    $canParticipate = canParticipateTestSem($result);
    if (!$canParticipate) {
        echo "<h2>Nu participi la testul din 25 nov, ne vedem in sesiune...</h2>";
    } else {
        echo "<h2>Participi la testul din 25 nov</h2>";  
    }
} else {
    echo ("not found!");
}


//assignPdfs("A6", "students.csv", true);
//generateSubjectsV2("A1", 10);
//generateFinalAssignCsv();
?>