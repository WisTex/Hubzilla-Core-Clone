$(document).ready(function() {

	if (bParam_mid) {
		src = 'hq';
		$('.channel-activities-toggle').removeClass('d-none');
	}
	else {
		$('#channel-activities').removeClass('d-none');
	}

	$(document).one('click', '.notification, .message', function(e) {
		page_load = false;
		followUpPageLoad = true;
		src = 'hq';
		$('#channel-activities').addClass('d-none');
		$('.channel-activities-toggle').removeClass('d-none');
	});

	$(document).on('click', '.channel-activities-toggle', function(e) {
		$(window).scrollTop(0);
		$(document).trigger('hz:hqControlsClickAction');
		$('#channel-activities').toggleClass('d-none');
		$(this).toggleClass('active');
	});

	$(document).on('click', '.jot-toggle', function(e) {
		$(window).scrollTop(0);
		$(document).trigger('hz:hqControlsClickAction');
		$('#jot-popup').toggle();
		$('#profile-jot-text').focus();
		$(this).toggleClass('active');
	});

	$(document).on('click', '.notes-toggle', function(e) {
		$(window).scrollTop(0);
		$(document).trigger('hz:hqControlsClickAction');
		$('#personal-notes').toggleClass('d-none');
		$('#note-text').focus();
		$(this).toggleClass('active');
	});

});
