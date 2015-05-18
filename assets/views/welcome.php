<?php if(empty($connected_services)): ?>

<h1>Twitter and Tumblr APIs with PHP &amp; OAuth</h1>
<p class="lead">This wee application demonstrates connecting to the Twitter and Tumblr APIs to perform a few tasks.</p>
<p>Click the button below to authorize this app with your accounts!</p>

<div class="row">
	<?php
	
		// loop through our services and print the connect/revoke buttons
		// we're getting the services from the connect page
		foreach($services as $service) {
	
			// loop through our services and the database results, if there's a match in the database we know the user has authenticated our app
			if(in_array($service, $connected_services)) {
			
				// user has authorized our app
				$status = 'revoke';
				$label  = 'Revoke Access to '.ucwords($service);
		
			} else {
			
				// user has not authorized our app
				$status = 'service';
				$label  = 'Connect to '.ucwords($service);
			}
	
			echo '<div class="col-sm-4 col-md-3 col-xs-6">';
			echo '	<a href="/index.php?'.$status.'='.$service.'">';
			echo '		<button class="btn btn-'.$service.' btn-block"><i class="fa fa-'.$service.'"></i> '.$label.'</button>';
			echo '	</a>';
			echo '</div>';
		}
	?>
</div>

<?php endif; ?>