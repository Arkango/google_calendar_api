<?php 
		
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
				if(!isset($_GET['NO_BACK_PAGE'])) $_SESSION['POST_BACK_PAGE'] = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; 

		$idInterno = (isset($_GET['idInterno'])) ? "idInterno=".check($_GET['idInterno']) : '';	
	
		require_once('google-calendar-api.php');
		$capi = new GoogleCalendarApi();

		$calendars = $capi->GetCalendarsList($_SESSION['access_token']['access_token']);

		//print_r($calendars);

		$links = '';

		foreach($calendars as $key => $values){
			$links .= '<li><a href="?id='.$values['id'].'">'.$values['summary'].'</a></li>';
		}

		/*

		
		GET CALENDARI DA GOOGLE
		CREARE LA ARRAY DEI COLORI SOPRA COLLEGANDOLA AGLI ID DEI CALENDARI


		*/	

	

		//$calendari = array();	

		echo $module_menu = '<li><a href=\"c=0&b=0\">Tutti</a></li>'.$links;


		$b = (!isset($_GET['b'])) ? 'Tutti' : check($_GET['b']);
	
		
		// foreach($calendari as $idCalendario => $nomeCalendario){ // Recursione Indici di Categoria

		// $selected = ($b  == $label) ? " class=\"selected\"" : "";
		// $module_menu .= "<li $selected><a href=\"$link&b=$nomeCalendario\">".ucfirst($nomeCalendario)."</a></li>\r\n"; 
		// $colors[] = '';
		// }
	

		//$colors = array(122=>'#380fa8',123=>'#3DA042',124=>'#4c9ed9',125=>'#E3CC23',195=>'#B148B0');


		/*

		
		GET EVENTI DA GOOGLE
		SE 0 CARICARE TUTTI GLI EVENTI


		*/	

		$id_calendario = (isset($_GET['id'])) ? $_GET['id'] : null ;

		if($id_calendario == null){
			echo 'è necessario selezionare un calendario';
			exit;
		}

		$_SESSION['calendar_id'] = $id_calendario;

		$events = $capi->GetCalendarEvents($id_calendario,$_SESSION['access_token']['access_token']);

	
		$eventi = '';



		foreach($events as $key => $values){


			$allDay = false;

	


			if(isset($values['start']['date'])){
				$start = $values['start']['date'];
				$end = $values['start']['date'] ;
				$allDay = true;
			}else{
				$start = $values['start']['dateTime'];
				$end = $values['start']['dateTime'] ;
			}
			
			$eventi .= "
			{
				title: '".$values['summary']."',
				start: '".$start."',
				end: '".$end."',
				url: './mod_modifica.php?id=".$values['id']."',
				allDay: '".$allDay."',
				color: ''
			},";
		
		}
		
		// while ($riga = mysql_fetch_array($risultato)) 
		// {
		// 	$coloreSelezionato = $colors[122]; // se si puo prendere da colore calendario
		// 	$eventi .= "
		// 	{
		// 		title: '".str_replace("&rsquo;", "\'", str_replace("'", "",$riga['nominativo']))."',
		// 		start: '".str_replace(" ","T",$riga['start'])."',
		// 		end: '".str_replace(" ","T",$riga['end_meeting'])."',
		// 		url: './mod_modifica_calendario.php?',
		// 		allDay: false,
		// 		color: '".@$coloreSelezionato."'
		// 	},";
		// } ?>



<link href='<?php echo ROOT.$cp_admin.$cp_set; ?>jsc/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo ROOT.$cp_admin.$cp_set; ?>jsc/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo ROOT.$cp_admin.$cp_set; ?>jsc/fullcalendar/lib/moment.min.js'></script>
<script src='<?php echo ROOT.$cp_admin.$cp_set; ?>jsc/fullcalendar/fullcalendar.min.js'></script>
<script src='<?php echo ROOT.$cp_admin.$cp_set; ?>jsc/fullcalendar/lang/it.js'></script>

<script>
		
		$(document).ready(function() {

						$('#calendar').fullCalendar({
							lang: 'it',
							header: {
								left: 'prevYear,prev,today,next,nextYear',
								center: 'title',
								right: 'month,agendaWeek,agendaDay'
							},
							selectable: true,

							select: function(start, end, allDay) {

								if (start) {
									var date = start.format("YYYY-MM-DD HH:mm") ;
									var dateend = end.format("YYYY-MM-DD HH:mm") ;
									window.location.href = 'mod_inserisci_calendario.php?idInterno=1&start=' + date + '&end='+ dateend
								}
								
							},
							editable: true,
							defaultDate: '<?php echo date('Y-m-d'); ?>',
							defaultView: 'agendaWeek',	
							editable: false,
							eventLimit: true, // allow "more" link when too many events

			events: [
			
			<?php echo $eventi ; ?> 


			]
		});

					});


</script>



<div id="calendar"></div>



