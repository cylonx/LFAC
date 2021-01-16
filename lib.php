<?php
require_once __DIR__ . '/vendor/autoload.php';

$studentsCsv = 'students.csv';
$asignariAll = 'asignariAll.csv';
$idxPath = 'GlobalSubjects/index.html';
$startFile1 = "on1.txt";
$startFile2 = "on2.txt";
function getExercisesDirs($srcDir)
{
   $scan = array_diff( scandir($srcDir), array(".", ".."));
   
   $dirs = array();
   foreach($scan as $dir) {
      if (strpos($dir,'Ex') !== false && is_dir($srcDir.'/'.$dir)) {
         $dirs[] = $srcDir.'/'.$dir;
      }
   }
   return $dirs;
}

function getGroupDirs ()
{
   $srcDir = getcwd();
   $scan = array_diff( scandir($srcDir), array(".", ".."));
   $grupe = array('A1','A2','A3','A4','A5','A6','B1','B2','B3','B4','B5','X1','X2','E1','E2','E3','E4');
   $dirs = array();
   
   foreach($scan as $dir) {
      if (in_array($dir,$grupe) && is_dir($srcDir.'/'.$dir)) {
         $dirs[] = $dir;
      }
   }
   return $dirs;
}

function getAllAssignCsvs ()
{
  $dirs = getGroupDirs();
  $files = array();
  foreach($dirs as $dir) {
      $file = "$dir/asignariPDF.csv";
      if (file_exists($file)) {
         $files[] = $file;
      }
  }
  return $files;
}

function getAllAssignsInfo () 
{
   global $asignariAll;
   if (file_exists($asignariAll)) {
      return getCsvContent($asignariAll);
   }

   $files = getAllAssignCsvs();
   $students = array();
   $fp = fopen($asignariAll, "a");
  
   $headerAdded = false;
   foreach($files as $file) {
      $file = fopen($file,"r");
      //["Nume Student",  "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original", "email","grupa"];
      while($studentInfo = fgetcsv($file, 1000, ',')) {
         if ($studentInfo[0] !== "Nume Student" || !$headerAdded) {
            $students[] = $studentInfo;
            fputcsv($fp, $studentInfo); 
            if ($studentInfo[0] == "Nume Student") {
               $headerAdded = true;
            }
         }
       }
   }
   return $students;
}

function isCsv($file)
{
   return strpos($file,'.csv') !== false;
}

function getStudentsCsv($dir)
{
   global $studentsCsv;
   if (!$dir || $dir == "all") {
      return $studentsCsv;
   }

   $scan = scandir($dir);
   $files = array_filter($scan,isCsv);
   foreach($files as $file) {
      if (strpos($file,'asignari') == 0) {
         return $dir.'/'.$file;
      }
   }
   return $studentsCsv;
}
//genereaza o lista de subjectCount subiecte alese random din fiecare din directoarele 'SubiectX" 
function makeExercisesList($nr, $srcDir) //nr = -1 => alege exercitii random din fiecare dir Ex
{
     $list = array();
     $dirs = getExercisesDirs($srcDir);
   
     foreach ($dirs as $Dir) {
          $files = array_values(array_diff( scandir($Dir), array(".", "..")));
          //echo "files in:".$Dir;
          //print_r($files);
          $sindex = ($nr > 0 && $nr < count($files)-1) ? nr : rand(0,count($files)-1);
          //echo "sindex:".$sindex;
          $list[] = $Dir.'/'.$files[$sindex];  
     }
     return $list; 
}

function getExFilesForIndexes($exIndexes, $srcDir) //nr = -1 => alege exercitii random din fiecare dir Ex
{
     $list = array();
     $dirs = getExercisesDirs($srcDir);
     $i = 0;
     //exIndexs count = $count(dirs)
     foreach ($dirs as $Dir) {
          $files = array_values(array_diff( scandir($Dir), array(".", "..","enunt.txt")));
          $sindex = $exIndexes[$i];
          $i++;
          //echo "sindex:".$sindex;
          $list[] = $Dir.'/'.$files[$sindex];  
     }
     return $list; 
}

function swap($arr, $i, $j) {
   //echo "<br>Swap!";
   $temp = $arr[$i];
   $arr[$i] = $arr[$j];
   $arr[$j] = $temp;
   return $arr;
}

function fillVal($arr, $index, $val, $vals, &$output) {
      $arr[$index] = $val; 
      if ($index == count($arr) - 1) {
         $result = array_count_values($arr);//frequency vector
         if (!in_array(3,$result)) { //nu vor exista subiecte diferite cu 3 ex in comun
            $output[] = $arr;    
         }
           
      }  else {
         foreach ($vals as $v) {
           fillVal($arr, $index+1, $v, $vals, $output);
         }
      }
}

