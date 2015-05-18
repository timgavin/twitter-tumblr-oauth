<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<i class="fa fa-bars"></i>
			</button>
			<a class="navbar-brand" href="index.php">OAuth Test App</a>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<?php if($connected_services): ?>
					<!-- user has authenticated with a service, show a button that will launch our form modal -->
					<li><button type="button" data-toggle="modal" data-target="#post-modal" class="btn btn-success post-something" data-uid="<?php echo $_SESSION['uid'] ?>"><i class="fa fa-plus"></i> Post Something</button></li>
				<?php endif; ?>
				<?php

					// loop through our services and print the connect/revoke buttons
					// we're getting the services from the connect page
					foreach($services as $service) {

						// loop through our services and the database results, if there's a match in the database we know the user has authenticated our app
						if(in_array($service, $connected_services)) {

							// user has authorized our app
							$status = 'revoke';
							$label  = 'Revoke ';

						} else {

							// user has not authorized our app
							$status = 'service';
							$label  = 'Connect';
						}

						echo '<li>';
						echo '	<a href="/index.php?'.$status.'='.$service.'">';
						echo '		<button class="btn btn-'.$service.' btn-block"><i class="fa fa-'.$service.'"></i> '.$label.'</button>';
						echo '	</a>';
						echo '</li>';
					}
				?>
			</ul>
		</div>
	</div>
</nav>