<?php

require_once '../config.php';

// get the user's id
$uid = (int) $_GET['uid'];

// check if the user has more than one tumblr blog
// if they do, we're going to return them as a dropdown to let the user decide into which blog this should be posted

if($result = $db->query("SELECT service_userurl FROM users_oauth WHERE uo_usr_id=".$db->escape_string($uid))) {
	
	// if the value of service_userurl contains an apostrophe (,) we know there is more than one blog
	foreach($result as $row) {
		
		if(strpos($row['service_userurl'],',') !== false) {
			
			$parts = explode(',', $result);
			
			echo '<div class="form-group">';
			echo '	<select name="blog_url" class="form-control">';
			echo '		<option>Select a Blog...</option>';
			foreach($parts as $part) {
				echo '<option value="'.$part.'">'.$part.'</option>';
			}
			echo '	</select>';
			echo '</div>';
		}
	}
	
	die();
}

die('fail');