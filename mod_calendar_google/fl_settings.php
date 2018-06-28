<?php 
	
	// Variabili Modulo
	$active = 1;
	$sezione_tab = 1;
	$tab_id = 71;
	$tabella = $tables[$tab_id];
	$select = "*";
	$step = 1000; 
	$sezione_id = -1;
	$jorel = 0;
	//$text_editor = 0;
	$jquery = 1;
	$searchbox = "Cerca nome...";
	$fancybox = 1;
	$calendar = 1;
	$documentazione_auto = 8;
	$dateTimePicker = 1;
	if($_SESSION['usertype'] == 0 || $_SESSION['usertype'] == 3) { $filtri = 1; }
    $module_title = "Agenda Appuntamenti";
		
	
	/* RICERCA */
	$tipologia = 0;
	$tipologia_main = "WHERE id != 1 ";
	if(isset($userid) && @$userid > 0) {  $tipologia_main .= " AND potential_rel = $userid ";	 }

	
	/* Inclusioni Oggetti Dati */
	include('../../fl_core/dataset/array_statiche.php');
	require('../../fl_core/class/ARY_dataInterface.class.php');
	$data_set = new ARY_dataInterface();

	$proprietario = $data_set->data_retriever('fl_account','nominativo',"WHERE id != 1 AND attivo = 1",'nominativo ASC');
	$tipologia_appuntamento = $data_set->get_items_key("tipologia_appuntamento");
	unset($tipologia_appuntamento[0]);

	function select_type($who){
	
	/* Gestione Oggetto Statica */	
	$textareas = array(); 
	$select = array();
	$disabled = array();
	$hidden = array("data_creazione",'proprietario','marchio','callcenter',"data_arrived",'potential_rel','is_customer',"data_aggiornamento","marchio","ip","operatore");
	$radio = array();
	$text = array();
	$calendario = array();	
	$file = array();
	$timer = array();
	$touch = array();
	$checkbox = array('tipologia_appuntamento');
	$datePicker = array('end_meeting','start_meeting','start_date','end_date');
	if(defined('MULTI_LOCATION')) { $select[] = 'meeting_location'; } else { $hidden[] = 'meeting_location'; }
	$type = 1;
	
	if(in_array($who,$select)) { $type = 2; }
	if(in_array($who,$textareas)){ $type = 3; }
	if(in_array($who,$disabled)){ $type = 4; }
	if(in_array($who,$radio)){ $type = 8; }
	if(in_array($who,$calendario)){ $type = 20; }
	if(in_array($who,$file)){ $type = 18; }
	if(in_array($who,$text)){ $type = 24; }
	if(in_array($who,$timer)){ $type = 7; }
	if(in_array($who,$datePicker)){ $type = 11; }
	if(in_array($who,$checkbox)){ $type = 19; }
	if(in_array($who,$hidden)){ $type = 5; }

	
	return $type;
	}
	
	


	
?>
