<?php
	
	// quick 'n dirty messaging function
	function messages($string, $type='error') {
	
		if($type == 'success') {
			$class = 'alert-success';
		} else {
			$class = 'alert-danger';
		}
	
		$return  = '<div class="alert '.$class.' alert-dismissable text-center" role="alert">';
		$return .= '	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		$return .= 		$string;
		$return .= '</div>';
	
		return $return;
	}
	
	
	// print date as relative to today (today, yesterday, etc.)
	function relative_date($time) {
		$today = strtotime(date('M j, Y'));
		$reldays = ($time - $today)/86400;
		if ($reldays >= 0 && $reldays < 1) {
			return 'today';
		} else if ($reldays >= 1 && $reldays < 2) {
			return 'tomorrow';
		} else if ($reldays >= -1 && $reldays < 0) {
			return 'yesterday';
		}
		if (abs($reldays) < 7) {
			if ($reldays > 0) {
				$reldays = floor($reldays);
				return 'in ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
			} else {
				$reldays = abs(floor($reldays));
				return $reldays . ' day'  . ($reldays != 1 ? 's' : '') . ' ago';
			}
		}
		if (abs($reldays) < 182) {
			return date('D, M j',$time ? $time : time());
		} else {
			return date('D, M j, Y',$time ? $time : time());
		}
	}



	function tweet_text($string) {
	
		// lifted from http://www.aljtmedia.com/blog/displaying-latest-tweets-using-the-twitter-api-v11-in-php/			
	
		// Access as an object
		$return = $string;

		// Make links active
		$return = preg_replace("#(http://|(www.))(([^s<]{4,68})[^s<]*)#", '<a href="http://$2$3" target="_blank">$1$2$4</a>', $return);

		// Linkify user mentions
		$return = preg_replace("/@(w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $return);

		// Linkify tags
		$return = preg_replace("/#(w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>', $return);
	
		return $return;
	}


	// put our results into an array for later
	
	function assign_oauth_values($array, $service) {

		foreach($array as $key => $value) {

			if($service == 'twitter' && $value['service'] == 'twitter') {
				$twitter['service_username']= $value['service_username'];
				$twitter['service_userurl'] = $value['service_userurl'];
				$twitter['oauth_token'] 	= $value['oauth_token'];
				$twitter['oauth_secret'] 	= $value['oauth_secret'];
			
				return $twitter;
			}
	
			if($service == 'tumblr' && $value['service'] == 'tumblr') {
				$tumblr['service_username'] = $value['service_username'];
				$tumblr['service_userurl'] 	= $value['service_userurl'];
				$tumblr['oauth_token'] 		= $value['oauth_token'];
				$tumblr['oauth_secret'] 	= $value['oauth_secret'];
			
				return $tumblr;
			}
		}
	
		return false;
	}


	function do_cols($blog, $tweets) {
			
		// figure out some column widths to display our content in a way that makes sense
		if(count($blog) == 0 && count($tweets) > 0) {
			return array('twitter' => 12, 'tumblr' => 0);
		}
		elseif(count($blog) > 0 && count($tweets) > 0) {
			return array('twitter' => 4, 'tumblr' => 8);
		}
		elseif(count($blog) > 0 && count($tweets) == 0) {
			return array('twitter' => 0, 'tumblr' => 12);
		}
	}