<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once("core.php"); 





// require_once  $_SERVER['DOCUMENT_ROOT'].'/fl_set/vendor/autoload.php';

// /*
// $client = new Google_Client();
// $client->setAuthConfig($_SERVER['DOCUMENT_ROOT'].'/fl_core/client_secret.json');
// $client->addScope('https://www.googleapis.com/auth/calendar');
// $client->addScope("email");
// $client->addScope("profile"); 
// $client->setAccessType('offline');
// $client->setSubject('account@domain.com');
// */

// if(defined('googleLogin')) {
// $client = new Google_Client();
// $client->setAuthConfig($_SERVER['DOCUMENT_ROOT'].'/fl_core/client_secrets.json');
// $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);

// if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
// 	$client->setAccessToken($_SESSION['access_token']);
// } else {
// 	$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/login.php';
// 	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
// }
// }





$callStartTime = microtime(true);

if(!isset($_SESSION['user']) || !isset($_SESSION['operatore'])){  header('Location: '.ROOT.$cp_admin.'login.php'); exit; }
if(isset($_GET['redirect'])){  header('Location: '.check($_GET['redirect'])); exit; }
if(isset($_SESSION['aggiornamento_password']) && @$_SESSION['aggiornamento_password'] < -90 && !isset($change_password))  { header('Location: '.ROOT.$cp_admin.'fl_modules/mod_account/?active=strumenti'); exit; } 
if(!isset($_SESSION['active'])) $_SESSION['active'] = 'dashboard';
$_SESSION['active'] = ( isset($_GET['a']) ) ? check($_GET['a']) : $_SESSION['active'];
$notifiche = notifiche(0);
if(!isset($_SESSION['last_check']) || (time()-@$_SESSION['last_check']) > 60) check_message();

?>