function fillAll ($n, $vals) {
   $arr = array();
   $output = array();
   for ($i = 0; $i < $n; $i++) {
      $arr[] = 0;
   } 
   foreach($vals as $val) {
      fillVal($arr, 0, $val, $vals, $output);
   }
  //print_r($output);
   return $output;
}

function testOutput(& $output) {
   $arr = array(0,1,2,3,4,5);
   for($index = 0; $index < 3; $index++) {
      $output[] = $arr;
   }
}

//$name = numele studentului  
//$dir = directorul in care ar trebui sa fie subiectele: "SubiecteGrupaX" de ex

function makeSubject($name,$dir, $subjects, $useBase64)
{
   //echo 'makeHtml starting<br>'; 
   $doc = new DOMDocument('1.0');  
   $root = $doc->createElement('html');
   $doc->appendChild($root);
   
   $head = $doc->createElement('head');
   $root->appendChild($head);
   $link = $doc->createElement('link');
   $link->setAttribute("rel","stylesheet");
   $link->setAttribute("type","text/css");
   $link->setAttribute("href","stil.css");
   $head->appendChild($link);

   $body = $doc->createElement('body');
   $root->appendChild($body);
  
   for ($index = 0; $index < count($subjects); $index++) {
      $div = $doc->createElement('div');
      $div->setAttribute('class','ex');
      $body->appendChild($div);
      $h = $doc->createElement('h3', 'Exercitiul '.($index + 1));
      $div->appendChild($h);
      $dirname = dirname($subjects[$index]);
      $enfile = "$dirname/enunt.txt";
   
      if (file_exists( $enfile)) {
         $enunt = file_get_contents($enfile);
         $diven = $doc->createElement('div', $enunt);
         $div->appendChild($diven);
      }
     
      $url = null;
      if ($useBase64) {
         $data = file_get_contents($subjects[$index]);
         $url = 'data:image/jpg;base64,'. base64_encode($data); 
      } else {
         $url = "../".$subjects[$index];
      }
     
      $img = $doc->createElement('img');
      $img->setAttribute('src',$url);
      $div->appendChild($img);
   }
  
  
   $content = $doc->saveHtml();
   make_pdf($dir, $name, $content);
   
   return file_put_contents($dir.'/'.$name.'.html', $content);    
}

function make_pdf($dir, $name, $htmlContent)
{
   $mpdf = new \Mpdf\Mpdf();    
   $mpdf->WriteHTML($htmlContent);
   $mpdf->Output($dir.'/'.$name.'.pdf', 'F');
}

//cate un director per grupa, de luat din request numele dir, numele csv_filename...sau chiar intregul fisier?
function generateNamedSubjects($dir, $csv_filename)
{
   if (!is_dir($dir)){
	   mkdir($dir,0777);
   } else {
      cleanDir($dir);
   }

   //todo: poate fi preluat din directorul grupei sau directorul global
   $imagesDir =  $dir;
   if (!is_dir($imagesDir.'/Ex1')) {
      $imagesDir = 'GlobalSubjects';
   }

   copy('stil.css',$dir.'/stil.css');
   copy($csv_filename, $dir.'/'.$csv_filename);
   $file = fopen( $csv_filename,"r");
   //echo 'opened csv file <br>';
   $list = array(); //unde sunt folosite??
   $list[0] = ["Nume Student", "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original","email"]; //unde??

   while($studentInfo = fgetcsv($file, 1000, ',')) {
      $num = count($studentInfo);
      $name = str_replace(" ","_",$studentInfo[0]);
    
      $fileName = $name.'_'.uniqid();
      $list = makeExercisesList(-1, $imagesDir);
      //print_r($subjects);
      makeSubject($fileName, $dir, $list, true);
   }
  
   fclose($file);
   //move csv in dir?
}

function generateSubjects($dir, $nr, $randomOrder)
{
   if (!is_dir($dir)){
	   mkdir($dir,0777);
   } else {
      cleanDir($dir);
   }

   $outputDir = $dir.'/PDFS';
   if (!is_dir($outputDir)){
	   mkdir($outputDir,0777);
   } else {
      cleanDir($outputDir);
   }

   $imagesDir =  $dir;
   if (!is_dir($imagesDir.'/Ex1')) {
      $imagesDir = 'GlobalSubjects';
   }

   copy('stil.css',$dir.'/stil.css');
  
   $list = array();
   $list[0] = ["Nume Student", "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original","Email", "Grupa"];
   $count = $nr > 0 ? $nr : getSubjectsCount($imagesDir);
   
   for ($index = 1; $index <= $count; $index++) {
      $fileName = "Subiectul".$index;
      $exindex = $randomOrder ? -1 : $index; 
      $list = makeExercisesList($exindex, $imagesDir);
      makeSubject($fileName, $outputDir, $list, true);
   }
   //move csv in dir?
}

