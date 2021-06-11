$(document).ready(function() {

	$('#dm-widget').on('scroll', function() {
		if(this.scrollTop > this.scrollHeight - this.clientHeight - (this.scrollHeight/7)) {
			get_dm_page()
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

	$('.direct-message-timeago').timeago();

});

function get_dm_page() {
	if (get_dm_page_active)
		return;

	if (dm_last_id === -1)
		return;

	get_dm_page_active = true;
	$('#dm-loading').show();
	$.ajax({
		type: 'post',
		url: '/dm',
		data: {
			last_id: dm_last_id
		}
	}).done(function(obj) {
		get_dm_page_active = false;
		dm_last_id = obj.last_id;
		console.log(obj);
		let html;
		let tpl = $('#direct-message-template[rel=template]').html();	
			obj.entries.forEach(function(e) {
			html = tpl.format(e.b64mid, e.subject, e.created, e.summary, e.recipients);
			$('#dm-loading').before(html);
		});
		$('#dm-loading').hide();
		$('.direct-message-timeago').timeago();
	});
}
