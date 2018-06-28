<?php
require_once('../../fl_core/autentication.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.js"></script>
<style type="text/css">

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
<br>
<br>
<br>
<br>
<a style="text-decoration: none" href="calendarList.php"><button id="logout">Vedi i tuoi calendari</button></a>
<br>
<br>
<a style="text-decoration: none" href="createEvent.php"><button id="logout">Aggiungi evento</button></a>

</body>
</html>