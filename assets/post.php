<?php
	
	// use the twitter oauth class...
	use Abraham\TwitterOAuth\TwitterOAuth;
	
	/*
		This file is included when the user is posting something.
		
		I'm doing it this way because I think it's easier to break everything down so you 
		can see what's really going on. I didn't want to have a shit-ton of code buried
		in one file, or a bunch of ajax requests cluttering up the place. Harder to 
		learn that way. :)
	
		NOTE: I'm not writing custom error messages in our catch blocks because we're 
		developing the app so we need to see the actual errors returned from Tumblr's 
		API. Normally you'd want to hide those from the user and create something custom.
	*/
	
	// clean our post data
	$title = filter_var($_POST['title'],FILTER_SANITIZE_STRING);
	
	// get the textarea contents
	// for brevity in this app: this is our tweet, tumblr photo caption or tumblr text post
	$text  = filter_var($_POST['text'],FILTER_SANITIZE_STRING);
	
	// tags
	$tags = filter_var($_POST['tags'],FILTER_SANITIZE_STRING);
	
	$posted['twitter'] = null;
	$posted['tumblr']  = null;
	
	$image = $type = $file = $response = null;
	$photos = array();

	// we're not going to bother checking image types, sizes, etc. leave that for another tutorial
	// all we want to do for this example is get the image into the uploads folder and then pass it on to the service

	// we've also named our form file input 'files[]' so that we may upload one file, or many
	// the following block will handle both
	
	if($_FILES['file']['error'][0] === 0) {
	
		for($i = 0; $i < count($_FILES['file']['tmp_name']); $i++) {

			if(!empty($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'][$i])) {

				$handle['name'] 	= $_FILES['file']['name'][$i];
				$handle['size'] 	= $_FILES['file']['size'][$i];
				$handle['type'] 	= $_FILES['file']['type'][$i];
				$handle['tmp_name'] = $_FILES['file']['tmp_name'][$i];

				$file = SITE_ROOT.'/uploads/'.$handle['name'];
				move_uploaded_file($handle['tmp_name'], $file);
			
				// add the image to our $photos array
				array_push($photos, $file);
			}
		}
	}
	
	
	// get our user's information for both twitter and tumblr and put them into their respective arrays
	if($oauth = $db->query("SELECT service, service_username, service_userurl, oauth_token, oauth_secret FROM users_oauth WHERE uo_usr_id = ".$db->escape_string($_SESSION['uid']))) {
		
		$twitter = array();
		$tumblr  = array();
		
		foreach($oauth as $key => $value) {
		
			if($value['service'] == 'twitter') {
				$twitter['service_username']= $value['service_username'];
				$twitter['service_userurl'] = $value['service_userurl'];
				$twitter['oauth_token'] 	= $value['oauth_token'];
				$twitter['oauth_secret'] 	= $value['oauth_secret'];
			}
			
			if($value['service'] == 'tumblr') {
				$tumblr['service_username'] = $value['service_username'];
				$tumblr['service_userurl'] 	= $value['service_userurl'];
				$tumblr['oauth_token'] 		= $value['oauth_token'];
				$tumblr['oauth_secret'] 	= $value['oauth_secret'];
			}
		}
	}
	
	// if the post to twitter checkbox was checked...
	if(isset($_POST['tweet'])) {
		
		$twitteroauth = new TwitterOAuth(TWITTER_OAUTH_KEY, TWITTER_OAUTH_SECRET, $twitter['oauth_token'], $twitter['oauth_secret']);
		
		// tweeting a photo
		if(!empty($photos)) {
			
			// we're sending our photo(s) to twitter and receiving an id for each
			// put each id into an array for later
			
			$media = null;
			
			foreach($photos as $photo) {
				$m = $twitteroauth->upload('media/upload', array('media' => $photo));
				$media .= $m->media_id_string.',';
			}
			
			$media = rtrim($media,',');
			
			
			// attaching media will take away 23 characters from our 'status' string
			// if $text is more than 117 characters we must truncate it or twitter will reject
			if(strlen($text) > 117) {
				$text = substr($text, 0, 117);
			}
			
			// put our tweet into an array
			$data = array(
				'status' 	=> $text,
				'media_ids' => implode(',', array($media)),
			);
			
			// post to twitter!
			$result = $twitteroauth->post('statuses/update',$data);
		
		} else {
			
			// tweeting text
			if(!empty($text)) {
			
				// if $text is more than 140 characters we must truncate it or twitter will reject
				if(strlen($text) > 140) {
					$text = substr($text, 0, 140);
				}
				
				// put our tweet into an array
				$data = array(
					'status' => $text
				);
				
				// post to twitter!
				$result = $twitteroauth->post('statuses/update',$data);
			}
		}
		
		// make sure the connection was successful and set our success message
		if($twitteroauth->getLastHttpCode() == 200) {
			$posted['twitter'] = '<div class="col-sm-6"><a href="https://twitter.com/'.$twitter['service_username'].'/status/'.$result->id.'" target="_blank"><button class="btn btn-twitter btn-block"><i class="fa fa-twitter"></i> View Tweet on Twitter</button></a></div>';
		} else {
			$message = messages('<h3>Tweet not Sent</h3><p>'.$result->errors[0]->message.'</p>');
		}
	}
	
	
	// if the post to tumblr checkbox was checked...
	// also, don't post if there was an error with sending to twitter
	if(empty($message) && isset($_POST['tumble'])) {
		
		// create a new tumblr instance and authenticate our app
		$tumblroauth = new Tumblr\API\Client(TUMBLR_OAUTH_KEY, TUMBLR_OAUTH_SECRET);
		
		// authenticate user
		$tumblroauth->setToken($tumblr['oauth_token'], $tumblr['oauth_secret']);
		
		// let's make sure we've authenticated, otherwise the tumblr lib will crash our app with an ugly fatal error
		try {
			$tumblroauth->getUserInfo()->user;
		} catch (\Tumblr\API\RequestException $e) {
			$message = messages('<h3>WHO<i class="fa fa-frown-o"></i>PS!</h3><p>'.$e.'</p>');
		}
		
		if(empty($message)) {

			// if the user has more than one blog they will have been presented with a dropdown menu when submitting the post
			// let's get the url of the blog to which they'd like to send this post
		
			if(isset($_POST['blog_url'])) {

				// get the posted blog url
				$blog_url = (string) $_POST['blog_url'];

			} else {
			
				// this user only has one blog
				$blog_url = $tumblroauth->getUserInfo()->user->blogs[0]->url;
			}

			// strip the blog url down to subdomain.domain
			$url = parse_url($blog_url, PHP_URL_HOST);
			$blog_url = preg_replace('/^(ww\.)/i', '', $url);
		
			
			// check if we're uploading a photo
			if(!empty($photos)) {
				
				// post the photo(s) to tumblr api
				try {
					$response = $tumblroauth->createPost($blog_url, array('type' => 'photo', 'caption' => $text, 'data' => $photos, 'tags' => $tags));
				} catch (\Tumblr\API\RequestException $e) {
					// there was an error. you can email the API message to yourself, then spit out something to our user
					$message = messages('<h3>WHO<i class="fa fa-frown-o"></i>PS!</h3><p>'.$e.'</p>');
				}
		
			} else {
	
				// post the text to tumblr api
				try {
					$response = $tumblroauth->createPost($blog_url, array('type' => 'text', 'title' => $title, 'body' => $text, 'tags' => $tags));
				} catch (\Tumblr\API\RequestException $e) {
					// there was an error. you can email the API message to yourself, then spit out something to our user
					$message = messages('<h3>WHO<i class="fa fa-frown-o"></i>PS!</h3><p>'.$e.'</p>');
				}
			}
			
			// success!
			if($response->id) {
				$posted['tumblr'] = '<div class="col-sm-6"><a href="http://'.$blog_url.'/post/'.$response->id.'/" target="_blank"><button class="btn btn-tumblr btn-block"><i class="fa fa-tumblr"></i> View Post on Tumblr</button></a></div>';
			}
		}
			
	}
	
	
	
	// messages
	if($posted['twitter'] && $posted['tumblr']) {
		$message = messages('<h3>YAY!</h3><div class="row">'.$posted['twitter'].$posted['tumblr'].'</div>','success');
	}
	elseif($posted['twitter']) {
		$message = messages('<h3>YAY!</h3><div class="row">'.$posted['twitter'].'</div>','success');
	}
	elseif($posted['tumblr']) {
		$message = messages('<h3>YAY!</h3><div class="row">'.$posted['tumblr'].'</div>','success');
	}

	// user forgot to tick a checkbox when posting...
	if(!isset($_POST['tumble']) && !isset($_POST['tweet'])) {
		$message = messages('<h3>Nothing Posted</h3><p>You didn\'t select a site!</p>');
	}