<?php
	
	// this document loops through the tweets and formats them for printing to the screen
	
	if(isset($twitteroauth)) {
	
		$data = null;
		
		// displays a notification telling you when the cache was refreshed
		// turn this on/off in config.php
		if($notifications) {
			echo '<p class="text-danger"><small>Updated Twitter Cache</small></p>';
		}
	
		// loop through our tweets and build/format the output
		foreach($tweets as $tweet) {		
			
			$data .= '<article class="post" id="'.$tweet->id.'">';
			
			// add the tweet's date
			$data .= '<p class="text-muted text-right"><small>'.relative_date(strtotime($tweet->created_at)).'</small></p>';
	
			// do stuff to the raw tweet string
			$text = tweet_text($tweet->text);
			
			// format and display any photos
			if(isset($tweet->entities->media)) {
			
				// loop through the photos array
				foreach($tweet->entities->media as $media) {
				
					// print the photo
					// we're going to add some data attributes so we may display the tweet (with larger photo) in a modal
					// we're using attributes as an inexpensive way to pass the data to the modal so we won't have to call the API again
					$data .= '<p>';
					$data .= '	<a class="tweet-pop" data-toggle="modal" data-target="#tweet-pop" data-id="'.$tweet->id.'" data-photo="'.$media->media_url.'" data-text="'.str_replace($media->url,'',$tweet->text).'" data-date="'.relative_date(strtotime($tweet->created_at)).'">';
					$data .= '		<img src="'.$media->media_url.'" alt="" class="img-responsive img-rounded center-block">';
					$data .= '	</a>';
					$data .= '</p>';
				
					// print the tweet text/caption
					if(isset($tweet->text)) {
						$data .= '<p class="text-center">'.str_replace($media->url,'',$text).'</p>';
					}
				}
		
			} else {
				
				// no photos, just print the tweet text
				$data .= '<p>'.$text.'</p>';
			}
			
			$data .= '</article>';
		}
	
		// create/update twitter cache file
		file_put_contents($twitter_cache, $data, LOCK_EX);
		
		// since the data is in memory, just put it into the $tweets var instead of reading from the cache file
		$tweets = $data;
	}
	
	// print the cache file or the $tweets in memory
	echo $tweets;
	
	?>