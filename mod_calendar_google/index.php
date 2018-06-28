<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../fl_core/autentication.php');
include('fl_settings.php'); // Variabili Modulo 


include("../../fl_inc/headers.php");

if(!isset($_GET['external'])) include('../../fl_inc/testata.php'); 
if(!isset($_GET['external'])) include('../../fl_inc/menu.php'); 
if(!isset($_GET['external'])) include('../../fl_inc/module_menu.php'); 



/* Inclusione Pagina */
if($_SESSION['access_token'] == 1) {
    include("google-login.php"); 
}else{
    include("mod_calendario.php"); 
}




include("../../fl_inc/footer.php"); ?>