<?php
	
	session_start();
	
	
	// create a fake user account for this example
	$_SESSION['uid'] = 1;
	
	// turns on developer messages
	$notifications = true;
	
	// turn on/off tweet/tumblr caching
	$caching = true;
	
	// define path to root and site url
	defined('SITE_ROOT')? null : define('SITE_ROOT', realpath(__DIR__.'/..'));
	defined('SITE_URL') ? null : define('SITE_URL',  'http://localhost/oauthfun');		// Add the URL to this app
	
	// define Tumblr OAuth
	defined('TUMBLR_OAUTH_KEY')     ? null : define('TUMBLR_OAUTH_KEY', '');			// Tumblr OAuth credentials
	defined('TUMBLR_OAUTH_SECRET')  ? null : define('TUMBLR_OAUTH_SECRET', '');
	
	// define Twitter OAuth
    defined('TWITTER_OAUTH_KEY')    ? null : define('TWITTER_OAUTH_KEY', '');			// Twitter OAuth credentials
    defined('TWITTER_OAUTH_SECRET') ? null : define('TWITTER_OAUTH_SECRET', '');
	
	// define database credentials
	defined('DB_HOST') ? null : define('DB_HOST', 'localhost');							// MySQL credentials
	defined('DB_USER') ? null : define('DB_USER', '');
	defined('DB_PASS') ? null : define('DB_PASS', '');
	defined('DB_NAME') ? null : define('DB_NAME', 'oauth');
	
	// connect to database
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	if($db->connect_errno > 0) {
		die($db->connect_error);
	}
	
	// include functions
	require SITE_ROOT.'/assets/functions.php';

	// include the OAuth class
	require SITE_ROOT.'/assets/oauth/OAuth.php';
	
	// https://github.com/rchavik/TumblrOAuth
	require SITE_ROOT.'/assets/oauth/TumblrOAuth.php';
	
	// load the twitter and tumblr OAuth classes
	// https://github.com/abraham/twitteroauth
	// https://github.com/tumblr/tumblr.php
	require SITE_ROOT.'/assets/oauth/tumblr/vendor/autoload.php';
	require SITE_ROOT.'/assets/oauth/twitter/vendor/autoload.php';
	
	// include our connection/callback class
	require SITE_ROOT.'/assets/oauth/OauthConnections.php';
	
	// define the service's callback url
	defined('CALLBACK_URL') ? null : define('CALLBACK_URL', SITE_URL.'/index.php');
	
	// our services
	$services = array('tumblr', 'twitter');
	
	// set an empty messages var
	$message = null;