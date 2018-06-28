<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once  $_SERVER['DOCUMENT_ROOT'].'/fl_set/vendor/autoload.php';
if(defined('googleLogin')) {
	$client = new Google_Client();
	$client->setAuthConfig($_SERVER['DOCUMENT_ROOT'].'/fl_core/client_secrets.json');
	$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
	
	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
		$client->setAccessToken($_SESSION['access_token']);
	} else {
		$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/login.php';
		header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	}
	}

$calendarService = new Google_Service_Calendar($client);

class GoogleCalendarApi extends genericGoogle
{
	protected $plus;
	public function __construct(){
		parent::__construct();
		
        $this->plus =$GLOBALS['calendarService'];
	}
	/*********************************************************************************/
	public function GetUserCalendarTimezone($access_token) {
		$url_settings = 'https://www.googleapis.com/calendar/v3/users/me/settings/timezone';
		$data = parent::googleCurl($url_settings,false,false,true,false,'Failed to get timezone');
		return $data['value'];	
	}
	/*********************************************************************************/
	public function GetCalendarsList() {
		$url_parameters = array();
		$url_parameters['fields'] = 'items(id,summary,timeZone)';
		$url_parameters['minAccessRole'] = 'owner';
		$url_calendars = 'https://www.googleapis.com/calendar/v3/users/me/calendarList?'. http_build_query($url_parameters);
		$data = parent::googleCurl($url_calendars,false,false,true,false,'Failed to get calendars list');
		return $data['items'];	
	}
	/*********************************************************************************/
	public function GetCalendarEvents($calendar_id,$from = false,$to = false){
		$url_parameters = array();
		if($from){
			$url_parameters['timeMin'] = $from.'T00:00:00Z';
		}
		if($to){
			$url_parameters['timeMax'] = $to.'T00:00:00Z';
		}

		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events?'.http_build_query($url_parameters);
		$data = parent::googleCurl($url_events,false,false,true,true,'Failed to retrieve events');
		return $data['items'];

	}
	/*********************************************************************************/
	public function CreateCalendarEvent($calendar_id, $summary, $all_day, $event_time, $event_timezone, $access_token) {
		$url_events = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';

		$curlPost = array('summary' => $summary);
		if($all_day == 1) {
			$curlPost['start'] = array('date' => $event_time['event_date']);
			$curlPost['end'] = array('date' => $event_time['event_date']);
		}
		else {
			$curlPost['start'] = array('dateTime' => $event_time['start_time'], 'timeZone' => $event_timezone);
			$curlPost['end'] = array('dateTime' => $event_time['end_time'], 'timeZone' => $event_timezone);
		}

		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url_events);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_POST, 1);		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '. $access_token, 'Content-Type: application/json'));	
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));	
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200) 
			throw new Exception('Error : Failed to create event');

		return $data['id'];
		$data = parent::googleCurl($url_events,true,$curlPost,true,true,'Failed to create event');
		return $data['id'];
	}
	/*********************************************************************************/
	public function DeleteEvents($calendar_id,$eventId){

		$this->plus->events->delete($calendar_id, $eventId);

		echo $url_delete_event = "https://www.googleapis.com/calendar/v3/calendars/".$calendar_id."/events/".$eventId;
		$data = parent::googleCurl($url_delete_event,false,false,true,true,'Failed to delete event','DELETE');
		if($data == ''){
			return true;
		}else{
			return false;
		}
	}
	/*********************************************************************************/
	public function UpdateEvents($values = array())
	{
		# code...
	}
	/*********************************************************************************/
	public function GetSingleEvent($calendar_id,$event_id)
	{
		$url_event = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events?'.$event_id;
		$data = parent::googleCurl($url_event,false,false,true,true,'Failed to retrieve event id'.$event_id);
		return $data;
	}
}//fine classe


abstract class genericGoogle{

	public function __construct(){
		
	}

	public function googleCurl($url = false,$post = false, $curlPost = false,$header = false ,$json = false, $messaggioErrore = 'messaggio generico errore',$type = false ){

		$ch = curl_init();
		$arrayHeader = array("Authorization: Bearer ".$_SESSION['access_token']['access_token']);
		
		if(!$url){//validità url
			echo "url non valido";
			exit;
		}

		if($post){//chiamat di tipo post
			curl_setopt($ch, CURLOPT_POST, 1);
		}

		if($curlPost){//field da invare in post
			if(strpos('oauth2', $url) != false){
				$postFields = json_encode($curlPost);
			}else{
				$postFields = $curlPost;
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		}


		if($json){
			array_push($arrayHeader,'Content-Type: application/json');
		}

		if($header){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);	
		}

		if($type){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		}

		curl_setopt($ch, CURLOPT_URL, $url);		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);				
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$data = json_decode(curl_exec($ch), true);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);		
		if($http_code != 200){
			throw new Exception('Error : '.$messaggioErrore);
			exit;
		}else{
			return $data;
		}
	}

}

?>