function generateSubjectsV2($dir, $nr)
{
   global $idxPath;
   if (!is_dir($dir)){
	   mkdir($dir,0777);
   } else {
      cleanDir($dir);
   }

   $outputDir = $dir.'/PDFS';
   if (!is_dir($outputDir)){
	   mkdir($outputDir,0777);
   } else {
      cleanDir($outputDir);
   }

   $imagesDir =  $dir;
   if (!is_dir($imagesDir.'/Ex1')) {
      $imagesDir = 'GlobalSubjects';
   }

   copy('stil.css',$dir.'/stil.css');
   copy($idxPath,"$outputDir/index.html");
   copy($idxPath,"$dir/index.html");
   $list = array();
   $list[0] = ["Nume Student", "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original","Email", "Grupa"];
   $scount = getSubjectsCount($imagesDir); //4
   $excount = count(getExercisesDirs($imagesDir)); //12??, 6 mai degraba
  
   $vals = array();
   for ($ix = 0; $ix < $scount; $ix++) {
      $vals[] = $ix;
   }
   $exArrayAll = fillAll($excount, $vals);
   //echo("<br>number of subjects:".count($exArrayAll));
   $count = $nr > 0 ? $nr : max(count($exArrayAll), 30);
   for ($index = 1; $index <= $count; $index++) {
      $i = rand(0, count($exArrayAll) - 1);
      $exIndexes = $exArrayAll[$i];
      if (count($exArrayAll) > 4) {
         array_splice($exArrayAll,$i,1);
      } 
      $fileName = "Exercitiul_".$index."_".implode($exIndexes);
      //echo "<br>generate file:$fileName<br>";
      $list = getExFilesForIndexes( $exIndexes, $imagesDir);
      //print_r($list);
      makeSubject($fileName, $outputDir, $list, true);
   }
   //move csv in dir?
}

function getSubjectsCount($imagesDir)
{
   $exDir = $imagesDir.'/Ex1';
   if (!is_dir($exDir)) {
      return 0;
   }
   $scan = array_diff( scandir($exDir), array(".", "..", "enunt.txt"));
   $count = 0;
   foreach($scan as $entry) {
      $count = $count + 1;
      
   }
   return $count;
}

function getCsvContent($csv_filename) 
{
   $file = fopen($csv_filename,"r");
   $result = array();
   while($info = fgetcsv($file, 1000, ',')) {
     $result[] = $info;
   }
  
   fclose($file);
   return $result;
}

function assignPdfs($dir, $csv_filename, $generateNamedFile) {
   global $idxPath;
   if(!file_exists($csv_filename)) {
      return 0;
   }
   if (!is_dir($dir)){
	   mkdir($dir,0777);
   } else {
      cleanDir($dir);
      copy($idxPath,"$dir/index.html");
   }

   $sdir = $dir."/PDFS";
   if (!is_dir($sdir)) {
      $sdir = "GlobalSubjects/PDFS";
   } 
   copy($idxPath,"$sdir/index.html");
   copy($idxPath,"$dir/index.html");
   $allfiles = array_values(array_diff( scandir($sdir), array(".", "..","index.html")));
   $files = array();
   foreach($allfiles as $file) {
      if (strpos($file, ".pdf") != false) {
         $files[] = $file;
      }
   }
  
   //copy($csv_filename, $dir.'/'.$csv_filename); ? todo?
   $file = fopen( $csv_filename,"r");
  
   $list = array();
   $list[0] = ["Nume Student", "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original", "Email", "Grupa"];

   while($studentInfo = fgetcsv($file, 1000, ',')) {
      //student info in cvs: nume, email, grupa 
      if ($studentInfo[2] == $dir || $dir == "all") {
         $num = count($studentInfo);
         $name = str_replace(" ","_",$studentInfo[0]);
         $email = $studentInfo[1];
         $uuid = uniqid();
         $fileName = $dir."_".$email."_".$name.".pdf";
         if (count($files) == 0) {
            continue;
         }
         $i = rand(0,count($files)-1);
         $filePath = $sdir.'/'.$files[$i];
         $destPath = $dir.'/'.$fileName; //not used...
         if ($generateNamedFile) {
              echo("<br>copy!$fileName to $filePath<br>");
              copy($filePath,$destPath);
         }
         $list[] = [$name,getUrl($destPath), getMagicUrl($uuid,$dir), $destPath, $uuid, $filePath,$email,$dir]; //link to destPath 
      }
   }
   //chmod($dir, 0644); //read write for owner, read for anyboady ele
   fclose($file);
   generateOutputFile($dir,$list);
   return count($list) - 1;
   //write map in csv or generate a html with the entire link in it??
   
}

