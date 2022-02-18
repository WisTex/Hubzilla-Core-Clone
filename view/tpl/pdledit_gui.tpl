<div id="pdledit_gui_offcanvas" class="offcanvas offcanvas-lg offcanvas-bottom shadow border rounded-top start-50 translate-middle-x" tabindex="-1" data-bs-backdrop="false" data-bs-scroll="true" style="min-width: 300px">
	<div id="pdledit_gui_offcanvas_body" class="offcanvas-body"></div>
	<div class="offcanvas-header">
		<div class="offcanvas-title h3"></div>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
</div>

<div id="pdledit_gui_offcanvas_edit" class="offcanvas offcanvas-lg offcanvas-bottom shadow border rounded-top start-50 translate-middle-x" tabindex="-1" data-bs-backdrop="false" data-bs-scroll="true" style="min-width: 300px">
	<div id="pdledit_gui_offcanvas_edit_body" class="offcanvas-body">
		<textarea id="pdledit_gui_offcanvas_edit_textarea" class="form-control font-monospace h-100"></textarea>
	</div>
	<div class="offcanvas-header">
		<button id="pdledit_gui_offcanvas_edit_submit" type="button" class="btn btn-primary">Submit</button>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
</div>

<div id="pdledit_gui_offcanvas_submit" class="offcanvas offcanvas-lg offcanvas-bottom shadow border rounded-top start-50 translate-middle-x" tabindex="-1" data-bs-backdrop="false" data-bs-scroll="true" style="min-width: 300px">
	<div id="pdledit_gui_offcanvas_submit_body" class="offcanvas-body"></div>
	<div class="offcanvas-header">
		<button id="pdledit_gui_offcanvas_submit_submit" type="button" class="btn btn-primary">Submit</button>
		<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
</div>

<ul class="nav position-fixed bottom-0 start-50 bg-light translate-middle-x" style="min-width: 300px">
	<li class="nav-item">
		<a id="pdledit_gui_modules" class="nav-link" href="#">Modules</a>
	</li>
	<li class="nav-item">
		<a id="pdledit_gui_templates" class="nav-link" href="#">Templates</a>
	</li>
	<li class="nav-item">
		<a id="pdledit_gui_items" class="nav-link" href="#" >Items</a>
	</li>
	<li class="nav-item">
		<a id="pdledit_gui_src" class="nav-link" href="#">Source</a>
	</li>
	{{if $module_modified}}
	<li class="nav-item">
		<a id="pdledit_gui_reset" class="nav-link disabled" href="#">Reset</a>
	</li>
	{{/if}}
	<li class="nav-item">
		<a id="pdledit_gui_save" class="nav-link disabled" href="#">Save</a>
	</li>
</ul>

<script>
	$(document).ready(function() {
		let poi;

		let offcanvas = new bootstrap.Offcanvas(document.getElementById('pdledit_gui_offcanvas'));
		let edit_offcanvas = new bootstrap.Offcanvas(document.getElementById('pdledit_gui_offcanvas_edit'));
		let submit_offcanvas = new bootstrap.Offcanvas(document.getElementById('pdledit_gui_offcanvas_submit'));

		{{foreach $content_regions as $content_region}}
		let sortable_{{$content_region}} = document.getElementById('{{$content_region}}');
		new Sortable(sortable_{{$content_region}}, {
			group: 'shared',
			handle: '.pdledit_gui_item_handle',
			animation: 150
		});
		{{/foreach}}

		let sortable_items = document.getElementById('pdledit_gui_offcanvas_body');
		new Sortable(sortable_items, {
			group: {
				name: 'shared',
				pull: 'clone',
				put: false
			},
			sort: false,
			handle: '.pdledit_gui_item_handle',
			animation: 150,
			onEnd: function (e) {
				$(e.item).find('button').removeClass('disabled');
			}
		});

		$(document).on('click', '.pdledit_gui_item_src', function(e) {
			poi = this.closest('.pdledit_gui_item');
			let src = atob(poi.dataset.src);
			$('#pdledit_gui_offcanvas_edit_textarea').val(src);
			$('#pdledit_gui_offcanvas_edit_textarea').bbco_autocomplete('comanche');
			edit_offcanvas.show();
		});

		$(document).on('click', '.pdledit_gui_item_remove', function(e) {
			poi = this.closest('.pdledit_gui_item');
			$(poi).remove();
		});

		$(document).on('click', '#pdledit_gui_offcanvas_edit_submit', function(e) {
			let src = $('#pdledit_gui_offcanvas_edit_textarea').val();
			poi.dataset.src = btoa(src);
			edit_offcanvas.hide();
		});

// #################################

		$(document).on('click', '#pdledit_gui_src', function(e) {
			e.preventDefault();
			$('#pdledit_gui_offcanvas_edit_textarea').val(atob('{{$page_src}}'));
			$('#pdledit_gui_offcanvas_edit_textarea').bbco_autocomplete('comanche');
			edit_offcanvas.show();
		});

		$(document).on('click', '#pdledit_gui_items', function(e) {
			e.preventDefault();
			$('#pdledit_gui_offcanvas_body').html(atob('{{$items}}'));
			offcanvas.show();
		});

		$(document).on('click', '#pdledit_gui_templates', function(e) {
			e.preventDefault();
			$('#pdledit_gui_offcanvas_submit_body').html(atob('{{$templates}}'));

			submit_offcanvas.show();
		});

		$(document).on('click', '#pdledit_gui_modules', function(e) {
			e.preventDefault();
			$('#pdledit_gui_offcanvas_body').html(atob('{{$modules}}'));
			offcanvas.show();
		});

		$(document).on('click', '#pdledit_gui_save', function(e) {
			e.preventDefault();
		});

		$(document).on('click', '#pdledit_gui_reset', function(e) {
			e.preventDefault();
		});

	});
</script>
