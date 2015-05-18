$(document).ready(function() {
	
	// init bootstrap tooltips
	$('[data-toggle="tooltip"]').tooltip();
	
	// user is posting something
	$('body').on('click', '.post-something', function(e) {
		
		e.preventDefault();
		
		// get the user's id
		var uid = $(this).data('uid');
		
		// check if the user has more than one tumblr blog
		$.get('assets/ajax/get-tumblr-blogs.php', {uid:uid}, function(data, status){
			
			if(status != 'fail') {
				
				// show the blog dropdown if the 'post to tumblr' checkbox has been ticked
				$('#tumble').change(function(){
				if(this.checked)
					$('#blog-menu').html(data).removeClass('hide').show();
				else
					$('#blog-menu').hide();
				});	
			}
		});
	});
	
	// view tweet in a modal
	$('body').on('click', '.tweet-pop', function(e) {
		
		e.preventDefault();
		
		// get the tweet's id
		var id 		= $(this).data('id');
		var photo 	= $(this).data('photo');
		var text 	= $(this).data('text');
		var date 	= $(this).data('date');
		
		if(photo != '') {
			$('#tweet-pop-photo').html('<img src="' + photo + '" class="img-responsive img-rounded center-block"><br>');
		}
		if(text != '') {
			$('#tweet-pop-text').html('<p>' + text + '</p>');
		}
		if(date != '') {
			$('#tweet-pop-date').html(date);
		}
		
	});
	
	// tell the user if their tweet contains too many characters
	// lifted from http://stackoverflow.com/a/11132964/2101328
	$('#text').keyup(function () {
		var max = 140;
		var len = $(this).val().length;
		if (len >= max) {
			$('#tweet-count').text('This will be truncated if sending to twitter');
		} else {
			var char = max - len;
			$('#tweet-count').text(char + ' tweet max');
		}
	});
	
	// reset the modal when closed
	$('#post-modal').on('hidden.bs.modal', function(e) {
		$(this)
		.find('input,textarea,select').val('').end()
		.find('input[type=checkbox]').prop('checked', '').end();
		$('#tweet-count').text('');
	})

});
