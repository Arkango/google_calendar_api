<?php


session_start();
include('core.php');

//if(defined('googleLogin')) {
require_once $_SERVER['DOCUMENT_ROOT'].'/fl_set/vendor/autoload.php';
$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/fl_core/googleLogin.php');
$client->addScope('https://www.googleapis.com/auth/calendar');
$client->addScope("email");
$client->addScope("profile"); 
$client->setAccessType('offline');
//}


function logout($msg = '',$isLogout = false){
	global $client;
	if($isLogout){
	$queryx = "INSERT INTO `fl_accessi` ( `id` , `ip` , `agent` , `referrer` , `lang` , `data_creazione` , `pagina` , `utente` ,`session_id` )VALUES ('', '".@$_SERVER['REMOTE_ADDR']."', '', '".@$_SERVER['HTTP_REFERER']."', 'it', NOW(), 'Logout', '".$_SESSION['user']."','".session_id()."');";
	mysql_query($queryx, CONNECT);
	}
	session_unset();
	setcookie('user','');	
	mysql_close(CONNECT);
	if(defined('googleLogin')) $client->revokeToken();
	session_destroy();
	$msg = ($msg != '') ? 'esito='.$msg : '';
	$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/login.php?'.$msg;
	header('Location: ' . check($redirect_uri));
	exit;
}



