<?php
require_once('../../fl_core/autentication.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('google-calendar-api.php');

$capi = new GoogleCalendarApi();

$from = false;
$to = false;

if($_POST['from'] != ''){
	$from = $_POST['from']; 
}
if($_POST['to'] != ''){
	$to = $_POST['to']; 
}

$_SESSION['calendarIdScelto'] = $_POST['calendarId']; 

$eventList = $capi->GetCalendarEvents($_POST['calendarId'],$from,$to);

unset($_POST);

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.js"></script>
<style type="text/css">

#form-container {
	width: 200px;
	height: 200px;
	margin: 100px 50px;
	display: inline-block;
}

input[type="text"] {
	border: 1px solid rgba(0, 0, 0, 0.15);
	font-family: inherit;
	font-size: inherit;
	padding: 8px;
	border-radius: 0px;
	outline: none;
	display: block;
	margin: 0 0 20px 0;
	width: 100%;
	box-sizing: border-box;
}

select {
	border: 1px solid rgba(0, 0, 0, 0.15);
	font-family: inherit;
	font-size: inherit;
	padding: 8px;
	border-radius: 2px;
	display: block;
	width: 100%;
	box-sizing: border-box;
	outline: none;
	background: none;
	margin: 0 0 20px 0;
}

.input-error {
	border: 1px solid red !important;
}

#event-date {
	display: none;
}

#create-event {
	background: none;
	width: 100%;
    display: block;
    margin: 0 auto;
    border: 2px solid #2980b9;
    padding: 8px;
    background: none;
    color: #2980b9;
    cursor: pointer;
}

#logout {
	background: none;
	width: 42%;
    display: block;
    margin: 0 auto;
    border: 2px solid #2980b9;
    padding: 8px;
    background: none;
    color: #2980b9;
    cursor: pointer;
}

</style>
</head>

<body>

<?php

$divContent = '<div id="form-container">{{content}} <br><br><a style="text-decoration: none" href="editEvent.php?idEvent={{id}}"><button id="logout" >modifica</button></a><br><button id="logout">sposta data</button><br><a style="text-decoration: none" href="deleteEvent.php?idEvent={{id}}"><button id="logout">elimina</button></a></div>';
$calendar = '';

if(empty($eventList)){ echo "no events"; }

foreach ($eventList as $key => $value) {
	$calendar .= $divContent;
	$id = $value['id'];
	$status = $value['status'];
	$summary = $value['summary'];
	$start = (isset($value['start']['date']))?$value['start']['date']:$value['start']['dateTime'];
	$end = (isset($value['end']['date']))?$value['end']['date']:$value['end']['dateTime'];
	$content = 'mio id '.$id.'<br> mio status'.$status.'<br> mio nome'.$summary.'<br> inizio '.$start.'<br> fine'.$end;
	$calendar = str_replace('{{content}}', $content, $calendar);	
	$calendar = str_replace('{{id}}', $id, $calendar);	
}

echo $calendar;
?>
<a style="text-decoration: none" href="createEvent.php"><button id="logout">Aggiungi evento</button></a>
</body>
</html>
