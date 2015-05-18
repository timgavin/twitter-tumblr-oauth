<?php

// use the twitter oauth class...
use Abraham\TwitterOAuth\TwitterOAuth;

class Oauth {
	

	// This is a small wrapper class I whipped up so that we can use the same function names with multiple services. Use at your own risk.
	// © 2015. http://github.com/timgavin

	
	
	// add/configure OAuth services here
	// note: if adding other services you'll need to add them to the middle of the methods, between the 'service config' comments
	public function __construct($service,$db) {
	
		$this->service = strtolower($service);
		$this->db = $db;
		
		// set our KEY & SECRET constants in config.php
		
		if($this->service == 'twitter') {
			$this->oauth_key       = TWITTER_OAUTH_KEY;
			$this->oauth_secret    = TWITTER_OAUTH_SECRET;
			$this->verify_endpoint = 'account/verify_credentials';
		}
		
		if($this->service == 'tumblr') {
			$this->oauth_key       = TUMBLR_OAUTH_KEY;
			$this->oauth_secret    = TUMBLR_OAUTH_SECRET;
			$this->verify_endpoint = 'http://api.tumblr.com/v2/user/info';
		}
	}
	
	
	public function connect() {
		
		// build an OAuth object using our application's keys
		if($this->service == 'twitter') {
			$connection = new TwitterOAuth($this->oauth_key, $this->oauth_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		}
		if($this->service == 'tumblr') {
			$connection = new TumblrOAuth($this->oauth_key, $this->oauth_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		}
		
		// get temporary credentials from service
		if($this->service == 'twitter') {
			$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $this->callback_url.'?callback='.$this->service));
		}
		if($this->service == 'tumblr') {
			$request_token = $connection->getRequestToken($this->callback_url.'?callback='.$this->service);
		}

		// save temporary credentials to a session for use in callback
		$_SESSION['oauth_token'] 		= $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		
		// if connection is successful, build authorization link and redirect to service so the user can authorize our app
		if($this->service == 'twitter') {
			if($connection->getLastHttpCode() == 200){
				$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
				header('location: '. $url);
			} else {
				// It's a bad idea to kill the script, but we've got to know when there's an error.
				die('Authorization failed. <a href="'.$this->callback_url.'?service='.$this->service.'">Try again</a>');
			}
		}
		if($this->service == 'tumblr') {
			if($connection->http_code == 200){
				header('location:'.$connection->getAuthorizeURL($request_token['oauth_token']));
			} else {
				// It's a bad idea to kill the script, but we've got to know when there's an error.
				die('Authorization failed. <a href="'.$this->callback_url.'?service='.$this->service.'">Try again</a>');
			}
		}
	
	}

	
	public function callback() {
		
		// we're going to store the tumblr blog url(s); set an empty var here to avoid errors later
		$urls = '';
					
		// chances are the user rejected the authorization by clicking the deny button on the service's auth page
		if(!isset($_GET['oauth_verifier'])) {
			
			header('location:'.$this->callback_url);
		
		} else {
			
			// if the oauth_token is old redirect to the connect page and get a new one
			if(!empty($_REQUEST['oauth_token']) && ($_SESSION['oauth_token'] !== $_REQUEST['oauth_token'])) {
				
				header('location:'.$this->callback_url.'?service='.$this->service);
			
			} else {
				
				// user is attempting to authorize our app
				// create OAuth object with client key/secret and token key/secret
				
				
				if($this->service == 'twitter') {
					$connection = new TwitterOAuth($this->oauth_key, $this->oauth_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
				}
				if($this->service == 'tumblr') {
					$connection = new TumblrOAuth($this->oauth_key, $this->oauth_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
				}
				
			
				// request the user's access tokens from service
				if($this->service == 'twitter') {
					$user_access_tokens = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
				}
				if($this->service == 'tumblr') {
					$user_access_tokens = $connection->getAccessToken($_REQUEST['oauth_verifier']);
				}

			
				// make sure the tokens match!
				if(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] === $_REQUEST['oauth_token']) {
				
					// discard temporary tokens
					unset($_SESSION['oauth_token']);
					unset($_SESSION['oauth_token_secret']);
				
					
					// now we create an OAuth object with the user's tokens
					if($this->service == 'twitter') {
						$connection = new TwitterOAuth($this->oauth_key, $this->oauth_secret, $user_access_tokens['oauth_token'], $user_access_tokens['oauth_token_secret']);
					}
					if($this->service == 'tumblr') {
						$connection = new TumblrOAuth($this->oauth_key, $this->oauth_secret, $user_access_tokens['oauth_token'], $user_access_tokens['oauth_token_secret']);
					}
					
				
					// next we authenticate the user and retrieve their info
					if($this->service == 'twitter') {
						$user = $connection->get('account/verify_credentials');
					}
					if($this->service == 'tumblr') {
						$user = $connection->get($this->verify_endpoint);
					}
					
					
					// get the user's service username
					if($this->service == 'twitter') {
						$username = $user->screen_name;
					}
					if($this->service == 'tumblr') {
						$username = $user->response->user->name;
						foreach($user->response->user->blogs as $item){
							$urls .= rtrim($item->url,'/').',';
						}
						$urls = trim($urls, ',');
					}
					
					// insert the user's tokens and service username into our database
					// you could also check to see if the user has already registered your app with their blog and refresh their tokens instead
					
					if($user) {
						$query = "
						INSERT IGNORE users_oauth (
							 uo_usr_id
							,service
							,service_username
							,service_userurl
							,oauth_token
							,oauth_secret
						) VALUES (
							 ".$this->db->escape_string($this->userid).",
							'".$this->db->escape_string($this->service)."',
							'".$this->db->escape_string($username)."',
							'".$this->db->escape_string($urls)."',
							'".$this->db->escape_string($user_access_tokens['oauth_token'])."',
							'".$this->db->escape_string($user_access_tokens['oauth_token_secret'])."'
						)";
						$this->db->query($query);
					
						// transaction successful, redirect to account page, home page, post page, etc.
						header('location: '.$this->success_url);
					} else {
						// instead of killing the script, you'd be better off adding your messaging functions here...
						die('Failed to connect. <a href="'.$this->callback_url.'?service='.$this->service.'">Try again</a>');
					}
				} else {
					// instead of killing the script, you'd be better off adding your messaging functions here...
					die('Authorization failed. <a href="'.$this->callback_url.'?service='.$this->service.'">Try again</a>');
				}
			}
		}
	}

}