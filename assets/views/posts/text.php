<?php
	
	// this document formats a tumblr 'text' post
	
	// post title
	if(isset($post->title)) {
		echo '<h1>'.$post->title.'</h1>';
	}

	// post text
	if(isset($post->body)) {
		echo $post->body;
	}
?>