function generateOutputFile($dir, $map) {
   $filePath = "$dir/asignariPDF.csv";
   $fp = fopen($filePath, "w");
   foreach ($map as $fields) { 
      fputcsv($fp, $fields); 
   } 
  fclose($fp);
}

function assignPdfsToAll($studentsCsv) {
   $grupe = array('A1','A2','A3','A4','A5','A6','B1','B2','B3','B4','B5','X1','X2','E1','E2','E3','E4');
   foreach($grupe as $gr) {
     assignPdfs($gr,$studentsCsv,true);
   }

   generateFinalAssignCsv ();

}

function generateFinalAssignCsv ()
{
   global $asignariAll;
   if (file_exists($asignariAll)) {
      unlink($asignariAll);
   }
   getAllAssignsInfo();
}

function getTinyUrl($url)  {  
	$ch = curl_init();  
	$timeout = 5;  
	curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
	$data = curl_exec($ch);  
	curl_close($ch);  
	return $data;  
}

function donwloadFile()
{
   $fakeFileName= "fakeFileName.zip";
   $realFileName = "realFileName.zip";

   $file = "downloadFolder/".$realFileName;
   $fp = fopen($file, 'rb');

   header("Content-Type: application/octet-stream");
   header("Content-Disposition: attachment; filename=$fakeFileName");
   header("Content-Length: " . filesize($file));
   fpassthru($fp);
}


function getUrl($filename)
{
   $protocol = "";
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
         $protocol = "https://";   
    else  
         $protocol = "http://";   
   $full = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   $scriptname = basename($_SERVER["SCRIPT_FILENAME"]);
   $result = str_replace($scriptname, "", $full);
   return $result.$filename;
}

function getMagicUrl($uuid, $grupa)
{
   $protocol = "";
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
         $protocol = "https://";   
    else  
         $protocol = "http://";   
   $full = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   $scriptname = basename($_SERVER["SCRIPT_FILENAME"]);
   $result = str_replace($scriptname, "", $full);
   $result = str_replace("Subiecte/", "", $result);
   $url = $result.'test.php?grupa='.$grupa.'&id='.$uuid;
   //todo: the freaking url..
   return $url;
}

//elimina subiectele genereate din fisier?
function cleanDir($dir)
{
   $files = array_diff(scandir($dir), array('.','..'));
   foreach ($files as $file) {
    if (!is_dir("$dir/$file") && (strpos($file, "index.html") == false) && (strpos($file, ".html") !== false || strpos($file,".pdf") !== false) ) {
      unlink("$dir/$file");
    } 
   }
}

function isAdmin($emailS) {
   //$emailS =  trim($studentInfo[1]);
   $email = "otilia.captarencu@gmail.com";
   return strcasecmp($email,$emailS)==0;
}

function canStart($grupa) {
   $start = true;
   global $startFile;
   $grupe2 = array("E2","E3","A3","A4","B2","B3","B4","B5");
   $part = in_array($grupa,$grupe2) ? "2" : "1";
   return isTestStarted($part);
}

function startTest($start, $part) {
   $startFile = "on".$part.".txt";
   chmod(getcwd(),0757);
   if (!$start) {
      if (file_exists($startFile)) {
         unlink($startFile);
      }
   } else {
      $file = fopen($startFile,"a");
   }
   echo ("STARTED TEST [{$file}]?");
}

function isTestStarted($part) {
   $startFile = "on".$part.".txt";
   return file_exists($startFile);
}

function sendEmails($grupaDir)
{
   $csvFile = grupaDir.'/asignariPDF.csv';
   $url = getFilePathForId($csvFile, $id);
   $result = array();
   $file = fopen($csvFile,"r");
   //["Nume Student",  "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original", "email"];
   while($studentInfo = fgetcsv($file, 1000, ',')) {
      $email =  $studentInfo[6];
      $content = $studentInfo[2];
      $ret = sendEmail($email, $content);
      if ($ret) {
         $result[] = $email;
      }
   }
  
   fclose($file);
   return $result;
}