if (!isset($_GET['code'])) {

	if(isset($_GET['logout'])){	
		logout('',true);
	}

	if($_POST['user'] != '' && $_POST['pwd'] != '' ){

		$pwd = md5(check($_POST['pwd']));
		$email = check($_POST['user']);

		//query vedo se presente nel mio DB
		$query = "SELECT * FROM `fl_account`  WHERE `user`  = '$email' AND password = '$pwd' AND attivo = 1 LIMIT 1";
		
		$risultato = mysql_query($query, CONNECT);
		if(mysql_affected_rows() == 0){
			logout('USERNAME OR PASSWORD WRONG');		
		}else{

			$riga = mysql_fetch_array($risultato);

			if($riga['user'] == $email){
			//se non c'è data scadenza o è minore di oggi FACCIO LOGOUT
				if(isset($riga['data_scadenza']) && $riga['data_scadenza'] < date('Y-m-d')){ logout(); }

				$_SESSION['access_token'] = 'normalTokencreated';

				$utente = get_anagrafica($riga['anagrafica']);
				$permessi = get_permessi($riga['id']);
				$fido = get_fido($riga['anagrafica']);
				$last_login = last_login($email);

			//Autenticazione avvenuta
				session_cache_limiter('private' );
				session_cache_expire(30);

				$_SESSION['user'] = $riga['user'];
				$_SESSION['operatore'] = 1;
				$_SESSION['usertype'] = $riga['tipo'];	
				$_SESSION['nome'] = $riga['nominativo'];
				$_SESSION['mail'] = $riga['email'];	
				$_SESSION['marchio'] = $riga['marchio'];			
				$_SESSION['number'] = $riga['id'];			
				$_SESSION['time'] = $data;	
				$_SESSION['idh'] = $ip;
				$_SESSION['anagrafica'] = ($riga['anagrafica'] > 1) ? $riga['anagrafica'] : 1;
				$_SESSION['profilo_commissione'] = @$utente['profilo_commissione'];
				$_SESSION['profilo_genitore'] = @$utente['profilo_genitore'];
				$_SESSION['aggiornamento_password'] = giorni(@$riga['aggiornamento_password']);
				$_SESSION['last_login'] = mydatetime(@$last_login['data_creazione'])." (".@$last_login['ip'].")";
				$_SESSION['permessi'] = $permessi;
				$_SESSION['fido'] = $fido;
				$_SESSION['http_host'] = (isset($_POST['http_host'])) ? check($_POST['http_host']) : $_SERVER['http_host'];
				action_record('login','LOGIN',$riga['id'],'Login'); 

			// Fine Avvio Sessione	
				$querys = "UPDATE `".$tables[8]."` SET `visite` = visite+1 WHERE id = ".$riga['id']." LIMIT 1;"; 		
				$queryx = "INSERT INTO `fl_accessi` ( `id` , `ip` , `agent` , `referrer` , `lang` , `data_creazione` , `pagina` , `utente`,`session_id` )VALUES ('', '$ip', '$agent', '".@$_SERVER['HTTP_REFERER']."', 'it', NOW(), 'Login', '$email','".session_id()."');";
				 
				mysql_query($queryx, CONNECT);
				mysql_query($querys, CONNECT);	

				check_message();	
				mysql_close(CONNECT);	

				if(isset($_SESSION['redirect'])){ header("Location: ".check($_SESSION['redirect'])); }


					$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/index.php';
					header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
					exit;	

		}//riga user == $user



	}
}

$auth_url = $client->createAuthUrl();
  	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
  	
} else {



	$client->authenticate($_GET['code']);
	$infoaccess_token= $client->getAccessToken();
	$_SESSION['access_token'] = $infoaccess_token['access_token'];
	$plus = new Google_Service_Plus($client);
	$person = $plus->people->get('me');
	$googleId = $person['id'] ;//recupero id google+
	$email  = $person['emails'][0]->value;//valore della mail




	//query vedo se presente nel mio DB
	 $query = "SELECT * FROM `fl_account`  WHERE `user`  = '$email' LIMIT 1";

	 $risultato = mysql_query($query, CONNECT);
	 if(mysql_affected_rows() == 0){
	 	logout('YOUR GOOGLE EMAIL DOESN\'T EXIST ');		
	 }else{

	 	$riga = mysql_fetch_array($risultato);

	 	if($riga['user'] == $email){
			//se non c'è data scadenza o è minore di oggi FACCIO LOGOUT
	 		if(isset($riga['data_scadenza']) && $riga['data_scadenza'] < date('Y-m-d')){ logout(); }

	 		$_SESSION['access_token'] = $client->getAccessToken();

	 		$utente = get_anagrafica($riga['anagrafica']);
	 		$permessi = get_permessi($riga['id']);
	 		$fido = get_fido($riga['anagrafica']);
	 		$last_login = last_login($user);

			//Autenticazione avvenuta
	 		session_cache_limiter('private' );
	 		session_cache_expire(30);

	 		$_SESSION['user'] = $riga['user'];
	 		$_SESSION['operatore'] = 1;
	 		$_SESSION['usertype'] = $riga['tipo'];	
	 		$_SESSION['nome'] = $riga['nominativo'];
	 		$_SESSION['mail'] = $riga['email'];	
	 		$_SESSION['marchio'] = $riga['marchio'];			
	 		$_SESSION['number'] = $riga['id'];			
	 		$_SESSION['time'] = $data;	
	 		$_SESSION['idh'] = $ip;
	 		$_SESSION['anagrafica'] = ($riga['anagrafica'] > 1) ? $riga['anagrafica'] : 1;
	 		$_SESSION['profilo_commissione'] = @$utente['profilo_commissione'];
	 		$_SESSION['profilo_genitore'] = @$utente['profilo_genitore'];
	 		$_SESSION['aggiornamento_password'] = giorni(@$riga['aggiornamento_password']);
	 		$_SESSION['last_login'] = mydatetime(@$last_login['data_creazione'])." (".@$last_login['ip'].")";
	 		$_SESSION['permessi'] = $permessi;
	 		$_SESSION['fido'] = $fido;
	 		$_SESSION['http_host'] = (isset($_POST['http_host'])) ? check($_POST['http_host']) : $_SERVER['http_host'];
	 		action_record('login','LOGIN',$riga['id'],'Login'); 

			// Fine Avvio Sessione	
	 		$querys = "UPDATE `".$tables[8]."` SET `visite` = visite+1 WHERE id = ".$riga['id']." LIMIT 1;"; 		
	 		$queryx = "INSERT INTO `fl_accessi` ( `id` , `ip` , `agent` , `referrer` , `lang` , `data_creazione` , `pagina` , `utente`,`session_id` )VALUES ('', '$ip', '$agent', '".@$_SERVER['HTTP_REFERER']."', 'it', NOW(), 'Login', '$email','".session_id()."');";
	 		
	 		mysql_query($queryx, CONNECT);
	 		mysql_query($querys, CONNECT);	

	 		check_message();	
	 		mysql_close(CONNECT);	

	 		if(isset($_SESSION['redirect'])){ header("Location: ".check($_SESSION['redirect'])); }


	 			$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . '/index.php';
	 			header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	 			exit;	

		}//riga user == $user
		
	}//mysql affected rows

	logout();
}


		?>