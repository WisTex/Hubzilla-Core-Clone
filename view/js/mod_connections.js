$(document).ready(function() {
	$("#contacts-search").contact_autocomplete(baseurl + '/acl', 'a', true);
	$(".autotime").timeago();

	var poi;
	var section = 'roles';
	var sub_section;

	$(document).on('click', '.contact-edit', function () {
		poi = this.dataset.id;
		deactivate();

		$.get('contactedit/' + poi, function(data) {
			activate();
			$('#edit-modal-title').html(data.title);
			$('#edit-modal-body').html(data.body);
			$('#edit-modal-tools').html(data.tools);

		});
	});

	$(document).on('click', '#contact-save', function () {
		let form_data = $('#contact-edit-form').serialize() + '&section=' + section + '&sub_section=' + sub_section;

		deactivate();
		$.post('contactedit/' + poi, form_data, function(data) {
			activate();
			$('#edit-modal-title').html(data.title);
			$('#edit-modal-body').html(data.body);
			$('#edit-modal-tools').html(data.tools);
			$('#contact-role-' + poi).html(data.role);
			$.jGrowl(data.message, {sticky: false, theme: ((data.success) ? 'info' : 'notice'), life: ((data.success) ? 3000 : 10000)});
		});

	});

	$(document).on('click', '.contact-tool', function (e) {
		e.preventDefault();
		let cmd = this.dataset.cmd;

		$.get('contactedit/' + poi + '/' + cmd, function(data) {
			$('#edit-modal-tools').html(data.tools);
			$.jGrowl(data.message, {sticky: false, theme: ((data.success) ? 'info' : 'notice'), life: ((data.success) ? 3000 : 10000)});
			if (cmd === 'drop') {
				$('#contact-entry-wrapper-' + poi).fadeOut();
				$('#edit-modal').modal('hide');
			}
		});
	});

	$(document).on('click', '.section', function () {
		section = this.dataset.section;
		sub_section = '';
	});

	$(document).on('click', '.sub_section', function () {
		if ($(this).hasClass('sub_section_active')) {
			$(this).removeClass('sub_section_active');
			sub_section = '';
		}
		else {
			$(this).addClass('sub_section_active');
			sub_section = this.dataset.section;
		}
	});

	function deactivate() {
		$('#edit-modal-title').css('filter', 'blur(7px)');
		$('#edit-modal-body').css('filter', 'blur(7px)');
		$('#contact-save').addClass('disabled');
		$('#contact-tools').addClass('disabled');
	}

	function activate() {
		$('#edit-modal-title').css('filter', 'blur(0px)');
		$('#edit-modal-body').css('filter', 'blur(0px)');
		$('#contact-save').removeClass('disabled');
		$('#contact-tools').removeClass('disabled');
	}

});

