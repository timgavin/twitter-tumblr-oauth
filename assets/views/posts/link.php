<?php
	
	// this document formats a tumblr 'link' post
	
	// prin the url and title
	if(isset($post->title)) {
		echo '<h3><i class="fa fa-link text-muted"></i> <a href="'.$post->url.'">'.$post->title.'</a></h3>';
	}
	
	// print the description text
	if(isset($post->description)) {
		echo $post->description;
	}
?>
