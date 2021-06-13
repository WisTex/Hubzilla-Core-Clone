$(document).ready(function() {

	$('#messages-widget').on('scroll', function() {
		if(this.scrollTop > this.scrollHeight - this.clientHeight - (this.scrollHeight/7)) {
			get_messages_page('hq');
		}
	});

	$(document).on('click', '#jot-toggle', function(e) {
		e.preventDefault();
		e.stopPropagation();

		$(this).toggleClass('active');
		$(window).scrollTop(0);
		$('#jot-popup').toggle();
		$('#profile-jot-text').focus();

	});

	$('.messages-timeago').timeago();

});
