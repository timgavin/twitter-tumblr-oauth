<?php if(isset($post->photos)): ?>
	<?php
		
		// this document formats a tumblr 'photo' post
		
		$array = array();
		$count = count($post->photos);

		foreach($post->photos as $photo) {
			
			// if the original image width is under 500px wide, use it
			if($photo->original_size->width < 500) {
				array_push($array, $photo->original_size->url);
			} else {
				
				foreach($photo->alt_sizes as $size) {
					
					if($size->width >= 500 && $size->width <= 700 ) {
						array_push($array, $size->url);
						break;
					}
					elseif($size->width > 500) {
						array_push($array, $size->url);
						break;
					} else {
						array_push($array, $size->url);
					}
				}
			}
		}
	?>

	<?php if($count == 1): ?>
		<img src="<?php echo $array[0] ?>" class="img-responsive center-block img-rounded" alt="">
		<?php if(isset($post->caption)): ?>
			<div class="text-center">
				<p><?php echo $post->caption ?></p>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
			<!-- Indicators -->
			<ol class="carousel-indicators">
				<?php foreach($array as $key => $value): ?>
					<li data-target="#carousel-example-generic" data-slide-to="<?php echo $key ?>"<?php if($key == 0): ?> class="active"<?php endif; ?>></li>
					<li data-target="#carousel-example-generic" data-slide-to="<?php echo $key ?>"></li>
					<li data-target="#carousel-example-generic" data-slide-to="<?php echo $key ?>"></li>
				<?php endforeach; ?>
			</ol>

			<!-- Wrapper for slides -->
			<div class="carousel-inner" role="listbox">
				<?php foreach($array as $key => $value): ?>
					<div class="item<?php if($key == 0): ?> active<?php endif; ?>">
						<img src="<?php echo $value ?>" alt="">
					</div>
				<?php endforeach ?>
			</div>

			<!-- Controls -->
			<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	<?php endif; ?>

<?php endif; ?>