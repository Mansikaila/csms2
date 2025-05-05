<?php
// Start the session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
global $database_name;
global $_dbh;
$servername = "localhost";
$username = "root";
$password = "";
$database_name="csms1";
date_default_timezone_set("Asia/Kolkata");
try {
  
  $_dbh = new PDO("mysql:host=$servername;dbname=".$database_name, $username, $password);
  // set the PDO error mode to exception
  $_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
// connection.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION["sess_company_id"]=2;

if(isset($_SESSION["sess_user_id"]) && $_SESSION["sess_user_id"]>0) {
    $usrid=$_SESSION["sess_user_id"];
} else {
    $usrid=0;
}
if(isset($_SESSION["sess_person_name"]) && $_SESSION["sess_person_name"]>0) {
    $personname=$_SESSION["sess_person_name"];
} else {
    $personname="";
}
if(isset($_SESSION["sess_company_id"]) && $_SESSION["sess_company_id"]>0) {
    $companyid=$_SESSION["sess_company_id"];
} else {
    $companyid=1;
}
if (!defined('USER_ID')) {
    define('USER_ID', $usrid); // User ID
}
if (!defined('PERSON_NAME')) {
    define('PERSON_NAME', $personname); // USER DISPLAY NAME
}
if (!defined('COMPANY_ID')) {
    define('COMPANY_ID', $companyid); // COMPANY ID
}
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://CBS5-PC/csms1/'); // Base URL of your application
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/csms1/'); // Base path of your application
}
if (!defined('CSS_PATH')) {
    define('CSS_PATH', BASE_URL . 'dist/css/'); // Path to CSS files
}
if (!defined('JS_PATH')) {
    define('JS_PATH', BASE_URL . 'dist/js/'); // Path to JavaScript files
}
if (!defined('IMAGE_PATH')) {
    define('IMAGE_PATH', BASE_URL . 'images/'); // Path to image files
}
if (!defined('IMAGE_DIR')) {
    define('IMAGE_DIR', '/images/'); // Directory for image uploads
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', BASE_URL.'uploads/'); // URL to file uploads
}
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', BASE_PATH . 'uploads/'); // Path to file uploads
}
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', '/uploads/'); // Directory for file uploads
}


?> 
