<?php
	require_once 'assets/config.php';
	
	// include this file if user is posting something to tumblr...
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		require 'assets/post.php';
	}
	
	// set an empty array for later
	$connected_services = array();
	
	require 'assets/oauth/authenticate.php';
?>
<!DOCTYPE html>
	<html lang="en">
	<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Twitter + Tumblr OAuth Example App</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<link href="assets/css/flatly.min.css" rel="stylesheet">
		<link href="assets/css/styles.css" rel="stylesheet">
		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/libs/html5shiv/r29/html5.min.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
	
	<?php require 'assets/views/navigation.php' ?>
		
		<div class="container">
			
			<div class="row">
				<div class="col-sm-12">
					
					<!-- print messages -->
					<?php echo $message ?>
					
					<!-- 'welcome to my app' text -->
					<?php require 'assets/views/welcome.php' ?>
					
				</div>
			</div>
			
			<!-- show user's feeds -->
			<?php require 'assets/views/feeds.php'; ?>
		
		</div>
		
		<!-- include our post modal -->
		<?php require 'assets/views/modal.php' ?>
		
		<script src="//code.jquery.com/jquery.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script src="assets/js/scripts.js"></script>
	</body>
</html>
