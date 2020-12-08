/**
 * JavaScript for mod/cloud
 */

$(document).ready(function () {

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		UploadInit();
	}

	var attach_drop_id;
	var attach_draging;

	$('.cloud-tool-perms-btn').on('click', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		$('.cloud-tool').hide();
		$('.cloud-index').removeClass('cloud-index-active');

		$('#cloud-tool-submit-' + id).show();
		$('#cloud-index-' + id).addClass('cloud-index-active');
	});

	$('.cloud-tool-rename-btn').on('click', function (e) {
		e.preventDefault();
		let id = $(this).data('id');

		$('.cloud-tool').hide();
		$('.cloud-index').removeClass('cloud-index-active');

		$('#cloud-tool-rename-' + id + ', #cloud-tool-submit-' + id).show();
		$('#cloud-index-' + id).addClass('cloud-index-active');
	});

	$('.cloud-tool-move-btn').on('click', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		$('.cloud-tool').hide();
		$('.cloud-index').removeClass('cloud-index-active');

		$('#cloud-tool-move-' + id + ', #cloud-tool-submit-' + id).show();
		$('#cloud-index-' + id).addClass('cloud-index-active');
	});

	$('.cloud-tool-categories-btn').on('click', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		$('.cloud-tool').hide();
		$('.cloud-index').removeClass('cloud-index-active');

		$('#id_categories_' + id).tagsinput({
			tagClass: 'badge badge-pill badge-warning text-dark'
		});

		$('#cloud-tool-categories-' + id + ', #cloud-tool-submit-' + id).show();
		$('#cloud-index-' + id).addClass('cloud-index-active');
	});

	$('.cloud-tool-download-btn').on('click', function (e) {
		let id = $(this).data('id');
		$('.cloud-tool').hide();
	});

	$('.cloud-tool-delete-btn').on('click', function (e) {
		e.preventDefault();
		let id = $(this).data('id');

		$('.cloud-tool').hide();
		$('.cloud-index').removeClass('cloud-index-active');
		$('#cloud-index-' + id).addClass('cloud-index-active');

		let confirm = confirmDelete();
		if (confirm) {
			$('body').css('cursor', 'wait');
			$('#cloud-index-' + id).css('opacity', 0.33);

			let form = $('#attach_edit_form_' + id).serializeArray();
			form.push({name: 'delete', value: 1});

			$.post('attach_edit', form, function (data) {
				if (data.success) {
					$('#cloud-index-' + id + ', #cloud-tools-' + id).remove();
					$('body').css('cursor', 'auto');
				}
				return true;
			});

		}
		return false;
	});

	$('.cloud-tool-cancel-btn').on('click', function (e) {
		e.preventDefault();
		let id = $(this).data('id');
		$('.cloud-tool').hide();
		$('#cloud-index-' + id).removeClass('cloud-index-active');
		$('#attach_edit_form_' + id).trigger('reset');
		$('#id_categories_' + id).tagsinput('destroy');

	});

	// DnD

	$(document).on('drop', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});

	$(document).on('dragover', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});

	$(document).on('dragleave', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});

	$('.cloud-index.attach-drop').on('drop', function (e) {

		let target = $(this);
		let folder = target.data('folder');
		let id = target.data('id');


		if(typeof folder === typeof undefined) {
			return false;
		}

		// Check if it's a file
		if (e.dataTransfer.files[0]) {
			$('#file-folder').val(folder);
			return true;
		}

		if(id === attach_drop_id) {
			return false;
		}

		if(target.hasClass('attach-drop-zone') && attach_draging) {
			return false;
		}

		target.removeClass('attach-drop-ok');

		$.post('attach_edit', {'channel_id': channelId, 'dnd': 1, 'attach_id': attach_drop_id, ['newfolder_' + attach_drop_id]: folder }, function (data) {
			if (data.success) {
				$('#cloud-index-' + attach_drop_id + ', #cloud-tools-' + attach_drop_id).remove();
				attach_drop_id = null;
			}
		});
	});

	$('.cloud-index.attach-drop').on('dragover', function (e) {
		let target = $(this);

		if(target.hasClass('attach-drop-zone') && attach_draging) {
			return false;
		}

		target.addClass('attach-drop-ok');
	});

	$('.cloud-index').on('dragleave', function (e) {
		let target = $(this);
		target.removeClass('attach-drop-ok');
	});

	$('.cloud-index').on('dragstart', function (e) {
		let target = $(this);
		attach_drop_id = target.data('id');
		// dragstart is not fired if a file is draged onto the window
		// we use this to distinguish between drags and file drops
		attach_draging = true;
	});

	$('.cloud-index').on('dragend', function (e) {
		let target = $(this);
		target.removeClass('attach-drop-ok');
		attach_draging = false;
	});


});

