<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/fl_core/autentication.php');

if($_GET['idEvent']){

	require_once('google-calendar-api.php');

	$capi = new GoogleCalendarApi();

	$delete = $capi->DeleteEvents($_SESSION['calendarIdScelto'],$_GET['idEvent']);

	if($delete){
		echo "evento cancellato con successo";
	}else{
		echo "evento non cancellato";

	}



}

?>