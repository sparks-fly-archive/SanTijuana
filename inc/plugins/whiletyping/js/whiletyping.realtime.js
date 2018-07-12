/**
 * While you were typing
 * Copyright (c) 2011 Aries-Belgium
 * Copyright (c) 2014-2015 doylecc
 *
 *
 **/

jQuery(document).ready(function($)
{
	// add a container above the textfield
	var container = $(document.createElement('div')).attr('id', '#whiletyping_notifier').css('color','red');
	if($("#message"))
		container.insertBefore('#message');

	// add a periodical pull to check if there are new messages
	var current_script = THIS_SCRIPT.split('.')[0];
	var interval = setInterval(function() {
		$.ajax({
			url: 'xmlhttp.php?action=whiletyping&tid='+MYBB_TID+'&script='+current_script,
			type: 'get',
			success: function(response){
				container.html(response);
			}
		});
	}, 20000);


	// clear the whiletyping_notifier when the quick_reply_submit button is pressed
	$("#quick_reply_submit").on("click", function(){
		$("#whiletyping_notifier").html('');
		if (!navigator.userAgent.match(/msie/i) && !navigator.userAgent.match(/Trident.*rv\:11\./)) {
			setTimeout(function() {
				if(!$("#whiletyping_quickreply_message")) {
					$("#message").html('');
				}
			}, 1200);
		}
	});

	// remove the whiletyping_notifier when the show new post link is clicked
	if(container)
	{
		container.on("click", function(){
			if(current_script == 'showthread')
			{
				whiletypingShowPosts();
			}
			container.html('');
		});
	}

});

function whiletypingShowPosts()
{
	jQuery.ajax({
		url: 'xmlhttp.php?action=whiletyping_get_posts&tid='+MYBB_TID,
		type: 'get',
		success: function(response){
			var posts_html = response;
			var pids = posts_html.match(/id="post_([0-9]+)"/g);
			var lastpid = pids.pop().match(/id="post_([0-9]+)"/);
			if(lastpid !== null) lastpid = lastpid[1];
			var posts = document.createElement("div");
			posts.html = posts_html;
			jQuery('#posts').append(posts.html);

			if(jQuery('#lastpid') && lastpid !== null)
			{
				jQuery('#lastpid').value = lastpid;
			}
		}
	});
}



function whiletypingSubmitPreview()
{
	whiletypingSimulateClick(jQuery("input[name='previewpost']")[0]);
}
