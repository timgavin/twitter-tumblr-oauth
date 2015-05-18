<?php

	// user wants to authorize our app with a service
	if(isset($_GET['service'])) {
	
		// set some empty sessions
		$_SESSION['oauth_token'] = $_SESSION['oauth_token_secret'] = false;
		
		// user has clicked a 'connect with' button, redirect to appropriate service for authorization
		$service = filter_input(INPUT_GET,'service',FILTER_SANITIZE_STRING);
		
		// init our Oauth class
		$oauth = new Oauth($service, $db);
		
		// set some params
		$oauth->callback_url = CALLBACK_URL;
		
		// call our connect method
		$oauth->connect();
	}
	
	
	// user has granted access at the service provider's site, let's authenticate
	elseif(isset($_GET['callback'])) {
		
		// get the service to which the user wants to connect
		$service = filter_input(INPUT_GET,'callback',FILTER_SANITIZE_STRING);
		
		// init our Oauth class
		$oauth = new Oauth($service, $db);
		
		// set some params
		$oauth->userid = $_SESSION['uid'];
		$oauth->callback_url = CALLBACK_URL;
		$oauth->success_url  = CALLBACK_URL.'?authenticated='.$service;
		
		// call our callback method
		$oauth->callback();
	}
	
	
	// user want's to revoke our app's authentication
	elseif(isset($_GET['revoke'])) {
	
		$service = (string) $_GET['revoke'];
		
		if($oauth = $db->query("DELETE FROM users_oauth WHERE service='".$db->escape_string($service)."' AND uo_usr_id=".$db->escape_string($_SESSION['uid'])) !== false) {
			$msg 	 = 'Access to '.ucwords($service).' has been revoked';
			$message = messages($msg,'success');
		}
	
	}
	
	
	// put the connected services into an array for when we display the buttons and get the user's info
	else {
		
		// user has been authenticated; display success message
		if(isset($_GET['authenticated'])) {
			$msg 	 = '<h3>Connected!</h3><p>You have successfully authenticated your account with '.ucwords((string) $_GET['authenticated']).'</p>';
			$message = messages($msg,'success');
		}
	
	}
	
	// if the user has authenticated any services they'll be in our database; let's get them!
	if($connection = $db->query("SELECT service, service_username, service_userurl, oauth_token, oauth_secret FROM users_oauth WHERE uo_usr_id=".$db->escape_string($_SESSION['uid']))) {
		foreach($connection as $row){
			array_push($connected_services, $row['service']);
		}
	}