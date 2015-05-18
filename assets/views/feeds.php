<?php
	
	// use the twitter oauth class...
	use Abraham\TwitterOAuth\TwitterOAuth;
	
	// this document assembles and prints the twitter and tumblr feeds
	
	// get our user's oauth info if available
	if($oauth = $db->query("SELECT service, service_username, service_userurl, oauth_token, oauth_secret FROM users_oauth WHERE uo_usr_id = ".$db->escape_string($_SESSION['uid']))) {
		
		$tweets = $blog = null;
		
		// put our $oauth result values into their respective arrays
		$twitter = assign_oauth_values($oauth,'twitter');
		$tumblr  = assign_oauth_values($oauth,'tumblr');


		// get tweets
		if(!empty($twitter)) {

			// cache the tweets
			$twitter_cache = SITE_ROOT.'/cache/'.$twitter['service_username'].'-twitter.txt';		

			// if a cache file exists and is less than a minute old, use it, otherwise we're going to create it later in the tweets.php view
			if($caching && (file_exists($twitter_cache) && (filemtime($twitter_cache) > (time() - 60 * 1)))) {
				$tweets = file_get_contents($twitter_cache);
			}
			
			// get new tweets from the twitter api
			else {
				
				// create a new twitter instance
				$twitteroauth = new TwitterOAuth(TWITTER_OAUTH_KEY, TWITTER_OAUTH_SECRET, $twitter['oauth_token'], $twitter['oauth_secret']);
				
				// authenticate and get the tweets
				$tweets = $twitteroauth->get('statuses/user_timeline', array('screen_name' => $twitter['service_username'], 'exclude_replies' => true, 'include_rts' => false, 'count' => 30));
			}
		}
		
		
		// get tumblr posts
		if(!empty($tumblr)) {
			
			// cache the tumblr blog posts
			$tumblr_cache = SITE_ROOT.'/cache/'.$tumblr['service_username'].'-tumblr.txt';		

			// if a cache file exists and is less than a minute old, use it, otherwise we're going to create it later in the blog.php view
			if($caching && (file_exists($tumblr_cache) && (filemtime($tumblr_cache) > (time() - 60 * 1)))) {
				$blog = file_get_contents($tumblr_cache);
			}
			
			// get new posts from the tumblr api
			else {
				
				// create a new tumblr instance
				$tumblroauth = new Tumblr\API\Client(TUMBLR_OAUTH_KEY, TUMBLR_OAUTH_SECRET);

				// authenticate
				$tumblroauth->setToken($tumblr['oauth_token'], $tumblr['oauth_secret']);

				// let's make sure we've authenticated, otherwise the tumblr lib will crash our app with an ugly fatal error
				try {
					$user = $tumblroauth->getUserInfo()->user;
				} catch (\Tumblr\API\RequestException $e) {
					$message = messages('<h3>WHO<i class="fa fa-frown-o"></i>PS!</h3><p>'.$e.'</p>');
				}

				// get the user's 10 most recent posts from tumblr
				$blog = $tumblroauth->getBlogPosts($user->name, array('limit' => 10));
			}
		}
		
		
		// figure out the bootstrap column widths to display our content in a way that makes sense
		$columns = do_cols($blog, $tweets);
		
	}
?>


<?php if($oauth): ?>
	
	<div class="row">
	
		<?php if(!empty($tumblr)): ?>
			<div class="col-sm-<?php echo $columns['tumblr'] ?>" id="tumblr-posts">
				<h3 class="text-primary"><i class="fa fa-tumblr"></i> My Tumblr Posts</h3>
				<?php include 'assets/views/posts.php' ?>
			</div>
		<?php endif; ?>
		
		
		<?php if(!empty($twitter)): ?>
			<div class="col-sm-<?php echo $columns['twitter'] ?>" id="twitter-tweets">
				<h3 class="text-info"><i class="fa fa-twitter"></i> My Tweets</h3>
				<?php include 'assets/views/tweets.php' ?>
			</div>
		<?php endif; ?>
		
	</div>

<?php endif; ?>