function sendEmail ($email, $content)
{
   $to = "oana.prisecaru@uaic.ro";
   $subject = "Subiect Test Partial";
   
   $message = $content; //or format as html...
   $header = "From:otto@info.uaic.ro \r\n";
   $header .= "MIME-Version: 1.0\r\n";
   $header .= "Content-type: text/html\r\n";
   
   $retval = mail ($to,$subject,$message,$header);
   return $retval;


}

function getFilePathForId($csvFile, $id)
{
   $file = fopen($csvFile,"r");
   //["Nume Student",  "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original"];
   while($studentInfo = fgetcsv($file, 1000, ',')) {
      $idS =  $studentInfo[4];
      if ($idS == $id) {
         return $studentInfo[3]; //filename
      }
   }
    return null;
  
   fclose($file);

}

function getFileForStudentV1($email)
{
   $csvFile = 'asignariAll.csv';
   $result = null;
   $file = fopen($csvFile,"r");
   //["Nume Student",  "Real Link", "Magic Link", "Nume pdf", "Id", "Fisier/Subiect original", "email","grupa"];
   while($studentInfo = fgetcsv($file, 1000, ',')) {
      $emailS =  $studentInfo[6];
      if ($emailS == $email) {
         $result = $studentInfo[5];
      break;
      }
    
   }
   
   fclose($file);
   return $result;
}

function getFileForStudent($email)
{
   $students = getAllAssignsInfo();
   $result = null;

   foreach($students as $studentInfo) {
      $emailS =  trim($studentInfo[6]);
      if (strcasecmp($emailS,$email)==0) {
         $result = $studentInfo[5];
         if (!file_exists($result)) {
            return null;
         }
         break;
      }
   }
   
   return $result;
}


function findStudent($email, $findName) {
   $file = fopen("students.csv","r");
   $result = null;

   //["Nume Student", "email", "grupa cu care vine la seminar", "voi participa la" "cod"];
   while($studentInfo = fgetcsv($file, 1000, ',')) {
      $emailS =  trim($studentInfo[1]);
      if (strcasecmp($emailS,$email) == 0) {
         $result = $findName ? $studentInfo[0] : $studentInfo[2];
         break;
      } 
   }
   fclose($file);
   return $result;    
}

function findStudentInfo($email) {
   $file = fopen("students.csv","r");
   $result = null;
   $i = 0;
   //["Nume Student", "email", "grupa cu care vine la seminar", "voi participa la" "cod"];
   while($studentInfo = fgetcsv($file, 1000, ',')) {
      $i++;
      $emailS =  trim($studentInfo[1]);
     // echo "<br> $i nume:$studentInfo[0] email:[$emailS]";
      if (strcasecmp($email,$emailS)==0) {
         $result = $studentInfo;
         break;
      } 
   }
   fclose($file);
   return $result;    
}

//$info = ["Nume Student", "email", "grupa cu care vine la seminar", "voi participa la" "cod"];
function canParticipateTestSem($info) {
   return (strpos(strval($info[4]),'1') >= 0);
}

function canParticipateT1($info) {
   return (strpos(strval($info[4]),'2') >= 0);
}

function canParticipateT2($info) {
   return (strpos(strval($info[4]),'3') >= 0);
}

function goToPdf($id,$gr) {
    $csvFile = 'Subiecte/'.$gr.'/asignariPDF.csv';
    $file = 'Subiecte/'.getFilePathForId($csvFile, $id);
   //  echo "file: {$file} <br>";
   //  echo "csvFile: {$csvFile} <br>";
   //  echo "id:{$id}";
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Content-Type: application/force-download");
    header('Content-Disposition: attachment; filename=' . urlencode(basename($file)));
    // header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush(); 
    readfile($file);
    //exit;
}

function goToSomePdf()
{
   $file = 'Gigi.pdf';
   header('Content-type: application/pdf');
   header("Content-disposition: attachment; filename= '$file'");
   header("location: $file");
   readfile($file);
   header("location: test.php");
}


function downloadPdf($email,$gr)
{
   $file = getFileForStudent($email);
   header('Content-type: application/pdf');
   header("Content-disposition: attachment; filename= test.pdf");
   //header("Content-Type: application/force-download");
   //header("location: $file");
   readfile($file);
   header("location: test.php");
}

function openPdf($email,$gr)
{
   $path = getFileForStudent($email);
   header('Content-Type: application/pdf');
   header('Content-Disposition: inline; filename='.$path);
   header('Content-Transfer-Encoding: binary');
   header('Accept-Ranges: bytes');
   readfile($path);
}

function isMobile() {
   return preg_match("/(android|webos|avantgo|iphone|ipad|ipod|blackberry|iemobile|bolt|boost|cricket|docomo|fone|hiptop|mini|opera mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
?>