// initialize
function UploadInit() {

	var submit = $("#upload-submit");
	var count = 1;
	var filedrag = $(".cloud-index.attach-drop");

	$('#invisible-cloud-file-upload').fileupload({
			url: 'file_upload',
			dataType: 'json',
			dropZone: filedrag,
			maxChunkSize: 4 * 1024 * 1024,

			add: function(e,data) {
				$(data.files).each( function() { this.count = ++ count; prepareHtml(this); });

				var allow_cid = ($('#ajax-upload-files').data('allow_cid') || []);
				var allow_gid = ($('#ajax-upload-files').data('allow_gid') || []);
				var deny_cid  = ($('#ajax-upload-files').data('deny_cid') || []);
				var deny_gid  = ($('#ajax-upload-files').data('deny_gid') || []);

				$('.acl-field').remove();

				$(allow_gid).each(function(i,v) {
					$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='group_allow[]' value='"+v+"'>");
				});
				$(allow_cid).each(function(i,v) {
					$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='contact_allow[]' value='"+v+"'>");
				});
				$(deny_gid).each(function(i,v) {
					$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='group_deny[]' value='"+v+"'>");
				});
				$(deny_cid).each(function(i,v) {
					$('#ajax-upload-files').append("<input class='acl-field' type='hidden' name='contact_deny[]' value='"+v+"'>");
				});

				data.formData = $('#ajax-upload-files').serializeArray();
				data.submit();
			},


			progress: function(e,data) {

				// there will only be one file, the one we are looking for

				$(data.files).each( function() {
					var idx = this.count;

					// Dynamically update the percentage complete displayed in the file upload list
					$('#upload-progress-' + idx).html(Math.round(data.loaded / data.total * 100) + '%');
					$('#upload-progress-bar-' + idx).css('background-size', Math.round(data.loaded / data.total * 100) + '%');

				});


			},

			stop: function(e,data) {
				window.location.href = window.location.href;
			}

		});

		$('#upload-submit').click(function(event) { event.preventDefault(); $('#invisible-cloud-file-upload').trigger('click'); return false;});

}



function prepareHtml(f) {
	var num = f.count - 1;
	var i = f.count;
	$('#cloud-index #new-upload-progress-bar-' + num.toString()).after(
		'<tr id="new-upload-' + i + '" class="new-upload">' +
		'<td><i class="fa ' + getIconFromType(f.type) + '" title="' + f.type + '"></i></td>' +
		'<td>' + f.name + '</td>' +
		'<td id="upload-progress-' + i + '"></td><td></td><td></td>' +
		'<td class="d-none d-md-table-cell">' + formatSizeUnits(f.size) + '</td><td class="d-none d-md-table-cell"></td>' +
		'</tr>' +
		'<tr id="new-upload-progress-bar-' + i + '" class="new-upload">' +
		'<td id="upload-progress-bar-' + i + '" colspan="9" class="upload-progress-bar"></td>' +
		'</tr>'
	);
}

function formatSizeUnits(bytes){
	if      (bytes>=1000000000) {bytes=(bytes/1000000000).toFixed(2)+' GB';}
	else if (bytes>=1000000)    {bytes=(bytes/1000000).toFixed(2)+' MB';}
	else if (bytes>=1000)       {bytes=(bytes/1000).toFixed(2)+' KB';}
	else if (bytes>1)           {bytes=bytes+' bytes';}
	else if (bytes==1)          {bytes=bytes+' byte';}
	else                        {bytes='0 byte';}
	return bytes;
}

// this is basically a js port of include/text.php getIconFromType() function
function getIconFromType(type) {
	var map = {
		//Common file
		'application/octet-stream': 'fa-file-o',
		//Text
		'text/plain': 'fa-file-text-o',
		'application/msword': 'fa-file-word-o',
		'application/pdf': 'fa-file-pdf-o',
		'application/vnd.oasis.opendocument.text': 'fa-file-word-o',
		'application/epub+zip': 'fa-book',
		//Spreadsheet
		'application/vnd.oasis.opendocument.spreadsheet': 'fa-file-excel-o',
		'application/vnd.ms-excel': 'fa-file-excel-o',
		//Image
		'image/jpeg': 'fa-picture-o',
		'image/png': 'fa-picture-o',
		'image/gif': 'fa-picture-o',
		'image/svg+xml': 'fa-picture-o',
		//Archive
		'application/zip': 'fa-file-archive-o',
		'application/x-rar-compressed': 'fa-file-archive-o',
		//Audio
		'audio/mpeg': 'fa-file-audio-o',
		'audio/mp3': 'fa-file-audio-o', //webkit browsers need that
		'audio/wav': 'fa-file-audio-o',
		'application/ogg': 'fa-file-audio-o',
		'audio/ogg': 'fa-file-audio-o',
		'audio/webm': 'fa-file-audio-o',
		'audio/mp4': 'fa-file-audio-o',
		//Video
		'video/quicktime': 'fa-file-video-o',
		'video/webm': 'fa-file-video-o',
		'video/mp4': 'fa-file-video-o',
		'video/x-matroska': 'fa-file-video-o'
	};

	var iconFromType = 'fa-file-o';

	if (type in map) {
		iconFromType = map[type];
	}

	return iconFromType;
}


