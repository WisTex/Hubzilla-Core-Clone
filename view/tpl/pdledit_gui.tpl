<div id="pdledit_gui_item_modal" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div id="pdledit_gui_item_modal_title" class="modal-title h3">Modal title</div>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
			</div>
			<div id="pdledit_gui_item_modal_body" class="modal-body">
				<textarea id="pdledit_gui_modal_textarea" class="form-control font-monospace" rows="7"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
				<button id="pdledit_gui_item_modal_store" type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>
<div id="pdledit_gui_page_modal" class="modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div id="pdledit_gui_page_modal_title" class="modal-title h3">Modal title</div>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
			</div>
			<div id="pdledit_gui_page_modal_body" class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
				<button id="pdledit_gui_page_modal_store" type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<ul class="nav position-fixed bottom-0 start-50 bg-light translate-middle-x">
	<li class="nav-item">
		<a id="pdledit_gui_modules" class="nav-link" href="#">Select Module</a>
	</li>
	<li class="nav-item">
		<a id="pdledit_gui_templates" class="nav-link" href="#">Select Template</a>
	</li>
	<li class="nav-item">
		<a id="pdledit_gui_items" class="nav-link" href="#" >Add Item</a>
	</li>
	<li class="nav-item">
		<a id="pdledit_gui_src" class="nav-link" href="#">Page Source</a>
	</li>
	{{if $module_modified}}
	<li class="nav-item">
		<a id="pdledit_gui_reset" class="nav-link" href="#">Reset</a>
	</li>
	{{/if}}
	<li class="nav-item">
		<a id="pdledit_gui_save" class="nav-link" href="#">Save</a>
	</li>
</ul>

<script>
	$(document).ready(function() {
		var poi;
		var edit_modal = new bootstrap.Modal(document.getElementById('pdledit_gui_item_modal'));
		var page_modal = new bootstrap.Modal(document.getElementById('pdledit_gui_page_modal'));

		{{foreach $content_regions as $content_region}}
		let sortable_{{$content_region}} = document.getElementById('{{$content_region}}');
		new Sortable(sortable_{{$content_region}}, {
			group: 'shared',
			handle: '.pdledit_gui_item_handle',
			animation: 150,
			draggable: '.pdledit_gui_item'
		});
		{{/foreach}}

		$(document).on('click', '.pdledit_gui_item_src', function(e) {
			poi = this.closest('.pdledit_gui_item');
			let src = atob(poi.dataset.src);
			$('#pdledit_gui_modal_textarea').val(src);
			$('#pdledit_gui_modal_textarea').bbco_autocomplete('comanche');
			edit_modal.show();
		});

		$(document).on('click', '.pdledit_gui_item_remove', function(e) {
			poi = this.closest('.pdledit_gui_item');
			$(poi).remove();
		});

		$(document).on('click', '#pdledit_gui_modal_store', function(e) {
			let src = $('#pdledit_gui_modal_textarea').val();
			poi.dataset.src = btoa(src);
			edit_modal.hide();
		});

// #################################

		$(document).on('click', '#pdledit_gui_src', function(e) {
			e.preventDefault();
			$('#pdledit_gui_modal_textarea').val(atob('{{$page_src}}'));
			$('#pdledit_gui_modal_textarea').bbco_autocomplete('comanche');
			edit_modal.show();
		});

		$(document).on('click', '#pdledit_gui_items', function(e) {
			e.preventDefault();
			page_modal.show();
		});

		$(document).on('click', '#pdledit_gui_templates', function(e) {
			e.preventDefault();
			$('#pdledit_gui_page_modal_body').html(atob('{{$templates}}'));

			page_modal.show();
		});
		$(document).on('click', '#pdledit_gui_modules', function(e) {
			e.preventDefault();
			$('#pdledit_gui_page_modal_body').html(atob('{{$modules}}'));
			page_modal.show();
		});
		$(document).on('click', '#pdledit_gui_save', function(e) {
			e.preventDefault();
		});
		$(document).on('click', '#pdledit_gui_reset', function(e) {
			e.preventDefault();
		});

	});
</script>
