<?php 

//ABADIES SOLUTIONS

require_once("include/initialize.php");
   if (!isset($_SESSION['UID'])){
      redirect(WEB_ROOT."login.php");
 //   header("Location: login.php");

     }else{
      
     } 
$title="Home"; 
$content='home.php';
$view = (isset($_GET['page']) && $_GET['page'] != '') ? $_GET['page'] : '';
switch ($view) {
  case '1' :
         $title="Home"; 
     $content='home.php'; 
    
    break;  
  default :
    $content    = 'home.php'; 
}
require_once("theme/template.php");
?>

