<!-- post to tumblr modal -->
<div class="modal fade" id="post-modal" tabindex="-1" role="dialog" aria-labelledby="post-modal" aria-hidden="true">
	<div class="modal-dialog">
		<form id="post" action="index.php" method="post" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Post Something...</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="text" name="title" class="form-control" placeholder="Tumblr Post Title">
					</div>
					<div class="form-group">
						<input type="text" name="tags" class="form-control" placeholder="Tumblr Tags">
					</div>
					<div class="form-group">
						<span id="tweet-count"></span>
						<textarea name="text" id="text" class="form-control" placeholder="Tweet/Text/Caption" rows="5"></textarea>
					</div>
					<div class="form-group">
						<input type="file" name="file[]" class="form-control" multiple="multiple">
					</div>
					<div class="row">
						<?php if(in_array('tumblr',$connected_services)): ?>
						<div class="col-sm-4">
							<div class="form-group" id="post-to-tumblr">
								<div class="checkbox">
									<label><input type="checkbox" name="tumble" id="tumble" value="1" > Post to Tumblr</label>
								</div>
								<div id="blog-menu" class="hide"></div>
							</div>
						</div>
						<?php endif; ?>
						<?php if(in_array('twitter',$connected_services)): ?>
						<div class="col-sm-4">
							<div class="form-group" id="post-to-twitter">
								<div class="checkbox">
									<label><input type="checkbox" name="tweet" id="tweet" value="1" > Post to Twitter</label>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" name="submit" class="btn btn-primary">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>




<!-- view photo tweet in new window -->
<div class="modal fade" id="tweet-pop" tabindex="-1" role="dialog" aria-labelledby="tweet-pop" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title text-muted" id="tweet-pop-date"></h4>
			</div>
			<div class="modal-body">
				<div id="tweet-pop-photo"></div>
				<div id="tweet-pop-text"></div>
			</div>
		</div>
	</div>
</div>