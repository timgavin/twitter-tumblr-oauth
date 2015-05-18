<?php
	
	// this document formats a tumblr 'video' post
	
	// post title
	if(isset($post->title)) {
		echo '<h1>'.$post->title.'</h1>';
	}

	// make sure there is a video item
	if(isset($post->player)) {

		// handles vimeo and youtube...
		
		// we're embedding a video and we want to make it responsive
		echo '<div class="embed-responsive embed-responsive-16by9">';
	
		// add the bootstrap responsive class to the iframe element
		echo str_replace('<iframe', '<iframe class="embed-responsive-item"', $post->player[0]->embed_code);
	
		echo '</div><br>';
	}
?>