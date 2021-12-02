$(document).ready(function() {
	$("#contacts-search").contact_autocomplete(baseurl + '/acl', 'a', true);
	$(".autotime").timeago();

	var poi;
	var section = 'roles';
	var sub_section;

	init_hash();
	window.onhashchange = init_hash;

	$('#edit-modal').on('hidden.bs.modal', function (e) {
		deactivate();
		history.replaceState(null, '', 'connections');
	})

	$(document).on('click', '.contact-edit', function () {
		poi = this.dataset.id;
		history.replaceState(null, '', 'connections#' + poi);

		$.get('contactedit/' + poi, function(data) {
			if (!data.success) {
				$.jGrowl(data.message, {sticky: false, theme: 'notice', life: 10000});
				return;
			}
			activate(data);
		});
	});

	$(document).on('click', '#contact-save', function () {
		let form_data = $('#contact-edit-form').serialize() + '&section=' + section + '&sub_section=' + sub_section;

		$.post('contactedit/' + poi, form_data, function(data) {
			if (!data.success) {
				$.jGrowl(data.message, {sticky: false, theme: 'notice', life: 10000});
				return;
			}
			activate(data);
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

	function deactivate(data) {
		$('#edit-modal-title').css('filter', 'blur(7px)');
		$('#edit-modal-body').css('filter', 'blur(7px)');
		$('#contact-tools').addClass('disabled');
		$('#contact-save').addClass('disabled');
		$('#contact-save').addClass('btn-primary'),
		$('#contact-save').removeClass('btn-success')
		$('#contact-save').html(aStr['submit']);

	}

	function activate(data) {
		$('#edit-modal-title').css('filter', 'blur(0px)');
		$('#edit-modal-body').css('filter', 'blur(0px)');
		$('#contact-save').removeClass('disabled');
		$('#contact-tools').removeClass('disabled');

		if (data.title) {
			$('#edit-modal-title').html(data.title);
		}

		if (data.body) {
			$('#edit-modal-body').html(data.body);
		}

		if (data.tools) {
			$('#edit-modal-tools').html(data.tools);
		}

		if (data.submit) {
			$('#contact-save').html(data.submit);
		}

		if (data.role) {
			$('#contact-role-' + poi).html(data.role);
		}

		if (data.pending) {
			$('#contact-save').removeClass('btn-primary'),
			$('#contact-save').addClass('btn-success')
		}
	}

	function init_hash() {
		if(window.location.hash) {
			poi = window.location.hash.substr(1);
			deactivate();

			$.get('contactedit/' + poi, function(data) {
				if (!data.success) {
					$.jGrowl(data.message, {sticky: false, theme: 'notice', life: 10000});
					return;
				}
				activate(data);
				$('#edit-modal').modal('show');
			});
		}
	}
});

