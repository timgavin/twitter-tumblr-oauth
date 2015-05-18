<?php

	// this document loops through the tumblr blog posts and formats them for printing to the screen

	if(isset($tumblroauth)) {
	
		// since we're using file includes we'll get our data from the output buffer
		ob_start();
	
		// loop through our posts array and load the appropriate template
		foreach($blog->posts as $post) {

			echo '<article class="post">';
			
			// print the post's creation date
			echo '<p class="text-right small">';
			echo '	<a href="'.$post->post_url.'"class="text-muted" target="_blank" data-toggle="tooltip" title="View on Tumblr">'.relative_date(strtotime($post->date)).'</a>';
			echo '</p>';
			
			// load the appropriate view
			
			if($post->type == 'text') {
				include 'posts/text.php';
			}

			if($post->type == 'photo') {
				include 'posts/photo.php';
			}
			
			if($post->type == 'video') {
				include 'posts/video.php';
			}

			if($post->type == 'link') {
				include 'posts/link.php';
			}
			
			// build our notes var to display the post's notes count
			$notes = 0;
			if($post->note_count) {
				$notes = $post->note_count;
			}
			
			// print tags and notes
			echo '<div class="row">';
			echo '	<div class="col-xs-6 text-muted text-left small">';
			foreach($post->tags as $tag) {
				echo '<span class="text-muted">#'.$tag.'</span> ';
			}
			echo '	</div>';
			echo '	<div class="col-xs-6 text-muted text-right small">';
			echo 		$notes .' notes';
			echo '	</div>';
			echo '</div>';
			
			
			echo '</article>';
		}
	
		// put the buffer contents into our $data variable
		$data = ob_get_contents();
		
		// empty the buffer
		ob_end_clean();
		
		// create/update the tumblr cache file
		file_put_contents($tumblr_cache, $data, LOCK_EX);
		
		// since the data is in memory, just put it into the $blog var instead of reading from the cache file
		$blog = $data;
	
		if($notifications) {
			echo '<p class="text-danger"><small>Updated Tumblr Cache</small></p>';
		}

	}

	// print the cache file or the posts in memory
	echo $blog;